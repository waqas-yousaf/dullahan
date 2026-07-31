<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $mimes = implode(',', config('dullahan.uploads.mimes', ['jpeg', 'png', 'jpg', 'webp', 'svg']));

        $request->validate([
            'image' => ['required', 'file', 'mimes:' . $mimes, 'max:' . config('dullahan.uploads.max_kb', 4096)],
        ]);

        $file = $request->file('image');
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($request->filled('title')) {
            $safeName = Str::slug($request->input('title'));
        } else {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', Str::ascii($name)), '-');
        }
        
        $filename = now()->format('YmdHis') . '-' . ($safeName ?: 'image') . '.' . $extension;
        $relativePath = trim(config('dullahan.uploads.path', 'uploads/dullahan'), '/');
        $publicPath = public_path($relativePath);

        File::ensureDirectoryExists($publicPath, 0755, true);
        $file->move($publicPath, $filename);

        return response()->json([
            'url' => asset($relativePath . '/' . $filename),
        ]);
    }
}
