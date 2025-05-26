<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;



class AuthApiController extends Controller
{
  public function index()
  {
    return response()->json([
      'access_token' => User::where('roles', '=', 1)->get(),
      'token_type' => 'Bearer',
    ]);
  }


  public function sendResetLink(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? response()->json(['message' => 'Kiểm tra email xác thực'])
      : response()->json(['message' => 'Email không tồn tại!'], 404);
  }


  public function getUserActive(Request $request)
  {
    $user =  User::where("id", $request->id)->get();
    return response()->json([
      'data' => $user,

    ]);
  }


  public function register(Request $request)
  {

    // Validation
    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email|max:255|unique:users',
      'last_name' => 'required|string|max:255',
      'first_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      $errors = '';

      if ($validator->errors()->has('last_name')) {
        $errors = 'Vui lòng nhập họ!';
        return response()->json([
          'errors' => $errors,
        ], 422);
      }

      if ($validator->errors()->has('first_name')) {
        $errors = 'Vui lòng nhập tên!';
        return response()->json([
          'errors' => $errors,
        ], 422);
      }

      if ($validator->errors()->has('phone')) {
        $errors = 'Số điện thoại không hợp lệ!';
        return response()->json([
          'errors' => $errors,
        ], 422);
      }

      if ($validator->errors()->has('email')) {
        $errors = 'Email không hợp lệ!';
        return response()->json([
          'errors' => $errors,
        ], 422);
      }

      if ($validator->errors()->has('password')) {
        $errors = 'Mật khẩu phải có ít nhất 8 ký tự và một chữ viết hoa!';
        return response()->json([
          'errors' => $errors,
        ], 422);
      }

      // Kiểm tra nếu email đã tồn tại nhưng chưa xác nhận
      $existingUser = User::where('email', $request->email)->first();

      if ($existingUser) {
        if (!$existingUser->hasVerifiedEmail()) {
          // Gửi lại email xác nhận
          $existingUser->sendEmailVerificationNotification();
          return response()->json([
            'message' => 'Hệ thống đã gửi lại email xác nhận!',
          ], 200);
        } else {
          $errors = 'Email đã tồn tại hoặc không hợp lệ!';
          return response()->json([
            'errors' => $errors,
          ], 422);
        }
      }

      return response()->json([
        'errors' => 'Vui lòng nhập đầy đủ thông tin!',
      ], 422);
    }

    // Tạo user mới
    $user = User::create([
      'email' => $request->email,
      'last_name' => $request->last_name,
      'first_name' => $request->first_name,
      'phone' => $request->phone,
      'password' => Hash::make($request->password),
    ]);

    // Tạo token cho user
    // $token = $user->createToken('auth_token')->plainTextToken;
    event(new Registered($user));
    return response()->json(['message' => 'Vui lòng kiểm tra email để xác nhận tài khoản.'], 201);
    // return response()->json([
    //   'message' => 'Đăng ký thành công!',
    //   'user' => $user,
    //   'access_token' => $token,
    //   'token_type' => 'Bearer',
    // ], 201);
  }



  public function login(Request $request)
  {
    $validated = $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    $lockUser = User::where('email', $validated['email'])->where('account_lock', 0)->first();

    if ($lockUser) {
      return response()->json(['message' => 'Tài khoản của bạn đã bị khóa!'], 401);
    }

    $user = User::where('email', $validated['email'])->where('account_lock', 1)->first();

    if (!$user || !Auth::attempt($validated)) {
      return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
    }

    if (!$user->hasVerifiedEmail()) {
      return response()->json([
        'message' => 'Vui lòng xác thực email!',
      ], 401);
    }


    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Đăng nhập thành công!',
      'access_token' => $token,
      'user' => auth()->user(), // Lấy thông tin người dùng hiện tại
      'token_type' => 'Bearer',
    ]);
  }

  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return response()->json([
      'message' => 'Logged out successfully',
    ]);
  }

  public function updateUser(Request $request)
  {

    try {
      // Kiểm tra nếu có file ảnh thì xử lý
      if ($request->hasFile('img')) {
        $request->validate([
          'img' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $request->file('img');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('/img/user'), $imageName);

        $imagePath = '/img/user/' . $imageName;
      } else {
        $imagePath = null; // Không có ảnh mới
      }

      // Cập nhật thông tin user
      $user = auth()->user();
      // Kiểm tra nếu input không rỗng thì mới cập nhật
      if ($request->filled('phone')) {
        $user->phone = $request->input('phone');
      }

      if ($request->filled('dob')) {
        $request->validate([]);

        // Validation
        $validator = Validator::make($request->all(), [
          'dob' => 'date|before_or_equal:today',
        ]);

        if ($validator->errors()->has('dob')) {
          $errors = 'Vui lòng nhập đúng ngày sinh!';
          return response()->json([
            'errors' => $errors,
          ], 422);
        }


        $user->dob = $request->input('dob');
      }

      if ($request->filled('gender')) {
        $user->gender = $request->input('gender');
      }

      if ($request->filled('address')) {
        $user->address = $request->input('address');
      }


      if ($imagePath) {
        $user->avatar = $imagePath; // Nếu có ảnh mới, cập nhật avatar
      }

      $user->save();

      return response()->json([
        'message' => 'Cập nhật thành công!',
        'user' => $user,
        'request' =>  $request
      ]);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function changePassword(Request $request)
  {
    // Lấy user hiện tại
    $user = Auth::user();

    // Xác thực dữ liệu đầu vào
    $request->validate([
      'current_password' => 'required',
      'new_password' => 'required|string|min:8|confirmed',

    ]);

    // Kiểm tra mật khẩu hiện tại có đúng không
    if (!Hash::check($request->current_password, $user->password)) {
      throw ValidationException::withMessages([
        'current_password' => ['Mật khẩu hiện tại không đúng.'],
      ]);
    }

    // Cập nhật mật khẩu mới
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
      'status' => 'success',
      'message' => 'Mật khẩu đã được thay đổi thành công!',
    ]);
  }
}
