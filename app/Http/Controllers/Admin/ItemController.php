<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Lab;
use App\Notifications\NewItemNotification;
use App\Services\ActivityLogger;
use App\Services\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Item::class);

        $items = Item::query()
            ->with(['category', 'lab', 'labTable.group'])
            ->search($request->q)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('lab_id'), fn ($q) => $q->where('lab_id', $request->lab_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return view('admin.items.index', [
            'items' => $items,
            'categories' => Category::orderBy('nama')->get(),
            'labs' => Lab::orderBy('nama')->get(),
            'statuses' => ItemStatus::options(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        return view('admin.items.create', $this->formData());
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $item = Item::create($request->validated());

        // Beri tahu sekretaris ada barang baru.
        Notifier::toSekretaris(new NewItemNotification($item->load(['category', 'lab'])));

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $this->authorize('view', $item);

        $item->load(['category', 'lab', 'labTable.group', 'reports', 'repairs.user', 'audits.user']);

        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('admin.items.edit', array_merge(['item' => $item], $this->formData()));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return $this->backToList('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $item->forcedelete();

        return back()
            ->with('success', 'Barang berhasil dihapus.');
    }

    /** Atur (tambah/kurang/set) jumlah barang secara cepat. */
    public function adjustStock(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $data = $request->validate([
            'mode' => ['required', 'in:tambah,kurang,set'],
            'jumlah' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $before = $item->jumlah_total;

        $item->jumlah_total = match ($data['mode']) {
            'tambah' => $before + $data['jumlah'],
            'kurang' => max(0, $before - $data['jumlah']),
            'set' => $data['jumlah'],
        };
        $item->save();

        ActivityLogger::updated($item, "Mengubah jumlah \"{$item->nama}\" dari {$before} menjadi {$item->jumlah_total}.");

        return back()->with('success', "Jumlah barang diperbarui menjadi {$item->jumlah_total}.");
    }

    /** Data dropdown untuk form create/edit. */
    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('nama')->get(),
            'labs' => Lab::with('groups.tables')->orderBy('nama')->get(),
            'statuses' => ItemStatus::options(),
        ];
    }
}
