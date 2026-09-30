<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') — @yield('title') | Riches Corsos</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7f3ed;
            color: #1f2937;
        }
        main {
            width: min(92%, 680px);
            padding: 48px 32px;
            text-align: center;
            background: #fff;
            border: 1px solid #e5ded3;
            border-radius: 18px;
            box-shadow: 0 16px 45px rgba(31, 41, 55, .08);
        }
        .brand {
            margin-bottom: 28px;
            color: #7a4d24;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .code { margin: 0; color: #9a6737; font-size: clamp(64px, 14vw, 112px); line-height: .9; }
        h1 { margin: 22px 0 12px; font-size: clamp(26px, 5vw, 38px); }
        p { max-width: 520px; margin: 0 auto; color: #5f6772; font-size: 17px; line-height: 1.65; }
        a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 22px;
            border-radius: 999px;
            background: #7a4d24;
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }
        a:focus-visible { outline: 3px solid #d5a66f; outline-offset: 3px; }
    </style>
</head>
<body>
    <main>
        <div class="brand">Riches Corsos</div>
        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ route('home') }}">Return to homepage</a>
    </main>
</body>
</html>