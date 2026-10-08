<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->meta_title ?? $page->title . ' - ZIVO PAY' }}</title>
    <meta name="description" content="{{ $page->meta_description ?? '' }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #050b08;
            color: #e2e8f0;
        }
        .prose h1, .prose h2, .prose h3, .prose h4 {
            color: #ffffff;
            font-weight: 800;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            letter-spacing: -0.02em;
        }
        .prose h1 { font-size: 1.75rem; color: #10b981; }
        .prose h2 { font-size: 1.35rem; border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding-bottom: 0.4rem; color: #34d399; }
        .prose h3 { font-size: 1.1rem; color: #6ee7b7; }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.7;
            color: #94a3b8;
            font-size: 0.925rem;
        }
        .prose ul, .prose ol {
            margin-bottom: 1rem;
            padding-left: 1.25rem;
            color: #cbd5e1;
        }
        .prose ul { list-style-type: disc; }
        .prose ol { list-style-type: decimal; }
        .prose li { margin-bottom: 0.35rem; font-size: 0.9rem; }
        .prose a { color: #10b981; text-decoration: underline; }
        .prose strong { color: #ffffff; font-weight: 700; }
    </style>
</head>
<body class="min-h-screen antialiased selection:bg-emerald-500 selection:text-white p-4 sm:p-8">

    <!-- Background Decorative Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-72 bg-emerald-500/10 blur-[120px] pointer-events-none rounded-full"></div>

    <div class="max-w-3xl mx-auto relative z-10">

        <!-- Top Header for WebView -->
        <header class="mb-6 flex items-center justify-between pb-4 border-b border-emerald-500/20">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/fav.png') }}?v=999" alt="ZIVO PAY" class="w-8 h-8 object-contain">
                <div>
                    <h1 class="text-lg font-black text-white uppercase tracking-tight">ZIVO PAY</h1>
                    <p class="text-[10px] text-emerald-400 font-semibold tracking-wider uppercase">Legal & Compliance Portal</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                Official
            </span>
        </header>

        <!-- Main Content Card -->
        <main class="bg-neutral-900/90 border border-emerald-500/30 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur-md">
            <div class="mb-6">
                <span class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-widest block mb-1">
                    {{ strtoupper($page->category) }} DOCUMENTATION
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">
                    {{ $page->title }}
                </h1>
                <p class="text-xs text-neutral-400 mt-1">
                    Last Updated: {{ $page->updated_at->format('F d, Y') }}
                </p>
            </div>

            <article class="prose prose-invert max-w-none border-t border-neutral-800/80 pt-6">
                {!! $page->content !!}
            </article>
        </main>

        <!-- Footer -->
        <footer class="mt-8 text-center text-xs text-neutral-500 pb-6">
            <p>&copy; {{ date('Y') }} ZIVO PAY. All rights reserved.</p>
        </footer>
    </div>

</body>
</html>
