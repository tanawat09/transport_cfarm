<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'เข้าสู่ระบบ' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #16324b 0%, #214c6f 45%, #f0b64f 100%);
            display: flex;
            align-items: center;
        }
        .login-card { border: 0; border-radius: 1.25rem; box-shadow: 0 20px 50px rgba(0,0,0,.18); }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
