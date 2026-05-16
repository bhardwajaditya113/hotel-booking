<?php

namespace App\Http\Controllers;

use App\Support\PortalSync;
use Illuminate\Http\JsonResponse;

class PortalSyncController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'version' => PortalSync::version(),
        ]);
    }
}
