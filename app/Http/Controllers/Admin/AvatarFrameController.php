<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvatarFrame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarFrameController extends Controller
{
    public function index()
    {
        $frames = AvatarFrame::latest()->paginate(20);
        return view('admin.avatar_frames.index', compact('frames'));
    }

    public function create()
    {
        return view('admin.avatar_frames.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:png,gif,webp,jpg,jpeg,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        $path = $request->file('image')->store('avatar-frames', 'public');

        AvatarFrame::create([
            'name' => $request->name,
            'image_path' => $path,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.avatar-frames.index')
            ->with('success', 'Đã thêm Khung Avatar mới thành công.');
    }

    public function edit(string $id)
    {
        $frame = AvatarFrame::findOrFail($id);
        return view('admin.avatar_frames.create', compact('frame'));
    }

    public function update(Request $request, string $id)
    {
        $frame = AvatarFrame::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:png,gif,webp,jpg,jpeg,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($frame->image_path && Storage::disk('public')->exists($frame->image_path)) {
                Storage::disk('public')->delete($frame->image_path);
            }
            $data['image_path'] = $request->file('image')->store('avatar-frames', 'public');
        }

        $frame->update($data);

        return redirect()->route('admin.avatar-frames.index')
            ->with('success', 'Đã cập nhật Khung Avatar thành công.');
    }

    public function destroy(string $id)
    {
        $frame = AvatarFrame::findOrFail($id);
        if ($frame->image_path && Storage::disk('public')->exists($frame->image_path)) {
            Storage::disk('public')->delete($frame->image_path);
        }
        $frame->delete();
        return redirect()->route('admin.avatar-frames.index')
            ->with('success', 'Đã xóa Khung Avatar thành công.');
    }
}
