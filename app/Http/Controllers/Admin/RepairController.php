<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemStatus;
use App\Enums\ReportStatus;
use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Repair;
use App\Models\Report;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RepairController extends Controller
{
    public function index(Request $request): View
    {
        $repairs = Repair::query()
            ->with(['item', 'user', 'report'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('tanggal')
            ->paginate(12)
            ->withQueryString();

        return view('admin.repairs.index', [
            'repairs' => $repairs,
            'statuses' => RepairStatus::options(),
        ]);
    }

    public function create(Request $request): View
    {
        $report = $request->filled('report_id')
            ? Report::with('item')->find($request->report_id)
            : null;

        return view('admin.repairs.create', [
            'items' => Item::groupedByName(),
            'statuses' => RepairStatus::options(),
            'report' => $report,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'report_id' => ['nullable', 'exists:reports,id'],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(RepairStatus::class)],
        ]);

        $data['user_id'] = $request->user()->id;

        $repair = Repair::create($data);
        ActivityLogger::created($repair, "Mencatat riwayat perbaikan untuk barang #{$repair->item_id}.");

        // Sinkronkan status barang & laporan bila perbaikan selesai.
        if ($repair->status === RepairStatus::SELESAI) {
            $repair->item?->update(['status' => ItemStatus::BAIK]);
            $repair->report?->update(['status' => ReportStatus::SELESAI]);
        } elseif ($repair->status === RepairStatus::PROSES) {
            $repair->item?->update(['status' => ItemStatus::PERBAIKAN]);
            $repair->report?->update(['status' => ReportStatus::DIPROSES]);
        }

        return redirect()->route('admin.repairs.index')
            ->with('success', 'Riwayat perbaikan berhasil dicatat.');
    }

    public function edit(Repair $repair): View
    {
        return view('admin.repairs.edit', [
            'repair' => $repair,
            'items' => Item::groupedByName(),
            'statuses' => RepairStatus::options(),
        ]);
    }

    public function update(Request $request, Repair $repair): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(RepairStatus::class)],
        ]);

        $repair->update($data);

        if ($repair->status === RepairStatus::SELESAI) {
            $repair->item?->update(['status' => ItemStatus::BAIK]);
            $repair->report?->update(['status' => ReportStatus::SELESAI]);
        }

        return $this->backToList('admin.repairs.index')
            ->with('success', 'Riwayat perbaikan berhasil diperbarui.');
    }

    public function destroy(Repair $repair): RedirectResponse
    {
        $repair->delete();

        return back()
            ->with('success', 'Riwayat perbaikan berhasil dihapus.');
    }
}
