<?php

namespace App\Http\Controllers\Sekretaris;

use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sekretaris\StoreReportRequest;
use App\Models\Item;
use App\Models\Lab;
use App\Models\Report;
use App\Notifications\NewReportNotification;
use App\Services\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    /** Riwayat laporan milik sekretaris (bisa difilter rusak/hilang). */
    public function index(Request $request): View
    {
        $reports = Report::query()
            ->with(['item', 'lab', 'group', 'labTable'])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->latest('reported_at')
            ->paginate(10)
            ->withQueryString();

        return view('sekretaris.reports.index', [
            'reports' => $reports,
            'types' => ReportType::options(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Report::class);

        $type = ReportType::tryFrom($request->query('type', 'rusak')) ?? ReportType::RUSAK;

        return view('sekretaris.reports.create', array_merge(
            ['type' => $type],
            $this->formData()
        ));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('reports', 'public');
        }

        $report = Report::create($data);

        // Beri tahu admin ada laporan baru.
        Notifier::toAdmins(new NewReportNotification($report->load(['item', 'lab', 'user'])));

        return redirect()->route('sekretaris.reports.index', ['type' => $data['type']])
            ->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load(['item', 'lab', 'group', 'labTable', 'repairs']);

        return view('sekretaris.reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        $this->authorize('update', $report);

        return view('sekretaris.reports.edit', array_merge(
            ['report' => $report, 'type' => $report->type],
            $this->formData()
        ));
    }

    public function update(StoreReportRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($report->foto) {
                Storage::disk('public')->delete($report->foto);
            }
            $data['foto'] = $request->file('foto')->store('reports', 'public');
        }

        $report->update($data);

        return $this->backToList('sekretaris.reports.index', ['type' => $report->type])
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        if ($report->foto) {
            Storage::disk('public')->delete($report->foto);
        }
        $report->forcedelete();

        return back()
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /** Data lokasi & barang untuk dropdown bertingkat (lab → kelompok → meja → barang). */
    private function formData(): array
    {
        return [
            'labs' => Lab::with('groups.tables')->orderBy('nama')->get(),
            'items' => Item::with('lab')->orderBy('nama')->get(['id', 'nama', 'lab_id', 'lab_table_id']),
        ];
    }
}
