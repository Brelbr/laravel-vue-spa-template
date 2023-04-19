<?php

use Illuminate\Support\Facades\Log;

if (! function_exists('helperTest')) {
    function helpertest($cmd): string
    {
        Log::info('Helper ping - '.$cmd);

        return $cmd;
    }
}
