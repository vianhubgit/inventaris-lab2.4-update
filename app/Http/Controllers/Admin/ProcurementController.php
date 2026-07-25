<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProcurementStatus;
use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Notifications\ProcurementStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    public function index(Request $request): View
    {
        $procurements = Procurement::query()
            ->with(['user', 'item', 'category'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('requested_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.procurements.index', [
            'procurements' => $procurements,
            'statuses' => ProcurementStatus::options(),
        ]);
    }

    public function show(Procurement $procurement): View
    {
        $procurement->load(['user', 'item', 'category']);

        return view('admin.procurements.show', [
            'procurement' => $procurement,
            'statuses' => ProcurementStatus::options(),
        ]);
    }

    public function updateStatus(Request $request, Procurement $procurement): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(ProcurementStatus::class)],
            // Admin boleh menyetujui jumlah lebih kecil dari yang diminta.
            'jumlah_disetujui' => ['nullable', 'integer', 'min:0', 'max:'.$procurement->jumlah],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $procurement->update($data);

        // Beri tahu sekretaris pengaju bahwa status pengajuannya berubah.
        if ($procurement->wasChanged('status') && $procurement->user) {
            $procurement->user->notify(new ProcurementStatusNotification($procurement));
        }

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement): RedirectResponse
    {
        $procurement->forcedelete();

        return redirect()->route('admin.procurements.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
