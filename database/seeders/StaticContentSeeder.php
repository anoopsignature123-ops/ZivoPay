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
            // [

            //     'slug' => 'refund-policy',
            //     'title' => 'Refund & Cancellation Policy',
            //     'category' => 'legal',
            //     'content' => '<h2>Refund & Cancellation Policy</h2><p>All transaction refund requests are processed according to our automated wallet refund guidelines.</p><h3>1. Failed Recharges</h3><p>If money is deducted from your wallet but the operator recharge fails, funds are automatically returned to your Deposit Wallet within 5 minutes.</p><h3>2. Pending Transactions</h3><p>Transactions marked as pending are automatically reconciled with the operator gateway within 24 hours.</p>',
            //     'meta_title' => 'Refund & Cancellation Policy - ZIVO PAY',
            //     'meta_description' => 'ZIVO PAY refund policy for failed recharges and wallet transactions.',
            //     'is_active' => true,
            // ],
        ];

        foreach ($pages as $page) {
            StaticContent::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
