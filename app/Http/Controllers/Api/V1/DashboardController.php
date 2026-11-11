<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Managers\HighChartManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * External plant-wide dashboard API (Manager Report data).
 * Auth: Sanctum Bearer token + ability dashboard:read (หรือ *).
 */
class DashboardController extends Controller
{
    /**
     * Overview: overall financial summary + operation stats.
     *
     * Query: date_start_at, date_end_at (overall), start_at, end_at (operations)
     */
    public function index(Request $request): JsonResponse
    {
        $this->validateDates($request, [
            'date_start_at',
            'date_end_at',
            'start_at',
            'end_at',
        ]);

        $overall = HighChartManager::getOverallSummary([
            'date_start_at' => $request->query('date_start_at', ''),
            'date_end_at' => $request->query('date_end_at', ''),
        ]);

        $operations = HighChartManager::getOperationStatsSummary(
            (string) $request->query('start_at', ''),
            (string) $request->query('end_at', '')
        );

        return $this->ok([
            'overall' => $overall,
            'operations' => $operations,
        ]);
    }

    /**
     * Operation stats (default last 7 days).
     */
    public function operations(Request $request): JsonResponse
    {
        $this->validateDates($request);

        return $this->ok(
            HighChartManager::getOperationStatsSummary(
                (string) $request->query('start_at', ''),
                (string) $request->query('end_at', '')
            )
        );
    }

    /**
     * Sales: year summary + latest days series.
     */
    public function sales(Request $request): JsonResponse
    {
        $this->validateDates($request);

        return $this->ok([
            'year' => HighChartManager::getSalesYearSummary(),
            'latest_days' => HighChartManager::getSalesLatestDaysSummary(
                (string) $request->query('start_at', ''),
                (string) $request->query('end_at', '')
            ),
        ]);
    }

    /**
     * Energy pie + daily series.
     */
    public function energy(Request $request): JsonResponse
    {
        $this->validateDates($request, [
            'date_start_at',
            'date_end_at',
            'start_at',
            'end_at',
        ]);

        return $this->ok([
            'summary' => HighChartManager::getEnergySummary([
                'date_start_at' => $request->query('date_start_at', ''),
                'date_end_at' => $request->query('date_end_at', ''),
            ]),
            'days' => HighChartManager::getEnergyDaysSummary(
                (string) $request->query('start_at', ''),
                (string) $request->query('end_at', '')
            ),
        ]);
    }

    /**
     * Income daily series.
     */
    public function income(Request $request): JsonResponse
    {
        $this->validateDates($request);

        return $this->ok(
            HighChartManager::getIncomeDaysSummary(
                (string) $request->query('start_at', ''),
                (string) $request->query('end_at', '')
            )
        );
    }

    /**
     * Expense daily series.
     */
    public function expense(Request $request): JsonResponse
    {
        $this->validateDates($request);

        return $this->ok(
            HighChartManager::getExpenseDaysSummary(
                (string) $request->query('start_at', ''),
                (string) $request->query('end_at', '')
            )
        );
    }

    /**
     * @param  array<int, string>  $extraKeys
     */
    private function validateDates(Request $request, array $extraKeys = []): void
    {
        $rules = [
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
        ];

        foreach ($extraKeys as $key) {
            if (!isset($rules[$key])) {
                $rules[$key] = ['nullable', 'date'];
            }
        }

        $request->validate($rules);
    }

    private function ok(mixed $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
