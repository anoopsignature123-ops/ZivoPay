<?php

namespace App\Http\Controllers;

use App\Models\StaticContent;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display static content page for Mobile App WebViews & Web Portal.
     */
    public function show(?string $slug = 'terms-conditions'): View
    {
        $targetSlug = (string) ($slug ?? 'terms-conditions');

        $normalizedSlug = match (strtolower(trim($targetSlug))) {
            'terms', 'terms-and-conditions' => 'terms-conditions',
            'privacy', 'privacy-and-policy' => 'privacy-policy',
            'contact' => 'contact-us',
            'about' => 'about-us',
            'refund' => 'refund-policy',
            default => strtolower(trim($targetSlug)),
        };

        $page = StaticContent::where('slug', $normalizedSlug)
            ->where('is_active', true)
            ->first();

        if (! $page && in_array($normalizedSlug, ['refund-policy', 'grievance-redressal'], true)) {
            $page = new StaticContent([
                'slug' => $normalizedSlug,
                'title' => $normalizedSlug === 'refund-policy' ? 'Refund Policy' : 'Grievance Redressal',
                'category' => 'legal',
                'content' => $normalizedSlug === 'refund-policy'
                    ? '<h2>Refund &amp; Cancellation Policy</h2><p>Refund eligibility depends on the product or service and the transaction status.</p><h3>Recharge and bill payments</h3><p>Completed transactions generally cannot be cancelled. If a transaction is marked failed after money was debited, contact support with the transaction reference so it can be reviewed with the relevant provider.</p><h3>Shop orders</h3><p>Cancellation, return, replacement, and refund eligibility depends on the item and order status. Contact support with your order ID before returning an item.</p><h3>Contact</h3><p>Email <a href="mailto:support@zivopay.com">support@zivopay.com</a> with your registered mobile number and transaction or order reference. Never share your password, OTP, card PIN, or UPI PIN.</p>'
                    : '<h2>Grievance Redressal</h2><p>For a concern about a ZIVO PAY service, contact our support team so we can review your account or transaction.</p><h3>Submit a complaint</h3><p>Email <a href="mailto:support@zivopay.com">support@zivopay.com</a> from your registered email address. Include your name, registered mobile number, transaction or order reference, relevant dates, and a description of the issue.</p><h3>Escalation</h3><p>If your concern remains unresolved, reply to the same email thread with your earlier complaint reference and explain what remains unresolved. Never share your password, OTP, card PIN, or UPI PIN.</p>',
                'meta_title' => ($normalizedSlug === 'refund-policy' ? 'Refund Policy' : 'Grievance Redressal').' - ZIVO PAY',
                'meta_description' => $normalizedSlug === 'refund-policy'
                    ? 'ZIVO PAY refund and cancellation information.'
                    : 'How to submit and escalate a complaint to ZIVO PAY support.',
                'updated_at' => now(),
            ]);
        }

        abort_if(! $page, 404);

        return view('pages.show', compact('page'));
    }

    /**
     * Alias method for index call.
     */
    public function index(?string $slug = 'terms-conditions'): View
    {
        return $this->show($slug);
    }

    /**
     * Fallback invoke method.
     */
    public function __invoke(?string $slug = 'terms-conditions'): View
    {
        return $this->show($slug);
    }
}
