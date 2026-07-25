<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Streamer;

trait InteractsWithStreamer
{
    /**
     * Resolve the streamer profile owned by the authenticated user.
     */
    protected function streamer(): Streamer
    {
        $streamer = auth()->user()?->streamer;

        abort_if($streamer === null, 403, 'ບັນຊີນີ້ຍັງບໍ່ມີໂປຣໄຟລ໌ສະຕຣີມເມີ');

        return $streamer;
    }
}
