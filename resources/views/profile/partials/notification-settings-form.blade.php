<section>
    <header class="pb-5 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Tùy chọn thông báo</h2>
        <p class="mt-1 text-sm text-gray-500">
            Những loại thông báo bạn đã tắt sẽ hiển thị ở đây. Bạn có thể bật lại bất cứ lúc nào.
        </p>
    </header>

    <div class="mt-6">
        @php
            $prefs = auth()->user()->notification_preferences ?? [];
            $disabledTypes = $prefs['disabled_types'] ?? [];
            $typesMap = [
                'App\Notifications\ArticleCommentMentioned' => 'Được nhắc đến trong bình luận tin tức',
                'App\Notifications\ForumReplyNotification'  => 'Có người trả lời bài viết của bạn trên Forum',
                'App\Notifications\NewFollowerNotification' => 'Có người theo dõi bạn mới',
            ];
        @endphp

        @if(empty($disabledTypes))
            <p class="text-sm text-gray-500 mt-4">Bạn chưa tắt loại thông báo nào. Mọi thứ đang hoạt động bình thường.</p>
        @else
            <form id="notification-prefs-form" method="POST" action="{{ route('settings.notifications.update') }}" data-unsaved-bar>
                @csrf
                @method('PATCH')

                <ul class="divide-y divide-gray-100" x-data="{ removed: [] }">
                    @foreach($disabledTypes as $type)
                        <li class="py-4 flex items-center justify-between transition-all duration-300"
                            x-show="!removed.includes('{{ $type }}')"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-20"
                            x-transition:leave-end="opacity-0 max-h-0">

                            {{-- Hidden input: nếu chưa nhấn Bật lại → type này vẫn disabled --}}
                            <input type="hidden" name="disabled_types[]" value="{{ $type }}"
                                   x-bind:disabled="removed.includes('{{ $type }}')">

                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900">{{ $typesMap[$type] ?? class_basename($type) }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">Loại thông báo này hiện đang bị tắt.</span>
                            </div>
                            <button type="button"
                                    @click="removed.push('{{ $type }}'); $el.closest('li').querySelector('input[type=hidden]').disabled = true; $dispatch('change');"
                                    class="px-3 py-1.5 text-sm font-medium text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition">
                                Bật lại
                            </button>
                        </li>
                    @endforeach
                </ul>
            </form>
        @endif
    </div>
</section>
