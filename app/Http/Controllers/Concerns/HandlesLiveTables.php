<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HandlesLiveTables
{
    /**
     * Deteksi permintaan live table (search as-you-type / pagination AJAX).
     */
    protected function isLiveTable(Request $request): bool
    {
        return $request->header('X-Live-Table') === '1'
            || $request->boolean('live');
    }
}