<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;

class CheckOutApiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function paymethor()
  {
    //
    $payment = Payment::where('status', '>', 0)->get();


    return response()->json([
      'status' => 'success',
      'data' => $payment
    ]);
  }

  public function getOrderById(Request $request)
  {
    $order = Order::where('id', $request->id)->with('payment')->where('status', '>', 0)->first();

    if (!$order) {
      return response()->json([
        'status' => 'error',
        'message' => 'Đơn hàng không tồn tại.'
      ], 404);
    }

    return response()->json([
      'status' => 'success',
      'data' => $order
    ]);
  }




  //put order 
  public function order(Request $request)
  {
    //
    $order = Order::create([
      'id_user' => $request->input('id_user'),
      'id_payment' => $request->input('id_payment'),
      'note' => $request->input('note'),
      'phone' => $request->input('phone'),
      'address' => $request->input('address'),
      'total_price' => $request->input('total_price')
    ]);
    $orderDetails = $request->input('order_details');
    if (is_array($orderDetails)) {
      foreach ($orderDetails as $detail) {
        OrderDetail::create([
          'id_order' => $order->id, // Lấy ID của đơn hàng
          'id_variant' => $detail['id_variant'],
          'price' => $detail['price'],
          'quantity' => $detail['quantity'],
        ]);
      }
    }

    $inFoOrder = Order::with('orders_detail.productVariant.product')->find($order->id);

    Mail::to($request->input('email'))->send(new OrderSuccessMail($inFoOrder,  $request->input('name')));


    return response()->json([
      'status' => 'success',
      "order" => $order->id,
      'message' => 'Đặt hàng thành công'
    ]);
  }

  // 
  public function webhook(Request $request)
  {

    if ($request->transferType !== "in") {
      return response()->json(['success' => false, 'message' =>  'not order ']);
    }

    $transaction_content = $request->content;
    $regex = '/MDH(\d+)/';
    preg_match($regex, $transaction_content, $matches);
    $pay_order_id = $matches[1];

    if (!is_numeric($pay_order_id)) {
      return response()->json(['success' => false, 'message' =>  'Order not found. Order_id ']);
    }

    $order = Order::where("id", $pay_order_id)
      ->where('thanh_toan', 0)
      ->where('total_price', $request->transferAmount)->first();


    if (!$order) {
      return response()->json(['success' => false, 'message' => 'Order not found. Order_id ' . $pay_order_id]);
    }
    $order->thanh_toan = 1;
    $order->save();
    return response()->json(['success' => true, 'data' =>  $order, 'message' => 'Order not found. Order_id '], 200);
  }

  // 
  public function KTThanhToan(Request $request)
  {


    // Tìm đơn hàng Điều kiện là id đơn hàng, số tiền, trạng thái đơn hàng phải là 'Unpaid'
    $order = Order::where("id", $request->id)
      ->where('thanh_toan', 1)
      ->first();

    if (!$order) {
      return response()->json(['success' => false, 'message' =>  'Chưa thanh toán'], 201);
    } else {
      return response()->json(['success' => true, 'message' =>  'thanh toán thành cong'], 200);
    }
  }
}
    // {
    //   gateway: "MBBank",
    //   transactionDate: "2025-03-22 19:50:00",
    //   accountNumber: "0827505405",
    //   subAccount: null,
    //   code: null,
    //   content:
    //     "MDH10 Ma giao dich Trace507428 Trace 507428 NG CHUYEN:CUSTOMER 0827505405",
    //   transferType: "in", // Loại giao dịch. in là tiền vào,
    //   description:
    //     "BankAPINotify MDH10 Ma giao dich Trace507428 Trace 507428 NG CHUYEN:CUSTOMER 0827505405",
    //   transferAmount: 7000, // Số tiền giao dịch
    //   referenceCode: "FT25081150695859",
    //   accumulated: 0,
    //   id: 10137216,
    // },
