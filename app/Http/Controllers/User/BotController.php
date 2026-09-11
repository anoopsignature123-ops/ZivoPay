<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BotController extends Controller
{
    /**
     * Display initial Start BOT Overview page.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('user.bot.index', compact('user'));
    }

    /**
     * Display TradingView Candlestick Chart page with pair selector & Start BOT trigger.
     */
    public function tradingView(): View
    {
        $user = Auth::user();

        return view('user.bot.trading', compact('user'));
    }

    /**
     * Process One-Time BOT Activation.
     */
    public function activate(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Account Activation Guard: User must have an active account / package to start the BOT
        if ($user->status !== 'active') {
            return redirect()->route('user.bot.trading')->with('error', '⚠️ Please activate your account first by purchasing an investment package before starting the Quant Trading BOT!');
        }

        if ($user->is_bot_active) {
            return redirect()->route('user.bot.trading')->with('info', 'Quant Trading BOT is already ACTIVE & mining ROI 24/7!');
        }

        $user->update([
            'is_bot_active' => true,
            'bot_activated_at' => now(),
        ]);

        return redirect()->route('user.bot.trading')->with('success', '🚀 Quant Trading BOT Activated Successfully! ROI Mining Enabled 24/7.');
    }
}
