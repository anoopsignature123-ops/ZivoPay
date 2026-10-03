<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportAdminController extends Controller
{
    /**
     * Display listing of support tickets for Admin.
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::with('user')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->get('status')));
        }

        if ($request->filled('category')) {
            $query->where('category', strtolower($request->get('category')));
        }

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('referral_code', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => SupportTicket::count(),
            'pending' => SupportTicket::where('status', 'pending')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    /**
     * Show single ticket detail view.
     */
    public function show(SupportTicket $ticket): View
    {
        $ticket->load('user');

        return view('admin.support.show', compact('ticket'));
    }

    /**
     * Submit Admin reply and update ticket status.
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'admin_reply' => 'required|string|min:2',
            'status' => 'required|string|in:pending,in_progress,resolved,closed',
        ]);

        $ticket->update([
            'admin_reply' => trim($request->input('admin_reply')),
            'status' => strtolower($request->input('status')),
            'replied_at' => now(),
        ]);

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', 'Admin reply sent and ticket status updated successfully.');
    }
}
