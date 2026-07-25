<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockAudit;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockAuditController extends Controller
{
    public function index(Request $request): View
    {
        $audits = StockAudit::query()
            ->with(['item', 'user'])
            ->when($request->filled('item_id'), fn ($q) => $q->where('item_id', $request->item_id))
            ->latest('tanggal')
            ->paginate(12)
            ->withQueryString();

        return view('admin.audits.index', [
            'audits' => $audits,
            'items' => Item::orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.audits.create', [
            'items' => Item::groupedByName(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'jumlah_fisik' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'tanggal' => ['required', 'date'],
            'sinkronkan' => ['sometimes', 'boolean'],
        ]);

        $item = Item::findOrFail($data['item_id']);
        // Barang bernama sama digabung sebagai satu, jadi jumlah tercatat
        // dihitung dari total seluruh barang dengan nama yang sama.
        $tercatat = Item::where('nama', $item->nama)->sum('jumlah_total');
        $fisik = (int) $data['jumlah_fisik'];

        $audit = StockAudit::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'jumlah_tercatat' => $tercatat,
            'jumlah_fisik' => $fisik,
            'selisih' => $fisik - $tercatat,
            'keterangan' => $data['keterangan'] ?? null,
            'tanggal' => $data['tanggal'],
        ]);

        ActivityLogger::created($audit, "Audit stok \"{$item->nama}\": tercatat {$tercatat}, fisik {$fisik}.");

        // Opsional: sinkronkan jumlah tercatat ke hasil fisik.
        // Hanya diterapkan bila barang bernama sama cuma satu record, karena
        // jika ada beberapa lokasi tidak jelas ke record mana harus disimpan.
        $jumlahRecord = Item::where('nama', $item->nama)->count();

        if ($request->boolean('sinkronkan') && $fisik !== $tercatat) {
            if ($jumlahRecord === 1) {
                $item->update(['jumlah_total' => $fisik]);
            } else {
                return redirect()->route('admin.audits.index')
                    ->with('success', 'Audit inventaris berhasil dicatat. Sinkronisasi otomatis dilewati karena barang "'.$item->nama.'" tersebar di beberapa lokasi — silakan sesuaikan jumlah tiap barang secara manual.');
            }
        }

        return redirect()->route('admin.audits.index')
            ->with('success', 'Audit inventaris berhasil dicatat.');
    }

    public function destroy(StockAudit $audit): RedirectResponse
    {
        $audit->delete();

        return back()
            ->with('success', 'Data audit berhasil dihapus.');
    }
}
