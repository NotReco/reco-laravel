<x-superadmin-layout title="Nhật ký hoạt động" pageTitle="Nhật ký hoạt động">

    {{-- ── Filters ───────────────────────────────────────────────── --}}
    <div class="mb-6">
        <form action="{{ route('super.activity-logs.index') }}" method="GET" class="flex flex-wrap gap-3 max-w-4xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm nội dung, action, IP..."
                class="input-dark text-sm flex-1 min-w-[200px] py-2.5">
            <select name="action_filter" class="input-dark text-sm w-48 py-2.5">
                <option value="">Tất cả Action</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" {{ request('action_filter') === $action ? 'selected' : '' }}>
                        {{ $action }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
            @if(request('search') || request('action_filter'))
                <a href="{{ route('super.activity-logs.index') }}" class="btn-secondary py-2.5 px-5 text-sm">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left">
                        <th class="px-5 py-3 font-medium whitespace-nowrap">Thời gian</th>
                        <th class="px-5 py-3 font-medium whitespace-nowrap">User</th>
                        <th class="px-5 py-3 font-medium whitespace-nowrap">Action</th>
                        <th class="px-5 py-3 font-medium whitespace-nowrap">Target</th>
                        <th class="px-5 py-3 font-medium whitespace-nowrap">Nội dung</th>
                        <th class="px-5 py-3 font-medium whitespace-nowrap">IP / Client</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-dark-800/30 transition-colors">
                            <td class="px-5 py-3 text-dark-400 text-xs whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 ring-1 ring-dark-700">
                                            @if ($log->user->avatar)
                                                <img src="{{ $log->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <span class="text-[9px] font-bold text-white">{{ mb_strtoupper(mb_substr($log->user->name, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                                            @endif
                                        </div>
                                        <span class="font-medium text-white text-xs">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-dark-500">Hệ thống / Guest</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="badge text-[10px] bg-indigo-500/20 text-indigo-400">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-dark-400 whitespace-nowrap">
                                @if($log->target_type && $log->target_id)
                                    {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                @else
                                    <span class="text-dark-600">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs text-dark-300 max-w-xs">
                                <div class="line-clamp-2" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-dark-500 max-w-[150px]">
                                <div class="truncate" title="IP: {{ $log->ip_address }} | Agent: {{ $log->user_agent }}">
                                    <span class="text-sky-400">{{ $log->ip_address }}</span><br>
                                    <span class="truncate block opacity-70">{{ $log->user_agent }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-dark-500">Không có nhật ký hoạt động nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>

</x-superadmin-layout>
