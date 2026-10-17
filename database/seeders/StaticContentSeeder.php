<?php

namespace Database\Seeders;

use App\Models\StaticContent;
use Illuminate\Database\Seeder;

class StaticContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'terms-conditions',
                'title' => 'Terms & Conditions',
                'category' => 'legal',
                'content' => '<h2>Terms & Conditions</h2><p>Welcome to ZIVO PAY. By downloading, accessing or using our mobile application or web portal, you agree to be bound by these terms and conditions.</p><h3>1. User Account Registration</h3><p>Users must provide accurate, current, and complete information during registration. You are responsible for maintaining the confidentiality of your account login credentials and transactions.</p><h3>2. Recharge & Utility Payments</h3><p>All recharges, bill payments, and top-ups processed through ZIVO PAY are subject to operator availability. In the event of a failed transaction, the deducted amount is automatically refunded back to your Deposit Wallet.</p><h3>3. Wallet Transfers & Direct P2P</h3><p>P2P wallet transfers between registered ZIVO PAY members are final once processed. Users must double-check receiver details before confirming transfers.</p><h3>4. Contact & Disputes</h3><p>For any disputes or query resolutions, please raise a support ticket via our Help & Support desk or email us at support@zivopay.com.</p>',
                'meta_title' => 'Terms & Conditions - ZIVO PAY',
                'meta_description' => 'Read ZIVO PAY Terms and Conditions for mobile app recharges, utility bill payments, and financial wallet services.',
                'is_active' => true,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'category' => 'legal',
                'content' => '<h2>Privacy Policy</h2><p>At ZIVO PAY, protecting your personal data and privacy is our highest priority.</p><h3>1. Data We Collect</h3><p>We collect essential information required to deliver services, including your name, mobile number, email address, transaction records, and device identifiers.</p><h3>2. How We Use Your Data</h3><p>Your data is strictly utilized to process recharges, verify user accounts, prevent fraud, and send transaction alerts.</p><h3>3. Data Protection & Security</h3><p>We employ 256-bit SSL encryption, secure API tokens, and strict access controls to safeguard your financial and personal information.</p><h3>4. Contact Us</h3><p>If you have questions regarding our privacy practices, contact us at privacy@zivopay.com.</p>',
                'meta_title' => 'Privacy Policy - ZIVO PAY',
                'meta_description' => 'ZIVO PAY Privacy Policy explaining data protection, security measures, and user data privacy rights.',
                'is_active' => true,
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'category' => 'company',
                'content' => '<h2>Contact Us</h2><p>Have questions or need assistance? Our support team is available to help you 24/7.</p><h3>Customer Support Desk</h3><p><strong>Email:</strong> support@zivopay.com<br><strong>Phone / WhatsApp:</strong> +91 98765 43210<br><strong>Working Hours:</strong> Monday - Saturday: 9:00 AM - 8:00 PM</p><h3>Corporate Headquarters</h3><p>ZIVO PAY Financial Technologies Pvt Ltd<br>Tower B, Financial District, Mumbai, India</p>',
                'meta_title' => 'Contact Us - ZIVO PAY',
                'meta_description' => 'Contact ZIVO PAY customer support desk for recharge help, wallet queries, and technical assistance.',
                'is_active' => true,
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'category' => 'company',
                'content' => '<h2>About ZIVO PAY</h2><p>ZIVO PAY is a Next-Generation Digital Payments & Utility Services Platform delivering instant mobile recharges, DTH top-ups, utility bill payments, and financial wallet solutions.</p><h3>Our Vision</h3><p>To empower millions of users with seamless, secure, and lightning-fast digital financial transactions anytime, anywhere.</p>',
                'meta_title' => 'About Us - ZIVO PAY',
                'meta_description' => 'Learn about ZIVO PAY, India\'s trusted digital payments and utility recharge portal.',
                'is_active' => true,
            ],
            [
                'slug' => 'refund-policy',
                'title' => 'Refund Policy',
                'category' => 'legal',
                'content' => '<h2>Refund &amp; Cancellation Policy</h2><p>This policy explains how ZIVO PAY handles cancellations, returns, and refunds for services and shop orders. Refund eligibility depends on the product or service and the transaction status.</p><h3>1. Recharge and bill payments</h3><p>Completed recharges and bill payments generally cannot be cancelled. If a transaction is marked failed but money was debited, its status will be checked with the relevant payment or service provider and any confirmed refund will be processed to the original payment source or applicable account.</p><h3>2. Shop orders</h3><p>Cancellation, return, replacement, and refund eligibility for shop purchases depends on the item, order status, and applicable return conditions shown for that order. Contact support with your order ID before sending an item back. Refunds are initiated after the return or cancellation is reviewed and approved.</p><h3>3. Refund timelines</h3><p>After approval, the time for the funds to appear depends on the payment method, bank, and payment provider. Please allow the provider’s normal processing time and retain your transaction reference.</p><h3>4. Request help</h3><p>For a refund or transaction review, contact <a href="mailto:support@zivopay.com">support@zivopay.com</a> with your registered mobile number, transaction or order reference, date, and a short description. Do not send your password, OTP, card PIN, or UPI PIN.</p>',
                'meta_title' => 'Refund Policy - ZIVO PAY',
                'meta_description' => 'ZIVO PAY refund, cancellation, and return information for payments and shop orders.',
                'is_active' => true,
            ],
            [
                'slug' => 'grievance-redressal',
                'title' => 'Grievance Redressal',
                'category' => 'legal',
                'content' => '<h2>Grievance Redressal</h2><p>We aim to review and resolve concerns about ZIVO PAY services fairly. Please first contact our support team so we can locate the account or transaction and investigate.</p><h3>1. Submit a complaint</h3><p>Email <a href="mailto:support@zivopay.com">support@zivopay.com</a> using your registered email address. Include your name, registered mobile number, transaction or order reference, relevant dates, and a clear description of the issue. Attach only information needed to investigate; never share passwords, OTPs, card PINs, or UPI PINs.</p><h3>2. Review and updates</h3><p>Our team will acknowledge the complaint and review the available account and transaction records. We may contact you if more details are needed. We will communicate the outcome and any next steps through your registered contact details.</p><h3>3. Escalation</h3><p>If your concern remains unresolved after contacting support, reply to the same email thread and mark it for escalation. Include the earlier complaint reference and explain what remains unresolved.</p><p>Applicable laws and payment-provider processes may prescribe additional channels or timelines for particular services. Where relevant, those details will be provided in the applicable service terms.</p>',
                'meta_title' => 'Grievance Redressal - ZIVO PAY',
                'meta_description' => 'How to submit and escalate a complaint to ZIVO PAY support.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            StaticContent::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
