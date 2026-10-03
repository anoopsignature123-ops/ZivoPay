<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    use ApiResponse;

    /**
     * Get Help & Support Contact Info & FAQs API.
     */
    public function contact(Request $request): JsonResponse
    {
        $contactData = [
            'support_email' => config('mail.from.address', 'support@zivopay.com'),
            'support_phone' => '+91 98765 43210',
            'whatsapp' => '+91 98765 43210',
            'working_hours' => 'Monday to Saturday: 9:00 AM - 8:00 PM',
            'address' => 'ZIVO PAY Financial Technologies Pvt Ltd, Mumbai, India',
            'faqs' => [
                [
                    'question' => 'How long does a Mobile / DTH Recharge take?',
                    'answer' => 'All recharges on ZIVO PAY are processed instantly in real-time within 5-10 seconds.',
                ],
                [
                    'question' => 'What happens if my recharge fails?',
                    'answer' => 'If a recharge attempt fails due to operator server issues, the deducted amount is instantly refunded back to your Deposit Wallet.',
                ],
                [
                    'question' => 'How can I add funds to my ZIVO PAY wallet?',
                    'answer' => 'Go to Fund Wallet section, click Add Fund, select payment gateway (UPI/QR Code/Netbanking) and complete payment.',
                ],
                [
                    'question' => 'How do I raise a support ticket?',
                    'answer' => 'Select Create Ticket under Support tab, select category (Recharge/Deposit/Withdrawal), enter your issue details and submit.',
                ],
            ],
        ];

        return $this->successResponse($contactData, 'Help & Support contact details retrieved successfully.');
    }

    /**
     * Get List of User Support Tickets API.
     */
    public function tickets(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $tickets = SupportTicket::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'category' => $ticket->category,
                        'category_formatted' => ucfirst(str_replace('_', ' ', $ticket->category)),
                        'subject' => $ticket->subject,
                        'message' => $ticket->message,
                        'priority' => ucfirst($ticket->priority),
                        'status' => ucfirst($ticket->status),
                        'status_color' => match (strtolower($ticket->status)) {
                            'pending' => 'orange',
                            'in_progress', 'open' => 'blue',
                            'resolved' => 'green',
                            'closed' => 'gray',
                            default => 'black',
                        },
                        'has_admin_reply' => ! empty($ticket->admin_reply),
                        'admin_reply' => $ticket->admin_reply,
                        'replied_at' => $ticket->replied_at?->toIso8601String(),
                        'replied_at_formatted' => $ticket->replied_at?->format('d M Y, h:i A'),
                        'created_at' => $ticket->created_at->toIso8601String(),
                        'created_at_formatted' => $ticket->created_at->format('d M Y, h:i A'),
                    ];
                });

            return $this->successResponse([
                'tickets' => $tickets,
                'total_tickets' => $tickets->count(),
                'pending_tickets' => $tickets->where('status', 'Pending')->count(),
                'resolved_tickets' => $tickets->where('status', 'Resolved')->count(),
            ], 'Support tickets retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch support tickets: '.$e->getMessage(), 500);
        }
    }

    /**
     * Create a New Support Ticket API.
     */
    public function createTicket(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|in:recharge,deposit,withdrawal,account,general,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:5',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors(), 'Ticket submission failed validation.');
        }

        try {
            $user = $request->user();

            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'user_id' => $user->id,
                'category' => strtolower($request->input('category')),
                'subject' => trim($request->input('subject')),
                'message' => trim($request->input('message')),
                'priority' => strtolower($request->input('priority', 'medium')),
                'status' => 'pending',
            ]);

            return $this->successResponse([
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'category' => $ticket->category,
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'priority' => ucfirst($ticket->priority),
                    'status' => ucfirst($ticket->status),
                    'created_at' => $ticket->created_at->toIso8601String(),
                    'created_at_formatted' => $ticket->created_at->format('d M Y, h:i A'),
                ],
            ], 'Support ticket created successfully. Our team will review and respond shortly.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit support ticket: '.$e->getMessage(), 500);
        }
    }

    /**
     * View Single Support Ticket Details API.
     */
    public function showTicket(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $ticketId = $request->id;
             
            $ticket = SupportTicket::where('user_id', $user->id)
                ->where(function ($q) use ($ticketId) {
                    if (is_numeric($ticketId)) {
                        $q->where('id', $ticketId)->orWhere('ticket_number', $ticketId);
                    } else {
                        $q->where('ticket_number', $ticketId);
                    }
                })
                ->first();

            if (! $ticket) {
                return $this->errorResponse('Support ticket record not found.', 404);
            }

            return $this->successResponse([
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'category' => $ticket->category,
                    'category_formatted' => ucfirst(str_replace('_', ' ', $ticket->category)),
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'priority' => ucfirst($ticket->priority),
                    'status' => ucfirst($ticket->status),
                    'admin_reply' => $ticket->admin_reply,
                    'replied_at' => $ticket->replied_at?->toIso8601String(),
                    'replied_at_formatted' => $ticket->replied_at?->format('d M Y, h:i A'),
                    'created_at' => $ticket->created_at->toIso8601String(),
                    'created_at_formatted' => $ticket->created_at->format('d M Y, h:i A'),
                ],
            ], 'Support ticket details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch ticket details: '.$e->getMessage(), 500);
        }
    }
}