<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Halaman daftar semua notifikasi */
    public function index()
    {
        $user = auth()->user();

        // Ambil semua notifikasi, unread dulu lalu yang sudah dibaca
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        // Tandai semua yang baru dibuka sebagai sudah dibaca
        // (opsional, bisa pakai tombol eksplisit)
        return view('notifications.index', compact('notifications'));
    }

    /** Tandai satu notifikasi sebagai terbaca & redirect ke halaman detail */
    public function markRead(Request $request, string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        // Redirect ke detail visitor jika ada visitor_id
        $visitorId = $notification?->data['visitor_id'] ?? null;
        if ($visitorId) {
            return redirect()->route('visitors.show', $visitorId);
        }

        return redirect()->route('notifications.index');
    }

    /** Tandai SEMUA notifikasi sebagai terbaca */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai terbaca.');
    }

    /** Hapus satu notifikasi */
    public function destroy(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }

    /** JSON endpoint untuk polling unread count (AJAX) */
    public function count()
    {
        $count = auth()->user()->unreadNotifications()->count();
        $recent = auth()->user()->unreadNotifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($n) => [
                'id'           => $n->id,
                'visitor_name' => $n->data['visitor_name'],
                'institution'  => $n->data['institution'] ?? '-',
                'purpose'      => \Str::limit($n->data['purpose'], 50),
                'time_ago'     => $n->created_at->diffForHumans(),
                'check_in'     => $n->data['check_in_display'] ?? '-',
            ]);

        return response()->json([
            'count'  => $count,
            'recent' => $recent,
        ]);
    }
}
