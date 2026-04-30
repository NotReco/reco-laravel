<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => ['required', 'string'],
            'reportable_id'   => ['required', 'integer'],
            'reason'          => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'is_public'       => ['boolean'],
        ]);

        $user = Auth::user();

        // Kiểm tra xem user có đang bị cấm báo cáo không
        if ($user->isReportBanned()) {
            $until = $user->report_banned_until->locale('vi')->diffForHumans();
            return response()->json([
                'success' => false,
                'banned'  => true,
                'message' => "Tính năng báo cáo của bạn đang tạm dừng do một số báo cáo gần đây không phù hợp. Sẽ mở lại {$until}.",
            ], 403);
        }

        // Kiểm tra điểm uy tín âm (tự động cấm tạm thời)
        if ($user->reputation_score < 0) {
            $banDays = $user->reputation_score < -30 ? 7 : 3;
            $until   = now()->addDays($banDays);
            $user->update(['report_banned_until' => $until]);

            return response()->json([
                'success' => false,
                'banned'  => true,
                'message' => "Tính năng báo cáo của bạn đang tạm dừng trong {$banDays} ngày do nhiều báo cáo gần đây không phù hợp.",
            ], 403);
        }

        $type = $request->input('reportable_type');
        $id   = $request->input('reportable_id');

        // Resolve morph class
        $morphClass = Relation::getMorphedModel($type) ?? $type;

        if (!class_exists($morphClass)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại báo cáo không hợp lệ.',
            ], 422);
        }

        $model = $morphClass::find($id);
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Nội dung báo cáo không tồn tại.',
            ], 404);
        }

        // Không cho báo cáo nội dung của chính mình
        if ($model->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể báo cáo nội dung của chính mình.',
            ], 403);
        }

        // Đã báo cáo rồi — chặn nhưng giọng thân thiện
        $alreadyReported = $model->reports()->where('user_id', $user->id)->exists();
        if ($alreadyReported) {
            return response()->json([
                'success'  => false,
                'already'  => true,
                'message'  => 'Bạn đã gửi báo cáo về nội dung này rồi. Cảm ơn bạn đã đóng góp cho cộng đồng!',
            ], 422);
        }

        $model->reports()->create([
            'user_id'     => $user->id,
            'reason'      => $request->input('reason'),
            'description' => $request->input('description'),
            'is_public'   => $request->boolean('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi báo cáo thành công. Cảm ơn bạn!',
        ]);
    }
}
