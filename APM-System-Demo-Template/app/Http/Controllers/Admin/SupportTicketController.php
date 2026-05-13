<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $tickets = SupportTicket::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.support-tickets.index', [
            'tickets' => $tickets,
            'status' => $status,
        ]);
    }

    public function show(SupportTicket $supportTicket): View
    {
        return view('admin.support-tickets.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function update(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $supportTicket->update($validated);

        return redirect()
            ->route('admin.support-tickets.show', $supportTicket)
            ->with('status', 'Ticket updated.');
    }
}
