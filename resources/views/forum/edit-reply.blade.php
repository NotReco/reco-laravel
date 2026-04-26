<x-app-layout>
    <x-slot:title>Sửa phản hồi — Diễn đàn</x-slot:title>

<div class="bg-gray-50 min-h-screen pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('forum.index') }}" class="hover:text-gray-900 transition-colors">Diễn đàn</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('forum.show', $reply->thread) }}" class="hover:text-gray-900 transition-colors truncate max-w-[200px]">{{ $reply->thread->title }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">Sửa phản hồi</span>
        </nav>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            <h1 class="text-xl font-display font-bold text-gray-900 mb-6">Sửa phản hồi</h1>

            <form action="{{ route('forum.updateReply', $reply) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung</label>
                    <textarea id="content" name="content" rows="6"
                              class="js-markdown-editor w-full"
                              placeholder="Nội dung phản hồi..." required maxlength="10000">{!! old('content', $reply->content) !!}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('forum.show', $reply->thread) }}" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">
                        ← Quay lại
                    </a>
                    <button type="submit"
                            class="px-7 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                   hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.markdown-editor')
</x-app-layout>
