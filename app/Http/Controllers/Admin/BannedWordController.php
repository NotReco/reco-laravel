<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use App\Services\ModerationService;
use Illuminate\Http\Request;

class BannedWordController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function index()
    {
        $words = BannedWord::orderByDesc('id')->paginate(20);
        return view('admin.banned_words.index', compact('words'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100|unique:banned_words,word',
            'severity' => 'required|in:low,medium,high',
            'action' => 'required|in:pending,hide,delete',
            'is_active' => 'boolean',
        ]);

        BannedWord::create($request->all());
        
        $this->moderationService->clearCache();

        return back()->with('success', 'Đã thêm từ cấm mới thành công.');
    }

    public function update(Request $request, BannedWord $bannedWord)
    {
        $request->validate([
            'word' => 'required|string|max:100|unique:banned_words,word,' . $bannedWord->id,
            'severity' => 'required|in:low,medium,high',
            'action' => 'required|in:pending,hide,delete',
            'is_active' => 'boolean',
        ]);

        $bannedWord->update($request->all());
        
        $this->moderationService->clearCache();

        return back()->with('success', 'Đã cập nhật từ cấm thành công.');
    }

    public function destroy(BannedWord $bannedWord)
    {
        // Ưu tiên disable hơn delete, hoặc có thể delete tùy nhu cầu
        $bannedWord->delete();
        
        $this->moderationService->clearCache();

        return back()->with('success', 'Đã xóa từ cấm.');
    }

    public function toggle(BannedWord $bannedWord)
    {
        $bannedWord->update(['is_active' => !$bannedWord->is_active]);
        
        $this->moderationService->clearCache();

        return back()->with('success', 'Đã cập nhật trạng thái từ cấm.');
    }
}
