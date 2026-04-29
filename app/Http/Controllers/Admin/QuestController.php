<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestType;
use App\Http\Controllers\Controller;
use App\Models\AvatarFrame;
use App\Models\Quest;
use App\Models\UserTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestController extends Controller
{
    public function index()
    {
        $quests = Quest::with(['rewardTitle', 'rewardFrame'])
            ->withCount('userProgress')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.quests.index', compact('quests'));
    }

    public function create()
    {
        $questTypes = QuestType::cases();
        $titles     = UserTitle::where('is_active', true)->orderBy('name')->get();
        $frames     = AvatarFrame::where('is_active', true)->orderBy('name')->get();

        return view('admin.quests.form', [
            'quest'      => new Quest(),
            'questTypes' => $questTypes,
            'titles'     => $titles,
            'frames'     => $frames,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuest($request);

        $validated['slug'] = Str::slug($validated['name']);

        Quest::create($validated);

        return redirect()
            ->route('admin.quests.index')
            ->with('success', 'Nhiệm vụ đã được tạo thành công.');
    }

    public function edit(Quest $quest)
    {
        $questTypes = QuestType::cases();
        $titles     = UserTitle::where('is_active', true)->orderBy('name')->get();
        $frames     = AvatarFrame::where('is_active', true)->orderBy('name')->get();

        return view('admin.quests.form', compact('quest', 'questTypes', 'titles', 'frames'));
    }

    public function update(Request $request, Quest $quest)
    {
        $validated = $this->validateQuest($request, $quest->id);

        $quest->update($validated);

        return redirect()
            ->route('admin.quests.index')
            ->with('success', 'Nhiệm vụ đã được cập nhật.');
    }

    public function destroy(Quest $quest)
    {
        $quest->delete();

        return redirect()
            ->route('admin.quests.index')
            ->with('success', 'Đã xóa nhiệm vụ.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function validateQuest(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'required|in:' . implode(',', array_column(QuestType::cases(), 'value')),
            'target_value'   => 'required|integer|min:1',
            'reward_type'    => 'required|in:title,frame',
            'reward_title_id'=> 'nullable|exists:user_titles,id',
            'reward_frame_id'=> 'nullable|exists:avatar_frames,id',
            'is_active'      => 'boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        // Ensure correct reward FK is set
        if ($validated['reward_type'] === 'title') {
            $validated['reward_frame_id'] = null;
        } else {
            $validated['reward_title_id'] = null;
        }

        $validated['is_active']   = $request->boolean('is_active', true);
        $validated['sort_order']  = $validated['sort_order'] ?? 0;

        return $validated;
    }
}
