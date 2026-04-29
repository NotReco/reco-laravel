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

        $type = $request->input('reportable_type');
        $id = $request->input('reportable_id');

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

        // Check if user is reporting their own content
        if ($model->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể báo cáo nội dung của chính mình.',
            ], 403);
        }

        // Check if already reported
        $alreadyReported = $model->reports()->where('user_id', Auth::id())->exists();
        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã báo cáo nội dung này rồi.',
            ], 422);
        }

        $model->reports()->create([
            'user_id'     => Auth::id(),
            'reason'      => $request->input('reason'),
            'description' => $request->input('description'),
            'is_public'   => $request->boolean('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi báo cáo. Cảm ơn bạn!',
        ]);
    }
}
