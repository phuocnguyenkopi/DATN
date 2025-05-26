<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $orders = Order::with('user', 'payment', 'orders_detail.productVariant.product')
            ->where('id_user', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'orders' => $orders
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::with('user', 'payment', 'orders_detail.productVariant.product')->where('id_user', Auth::id())->where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }
        return response()->json([
            'success' => true,
            'order' => $order
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cancelOrder(Request $request, string $id)
    {
        //
        $order = Order::where('id_user', Auth::id())->where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }
        if ($order->status === 0) {
            return response()->json(['message' => 'Đơn hàng đã hủy trước đó'], 401);
        }
        if ($order->status === 1) {
            $order->status = 0;
            $order->save();
            return response()->json(['message' => 'Hủy đơn hàng thành công'], 200);
        }

        if ($order->status === 2) {
            return response()->json(['message' => 'Liên hệ cửa hàng để hủy'], 401);
        }



        return response()->json(['message' => 'Trạng thái đơn hàng không hợp lệ'], 400);
    }
}
