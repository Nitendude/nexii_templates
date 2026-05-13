<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\User;
use App\Models\Client;
use App\Services\JobOrderPdfPackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class JobOrderController extends Controller
{
    private const ALLOWED_JOB_CODES = ['FCL', 'LCL', 'AMO', 'AIR', 'PKL', 'BKK', 'NTC', 'OMB', 'ESB'];

    public function index(Request $request)
    {
        $this->authorizeAccess($request->user());

        $search = $request->string('q')->toString();

        $query = JobOrder::query()
            ->with(['createdBy', 'client'])
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($inner) use ($search) {
                    $inner->where('consignee', 'like', "%{$search}%")
                        ->orWhere('shipper', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('mo', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('CAST(number AS UNSIGNED) DESC')
            ->orderByDesc('number');

        $jobOrders = $query->paginate(10)->withQueryString();

        return view('job-orders.index', [
            'jobOrders' => $jobOrders,
            'canCreate' => $this->canCreate($request->user()),
            'search' => $search,
        ]);
    }

    public function show(Request $request, JobOrder $jobOrder)
    {
        $this->authorizeAccess($request->user());

        $jobOrder->load([
            'assignedUser',
            'attachments',
            'client',
            'billingStatements',
            'serviceInvoices',
            'debitCreditNotes',
        ]);

        return view('job-orders.show', [
            'jobOrder' => $jobOrder,
            'canManage' => $this->canEdit($request->user(), $jobOrder),
            'canEditStatus' => $this->canEditStatus($request->user(), $jobOrder),
            'assignees' => $this->assignees(),
            'canAssign' => false,
            'clients' => $this->clients(),
            'serverScans' => $this->serverScanFiles($request->user()),
            'serverScanInboxPath' => $this->serverScanInboxPath($request->user()),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorizeCreate($request->user());

        return view('job-orders.create', [
            'jobOrder' => new JobOrder(),
            'assignees' => $this->assignees(),
            'nextJoNumber' => $this->nextJoNumber(),
            'canAssign' => false,
            'clients' => $this->clients(),
            'existingJoNumbers' => JobOrder::query()
                ->whereNotNull('number')
                ->pluck('number')
                ->map(fn ($number) => trim((string) $number))
                ->filter()
                ->unique()
                ->values(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeCreate($request->user());

        $validated = $this->validateJobOrder($request);
        if (!$request->user()->isAdmin()) {
            unset($validated['assigned_user_id']);
        }
        $validated['number'] = $validated['number'] ?: $this->nextJoNumber();
        $validated = $this->syncCoWithAssignee($validated);
        $validated['created_by_user_id'] = $request->user()->id;

        $jobOrder = JobOrder::create($validated);
        $this->storeAttachments($request, $jobOrder);

        return redirect()
            ->route('operations.job-orders.index')
            ->with('status', 'job-order-created');
    }

    public function checkNumber(Request $request): JsonResponse
    {
        $this->authorizeAccess($request->user());

        $number = trim((string) $request->query('number', ''));
        $excludeId = $request->query('exclude_id');

        if ($number === '') {
            return response()->json([
                'exists' => false,
                'message' => null,
            ]);
        }

        $query = JobOrder::query()
            ->whereRaw('TRIM(number) = ?', [$number]);

        if (is_numeric($excludeId)) {
            $query->where('id', '!=', (int) $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? "JO number {$number} already exists." : null,
        ]);
    }

    public function edit(Request $request, JobOrder $jobOrder)
    {
        $this->authorizeEdit($request->user(), $jobOrder);

        $jobOrder->load('client');

        return view('job-orders.edit', [
            'jobOrder' => $jobOrder,
            'assignees' => $this->assignees(),
            'canAssign' => false,
            'clients' => $this->clients(),
            'existingJoNumbers' => JobOrder::query()
                ->whereNotNull('number')
                ->where('id', '!=', $jobOrder->id)
                ->pluck('number')
                ->map(fn ($number) => trim((string) $number))
                ->filter()
                ->unique()
                ->values(),
        ]);
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        if ($this->canEdit($request->user(), $jobOrder)) {
            $validated = $this->validateJobOrder($request, $jobOrder);
            if (!$request->user()->isAdmin()) {
                unset($validated['assigned_user_id']);
            }
            $validated = $this->syncCoWithAssignee($validated);
            $jobOrder->update($validated);
            $this->storeAttachments($request, $jobOrder);
        } elseif ($this->canEditStatus($request->user(), $jobOrder)) {
            $validated = $request->validate([
                'status' => ['nullable', 'string', 'max:255'],
            ]);
            $jobOrder->update([
                'status' => $validated['status'],
            ]);
        } else {
            abort(403);
        }

        return redirect()
            ->route('operations.job-orders.show', $jobOrder)
            ->with('status', 'job-order-updated');
    }

    public function storeSavedAttachments(Request $request, JobOrder $jobOrder)
    {
        $this->authorizeEdit($request->user(), $jobOrder);

        $this->storeAttachments($request, $jobOrder);

        return redirect()
            ->route('operations.job-orders.show', $jobOrder)
            ->with('status', 'job-order-attachments-uploaded');
    }

    public function attachServerScan(Request $request, JobOrder $jobOrder)
    {
        $this->authorizeEdit($request->user(), $jobOrder);

        $validated = $request->validate([
            'scan_file' => ['required', 'string', 'max:255'],
        ]);

        $inbox = $this->serverScanInboxPath($request->user());
        $filename = basename($validated['scan_file']);
        $source = null;
        $realInbox = null;
        $realSource = null;

        foreach ($this->serverScanInboxPaths($request->user()) as $candidateInbox) {
            $candidateSource = $candidateInbox . DIRECTORY_SEPARATOR . $filename;
            $candidateRealInbox = realpath($candidateInbox);
            $candidateRealSource = realpath($candidateSource);

            if ($candidateRealInbox && $candidateRealSource && str_starts_with($candidateRealSource, $candidateRealInbox . DIRECTORY_SEPARATOR) && is_file($candidateRealSource)) {
                $source = $candidateSource;
                $realInbox = $candidateRealInbox;
                $realSource = $candidateRealSource;
                break;
            }
        }

        if (!$source || !$realInbox || !$realSource) {
            return back()->withErrors(['scan_file' => 'The selected server scan could not be found.']);
        }

        if (!$this->isAllowedScanFile($realSource)) {
            return back()->withErrors(['scan_file' => 'The selected server scan file type is not allowed.']);
        }

        $safeFilename = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $storedFilename = 'server-scan-' . now()->format('Ymd-His') . '-' . ($safeFilename ?: 'document') . '.' . $extension;
        $destination = "job-orders/{$jobOrder->id}/{$storedFilename}";

        $stream = fopen($realSource, 'rb');
        Storage::disk('public')->put($destination, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        $jobOrder->attachments()->create([
            'filename' => $filename,
            'path' => $destination,
            'mime_type' => File::mimeType($realSource),
            'size' => filesize($realSource) ?: 0,
        ]);

        @unlink($realSource);

        return redirect()
            ->route('operations.job-orders.show', $jobOrder)
            ->with('status', 'job-order-server-scan-attached');
    }

    public function downloadPackage(Request $request, JobOrder $jobOrder, JobOrderPdfPackageService $jobOrderPdfPackageService)
    {
        $this->authorizeAccess($request->user());

        $pdf = $jobOrderPdfPackageService->make($jobOrder);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $jobOrderPdfPackageService->filename($jobOrder) . '"',
        ]);
    }

    public function emailPackage(Request $request, JobOrder $jobOrder, JobOrderPdfPackageService $jobOrderPdfPackageService)
    {
        $this->authorizeAccess($request->user());

        $validated = $request->validate([
            'email_to' => ['required', 'email', 'max:255'],
            'email_cc' => ['nullable', 'string', 'max:1000'],
            'email_subject' => ['nullable', 'string', 'max:255'],
            'email_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $cc = collect(explode(',', (string) ($validated['email_cc'] ?? '')))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->values();

        $invalidCc = $cc->first(fn ($email) => !filter_var($email, FILTER_VALIDATE_EMAIL));
        if ($invalidCc) {
            return back()
                ->withErrors(['email_cc' => 'Invalid CC email address: ' . $invalidCc])
                ->withInput();
        }

        $user = $request->user();
        $joNumber = trim(implode(' ', array_filter([$jobOrder->code, $jobOrder->mo, $jobOrder->number]))) ?: ($jobOrder->number ?? $jobOrder->id);
        $subject = $validated['email_subject'] ?: 'JO ' . $joNumber . ' Complete PDF Package';
        $body = trim((string) ($validated['email_message'] ?? ''));
        if ($body === '') {
            $body = "Good day,\n\nPlease see attached complete PDF package for JO {$joNumber}.\n\nThank you.";
        }

        $pdf = $jobOrderPdfPackageService->make($jobOrder);
        $filename = $jobOrderPdfPackageService->filename($jobOrder);

        Mail::raw($body, function ($message) use ($validated, $cc, $subject, $pdf, $filename, $user) {
            $message->to($validated['email_to'])
                ->from($user->email, $user->name)
                ->replyTo($user->email, $user->name)
                ->subject($subject)
                ->attachData($pdf, $filename, ['mime' => 'application/pdf']);

            if ($cc->isNotEmpty()) {
                $message->cc($cc->all());
            }
        });

        return redirect()
            ->route('operations.job-orders.show', $jobOrder)
            ->with('status', 'job-order-package-emailed');
    }

    private function validateJobOrder(Request $request, ?JobOrder $jobOrder = null): array
    {
        $request->merge([
            'number' => trim((string) $request->input('number', '')),
        ]);

        $clientNames = Client::query()->pluck('name')->all();

        $validated = $request->validate([
            'code' => ['nullable', 'string', Rule::in(self::ALLOWED_JOB_CODES)],
            'mo' => ['nullable', 'string', 'max:50'],
            'number' => ['nullable', 'string', 'max:50'],
            'jo_date' => ['nullable', 'date'],
            'costing' => ['nullable', 'string', 'max:100'],
            'co' => ['nullable', 'string', 'max:100'],
            'port' => ['nullable', 'string', 'max:100'],
            'eta' => ['nullable', 'date'],
            'demurrage_date' => ['nullable', 'date'],
            'detention_date' => ['nullable', 'date'],
            'port_storage_date' => ['nullable', 'date'],
            'discharge_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'date_delivered' => ['nullable', 'date'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'consignee' => ['nullable', 'string', 'max:255', Rule::in($clientNames)],
            'shipper' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'origin' => ['nullable', 'string', 'max:255'],
            'po_number' => ['nullable', 'string', 'max:100'],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'vessel_voyage_no' => ['nullable', 'string', 'max:255'],
            'date_of_arrival' => ['nullable', 'date'],
            'bl_awb_no' => ['nullable', 'string', 'max:100'],
            'no_of_container' => ['nullable', 'string', 'max:50'],
            'shipping_lines' => ['nullable', 'string', 'max:255'],
            'no_of_packages' => ['nullable', 'string', 'max:50'],
            'kind_of_packages' => ['nullable', 'string', 'max:100'],
            'gross_weight' => ['nullable', 'numeric', 'min:0'],
            'no_of_cbm' => ['nullable', 'numeric', 'min:0'],
            'entry_no' => ['nullable', 'string', 'max:100'],
            'ctnr_deposit' => ['nullable', 'numeric', 'min:0'],
            'date_refunded' => ['nullable', 'date'],
            'remarks_location' => ['nullable', 'string', 'max:2000'],
            'co_loader_forwarder' => ['nullable', 'string', 'max:255'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        if (empty($validated['client_id']) && empty($validated['consignee'])) {
            throw ValidationException::withMessages([
                'consignee' => 'Please select a consignee from the client list.',
            ]);
        }

        $normalizedNumber = trim((string) ($validated['number'] ?? ''));
        if ($normalizedNumber !== '') {
            $duplicateExists = JobOrder::query()
                ->whereRaw('TRIM(number) = ?', [$normalizedNumber])
                ->when($jobOrder, fn ($query) => $query->where('id', '!=', $jobOrder->id))
                ->exists();

            if ($duplicateExists) {
                throw ValidationException::withMessages([
                    'number' => "JO number {$normalizedNumber} already exists. Please use a different JO number.",
                ]);
            }
        }

        return $this->syncClientReference($validated);
    }

    private function assignees()
    {
        return User::query()
            ->orderBy('name')
            ->get();
    }

    private function clients()
    {
        return Client::query()
            ->orderBy('name')
            ->orderBy('address')
            ->get();
    }

    private function serverScanInboxPath(User $user): string
    {
        return $this->serverScanInboxBasePath() . DIRECTORY_SEPARATOR . 'user-' . $user->id;
    }

    private function serverScanFiles(User $user)
    {
        return collect($this->serverScanInboxPaths($user))
            ->flatMap(function ($inbox) {
                File::ensureDirectoryExists($inbox);

                return collect(File::files($inbox));
            })
            ->filter(fn ($file) => $this->isAllowedScanFile($file->getPathname()))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->take(25)
            ->map(fn ($file) => [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime()),
            ])
            ->values();
    }

    private function serverScanInboxPaths(User $user): array
    {
        return collect([
            $this->serverScanInboxPath($user),
        ])
            ->map(fn ($path) => rtrim($path, DIRECTORY_SEPARATOR))
            ->unique()
            ->values()
            ->all();
    }

    private function serverScanInboxBasePath(): string
    {
        return rtrim(env('APM_SCAN_INBOX_BASE', env('APM_SCAN_INBOX', '/home/apmserver/APM-Scanner-Inbox')), DIRECTORY_SEPARATOR);
    }

    private function isAllowedScanFile(string $path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), [
            'pdf',
            'jpg',
            'jpeg',
            'png',
            'webp',
            'bmp',
            'tif',
            'tiff',
        ], true);
    }

    private function authorizeAccess(User $user): void
    {
        if (!$user->hasAccess('job-orders')) {
            abort(403);
        }
    }

    private function authorizeCreate(User $user): void
    {
        if ($this->canCreate($user)) {
            return;
        }
        abort(403);
    }

    private function authorizeEdit(User $user, JobOrder $jobOrder): void
    {
        if ($this->canEdit($user, $jobOrder)) {
            return;
        }

        abort(403);
    }

    private function canCreate(User $user): bool
    {
        return $user->hasAccess('job-orders');
    }

    private function canEdit(User $user, JobOrder $jobOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $jobOrder->created_by_user_id === $user->id;
    }

    private function canEditStatus(User $user, JobOrder $jobOrder): bool
    {
        return $this->canEdit($user, $jobOrder);
    }

    private function nextJoNumber(): string
    {
        $max = JobOrder::query()
            ->selectRaw('MAX(CAST(number AS UNSIGNED)) as max_no')
            ->value('max_no');

        if (!$max || (int) $max < 12392) {
            return '12392';
        }

        return (string) ((int) $max + 1);
    }

    private function syncCoWithAssignee(array $validated): array
    {
        if (!empty($validated['assigned_user_id'])) {
            $assignee = User::find($validated['assigned_user_id']);
            if ($assignee) {
                $validated['co'] = $assignee->name;
            }
        }

        return $validated;
    }

    private function syncClientReference(array $validated): array
    {
        $clientId = $validated['client_id'] ?? null;
        $consignee = trim((string) ($validated['consignee'] ?? ''));

        if ($clientId) {
            $client = Client::find($clientId);
            if ($client) {
                $validated['client_id'] = $client->id;
                $validated['consignee'] = $client->name;

                return $validated;
            }
        }

        if ($consignee === '') {
            $validated['client_id'] = null;

            return $validated;
        }

        $matches = Client::query()
            ->where('name', $consignee)
            ->orderBy('id')
            ->get();

        $validated['client_id'] = $matches->count() === 1
            ? $matches->first()->id
            : null;
        $validated['consignee'] = $consignee;

        return $validated;
    }

    private function storeAttachments(Request $request, JobOrder $jobOrder): void
    {
        $request->validate([
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);

        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store("job-orders/{$jobOrder->id}", 'public');

            $jobOrder->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
