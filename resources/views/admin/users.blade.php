@extends('admin.layouts.app')

@section('content')
<!-- Header Banner -->
<div class="p-6 rounded-2xl bg-gradient-to-r from-amber-950 via-neutral-900 to-black border border-amber-500/30 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">DEX TRADE NETWORK</span>
        <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">USER & TEAM MANAGEMENT</h1>
        <p class="text-xs text-neutral-400 mt-1">Manage platform members, binary legs ratio (50:50), active packages, and total business.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-400 font-bold text-xs border border-amber-500/30">
            50:50 Leg Ratio Active
        </span>
    </div>
</div>

<!-- Team A vs Team B Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-6 rounded-2xl pdf-package-card shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">PRIMARY VOLUME</span>
                <h3 class="text-xl font-black text-white">TEAM A BUSINESS</h3>
            </div>
            <div class="pdf-num-badge shrink-0">50%</div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-amber-400">$1,850,000</span>
            <span class="text-xs text-neutral-400">Total Team A Volume</span>
        </div>
        <div class="w-full bg-black/80 h-2 rounded-full overflow-hidden border border-amber-500/30">
            <div class="bg-amber-500 h-full w-1/2"></div>
        </div>
        <p class="text-xs text-neutral-400">Calculated on 50:50 ratio for Team Salary & Matching Income.</p>
    </div>

    <div class="p-6 rounded-2xl bg-panel border border-emerald-500/40 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">SECONDARY VOLUME</span>
                <h3 class="text-xl font-black text-white">WEAKER LEG BUSINESS</h3>
            </div>
            <div class="pdf-num-badge shrink-0">50%</div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-emerald-400">$1,600,000</span>
            <span class="text-xs text-muted">Total Remaining Volume</span>
        </div>
        <div class="w-full bg-neutral-900 h-2 rounded-full overflow-hidden">
            <div class="bg-emerald-500 h-full w-1/2"></div>
        </div>
        <p class="text-xs text-neutral-400">Matching Volume: $1,600,000 (5% Matching Commission Distributed).</p>
    </div>
</div>

<!-- Users List Table with Live Search Filter & Pagination -->
<div class="p-6 rounded-2xl bg-panel border border-border shadow-xl space-y-4">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-white">Platform Members Directory</h3>
            <p class="text-xs text-neutral-400">Search and filter active users</p>
        </div>

        <!-- Filter & Search Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                <input type="text" id="userSearchInput" placeholder="Search User ID or Name..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-bg border border-amber-500/30 text-xs text-white focus:outline-none focus:border-amber-400" oninput="filterUserTable()">
            </div>
            <select id="packageFilter" class="px-3 py-2 rounded-xl bg-bg border border-amber-500/30 text-xs text-amber-400 font-bold focus:outline-none" onchange="filterUserTable()">
                <option value="">All Packages</option>
                <option value="Package 5">Package 5 ($5,000+)</option>
                <option value="Package 4">Package 4 ($1,000)</option>
                <option value="Package 3">Package 3 ($500)</option>
                <option value="Package 2">Package 2 ($100)</option>
                <option value="Package 1">Package 1 ($10)</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" id="userTable">
            <thead class="bg-bg text-muted uppercase text-xs font-bold">
                <tr>
                    <th class="p-3 rounded-l-xl">User ID</th>
                    <th class="p-3">Member Name</th>
                    <th class="p-3">Direct Referrals</th>
                    <th class="p-3">Active Package</th>
                    <th class="p-3">Power Leg Vol</th>
                    <th class="p-3">Weaker Leg Vol</th>
                    <th class="p-3">Total Earned</th>
                    <th class="p-3 rounded-r-xl">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-neutral-300" id="userTableBody">
                <tr class="user-row hover:bg-border/30 transition">
                    <td class="p-3 font-bold text-amber-400 user-id">NX1001</td>
                    <td class="p-3 font-semibold text-white user-name">Alex Johnson</td>
                    <td class="p-3"><span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 font-bold text-xs">12 Directs</span></td>
                    <td class="p-3 user-pkg"><span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-bold text-xs">Package 5 ($5,000+)</span></td>
                    <td class="p-3 font-mono text-xs">$250,000</td>
                    <td class="p-3 font-mono text-xs">$220,000</td>
                    <td class="p-3 font-extrabold text-amber-400">$48,500.00</td>
                    <td class="p-3"><button class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-bold hover:bg-amber-500/20">View Tree</button></td>
                </tr>
                <tr class="user-row hover:bg-border/30 transition">
                    <td class="p-3 font-bold text-amber-400 user-id">NX1002</td>
                    <td class="p-3 font-semibold text-white user-name">Sophia Martinez</td>
                    <td class="p-3"><span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 font-bold text-xs">8 Directs</span></td>
                    <td class="p-3 user-pkg"><span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-bold text-xs">Package 4 ($1,000)</span></td>
                    <td class="p-3 font-mono text-xs">$110,000</td>
                    <td class="p-3 font-mono text-xs">$95,000</td>
                    <td class="p-3 font-extrabold text-amber-400">$19,200.00</td>
                    <td class="p-3"><button class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-bold hover:bg-amber-500/20">View Tree</button></td>
                </tr>
                <tr class="user-row hover:bg-border/30 transition">
                    <td class="p-3 font-bold text-amber-400 user-id">NX1003</td>
                    <td class="p-3 font-semibold text-white user-name">Robert Smith</td>
                    <td class="p-3"><span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 font-bold text-xs">5 Directs</span></td>
                    <td class="p-3 user-pkg"><span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-bold text-xs">Package 3 ($500)</span></td>
                    <td class="p-3 font-mono text-xs">$50,000</td>
                    <td class="p-3 font-mono text-xs">$48,000</td>
                    <td class="p-3 font-extrabold text-amber-400">$8,400.00</td>
                    <td class="p-3"><button class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-bold hover:bg-amber-500/20">View Tree</button></td>
                </tr>
                <tr class="user-row hover:bg-border/30 transition">
                    <td class="p-3 font-bold text-amber-400 user-id">NX1004</td>
                    <td class="p-3 font-semibold text-white user-name">Emma Williams</td>
                    <td class="p-3"><span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 font-bold text-xs">15 Directs</span></td>
                    <td class="p-3 user-pkg"><span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-bold text-xs">Package 5 ($5,000+)</span></td>
                    <td class="p-3 font-mono text-xs">$340,000</td>
                    <td class="p-3 font-mono text-xs">$310,000</td>
                    <td class="p-3 font-extrabold text-amber-400">$62,100.00</td>
                    <td class="p-3"><button class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-bold hover:bg-amber-500/20">View Tree</button></td>
                </tr>
                <tr class="user-row hover:bg-border/30 transition">
                    <td class="p-3 font-bold text-amber-400 user-id">NX1005</td>
                    <td class="p-3 font-semibold text-white user-name">Daniel Brown</td>
                    <td class="p-3"><span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 font-bold text-xs">3 Directs</span></td>
                    <td class="p-3 user-pkg"><span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-bold text-xs">Package 2 ($100)</span></td>
                    <td class="p-3 font-mono text-xs">$15,000</td>
                    <td class="p-3 font-mono text-xs">$12,000</td>
                    <td class="p-3 font-extrabold text-amber-400">$2,800.00</td>
                    <td class="p-3"><button class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-bold hover:bg-amber-500/20">View Tree</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-border">
        <p class="text-xs text-neutral-400" id="paginationInfo">Showing 1 to 5 of 5 entries</p>
        <div class="flex items-center gap-1.5">
            <button class="pagination-btn" id="prevBtn" disabled onclick="changePage(-1)">Previous</button>
            <button class="pagination-btn active-page">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">Next</button>
        </div>
    </div>
</div>

<script>
    function filterUserTable() {
        const query = document.getElementById('userSearchInput').value.toLowerCase();
        const pkgFilter = document.getElementById('packageFilter').value.toLowerCase();
        const rows = document.querySelectorAll('.user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const id = row.querySelector('.user-id').innerText.toLowerCase();
            const name = row.querySelector('.user-name').innerText.toLowerCase();
            const pkg = row.querySelector('.user-pkg').innerText.toLowerCase();

            const matchesSearch = id.includes(query) || name.includes(query);
            const matchesPkg = pkgFilter === '' || pkg.includes(pkgFilter);

            if (matchesSearch && matchesPkg) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('paginationInfo').innerText = `Showing ${visibleCount} of ${rows.length} entries`;
    }

    function changePage(direction) {
        // Interactive pagination feedback
    }
</script>
@endsection
