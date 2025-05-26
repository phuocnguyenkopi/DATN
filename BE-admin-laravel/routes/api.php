<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckOutApiController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerSermentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\YeuThichController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;



use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserReviewController;

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/history-orders', [OrderController::class, 'index']); // Lấy danh sách đơn hàng
  Route::get('/history-orders/{id}', [OrderController::class, 'show']); // Lấy chi tiết đơn hàng
  Route::get('/cancel-order/{id}', [OrderController::class, 'cancelOrder']); // Lấy chi tiết đơn hàng
  Route::post('/post-review-web', [UserReviewController::class, 'postReviewWeb']); // Lấy chi tiết đơn hàng
});
Route::get('/get-review-web', [UserReviewController::class, 'getReviewWeb']); // Lấy chi tiết đơn hàng


Route::get('/payment', [CheckOutApiController::class, 'paymethor']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/change-password', [AuthApiController::class, 'changePassword']);

Route::get('/comment', [CommentController::class, 'getComment']);
Route::get('/productscategory', [ProductController::class, 'getCategory']);
Route::get('/product-relate', [ProductController::class, 'productRelate']);
Route::get('/customersegment', [CustomerSermentController::class, 'getAllcustomer']);
Route::get('/flashSale', [ProductController::class, 'flashSale']);
Route::post('/cartproduct', [ProductController::class, 'cartProduct']);
Route::get('/mainCategoryWithSub', [CategoryController::class, 'mainCategoryWithSub']);
Route::apiResource('/product', ProductController::class);
Route::apiResource('/category', CategoryController::class);
Route::apiResource('/banner', BannerController::class);
Route::apiResource('/brand', BrandController::class);
Route::apiResource('/post', PostController::class);
Route::get('/hot-views-post', [PostController::class, 'HotAndViewsPost']);
Route::get('/main-post', [PostController::class, 'mainPostCategory']);


Route::get('/search', [ProductController::class, 'search']);



Route::middleware('auth:sanctum')->group(function () {
  Route::post('/pulish-comment', [CommentController::class, 'pulishComment']);
  Route::post('/order', [CheckOutApiController::class, 'order']);
  Route::post('/updateUser', [AuthApiController::class, 'updateUser']);
  Route::get('/getUserActive', [AuthApiController::class, 'getUserActive']);
  Route::get('/check-out-online', [CheckOutApiController::class, 'getOrderById']);
  Route::post('/kt-thanh-toan', [CheckOutApiController::class, 'KTThanhToan']);
});

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/favorite/list', [YeuThichController::class, 'postYeuThich']);
  Route::post('/favorite/add', [YeuThichController::class, 'addFavorite']);
  Route::delete('/favorite/remove', [YeuThichController::class, 'removeFavorite']);
});

Route::middleware('auth:sanctum')->get('/check-token', function (Request $request) {
  return response()->json([
    'valid' => true,
    'user' => $request->user(),
  ]);
});

// Route::get('/user', [AuthApiController::class, 'index']);
// routes/api.php
Route::post('/forgot-password', [AuthApiController::class, 'sendResetLink']);

// routes/api.php
Route::post('/contact', [ContactController::class, 'sendContact']);


Route::post('/sepay-webhook', [CheckOutApiController::class, 'webhook']);

Route::get('/email/verify', function () {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');



Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
  // Tìm user theo ID
  $user = User::find($id);

  if (!$user) {
    return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
  }

  // Kiểm tra email đã xác thực chưa
  if ($user->hasVerifiedEmail()) {
    $title = 'Email đã được xác nhận trước đó.';
    $frontendUrl = env('FRONTEND_URL');
    return view('email.verifyEmail', compact('title', 'frontendUrl'));
  }

  // Xác thực email
  $user->markEmailAsVerified();
  $title = 'Email đã được xác nhận thành công..';
  $frontendUrl = env('FRONTEND_URL');
  return view('email.verifyEmail', compact('title', 'frontendUrl'));
})->middleware(['signed'])->name('verification.verify');


// Route::post('/email/verification-notification', function (Request $request) {
//   $request->user()->sendEmailVerificationNotification();


//   return back()->with('message', 'Verification link sent!');
// })->middleware(['throttle:6,1'])->name('verification.send');
