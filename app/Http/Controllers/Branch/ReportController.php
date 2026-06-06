<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Traits\Report;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

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
        $dateRange = $request->query('date_range', ALL_TIME);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search  = $request->query('search');

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $queryParam = collect([
            'per_page'   => $perPage,
            'date_range' => $dateRange,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'search'     => $search
        ])->filter(fn($value) => filled($value))->all();

        $orderDetailQuery = $this->orderDetail
            ->with(['product', 'order'])
            ->whereHas('order', fn($q) => $q->where('order_status', 'delivered'))
            ->whereHas('order', fn($sub) => $sub->where('branch_id', auth('branch')->id()))
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
            ->selectRaw('
                        SUM(quantity) as total_quantity,
                        SUM(quantity * (price - discount_on_product)) as total_amount,
                        AVG(quantity * (price - discount_on_product)) as avg_price
                    ')
            ->groupBy('product_id')
            ->orderByDesc('product_id')
            ->paginate($perPage)
            ->appends($queryParam);

        $totalOrders = $orderDetailQuery->pluck('order_id')->unique()->count();
        $totalAmount = $orderDetailQuery->selectRaw('SUM(quantity * (price - discount_on_product)) as total_amount')->value('total_amount');
        $totalQuantity = $orderDetailQuery->sum("quantity");

        return view('branch-views.report.sale-report', compact(
            'dateRange',
            'perPage',
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
        $dateRange = $request->date_range;
        $search     = $request->search ?? null;
        $startDate     = $request->startDate;
        $endDate     = $request->endDate;

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $products = $this->orderDetail
            ->with(['product', 'order'])
            ->whereHas('order', fn($q) => $q->where('order_status', 'delivered'))
            ->whereHas('order', fn($sub) => $sub->where('branch_id', auth('branch')->id()))
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
                'Product Name'   => $productName,
                'Total Quantity' => $row->total_quantity,
                'Total Amount'   => Helpers::set_symbol($row->total_amount),
                'Average Price'  => Helpers::set_symbol($row->avg_price),
            ];
        });
    }

    public function orderReportIndex(Request $request)
    {
        $branchId = auth('branch')->id();
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $dateRange = $request->query('date_range', ALL_TIME);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search  = $request->query('search');

        if (!is_null($dateRange) && $dateRange != CUSTOM_DATE) {
            $date = Helpers::getDateRange($dateRange);
        } elseif (!is_null($dateRange)) {
            $date = Helpers::getDateRange([
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        $queryParam = collect([
            'per_page'   => $perPage,
            'date_range' => $dateRange,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'search'     => $search
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
            ->where('branch_id', $branchId)
            ->when(!is_null($date), fn($q) => $q->whereBetween('created_at', $date))
            ->when($search, function ($q) use ($search) {
                $q->Where('id', (int) $search);
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParam);

        return view(
            'branch-views.report.order-report',
            compact(
                'perPage',
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
        $branchId   = auth('branch')->id();
        $dateRange = $request->date_range;
        $search     = $request->search ?? null;
        $startDate     = $request->startDate;
        $endDate     = $request->endDate;

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

                if (!$product) return 0;

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
}
