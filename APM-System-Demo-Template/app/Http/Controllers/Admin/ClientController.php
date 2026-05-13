<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ClientRenamePropagationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $clients = Client::query()
            ->when($status === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($status === 'all', fn ($query) => $query->withTrashed())
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('tin_number', 'like', "%{$search}%")
                    ->orWhere('business_style', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'tin_number' => ['nullable', 'string', 'max:100'],
            'business_style' => ['nullable', 'string', 'max:255'],
        ]);

        Client::create($validated);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'client-created');
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.edit', [
            'client' => $client,
        ]);
    }

    public function update(
        Request $request,
        Client $client,
        ClientRenamePropagationService $clientRenamePropagationService
    ): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'tin_number' => ['nullable', 'string', 'max:100'],
            'business_style' => ['nullable', 'string', 'max:255'],
        ]);

        $oldName = (string) $client->name;
        $client->update($validated);
        $clientRenamePropagationService->propagate($client->fresh(), $oldName);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'client-updated');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'client-deleted');
    }

    public function restore(int $clientId): RedirectResponse
    {
        $client = Client::onlyTrashed()->findOrFail($clientId);
        $client->restore();

        return redirect()
            ->route('admin.clients.index', ['status' => 'archived'])
            ->with('status', 'client-restored');
    }
}
