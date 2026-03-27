<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tampilkan semua notifikasi user
     */
    public function index()
    {
        $notifs = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('notifications.index', compact('notifs'));
    }

    /**
     * Klik notif (non AJAX / fallback)
     */
    public function read($id)
    {
        $notif = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        // tandai sudah dibaca
        $notif->update([
            'is_read' => true
        ]);

        // jika notif punya url
        if (!empty($notif->url)) {
            return redirect()->to($notif->url);
        }

        // fallback ke halaman notif
        return redirect()->route('notifications.index');
    }

    /**
     * AJAX: tandai 1 notif dibaca
     */
    public function markRead($id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notif) {
            $notif->update([
                'is_read' => true
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * AJAX: tandai semua notif dibaca
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * AJAX: hapus satu notif
     */
    public function delete($id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notif) {
            $notif->delete();
        }

        return response()->json([
            'success' => true
        ]);
    }
}