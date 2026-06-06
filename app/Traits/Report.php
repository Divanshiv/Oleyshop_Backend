<?php

namespace App\Traits;

use Carbon\Carbon;
use App\CentralLogics\Helpers;

trait Report
{
    public function orderStatusCount($branchId, $date)
    {
        $orderCounts = $this->order
            ->selectRaw('order_status, COUNT(*) as total')
            ->when($branchId && $branchId !== 'all', fn($q) => $q->where('branch_id', $branchId))
            ->when(!is_null($date), fn($q) => $q->whereBetween('created_at', $date))
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $total = collect($orderCounts)->sum();

        $groups = [
            'ongoing'   => ['pending', 'confirmed', 'processing', 'out_for_delivery'],
            'delivered' => ['delivered'],
            'failed'    => ['failed'],
            'returned'  => ['returned'],
            'cancelled' => ['canceled'],
        ];

        return  collect($groups)->mapWithKeys(function ($statuses, $group) use ($orderCounts, $total) {
            $count = collect($orderCounts)->only($statuses)->sum();
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;


            $style = Helpers::order_status_card_style($group);

            return [
                $group => [
                    'count'      => $count,
                    'percentage' => $percentage,
                    'style' => $style,
                ]
            ];
        });
    }

    public function getAnalytics($branchId = null, $dateRange, $date): mixed
    {
        if ($branchId == 'all') {
            $branchId = null;
        }

        $monthlyOrder = [];
        $label = [];

        switch ($dateRange) {
            case 'day':
                $label = [
                    '"6:00 am"', '"8:00 am"', '"10:00 am"', '"12:00 pm"',
                    '"2:00 pm"', '"4:00 pm"', '"6:00 pm"', '"8:00 pm"',
                    '"10:00 pm"', '"12:00 am"', '"2:00 am"', '"4:00 am"'
                ];

                $startTime = strtotime('6:00 AM');
                for ($i = 0; $i < 12; $i++) {
                    $monthlyOrder[$i] = $this->OrderChartData(branchId: $branchId, startTime: $startTime);
                    $startTime = strtotime('+2 hours', $startTime);
                }
                break;

            case 'week':
                $weekStartDate = $date['start']->copy()->startOfWeek(Carbon::SUNDAY);

                for ($i = 1; $i <= 7; $i++) {
                    $monthlyOrder[$i] = $this->OrderChartData(
                        branchId: $branchId,
                        startDate: $weekStartDate,
                        endDate: $weekStartDate
                    );

                    $label[] = $weekStartDate->format('"D"');

                    $weekStartDate->addDay();
                }
                break;


            case 'month':
                $label = [];
                $monthlyOrder = [];

                $daysInMonth = $date['start']->daysInMonth;
                $start = $date['start']->copy()->startOfMonth();
                $end   = $start->copy()->addDays(4);

                for ($i = 1; $i <= 6; $i++) {
                    if ($i == 6) {
                        $end = $date['start']->copy()->endOfMonth();
                    }

                    $label[] = '"Day ' . $start->day . '-' . $end->day . '"';

                    $monthlyOrder[$i] = $this->OrderChartData(
                        branchId: $branchId,
                        startDate: $start,
                        endDate: $end
                    );

                    $start = $end->copy()->addDay();
                    $end   = $start->copy()->addDays(4);
                }
                break;


            case 'year':
                $label = ['"Jan"', '"Feb"', '"Mar"', '"Apr"', '"May"', '"Jun"', '"Jul"', '"Aug"', '"Sep"', '"Oct"', '"Nov"', '"Dec"'];
                $year = $date['start']->year;
                for ($i = 1; $i <= 12; $i++) {
                    $monthlyOrder[$i - 1] = $this->OrderChartData(branchId: $branchId, month: $i, year: $year);
                }
                break;

            case 'CUSTOM_DATE':
            default:
                $businessStartDate = $date['start'];
                $today = $date['end'];

                if ($businessStartDate->year < $today->year) {
                    for ($y = $businessStartDate->year; $y <= $today->year; $y++) {
                        $label[] = '"' . $y . '"';
                        $startDate = $y == $businessStartDate->year ? $businessStartDate : now()->setYear($y)->startOfYear();
                        $endDate = $y == $today->year ? $today : now()->setYear($y)->endOfYear();
                        $monthlyOrder[] = $this->OrderChartData(branchId: $branchId, startDate: $startDate, endDate: $endDate);
                    }
                } else {
                    $label = ['"Jan"', '"Feb"', '"Mar"', '"Apr"', '"May"', '"Jun"', '"Jul"', '"Aug"', '"Sep"', '"Oct"', '"Nov"', '"Dec"'];
                    for ($i = 1; $i <= 12; $i++) {
                        $monthStart = now()->setYear($businessStartDate->year)->setMonth($i)->startOfMonth();
                        $monthEnd = now()->setYear($businessStartDate->year)->setMonth($i)->endOfMonth();
                        if ($monthStart < $businessStartDate) $monthStart = $businessStartDate;
                        if ($monthEnd > $today) $monthEnd = $today;
                        $monthlyOrder[$i - 1] = $this->OrderChartData(branchId: $branchId, startDate: $monthStart, endDate: $monthEnd);
                    }
                }
                break;
        }
        return [$label, $monthlyOrder];
    }
    private function OrderChartData(
        $branchId = null,
        $startDate = null,
        $endDate = null,
        $startTime = null,
        $month = null,
        $year = null
    ) {
        $query = $this->order
            ->where('order_status', 'delivered')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ]);
        } elseif ($startTime !== null) {
            $today = Carbon::today();
            $query->whereBetween('created_at', [
                $today->copy()->setTimeFromTimeString(date('H:i:s', $startTime)),
                $today->copy()->setTimeFromTimeString(date('H:i:s', strtotime('+2 hours', $startTime))),
            ]);
        } elseif ($month !== null) {
            $query->whereMonth('created_at', $month);
            if ($year !== null) {
                $query->whereYear('created_at', $year);
            }
        } elseif ($year !== null) {
            $query->whereYear('created_at', $year);
        }

        return [
            'totalOrder' => (float) $query->sum('order_amount'),
            'totalTax' => (float) $query->sum('total_tax_amount'),
            'totalDelivery' => (float) $query->sum('delivery_charge'),
        ];
    }

}
