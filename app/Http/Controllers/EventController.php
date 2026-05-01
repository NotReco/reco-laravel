<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Services\QuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display the events/quests hub.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Update progress just in case
        if ($user) {
            app(QuestService::class)->checkAll($user);
        }

        $quests = Quest::active()
            ->with(['rewardTitle', 'rewardFrame'])
            ->when($user, function ($query) use ($user) {
                $query->with(['userProgress' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }]);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('events.index', compact('quests'));
    }

    /**
     * Claim a reward for a completed quest.
     */
    public function claim(Request $request, Quest $quest)
    {
        $user = Auth::user();
        $service = app(QuestService::class);

        // checkAll is already handled mostly by observers, but we can do it if needed.
        // It's better to just try claiming.
        if ($service->claimReward($user, $quest)) {
            return response()->json([
                'success' => true,
                'message' => 'Nhận thưởng thành công!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn chưa đủ điều kiện hoặc đã nhận thưởng rồi.',
        ], 400);
    }
}
