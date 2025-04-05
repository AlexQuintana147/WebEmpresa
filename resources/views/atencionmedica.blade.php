<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session('success'))
    <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if(session('error'))
    <meta name="error-message" content="{{ session('error') }}">
    @endif
    <title>Atención Médica - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex" x-data="{ modalOpen: false }">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
           
           
        </div>
    </div>
</body>
</html>
