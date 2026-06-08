<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
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
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'created_by'   => Auth::id(),
            'published_at' => now(),
        ]);

        // Notify all active residents
        User::where('role', 'resident')
            ->where('status', 'active')
            ->each(function (User $user) use ($announcement) {
                try {
                    $user->notify(new NewAnnouncementNotification($announcement));
                } catch (\Throwable $e) {
                    // Don't block the request if notification fails
                }
            });

        return back()->with('success', 'Announcement published and residents notified!');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'string|max:255',
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
