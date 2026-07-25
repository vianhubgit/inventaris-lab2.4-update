<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Kembali ke daftar semula (mempertahankan filter & halaman) menggunakan
     * URL yang dibawa field tersembunyi `_prev`. Hanya URL host yang sama yang
     * diterima (mencegah open-redirect); selain itu jatuh ke route index.
     *
     * @param  array<string,mixed>  $fallbackParams
     */
    protected function backToList(string $fallbackRoute, array $fallbackParams = []): RedirectResponse
    {
        $prev = request()->input('_prev');

        if (is_string($prev) && $prev !== '' && str_starts_with($prev, request()->schemeAndHttpHost())) {
            return redirect()->to($prev);
        }

        return redirect()->route($fallbackRoute, $fallbackParams);
    }
}
