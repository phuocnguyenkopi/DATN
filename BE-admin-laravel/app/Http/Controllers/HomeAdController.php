<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Order;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\ProductVariant;
use App\Models\UserReview;
use DateTime;
use PHPUnit\Framework\Constraint\Count;

class HomeAdController extends Controller
{
    public function home(Request $request)
    {
        $now = Carbon::now();
        $stawe = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endwe = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $stLaWe = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $endLaWe = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
        $staMo = Carbon::now()->startOfMonth();
        $endMo = Carbon::now()->endOfMonth();
        $staLaMo = Carbon::now()->subMonth()->startOfMonth();
        $endLaMo = Carbon::now()->subMonth()->endOfMonth();
        $queOr = Order::query();
        $queUs = User::query();
        $queRe = UserReview::query();
        $date = $request->date_fillter;
        switch ($date) {
            case 'homqua':
                $queOr->whereDate('order_date', Carbon::yesterday());
                $queUs->whereDate('created_at', Carbon::yesterday());
                $queRe->whereDate('created_at', Carbon::yesterday());
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereDate('updated_at', Carbon::yesterday())
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereDate('order_date', Carbon::yesterday())
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
            case 'tuannay':
                $queOr->whereBetween('order_date', [$stawe, $endwe]);
                $queUs->whereBetween('created_at', [$stawe, $endwe]);
                $queRe->whereBetween('created_at', [$stawe, $endwe]);
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereBetween('updated_at', [$stawe, $endwe])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereBetween('order_date', [$stawe, $endwe])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
            case 'tuantruoc':
                $queOr->whereBetween('order_date', [$stLaWe, $endLaWe]);
                $queUs->whereBetween('created_at', [$stLaWe, $endLaWe]);
                $queRe->whereBetween('created_at', [$stLaWe, $endLaWe]);
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereBetween('updated_at', [$stLaWe, $endLaWe])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereBetween('order_date', [$stLaWe, $endLaWe])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
            case 'thangnay':
                $queOr->whereBetween('order_date', [$staMo, $endMo]);
                $queUs->whereBetween('created_at', [$staMo, $endMo]);
                $queRe->whereBetween('created_at', [$staMo, $endMo]);
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereBetween('updated_at', [$staMo, $endMo])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereBetween('order_date', [$staMo, $endMo])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
            case 'thangtruoc':
                $queOr->whereBetween('order_date', [$staLaMo, $endLaMo]);
                $queUs->whereBetween('created_at', [$staLaMo, $endLaMo]);
                $queRe->whereBetween('created_at', [$staLaMo, $endLaMo]);
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereBetween('updated_at', [$staLaMo, $endLaMo])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereBetween('order_date', [$staLaMo, $endLaMo])
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
            default:
                $queOr->whereDate('order_date', Carbon::today());
                $queUs->whereDate('created_at', Carbon::today());
                $queRe->whereDate('created_at', Carbon::today());
                $ordersUpdated = Order::whereNotNull('updated_at')
                    ->whereDate('updated_at', Carbon::today())
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                $ordersNotUpdated = Order::whereNull('updated_at')
                    ->whereDate('order_date', Carbon::today())
                    ->where('status', 3)
                    ->where('thanh_toan', 1)
                    ->get();
                break;
        }
        $dhm = $queOr->where('status', 3)->where('thanh_toan', 1)->count();
        $ust = $queUs->where('roles', 1)->count();
        $usRe = $queRe->count();
        $total_price = $ordersUpdated->sum('total_price') + $ordersNotUpdated->sum('total_price');
        $sptop = Product::with([
            'productVariants'
        ])->withSum(['orderDetails as total_buy' => function ($orderQuery) {
            $orderQuery->whereHas('order', function ($orderQuery) {
                $orderQuery->where('status', 3);
            });
        }], 'quantity')->having('total_buy', '>', 0)->orderBy('total_buy', 'DESC')->limit(5)->get();
        $month_canvas = Order::selectRaw('MONTH(CAST(order_date AS DATE)) as month, SUM(total_price) as total')
            ->whereYear('order_date', Carbon::now()->year)
            ->where('status', 3)
            ->where('thanh_toan', 1)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $label = [];
        $data_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $label[] = "Tháng " . $i;
            $data_month[] = $month_canvas->where('month', $i)->first()->total ?? 0;
        }
        $threeday = Carbon::now()->subDays(3);
        $sotrang = $request->input('show', 5);
        $sotrang_usernew = $request->input('show_user', 5);
        //sản phẩm hết hàng
        $use = ProductVariant::with('product')->where('stock', '<=', 5)->paginate($sotrang, ['*'], 'page_stock');
        $pageht = $use->currentPage();
        $lapa = $use->lastPage();
        $start = max($pageht - 1, 1);
        $end = min($pageht + 1, $lapa);
        // người dùng mới
        $usernew = User::where('roles', 1)->whereBetween('created_at', [$threeday, $now])->orderBy('created_at', 'DESC')->paginate($sotrang_usernew, ['*'], 'page_user');
        $pageht_user = $usernew->currentPage();
        $lapa_user = $usernew->lastPage();
        $start_user = max($pageht_user - 1, 1);
        $end_user = min($pageht_user + 1, $lapa_user);


        $sphh = ProductVariant::with('product')->where('stock', '<=', 5)->orderBy('stock', 'asc')->paginate($sotrang);
        $now = Carbon::now();
        $us = User::where('roles', 1)->whereBetween('created_at', [$threeday, $now])->orderBy('created_at', 'DESC')->paginate($sotrang_usernew);
        $newOrd = Order::whereIn('status', [1])->count();
        $newCom = Comment::whereDate('created_at', Carbon::today())->count();
        $newUV = UserReview::whereDate('created_at', Carbon::today())->count();
        return view('index', compact(
            'sptop',
            'ust',
            'us',
            'usRe',
            'newOrd',
            'newCom',
            'newUV',
            'dhm',
            'sphh',
            'use',
            'start',
            'end',
            'pageht',
            'lapa',
            'label',
            'data_month',
            'total_price',
            'usernew',
            'pageht_user',
            'lapa_user',
            'start_user',
            'end_user'

        ));
    }
}
