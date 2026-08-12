<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(
        protected StatisticsService $service
    ) {}

    public function index(Request $request)
    {
        $stats = $this->service->forUser($request->user()->id);
        return ApiResponseService::success($stats);
    }
}