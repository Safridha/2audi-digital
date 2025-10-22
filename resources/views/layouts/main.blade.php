<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '2 Audi Digital')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { background-color: #f8f8f8; }
        header { background-color: #1e3a8a; color: white; }
        footer { background-color: #1e40af; color: white; padding: 2rem 0; }
        .category img { width: 100%; border-radius: 10px; }
        .service-icon { font-size: 30px; color: #ff7b00; }

        /* WhatsApp Bubble */
        .whatsapp-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 50;
        }
        .whatsapp-bubble:hover { transform: scale(1.1); }
        .whatsapp-bubble i { color: white; font-size: 28px; }
    </style>
</head>
<body class="font-sans">

    @include('layouts.header')

    <main class="mt-4">
        @yield('content')
    </main>

    @include('layouts.footer')

    <!-- WhatsApp Bubble -->
    <a href="https://wa.me/6285290474524" target="_blank" class="whatsapp-bubble">
        <i class="bi bi-whatsapp"></i>
    </a>

</body>
</html>
