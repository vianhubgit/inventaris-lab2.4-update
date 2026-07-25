<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /** Jumlah maksimum log aktivitas yang disimpan (log lama dihapus otomatis). */
    public const MAX_LOGS = 100;

    /**
     * Catat satu aktivitas ke tabel activity_logs.
     */
    public static function log(string $action, string $description, ?Model $subject = null): ActivityLog
    {
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);

        // Batasi hanya menyimpan log terbaru agar tabel tidak menumpuk.
        self::prune();

        return $log;
    }

    /**
     * Hapus log lama sehingga hanya tersisa MAX_LOGS log terbaru.
     */
    protected static function prune(int $keep = self::MAX_LOGS): void
    {
        // id ke-(keep+1) terbaru; semua id <= ini adalah log lama yang dihapus.
        $threshold = ActivityLog::query()
            ->orderByDesc('id')
            ->skip($keep)
            ->take(1)
            ->value('id');

        if ($threshold !== null) {
            ActivityLog::where('id', '<=', $threshold)->delete();
        }
    }

    public static function created(Model $subject, string $description): ActivityLog
    {
        return self::log('created', $description, $subject);
    }

    public static function updated(Model $subject, string $description): ActivityLog
    {
        return self::log('updated', $description, $subject);
    }

    public static function deleted(Model $subject, string $description): ActivityLog
    {
        return self::log('deleted', $description, $subject);
    }
}
