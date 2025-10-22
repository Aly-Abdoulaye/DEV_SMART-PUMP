<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['company', 'user', 'assignedTo'])
                               ->latest()
                               ->paginate(20);
        
        $stats = [
            'pending' => SupportTicket::where('status', 'pending')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'total' => SupportTicket::count(),
        ];

        return view('super-admin.support.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['company', 'user', 'assignedTo', 'replies.user']);
        return view('super-admin.support.show', compact('ticket'));
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'response' => 'required|string',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to']
        ]);

        // Ajouter une réponse
        if ($validated['response']) {
            $ticket->replies()->create([
                'user_id' => auth()->id(),
                'message' => $validated['response'],
                'is_internal' => false,
            ]);
        }

        // Si le ticket est résolu ou fermé
        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $ticket->update(['closed_at' => now()]);
        }

        return redirect()->route('super-admin.support.show', $ticket)
            ->with('success', 'Ticket mis à jour avec succès.');
    }

    public function broadcastForm()
    {
        $companies = Company::where('is_active', true)->get();
        return view('super-admin.support.broadcast', compact('companies'));
    }

    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:maintenance,update,alert,info',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'schedule' => 'nullable|date|after:now',
        ]);

        $companies = $validated['companies'] 
            ? Company::whereIn('id', $validated['companies'])->get()
            : Company::where('is_active', true)->get();

        $count = Notification::createBroadcast(
            $validated['type'],
            $validated['subject'],
            $validated['message'],
            auth()->id(),
            $companies,
            $validated['schedule'] ?? null
        );

        $scheduleInfo = $validated['schedule'] ? " programmée pour " . \Carbon\Carbon::parse($validated['schedule'])->format('d/m/Y H:i') : '';

        return redirect()->route('super-admin.support.index')
            ->with('success', "Notification{$scheduleInfo} envoyée à {$count} entreprises.");
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        return redirect()->route('super-admin.support.show', $ticket)
            ->with('success', 'Ticket assigné avec succès.');
    }
}