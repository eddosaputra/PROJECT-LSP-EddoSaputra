<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $this->admin();

        return view('admin.announcements.index', [
            'announcements' => Announcement::latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $this->admin();

        $validated = $this->validateAnnouncement($request);
        $validated['user_id'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published');
        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement)
    {
        $this->admin();

        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->admin();

        $validated = $this->validateAnnouncement($request);
        $validated['is_published'] = $request->boolean('is_published');
        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->admin();
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:20000'],
            'is_published' => ['sometimes', 'boolean'],
        ]);
    }

    private function admin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}
