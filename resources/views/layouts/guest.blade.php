<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Barangay Information System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Figtree', Arial, Helvetica, sans-serif;
            color: #0f172a;
            background:
                linear-gradient(rgba(15, 23, 42, 0.74), rgba(30, 58, 138, 0.78)),
                linear-gradient(135deg, #0f172a, #1e3a8a, #2563eb);
        }

        .guest-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .guest-board {
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.4s ease;
        }

        .guest-brand {
            text-align: center;
            margin-bottom: 18px;
        }

        .guest-logo {
            width: 76px;
            height: 76px;
            margin: 0 auto 12px;
            background: #ffffff;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
        }

        .guest-logo svg {
            width: 48px !important;
            height: 48px !important;
            color: #1e3a8a;
            fill: currentColor;
            display: block;
        }

        .guest-system-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.3px;
        }

        .guest-system-subtitle {
            margin: 6px 0 0;
            font-size: 14px;
            color: #dbeafe;
        }

        .guest-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 34px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.32);
            border: 1px solid rgba(255, 255, 255, 0.55);
            transition: 0.25s ease;
        }

        .guest-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 38px 90px rgba(0, 0, 0, 0.38);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .guest-page {
                padding: 18px;
            }

            .guest-card {
                padding: 26px 22px;
                border-radius: 20px;
            }

            .guest-system-title {
                font-size: 21px;
            }

            .guest-logo {
                width: 66px;
                height: 66px;
                border-radius: 18px;
            }

            .guest-logo svg {
                width: 42px !important;
                height: 42px !important;
            }
        }
    </style>
</head>
<body>
    <main class="guest-page">
        <div class="guest-board">
            <div class="guest-brand">
                <a href="/" class="guest-logo">
                    <x-application-logo />
                </a>

                <h1 class="guest-system-title">Barangay Information System</h1>
                <p class="guest-system-subtitle">Resident services and barangay records portal</p>
            </div>

            <div class="guest-card">
                {{ $slot }}
            </div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>