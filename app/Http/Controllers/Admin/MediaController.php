<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, File, Storage};
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::with('uploader')->latest()->paginate(40);
        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240']);

        $file     = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        $uploadDir = public_path('uploads');

        if (! File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $file->move($uploadDir, $filename);
        $url = '/uploads/' . $filename;

        $media = Media::create([
            'filename'      => $filename,
            'original_name' => $originalName,
            'mimetype'      => $mimeType,
            'size'          => $size,
            'url'           => $url,
            'disk'          => 'public_uploads',
            'uploaded_by'   => Auth::id(),
        ]);

        return response()->json([
            'id'   => $media->id,
            'url'  => $media->url,
            'name' => $media->original_name,
        ]);
    }

    public function destroy(Media $media)
    {
        if ($media->disk === 'public_uploads') {
            File::delete(public_path('uploads/' . $media->filename));
        } else {
            Storage::disk('public')->delete('uploads/' . $media->filename);
        }

        $media->delete();
        return back()->with('success', 'Image deleted.');
    }
}
