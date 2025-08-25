<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MobileJobcardPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// Removed: use App\Traits\TenantDatabaseSwitch;

class MobileJobcardPhotoController extends Controller
{
    // Removed: use TenantDatabaseSwitch
    
    public function store(Request $request)
    {
    // Removed: $this->switchToTenantDatabase();
        
        Log::info('Photo upload hit', $request->all());
        $request->validate([
            'jobcard_id' => 'required|exists:jobcards,id',
            'photo' => 'required|image|max:15360', // 15MB
            'caption' => 'nullable|string|max:255',
        ]);

        // Store without 'public/' prefix for correct Storage::url
        $path = $request->file('photo')->store('jobcards/' . $request->jobcard_id, 'public');
        $photo = MobileJobcardPhoto::create([
            'jobcard_id' => $request->jobcard_id,
            'file_path' => $path, // e.g. 'jobcards/7/filename.jpg'
            'uploaded_at' => now(),
            'uploaded_by' => Auth::id(),
            'caption' => $request->caption,
        ]);

        // Redirect back to show the new photo
        return redirect()->back()->with('success', 'Photo uploaded!');
    }

    public function destroy($id)
    {
        $this->switchToTenantDatabase();
        
        $photo = MobileJobcardPhoto::findOrFail($id);
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();
        return response()->json(['success' => true]);
    }
} 