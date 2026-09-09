@extends('admin.layouts.app')

@section('title', 'Referral ROI Income Report')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-teal-400 uppercase tracking-widest mb-1">
                <i data-lucide="repeat" class="w-4 h-4 text-teal-400"></i>
                <span>4. Dex Trade Income Stream</span>
            </div>
            <h1 class="text-2xl font-black font-heading text-white uppercase tracking-wider">
                Referral ROI Income Report
            </h1>
            <p class="text-xs text-neutral-400 mt-1">Audit log of 0.5% daily yield paid to sponsors based on direct team total investment for 150 days</p>
        </div>
        <div class="px-5 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 shrink-0 text-right">
            <span class="text-[10px] font-bold text-amber-400 uppercase block">Total Referral ROI Distributed</span>
            <span class="text-2xl font-black font-mono text-emerald-400">${{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-amber-500/30 bg-amber-500/5">
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Txn ID</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Member Details</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Description</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Amount</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/10 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-amber-500/5 transition">
                            <td class="py-3 px-4 font-mono text-neutral-300">{{ $log->txn_number }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $log->user->name ?? 'User' }}</div>
                                <div class="text-[10px] text-amber-400/80 font-mono">{{ $log->user->referral_code ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 text-neutral-300">{{ $log->description }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-400">+${{ number_format($log->amount, 2) }}</td>
                            <td class="py-3 px-4 text-neutral-400 font-mono">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-neutral-400 italic">No Referral ROI income transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
