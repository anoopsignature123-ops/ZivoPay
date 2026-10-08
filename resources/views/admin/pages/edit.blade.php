@extends('admin.layouts.app')

@section('title', 'Edit ' . $page->title)

@section('content')
    <div class="w-full max-w-4xl mx-auto space-y-6 font-sans relative">

        <!-- Top Header Banner -->
        <div class="flex items-center justify-between p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl">
            <div>
                <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-emerald-400 hover:underline flex items-center gap-1 mb-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Static Pages
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-white font-heading uppercase">EDIT {{ strtoupper($page->title) }}</h1>
                <p class="text-xs text-neutral-300 mt-0.5">WebView URL: <span class="font-mono text-emerald-400">{{ url('/page/'.$page->slug) }}</span></p>
            </div>

            <a href="{{ url('/page/'.$page->slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/30 text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="eye" class="w-4 h-4"></i> Live Preview
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Page Edit Form -->
        <div class="p-6 sm:p-8 rounded-3xl bg-neutral-900/90 border border-emerald-500/20 shadow-2xl space-y-6">
            <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Page Title</label>
                        <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                            class="w-full px-4 py-3 rounded-2xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        @error('title')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Page Slug (Read-Only)</label>
                        <input type="text" value="{{ $page->slug }}" readonly
                            class="w-full px-4 py-3 rounded-2xl bg-neutral-950/60 border border-neutral-800/80 text-neutral-400 font-mono text-xs cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">
                        Rich Text Content Body (WebView Display Content)
                    </label>
                    <textarea id="pageContentEditor" name="content" rows="14" required
                        class="w-full p-4 rounded-2xl bg-neutral-950 border border-neutral-800 text-white text-xs font-mono focus:border-emerald-500 focus:outline-none leading-relaxed">{{ old('content', $page->content) }}</textarea>
                    <p class="text-[11px] text-neutral-500 mt-1">Use the rich text toolbar above to format headings, bold, lists, links, and text formatting.</p>
                    @error('content')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Meta Title (SEO)</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                            class="w-full px-4 py-3 rounded-2xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Status</label>
                        <select name="is_active" class="w-full px-4 py-3 rounded-2xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <option value="1" {{ old('is_active', $page->is_active) ? 'selected' : '' }}>Active (Enabled)</option>
                            <option value="0" {{ !old('is_active', $page->is_active) ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-neutral-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-neutral-300 text-xs font-bold transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Save Page Changes
                    </button>
                </div>
            </form>
        </div>

    <style>
        .ck-editor__editable_inline {
            min-height: 350px !important;
            background-color: #030712 !important;
            color: #f3f4f6 !important;
            border-radius: 0 0 1rem 1rem !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            padding: 1.25rem !important;
            font-size: 0.875rem !important;
        }
        .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
            background-color: #0f172a !important;
            border-radius: 1rem 1rem 0 0 !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            padding: 0.5rem !important;
        }
        .ck.ck-button, .ck.ck-dropdown__button {
            color: #e2e8f0 !important;
            border-radius: 0.5rem !important;
        }
        .ck.ck-button:hover, .ck.ck-dropdown__button:hover {
            background-color: #1e293b !important;
            color: #10b981 !important;
        }
        .ck.ck-button.ck-on {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        .ck.ck-list {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
        }
        .ck.ck-list__item .ck-button {
            color: #e2e8f0 !important;
        }
        .ck.ck-list__item .ck-button:hover {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
    </style>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('#pageContentEditor')) {
                ClassicEditor
                    .create(document.querySelector('#pageContentEditor'), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'bulletedList', 'numberedList', 'blockQuote', '|',
                                'link', 'insertTable', '|',
                                'undo', 'redo'
                            ]
                        }
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                    });
            }
        });
    </script>
@endsection
