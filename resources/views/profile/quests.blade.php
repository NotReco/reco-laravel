<x-app-layout>
    <x-slot:title>Nhiệm vụ của tôi</x-slot:title>

    <div class="min-h-screen py-12 relative overflow-hidden">
        {{-- Overall Page Background Blobs --}}
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-1/4 -left-1/4 w-[800px] h-[800px] bg-sky-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-200/20 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-gray-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center shadow-md shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Nhiệm vụ & Thành tích</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Hoàn thành nhiệm vụ để nhận các phần thưởng độc quyền</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Đã hoàn thành</p>
                        <p class="text-xl font-bold text-sky-600">{{ $completedCount }} <span class="text-sm font-medium text-gray-400">/ {{ $quests->count() }}</span></p>
                    </div>
                    <a href="{{ route('profile.show', $user) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Quay lại
                    </a>
                </div>
            </div>

            {{-- Quests Grid --}}
            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($quests as $quest)
                    @php
                        $progress = $quest->userProgress->first();
                        $isCompleted = $progress?->isCompleted() ?? false;
                        $percent = $progress ? $progress->percentageFor($quest) : 0;
                        $currentVal = $progress ? $progress->progress : 0;
                    @endphp

                    <div class="relative bg-white/80 backdrop-blur-xl border border-gray-200/80 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] rounded-2xl p-5 overflow-hidden transition-all hover:border-gray-300/80 hover:shadow-[0_4px_15px_-4px_rgba(0,0,0,0.08)]">
                        
                        {{-- Completed Background Indicator --}}
                        @if($isCompleted)
                            <div class="absolute inset-0 bg-emerald-50/50 pointer-events-none"></div>
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-100 rounded-full blur-2xl pointer-events-none"></div>
                            
                            {{-- Checkmark Watermark --}}
                            <div class="absolute right-4 top-4 text-emerald-200/50 pointer-events-none">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif

                        <div class="relative z-10">
                            {{-- Quest Info --}}
                            <div class="flex items-start gap-4 mb-4">
                                {{-- Reward Icon --}}
                                <div class="shrink-0 w-12 h-12 rounded-xl {{ $isCompleted ? 'bg-emerald-100 text-emerald-600' : 'bg-sky-50 text-sky-500' }} flex items-center justify-center border {{ $isCompleted ? 'border-emerald-200' : 'border-sky-100' }}">
                                    @if($quest->reward_type === 'title')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-[15px] font-bold text-gray-900 truncate" title="{{ $quest->name }}">{{ $quest->name }}</h3>
                                    @if($quest->description)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" title="{{ $quest->description }}">{{ $quest->description }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Reward Preview --}}
                            <div class="mb-5 inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-gray-50 border border-gray-100">
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Phần thưởng:</span>
                                @if($quest->reward_type === 'title' && $quest->rewardTitle)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold tracking-wide border"
                                          style="color: {{ $quest->rewardTitle->color_hex }}; border-color: {{ $quest->rewardTitle->color_hex }}40; background-color: {{ $quest->rewardTitle->color_hex }}10;">
                                        {{ $quest->rewardTitle->name }}
                                    </span>
                                @elseif($quest->reward_type === 'frame' && $quest->rewardFrame)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-gray-700">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 relative overflow-hidden">
                                            <img src="{{ Storage::url($quest->rewardFrame->image_path) }}" class="absolute inset-0 w-full h-full object-contain" alt="">
                                        </div>
                                        {{ $quest->rewardFrame->name }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-gray-500">—</span>
                                @endif
                            </div>

                            {{-- Progress Bar --}}
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="{{ $isCompleted ? 'text-emerald-600' : 'text-gray-500' }}">
                                        {{ $isCompleted ? 'Hoàn thành' : 'Tiến độ' }}
                                    </span>
                                    <span class="{{ $isCompleted ? 'text-emerald-700' : 'text-gray-700' }}">
                                        {{ min($currentVal, $quest->target_value) }} / {{ $quest->target_value }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 border border-gray-200 overflow-hidden">
                                    <div class="h-2.5 rounded-full {{ $isCompleted ? 'bg-emerald-500' : 'bg-sky-500' }} transition-all duration-500" 
                                         style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white/80 backdrop-blur-xl border border-gray-200/80 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] rounded-3xl p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Chưa có nhiệm vụ nào</h3>
                        <p class="text-sm text-gray-500">Hệ thống nhiệm vụ hiện tại chưa được kích hoạt. Vui lòng quay lại sau!</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
