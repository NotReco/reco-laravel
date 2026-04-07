<section x-data="notificationSettings()">
    <header>
        <h2 class="text-lg font-medium text-gray-900 border-b border-gray-100 pb-3">Tùy chọn Thông báo</h2>
        <p class="mt-2 text-sm text-gray-600">Những loại thông báo bạn đã tắt sẽ hiển thị ở đây. Bạn có thể bật lại bất cứ lúc nào.</p>
    </header>

    <div class="mt-6 space-y-6">
        @php
            $prefs = auth()->user()->notification_preferences ?? [];
            $disabledTypes = $prefs['disabled_types'] ?? [];
            
            $typesMap = [
                'App\Notifications\ArticleCommentMentioned' => 'Được nhắc đến trong bình luận tin tức',
                'App\Notifications\ForumReplyNotification' => 'Có người trả lời bài viết của bạn trên Forum',
                'App\Notifications\NewFollowerNotification' => 'Có người theo dõi bạn mới',
                // Add more logic here or generic parser
            ];
        @endphp

        @if(empty($disabledTypes))
            <p class="text-sm text-gray-500 italic">Bạn chưa tắt loại thông báo nào.</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($disabledTypes as $type)
                    <li class="py-4 flex items-center justify-between" x-show="!restored.includes('{{ $type }}')">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $typesMap[$type] ?? class_basename($type) }}
                            </span>
                            <span class="text-xs text-gray-500">Loại thông báo này hiện đang bị tắt.</span>
                        </div>
                        <button type="button" @click="turnOn('{{ $type }}')" class="px-3 py-1.5 text-sm font-medium text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition">
                            Bật lại
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>

<script>
    function notificationSettings() {
        return {
            restored: [],
            async turnOn(type) {
                if (confirm('Bạn có chắc chắn muốn bật lại thông báo này?')) {
                    try {
                        let res = await fetch('/api/notifications/turn-on', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ type: type })
                        });
                        if (res.ok) {
                            this.restored.push(type);
                            alert('Đã bật lại thành công!');
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        }
    }
</script>
