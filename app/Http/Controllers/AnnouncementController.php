<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController
{
    public function index(): View
    {
        $announcements = Announcement::published()->latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Announcement::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
            'published_at' => now(),
        ]);

        return back()->with('success', 'Announcement published successfully!');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
        ]);

        $announcement->update($validated);
        return back()->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully!');
    }
}
