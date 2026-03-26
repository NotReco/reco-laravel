<x-app-layout>
<x-slot:title>Tin tức</x-slot:title>

<div x-data="newsFilter()">


    {{-- ── Tags Filter ──────────────────────────────────────── --}}
    @if($tags->isNotEmpty())
    <section class="max-w-6xl mx-auto px-4 pt-8" x-data="{ showAllTags: false }">
        <div class="flex flex-wrap gap-2 items-center">
            <button @click="fetchArticles('{{ route('news.index') }}', '')"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-all border"
               :class="!activeTag ? 'bg-gray-900 text-white border-gray-900 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400 hover:text-gray-900 hover:bg-gray-50'">
                Tất cả
            </button>
            @foreach($tags as $index => $tag)
                <button @click="fetchArticles('{{ route('news.index', ['tag' => $tag->slug]) }}', '{{ $tag->slug }}')"
                   class="px-4 py-1.5 rounded-full text-sm font-medium transition-all border uppercase"
                   :class="activeTag === '{{ $tag->slug }}' ? 'bg-gray-900 text-white border-gray-900 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400 hover:text-gray-900 hover:bg-gray-50'"
                   x-show="{{ $index }} < 10 || showAllTags"
                   x-transition>
                    {{ $tag->name }}
                </button>
            @endforeach
            @if($tags->count() > 10)
                <button @click="showAllTags = !showAllTags"
                   class="px-4 py-1.5 rounded-full text-sm font-medium transition-all border border-dashed border-gray-300 text-gray-500 hover:border-gray-400 hover:text-gray-700 hover:bg-gray-50">
                    <span x-text="showAllTags ? 'Thu gọn' : '+{{ $tags->count() - 10 }} khác'"></span>
                </button>
            @endif
        </div>
    </section>
    @endif

    {{-- ── Articles Grid ────────────────────────────────────── --}}
    <section class="max-w-6xl mx-auto px-4 py-8 relative">
        {{-- Loading overlay --}}
        <div x-show="loading" x-transition.opacity 
             class="absolute inset-x-4 inset-y-8 bg-white/60 backdrop-blur-sm z-10 flex items-start pt-20 justify-center rounded-2xl" style="display: none;">
            <svg class="animate-spin h-8 w-8 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div id="articles-container" :class="loading ? 'opacity-50 pointer-events-none' : 'transition-opacity duration-300'">
            @include('news.partials.article_list', ['articles' => $articles])
        </div>
    </section>
</div>

<script>
function newsFilter() {
    return {
        activeTag: '{{ $activeTag }}',
        loading: false,
        
        async fetchArticles(url, tagValue) {
            // Prevent duplicate requests
            if (this.loading) return;
            
            this.loading = true;
            this.activeTag = tagValue;
            
            try {
                const res = await fetch(url, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!res.ok) throw new Error('Network response was not ok');
                
                const data = await res.json();
                
                // Update HTML content
                document.getElementById('articles-container').innerHTML = data.html;
                
                // Update the URL without reloading the page
                window.history.pushState({tag: this.activeTag}, '', url);
                
            } catch (error) {
                console.error('Error fetching articles:', error);
            } finally {
                this.loading = false;
            }
        },
        
        init() {
            // Handle browser back/forward buttons
            window.addEventListener('popstate', (e) => {
                const url = window.location.href;
                const urlParams = new URL(url).searchParams;
                this.activeTag = urlParams.get('tag') || '';
                
                // Fetch content for the history state
                this.fetchArticles(url, this.activeTag);
            });
            
            // Intercept pagination clicks within the articles container
            document.getElementById('articles-container').addEventListener('click', (e) => {
                const link = e.target.closest('.ajax-pagination a');
                if (link) {
                    e.preventDefault();
                    
                    // We keep the current tag active, the pagination link already has the tag query param included
                    this.fetchArticles(link.href, this.activeTag);
                    
                    // Scroll mildly to the top of the grid
                    document.getElementById('articles-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }
    }
}
</script>

</x-app-layout>
