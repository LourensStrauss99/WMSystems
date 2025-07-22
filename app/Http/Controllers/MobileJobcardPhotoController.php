<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MobileJobcardPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MobileJobcardPhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jobcard_id' => 'required|exists:jobcards,id',
            'photo' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('photo')->store('public/jobcards/' . $request->jobcard_id);
        $photo = MobileJobcardPhoto::create([
            'jobcard_id' => $request->jobcard_id,
            'file_path' => $path,
            'uploaded_at' => now(),
            'uploaded_by' => Auth::id(),
            'caption' => $request->caption,
        ]);

        return response()->json(['success' => true, 'photo' => $photo]);
    }

    public function destroy($id)
    {
        $photo = MobileJobcardPhoto::findOrFail($id);
        Storage::delete($photo->file_path);
        $photo->delete();
        return response()->json(['success' => true]);
    }
} 