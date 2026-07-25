<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Procurement;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** Feed JSON untuk pembaruan lonceng secara realtime (polling). */
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = $user->notifications()->latest()->take(6)->get()->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? 'Notifikasi',
            'message' => $n->data['message'] ?? '',
            'icon' => $n->data['icon'] ?? null,
            'url' => route('notifications.read', $n->id),
            'read' => (bool) $n->read_at,
            'time' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }

    /** Daftar seluruh notifikasi milik pengguna. */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /** Tandai satu notifikasi dibaca, lalu arahkan ke tujuannya. */
    public function read(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data;
        $type = $data['type'] ?? null;
        $url = $data['url'] ?? route('home');

        // Jika target notifikasi (laporan/pengajuan/barang) sudah dihapus,
        // tampilkan pesan kecil alih-alih membuka halaman yang tidak ada (404).
        if ($label = $this->deletedTargetLabel($type, $url)) {
            return redirect()->route('notifications.index')
                ->with('status', "{$label} yang dituju notifikasi ini sudah dihapus.");
        }

        return redirect($url);
    }

    /**
     * Kembalikan label ("Laporan"/"Pengajuan"/"Barang") bila target sudah dihapus,
     * atau null bila target masih ada / tidak relevan.
     */
    private function deletedTargetLabel(?string $type, string $url): ?string
    {
        $targetId = (int) Str::afterLast((string) parse_url($url, PHP_URL_PATH), '/');
        if ($targetId <= 0) {
            return null;
        }

        return match ($type) {
            'report' => Report::whereKey($targetId)->exists() ? null : 'Laporan',
            'procurement', 'procurement-status' => Procurement::whereKey($targetId)->exists() ? null : 'Pengajuan',
            'item' => Item::whereKey($targetId)->exists() ? null : 'Barang',
            default => null,
        };
    }

    /** Tandai semua notifikasi sebagai sudah dibaca. */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /** Hapus satu notifikasi. */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /** Bersihkan (hapus) seluruh notifikasi milik pengguna. */
    public function clear(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }
}
