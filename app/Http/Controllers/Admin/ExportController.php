<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ItemsExport;
use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Report;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Exports\ProcurementsExport;
use App\Models\Procurement;
use App\Exports\RepairsExport;
use App\Exports\StockAuditsExport;
use App\Models\Repair;
use App\Models\StockAudit;

class ExportController extends Controller
{
    /* ===================== BARANG / ITEMS ===================== */

    public function itemsExcel(Request $request): BinaryFileResponse
    {
        ActivityLogger::log('export', 'Export inventaris ke Excel.');

        return Excel::download(
            new ItemsExport($request->only(['category_id', 'lab_id', 'status', 'q'])),
            'inventaris-'.now()->format('Ymd-His').'.xlsx'
        );
    }

public function itemsPdf(Request $request): Response
{
    $items = Item::query()
        ->with(['category', 'lab', 'labTable.group'])
        ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
        ->when($request->filled('lab_id'), fn($q) => $q->where('lab_id', $request->lab_id))
        ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
        ->when($request->filled('q'), fn($q) => $q->search($request->q))
        ->get();

    $labOrder = [
        'Lab A' => 1,
        'Lab B' => 2,
        'TEFA'  => 3,
    ];

    $statusOrder = [
        'baik'       => 1,
        'rusak'      => 2,
        'hilang'     => 3,
        'perbaikan'  => 4,
    ];

    $items = $items
        ->sort(function ($a, $b) use ($labOrder, $statusOrder) {

            $labCompare =
                ($labOrder[$a->lab->nama] ?? 99)
                <=>
                ($labOrder[$b->lab->nama] ?? 99);

            if ($labCompare !== 0) {
                return $labCompare;
            }

            $statusCompare =
                ($statusOrder[$a->status->value] ?? 99)
                <=>
                ($statusOrder[$b->status->value] ?? 99);

            if ($statusCompare !== 0) {
                return $statusCompare;
            }

            return strcmp($a->nama, $b->nama);

        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Kelompokkan per Lab
    |--------------------------------------------------------------------------
    */

    $labs = collect();

    foreach ($items as $item) {

        $labName = $item->lab->nama;

        if (!isset($labs[$labName])) {

            $labs[$labName] = collect();

        }

        /*
        |--------------------------------------------------------------------------
        | Barang Baik digabung
        |--------------------------------------------------------------------------
        */

        if ($item->status->value === 'baik') {

            $key = implode('|', [

                $item->lab_id,
                $item->category_id,
                $item->nama,
                'baik',

            ]);

            if (!isset($labs[$labName][$key])) {

                $labs[$labName][$key] = (object)[

                    'nama' => $item->nama,

                    'kategori' => $item->category?->nama,

                    'lokasi' => $item->lab->nama,

                    'jumlah' => $item->jumlah_total,

                    'status' => $item->status,

                    'keterangan' => $item->keterangan,

                ];

            } else {

                $labs[$labName][$key]->jumlah += $item->jumlah_total;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Rusak / Hilang / Perbaikan
        |--------------------------------------------------------------------------
        */

        else {

            $labs[$labName]->push((object)[

                'nama' => $item->nama,

                'kategori' => $item->category?->nama,

                'lokasi' => $item->lokasi_lengkap,

                'jumlah' => $item->jumlah_total,

                'status' => $item->status,

                'keterangan' => $item->keterangan,

            ]);

        }

    }

    ActivityLogger::log('export', 'Export inventaris ke PDF.');

    $pdf = Pdf::loadView('pdf.items', [

        'labs' => $labs,

        'tanggal' => now()->translatedFormat('d F Y H:i'),

    ])->setPaper('a4', 'landscape');

    return $pdf->download(
        'inventaris-' . now()->format('Ymd-His') . '.pdf'
    );
}


    /* ================= PINJAMAN BARANG =========================*/
    public function procurementsExcel(Request $request): BinaryFileResponse
    {
       ActivityLogger::log('export', 'Export peminjaman ke Excel.');

       return Excel::download(
           new ProcurementsExport($request->only(['status'])),
           'peminjaman-'.now()->format('Ymd-His').'.xlsx'
       );
    }

public function procurementsPdf(Request $request): Response
{
    $rows = Procurement::query()
        ->with(['user'])
        ->when(
            $request->filled('status'),
            fn ($q) => $q->where('status', $request->status)
        )
        ->orderBy('requested_at')
        ->get();

    $groups = collect();

    foreach ($rows as $row) {

        $last = $groups->last();

        if (
            $last &&
            $last['user_id'] == $row->user_id &&
            $last['requested_at']->diffInMinutes($row->requested_at) <= 10
        ) {

            $last['items'][] = $row;

            $groups->pop();
            $groups->push($last);

        } else {

            $groups->push([
                'requested_at' => $row->requested_at,
                'user_id'      => $row->user_id,
                'user'         => $row->user,
                'items'        => [$row],
            ]);
        }
    }

    ActivityLogger::log('export', 'Export peminjaman ke PDF.');

    $pdf = Pdf::loadView('pdf.procurements', [
        'groups' => $groups,
        'tanggal' => now()->translatedFormat('d F Y H:i'),
    ])->setPaper('a4', 'landscape');

    return $pdf->download(
        'peminjaman-' . now()->format('Ymd-His') . '.pdf'
    );
}

    /* ===================== LAPORAN / REPORTS ===================== */

    public function reportsExcel(Request $request): BinaryFileResponse
    {
        ActivityLogger::log('export', 'Export laporan ke Excel.');

        return Excel::download(
            new ReportsExport($request->only(['type', 'status', 'lab_id'])),
            'laporan-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function reportsPdf(Request $request): Response
    {
        $reports = Report::query()
            ->with(['user', 'lab', 'group', 'labTable', 'item'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('lab_id'), fn ($q) => $q->where('lab_id', $request->lab_id))
            ->latest('reported_at')
            ->get();

        ActivityLogger::log('export', 'Export laporan ke PDF.');

        $pdf = Pdf::loadView('pdf.reports', [
            'reports' => $reports,
            'tanggal' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-'.now()->format('Ymd-His').'.pdf');
    }

    /* ===================== RIWAYAT PERBAIKAN / REPAIRS ===================== */

    public function repairsExcel(Request $request): BinaryFileResponse
    {
        ActivityLogger::log('export', 'Export riwayat perbaikan ke Excel.');

        return Excel::download(
            new RepairsExport($request->only(['status'])),
            'riwayat-perbaikan-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function repairsPdf(Request $request): Response
    {
        $repairs = Repair::query()
            ->with(['item', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('tanggal')
            ->get();

        ActivityLogger::log('export', 'Export riwayat perbaikan ke PDF.');

        $pdf = Pdf::loadView('pdf.repairs', [
            'repairs' => $repairs,
            'tanggal' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('riwayat-perbaikan-'.now()->format('Ymd-His').'.pdf');
    }

    /* ===================== AUDIT INVENTARIS / STOCK AUDITS ===================== */

    public function auditsExcel(Request $request): BinaryFileResponse
    {
        ActivityLogger::log('export', 'Export audit inventaris ke Excel.');

        return Excel::download(
            new StockAuditsExport($request->only(['item_id'])),
            'audit-inventaris-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function auditsPdf(Request $request): Response
    {
        $audits = StockAudit::query()
            ->with(['item', 'user'])
            ->when($request->filled('item_id'), fn ($q) => $q->where('item_id', $request->item_id))
            ->latest('tanggal')
            ->get();

        ActivityLogger::log('export', 'Export audit inventaris ke PDF.');

        $pdf = Pdf::loadView('pdf.audits', [
            'audits' => $audits,
            'tanggal' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('audit-inventaris-'.now()->format('Ymd-His').'.pdf');
    }
}
