<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sekretaris\StoreProcurementRequest;
use App\Models\Item;
use App\Models\Kelas;
use App\Models\Lab;
use App\Models\Procurement;
use App\Notifications\NewProcurementNotification;
use App\Services\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    /** Daftar pengajuan milik sekretaris + status. */
    public function index(Request $request): View
    {
        $procurements = Procurement::query()
            ->with(['item', 'category'])
            ->where('user_id', $request->user()->id)
            ->latest('requested_at')
            ->paginate(10);

        return view('sekretaris.procurements.index', compact('procurements'));
    }

    public function create(): View
    {
        $this->authorize('create', Procurement::class);

        return view('sekretaris.procurements.create', $this->formData());
    }

    public function store(StoreProcurementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_new_item'] = false;

        $procurement = Procurement::create($data);

        // Beri tahu admin ada pengajuan baru.
        Notifier::toAdmins(new NewProcurementNotification($procurement->load(['user', 'item', 'category'])));

        return redirect()->route('sekretaris.procurements.index')
            ->with('success', 'Pengajuan barang berhasil dikirim.');
    }

    public function show(Procurement $procurement): View
    {
        $this->authorize('view', $procurement);

        $procurement->load(['item', 'category']);

        return view('sekretaris.procurements.show', compact('procurement'));
    }

    public function edit(Procurement $procurement): View
    {
        $this->authorize('update', $procurement);

        return view('sekretaris.procurements.edit', array_merge(
            ['procurement' => $procurement],
            $this->formData()
        ));
    }

    public function update(StoreProcurementRequest $request, Procurement $procurement): RedirectResponse
    {
        $this->authorize('update', $procurement);

        $data = $request->validated();
        $data['is_new_item'] = false;

        $procurement->update($data);

        return $this->backToList('sekretaris.procurements.index')
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement): RedirectResponse
    {
        $this->authorize('delete', $procurement);

        $procurement->delete();

        return back()
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'labs' => Lab::orderBy('nama')->get(),
            // jumlah_total dipakai untuk membatasi jumlah pinjaman sesuai stok.
            'items' => Item::orderBy('nama')->get(['id', 'nama', 'lab_id', 'jumlah_total']),
            'kelasList' => Kelas::orderBy('nama')->pluck('nama')->all(),
        ];
    }
}
