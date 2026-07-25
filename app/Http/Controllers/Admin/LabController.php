<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LabController extends Controller
{
    public function index(): View
    {
        $labs = Lab::withCount(['groups', 'items'])->orderBy('nama')->get();

        return view('admin.labs.index', compact('labs'));
    }

    public function create(): View
    {
        return view('admin.labs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $lab = Lab::create($data);
        ActivityLogger::created($lab, "Menambah laboratorium \"{$lab->nama}\".");

        return redirect()->route('admin.labs.show', $lab)
            ->with('success', 'Laboratorium berhasil ditambahkan. Silakan atur kelompok & meja.');
    }

    /** Halaman tata letak: kelompok & meja sebuah lab. */
    public function show(Lab $lab): View
    {
        $lab->load(['groups.tables']);

        return view('admin.labs.show', compact('lab'));
    }

    public function edit(Lab $lab): View
    {
        return view('admin.labs.edit', compact('lab'));
    }

    public function update(Request $request, Lab $lab): RedirectResponse
    {
        $data = $this->validateData($request, $lab->id);

        $lab->update($data);
        ActivityLogger::updated($lab, "Mengubah laboratorium \"{$lab->nama}\".");

        return $this->backToList('admin.labs.index')
            ->with('success', 'Laboratorium berhasil diperbarui.');
    }

    public function destroy(Lab $lab): RedirectResponse
    {
        if ($lab->items()->exists()) {
            return back()->with('error', 'Laboratorium masih memiliki barang dan tidak dapat dihapus.');
        }

        $nama = $lab->nama;
        $lab->delete();
        ActivityLogger::log('deleted', "Menghapus laboratorium \"{$nama}\".");

        return redirect()->route('admin.labs.index')
            ->with('success', 'Laboratorium berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('labs', 'kode')->ignore($id)],
            'has_groups' => ['sometimes', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $data['has_groups'] = $request->boolean('has_groups');

        return $data;
    }
}
