<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KelasRequest;
use App\Models\Kelas;
use App\Models\Procurement;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Kelas::class);

        $kelas = Kelas::query()
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', "%{$request->q}%"))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return view('admin.kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        $this->authorize('create', Kelas::class);

        return view('admin.kelas.create');
    }

    public function store(KelasRequest $request): RedirectResponse
    {
        $kelas = Kelas::create($request->validated());
        ActivityLogger::created($kelas, "Menambah kelas \"{$kelas->nama}\".");

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        $this->authorize('update', $kelas);

        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(KelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $oldNama = $kelas->nama;
        $kelas->update($request->validated());

        // Jaga konsistensi: pengajuan peminjaman lama ikut memakai nama baru.
        if ($oldNama !== $kelas->nama) {
            Procurement::where('kelas', $oldNama)->update(['kelas' => $kelas->nama]);
        }

        ActivityLogger::updated($kelas, "Mengubah kelas \"{$kelas->nama}\".");

        return $this->backToList('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $this->authorize('delete', $kelas);

        if (Procurement::where('kelas', $kelas->nama)->exists()) {
            return back()->with('error', 'Kelas masih dipakai pada pengajuan peminjaman dan tidak dapat dihapus.');
        }

        $kelas->forceDelete();
        ActivityLogger::deleted($kelas, "Menghapus kelas \"{$kelas->nama}\".");

        return back()
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
