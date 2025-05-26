<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('/img/metaIcon.png') }}" type="image/x-icon" />
    <link href="{{ asset('/css/login.css') }}" rel="stylesheet" />
    <link rel="icon" href="{{ asset('/img/metaIcon.png') }}" type="image/x-icon" />
    <title>Login</title>
</head>

<body>
    <main>
        <section class="mri">
            <form method="POST" action="{{route('login')}}" role="form">
                <h1>Đăng nhập</h1>
                @csrf
                <div class="un">
                    <label for="">Email:</label><br>
                    <input type="text" placeholder="Nhập Email" name="email">
                </div>
                @if ($errors->has('email'))
                <span class="error-message">{{
                    $errors->first('email')}}</span>
                @endif
                <div class="pn">
                    <label for="">Mật khẩu:</label><br>
                    <input type="password" placeholder="Nhập mật khẩu" name="password">
                </div>
                @if ($errors->has('password'))
                <span class="error-message">{{
                    $errors->first('password')}}</span>
                @endif
                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>