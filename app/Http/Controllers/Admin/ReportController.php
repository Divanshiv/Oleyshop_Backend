<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Traits\Report;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    use Report;

    public function __construct(
        private Branch $branch,
        private BusinessSetting $businessSetting,
        private Order $order,
        private OrderDetail $orderDetail
    ) {}

    public function saleReportIndex(Request $request)
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $branches = $this->branch->all();
        $branchId = $request['branch_id'];
        $dateRange = $request->query('date_range', ALL_TIME);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $queryParam = collect([
            'per_page' => $perPage,
            'branch_id' => $branchId,
            'date_range' => $dateRange,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => $search
        ])->filter(fn($value) => filled($value))->all();

        $orderDetailQuery = $this->orderDetail
            ->with(['product', 'order'])
            ->whereHas('order', fn($q) => $q->where('order_status', 'delivered'))
            ->when($branchId && $branchId !== 'all', function ($q) use ($branchId) {
                $q->whereHas('order', fn($sub) => $sub->where('branch_id', $branchId));
            })
            ->when(!is_null($date), fn($q) => $q->whereBetween('created_at', $date));

        $products = (clone $orderDetailQuery)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })
                        ->orWhere(function ($or) use ($search) {
                            $or->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_details, '$.name')) LIKE ?", ["%{$search}%"])
                                ->orWhere('product_id', (int) $search);
                        });
                });
            })
            ->select('product_id', 'product_details')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(quantity * price) as total_amount')
            ->selectRaw('AVG(price) as avg_price')
            ->groupBy('product_id')
            ->orderByDesc('product_id')
            ->paginate($perPage)
            ->appends($queryParam);

        $totalOrders = $orderDetailQuery->pluck('order_id')->unique()->count();
        $totalAmount = $orderDetailQuery->selectRaw('SUM(quantity * (price - discount_on_product)) as total_amount')->value('total_amount');
        $totalQuantity = $orderDetailQuery->sum("quantity");

        return view('admin-views.report.sale-report', compact(
            'dateRange',
            'branches',
            'perPage',
            'branchId',
            'startDate',
            'endDate',
            'search',
            'products',
            'totalOrders',
            'totalQuantity',
            'totalAmount'
        ));
    }

    public function exportSaleReport(Request $request)
    {
        $branchId = $request->branch_id ?? null;
        $dateRange = $request->date_range;
        $search = $request->search ?? null;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $products = $this->orderDetail
            ->with('product')
            ->whereHas('order', fn($q) => $q->where('order_status', 'delivered'))
            ->when($branchId && $branchId !== 'all', function ($q) use ($branchId) {
                $q->whereHas('order', fn($sub) => $sub->where('branch_id', $branchId));
            })
            ->when(!is_null($date), fn($q) => $q->whereHas('order', fn($sub) => $sub->whereBetween('created_at', $date)))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })
                        ->orWhere(function ($or) use ($search) {
                            $or->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_details, '$.name')) LIKE ?", ["%{$search}%"])
                                ->orWhere('product_id', (int) $search);
                        });
                });
            })
            ->select('product_id', 'product_details')
            ->selectRaw('SUM(quantity) as total_quantity, SUM(quantity * price) as total_amount, AVG(price) as avg_price')
            ->groupBy('product_id')
            ->orderByDesc('product_id')
            ->get();

        return (new FastExcel($products))->download('sale-report.xlsx', function ($row) {
            $product_info = json_decode($row->product_details, true);

            $productName = optional($row->product)->name ?? ($product_info['name'] ?? 'Unknown Product');

            return [
                'Product Name' => $productName,
                'Total Quantity' => $row->total_quantity,
                'Total Amount' => Helpers::set_symbol($row->total_amount),
                'Average Price' => Helpers::set_symbol($row->avg_price),
            ];
        });
    }
    public function orderReportIndex(Request $request)
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $branches = $this->branch->pluck('name', 'id');
        $branchId = $request->query('branch_id');
        $dateRange = $request->query('date_range', ALL_TIME);
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $queryParam = collect([
            'per_page' => $perPage,
            'branch_id' => $branchId,
            'date_range' => $dateRange,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => $search
        ])->filter(fn($value) => filled($value))->all();

        $finalCounts = $this->orderStatusCount(branchId: $branchId, date: $date);
        $orderChartData = $this->getAnalytics(branchId: $branchId, dateRange: Helpers::getDateRangeType($date), date: $date);
        $labels = $orderChartData[0];
        $values = $orderChartData[1];
        $totalOrders = array_column($values, 'totalOrder');
        $taxData = array_column($values, 'totalTax');
        $deliveryChargeData = array_column($values, 'totalDelivery');

        $orders = $this->order
            ->with(['details'])
            ->where('order_status', 'delivered')
            ->when($branchId && $branchId !== 'all', fn($q) => $q->where('branch_id', $branchId))
            ->when(!is_null($date), fn($q) => $q->whereBetween('created_at', $date))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->Where('id', (int) $search);
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParam);

        return view(
            'admin-views.report.order-index',
            compact(
                'branches',
                'perPage',
                'branchId',
                'dateRange',
                'startDate',
                'endDate',
                'finalCounts',
                'totalOrders',
                'taxData',
                'deliveryChargeData',
                'labels',
                'orders',
                'search',
            )
        );
    }

    public function exportOrderReport(Request $request)
    {
        $branchId = $request->branch_id ?? null;
        $dateRange = $request->date_range;
        $search = $request->search ?? null;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $orders = $this->order
            ->with(['details'])
            ->where('order_status', 'delivered')
            ->when($branchId && $branchId !== 'all', fn($q) => $q->where('branch_id', $branchId))
            ->when(!is_null($date), fn($q) => $q->whereBetween('created_at', $date))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->Where('id', (int) $search);
                });
            })
            ->orderByDesc('id')
            ->get();

        $orders->transform(function ($order) {
            $productDiscount = $order->details->sum(function ($detail) {
                $product = $detail->product;

                if (!$product)
                    return 0;

                $price = $product->price;

                $discount = $product->discount_type == 'percentage'
                    ? ($price * $product->discount_value / 100)
                    : $product->discount_value;

                $discount *= $detail->quantity ?? 1;

                return $discount;
            });

            $order->product_discount = $productDiscount;

            $order->total_order_amount = $order->order_amount
                - $productDiscount
                - $order->coupon_discount_amount
                + $order->delivery_charge
                + $order->total_tax_amount;

            return $order;
        });

        return (new FastExcel($orders))->download('order-list.xlsx', function ($row) {

            return [
                'Order ID' => $row->id,
                'Date' => $row->created_at->format('d M Y h:i A'),
                'Order Amount' =>  Helpers::set_symbol($row->details->sum(fn($item) => $item->price * $item->quantity)),
                'Product Discount' => Helpers::set_symbol($row->details->sum(fn($item) => $item->discount_on_product * $item->quantity)),
                'Coupon Discount' => Helpers::set_symbol($row->coupon_discount_amount),
                'Extra Discount' => Helpers::set_symbol($row->extra_discount),
                'Delivery Charge' => Helpers::set_symbol($row->delivery_charge),
                'Charge On Weight' => Helpers::set_symbol($row->weight_charge_amount),
                'VAT / Tax' => Helpers::set_symbol($row->total_tax_amount),
                'Total Order Amount' => Helpers::set_symbol($row->order_amount),
            ];
        });
    }

    public function earningIndex(Request $request): Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|Application
    {
        $from = $request->from ? Carbon::createFromFormat('d M Y', $request->from)->startOfDay() : null;
        $to = $request->to ? Carbon::createFromFormat('d M Y', $request->to)->endOfDay() : null;

        $totalTax = $this->order
            ->where(['order_status' => 'delivered'])
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->sum('total_tax_amount');

        $totalDeliveryCharge = $this->order
            ->where(['order_status' => 'delivered'])
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->sum('delivery_charge');

        $totalSold = $this->order
            ->where(['order_status' => 'delivered'])
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->sum('order_amount');

        $totalEarning = $totalSold - $totalTax - $totalDeliveryCharge;

        $sold = [];
        $tax = [];
        $deliveryCharge = [];
        $thisYearTotalSold = 0;

        for ($i = 1; $i <= 12; $i++) {
            $startOfMonth = Carbon::create(null, $i)->startOfMonth();
            $endOfMonth = Carbon::create(null, $i)->endOfMonth();

            $sold[$i] = $this->order
                ->where(['order_status' => 'delivered'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('order_amount');

            $tax[$i] = $this->order
                ->where(['order_status' => 'delivered'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_tax_amount');

            $deliveryCharge[$i] = $this->order
                ->where(['order_status' => 'delivered'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('delivery_charge');

            $thisYearTotalSold += $sold[$i];
        }

        return view('admin-views.report.earning-index', compact('totalTax', 'totalDeliveryCharge', 'totalSold', 'totalEarning', 'sold', 'tax', 'deliveryCharge', 'thisYearTotalSold', 'from', 'to'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function setDate(Request $request): RedirectResponse
    {
        $fromDate = Carbon::parse($request['from'])->startOfDay();
        $toDate = Carbon::parse($request['to'])->endOfDay();

        session()->put('from_date', $fromDate);
        session()->put('to_date', $toDate);
        return back();
    }

    public function expenseIndex(Request $request)
    {
        $request->validate([
            'start_date' => 'required_if:date_type,custom_date',
            'end_date' => 'required_if:date_type,custom_date',
        ]);

        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request['search'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $dateType = $request['date_type'] ?? 'this_year';
        $expenseType = $request['expense_type'] ?? null;


        $queryParam = collect([
            'per_page' => $perPage,
            'date_type' => $dateType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expense_type' => $expenseType,
            'search' => $search
        ])->filter(fn($value) => filled($value))->all();

        $filterQuery = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query) {
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            })
            ->when(($dateType == 'this_year'), function ($query) {
                return $query->whereYear('created_at', date('Y'));
            })
            ->when(($dateType == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($dateType == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($dateType == 'custom_date' && !is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        $expenseCalculate = $filterQuery->latest()->get();

        $totalExpense = 0;
        $extraDiscount = 0;
        $freeDelivery = 0;
        $freeDeliveryOverAmount = 0;
        $couponDiscount = 0;
        if ($expenseCalculate) {
            foreach ($expenseCalculate as $calculate) {
                $extraDiscount += $calculate->extra_discount;
                $freeDeliveryOverAmount += $calculate->free_delivery_amount;
                if (isset($calculate->coupon->coupon_type) && $calculate->coupon->coupon_type == 'free_delivery') {
                    $freeDelivery += $calculate->coupon_discount_amount;
                } else {
                    $couponDiscount += $calculate->coupon_discount_amount;
                }
            }
        }

        $freeDelivery += $freeDeliveryOverAmount;
        $totalExpense = $extraDiscount + $freeDelivery + $couponDiscount;

        $expenseTransactionChart = self::expenseTransactionChartFilter($request);

        $expenseTransactionsTable = $filterQuery
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('coupon_code', 'like', "%{$search}%");
            })
            ->when(($expenseType == 'free_delivery'), function ($query) use ($expenseType) {
                $query->where('free_delivery_amount', '>', 0);
            })
            ->when(($expenseType == 'extra_discount'), function ($query) use ($expenseType) {
                $query->where('extra_discount', '>', 0);
            })
            ->when(($expenseType == 'coupon_discount'), function ($query) {
                $query->whereHas('coupon');
            })
            ->latest()
            ->paginate($perPage)
            ->appends($queryParam);

        return view('admin-views.report.expense-report', compact('perPage', 'search', 'startDate', 'endDate', 'dateType', 'expenseTransactionsTable', 'totalExpense', 'freeDelivery', 'couponDiscount', 'extraDiscount', 'expenseTransactionChart', 'queryParam', 'expenseType'));
    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function expenseExportExcel(Request $request): StreamedResponse|string
    {
        $search = $request['search'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $dateType = $request['date_type'] ?? 'this_year';
        $expenseType = $request['expenseType'];

        $filterQuery = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query) {
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            })
            ->when(($dateType == 'this_year'), function ($query) {
                return $query->whereYear('created_at', date('Y'));
            })
            ->when(($dateType == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($dateType == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($dateType == 'custom_date' && !is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        $expenseList = $filterQuery
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('coupon_code', 'like', "%{$search}%");
            })
            ->when(($expenseType == 'free_delivery'), function ($query) use ($expenseType) {
                $query->where('free_delivery_amount', '>', 0);
            })
            ->when(($expenseType == 'extra_discount'), function ($query) use ($expenseType) {
                $query->where('extra_discount', '>', 0);
            })
            ->when(($expenseType == 'coupon_discount'), function ($query) {
                $query->whereHas('coupon');
            })
            ->latest()
            ->get();

        $data = [];

        foreach ($expenseList as $transaction) {
            $expenseAmount = 0;
            if ($transaction->coupon_discount_amount > 0) {
                $expenseAmount = $transaction->coupon_discount_amount;
            } elseif ($transaction->extra_discount > 0) {
                $expenseAmount = $transaction->extra_discount;
            } elseif ($transaction->free_delivery_amount > 0) {
                $expenseAmount = $transaction->free_delivery_amount;
            }

            if (isset($transaction->coupon->coupon_type)) {
                $type = $transaction->coupon->coupon_type;
            } elseif ($transaction->free_delivery_amount > 0) {
                $type = 'Free Delivery';
            } elseif ($transaction->extra_discount > 0) {
                $type = 'Extra Discount';
            } else {
                $type = 'Coupon Deleted';
            }

            $data[] = [
                'Order Date' => date_format($transaction->created_at, 'd F Y'),
                'Order ID' => $transaction->id,
                'Expense Amount' => Helpers::set_symbol($expenseAmount),
                'Expense Type' => ucwords(str_replace('_', ' ', $type)),
            ];
        }

        return (new FastExcel($data))->download('expense_report.xlsx');
    }

    /**
     * @param $request
     * @return array[]|void
     */
    public function expenseTransactionChartFilter($request)
    {
        $from = $request['start_date'];
        $to = $request['end_date'];
        $dateType = $request['date_type'] ?? 'this_year';
        if ($dateType == 'this_year') {
            $number = 12;
            $default_inc = 1;
            $current_start_year = date('Y-01-01');
            $current_end_year = date('Y-12-31');
            $from_year = Carbon::parse($from)->format('Y');

            $this_year = self::expenseTransactionSameYear($request, $current_start_year, $current_end_year, $from_year, $number, $default_inc);
            return $this_year;
        } elseif ($dateType == 'this_month') { //this month table

            $current_month_start = date('Y-m-01');
            $current_month_end = date('Y-m-t');
            $inc = 1;
            $month = date('m');
            $number = date('d', strtotime($current_month_end));

            $this_month = self::expenseTransactionSameMonth($request, $current_month_start, $current_month_end, $month, $number, $inc);
            return $this_month;
        } elseif ($dateType == 'this_week') {

            $this_week = self::expenseTransactionThisWeek($request);
            return $this_week;
        } elseif ($dateType == 'custom_date' && !empty($from) && !empty($to)) {


            $start_date = Carbon::parse($from)->format('Y-m-d 00:00:00');
            $end_date = Carbon::parse($to)->format('Y-m-d 23:59:59');
            $from_year = Carbon::parse($from)->format('Y');
            $from_month = Carbon::parse($from)->format('m');
            $from_day = Carbon::parse($from)->format('d');
            $to_year = Carbon::parse($to)->format('Y');
            $to_month = Carbon::parse($to)->format('m');
            $to_day = Carbon::parse($to)->format('d');

            if ($from_year != $to_year) {
                $different_year = self::expenseTransactionDifferentYear($request, $start_date, $end_date, $from_year, $to_year);
                return $different_year;
            } elseif ($from_month != $to_month) {
                $same_year = self::expenseTransactionSameYear($request, $start_date, $end_date, $from_year, $to_month, $from_month);
                return $same_year;
            } elseif ($from_month == $to_month) {
                $same_month = self::expenseTransactionSameMonth($request, $start_date, $end_date, $from_month, $to_day, $from_day);
                return $same_month;
            }
        }
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $from_year
     * @param $number
     * @param $default_inc
     * @return array[]
     */
    public function expenseTransactionSameYear($request, $start_date, $end_date, $from_year, $number, $default_inc): array
    {
        $orders = self::expenseChartCommonQuery($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year, MONTH(created_at) month')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%M')"))
            ->latest('created_at')
            ->get();


        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $month = date("F", strtotime("2023-$inc-01"));
            $discount_amount[$month . '-' . $from_year] = 0;
            foreach ($orders as $match) {
                if ($match['month'] == $inc) {
                    $discount_amount[$month . '-' . $from_year] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $month_date
     * @param $number
     * @param $default_inc
     * @return array[]
     */
    public function expenseTransactionSameMonth($request, $start_date, $end_date, $month_date, $number, $default_inc): array
    {

        $month = date("F", strtotime("2023-$month_date-01"));
        $orders = self::expenseChartCommonQuery($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%D')"))
            ->latest('created_at')->get();

        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $day = Carbon::createFromFormat('j', $inc)->format('jS');
            $discount_amount[$day . '-' . $month] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $inc) {
                    $discount_amount[$day . '-' . $month] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @return array[]
     */
    public function expenseTransactionThisWeek($request): array
    {
        $start_date = Carbon::now()->startOfWeek();
        $end_date = Carbon::now()->endOfWeek();

        $number = 6;
        $period = CarbonPeriod::create(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $day_name = array();
        foreach ($period as $date) {
            $day_name[] = $date->format('l');
        }

        $orders = self::expenseChartCommonQuery($request)
            ->select(
                DB::raw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount'),
                DB::raw("(DATE_FORMAT(created_at, '%W')) as day")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%D')"))
            ->latest('created_at')->get();

        for ($inc = 0; $inc <= $number; $inc++) {
            $discount_amount[$day_name[$inc]] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $day_name[$inc]) {
                    $discount_amount[$day_name[$inc]] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $from_year
     * @param $to_year
     * @return array[]
     */
    public function expenseTransactionDifferentYear($request, $start_date, $end_date, $from_year, $to_year): array
    {
        $orders = self::expenseChartCommonQuery($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y')"))
            ->latest('created_at')->get();

        for ($inc = $from_year; $inc <= $to_year; $inc++) {
            $discount_amount[$inc] = 0;
            foreach ($orders as $match) {
                if ($match['year'] == $inc) {
                    $discount_amount[$inc] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @return mixed
     */
    public function expenseChartCommonQuery($request)
    {
        $from = $request['start_date'];
        $to = $request['end_date'];
        $dateType = $request['date_type'] ?? 'this_year';

        $order_query = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query) {
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            });

        return self::dateWiseCommonFilter($order_query, $dateType, $from, $to);
    }

    /**
     * @param $query
     * @param $date_type
     * @param $from
     * @param $to
     * @return mixed
     */
    public function dateWiseCommonFilter($query, $date_type, $from, $to)
    {
        return $query->when(($date_type == 'this_year'), function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
            ->when(($date_type == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($date_type == 'custom_date' && !is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                return $query->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
            });
    }

    /**
     * @param Request $request
     * @return void
     */
    public function expenseSummaryPdf(Request $request): void
    {
        $companyPhone = $this->businessSetting->where('key', 'phone')->first()->value ?? '';
        $companyEmail = $this->businessSetting->where('key', 'email_address')->first()->value ?? '';
        $companyName = $this->businessSetting->where('key', 'restaurant_name')->first()->value ?? '';
        $companyLogo = $this->businessSetting->where('key', 'logo')->first()->value ?? '';

        $search = $request['search'];
        $from = $request['start_date'];
        $to = $request['end_date'];
        $dateType = $request['date_type'] ?? 'this_year';

        $duration = str_replace('_', ' ', $dateType);
        if ($dateType == 'custom_date') {
            $duration = 'From ' . $from . ' To ' . $to;
        }

        $expenseReport = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query) {
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            });

        $expenseCalculate = self::dateWiseCommonFilter($expenseReport, $dateType, $from, $to)->get();

        $totalExpense = 0;
        $extraDiscount = 0;
        $freeDelivery = 0;
        $freeDeliveryOverAmount = 0;
        $couponDiscount = 0;
        if ($expenseCalculate) {
            foreach ($expenseCalculate as $calculate) {
                $extraDiscount += $calculate->extra_discount;
                $freeDeliveryOverAmount += $calculate->free_delivery_amount;
                if (isset($calculate->coupon->coupon_type) && $calculate->coupon->coupon_type == 'free_delivery') {
                    $freeDelivery += $calculate->coupon_discount_amount;
                } else {
                    $couponDiscount += $calculate->coupon_discount_amount;
                }
            }
        }
        $freeDelivery += $freeDeliveryOverAmount;
        $totalExpense = $extraDiscount + $freeDelivery + $couponDiscount;

        $data = [
            'total_expense' => $totalExpense,
            'free_delivery' => $freeDelivery,
            'coupon_discount' => $couponDiscount,
            'extra_discount' => $extraDiscount,
            'company_phone' => $companyPhone,
            'company_name' => $companyName,
            'company_email' => $companyEmail,
            'company_logo' => $companyLogo,
            'duration' => $duration,
        ];

        $mpdfView = View::make('admin-views.report.expense-summary-pdf', compact('data'));
        Helpers::gen_mpdf($mpdfView, 'expense-summary-report-', $dateType);
    }
}
