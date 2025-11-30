<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Immanuel Store') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>

                @if(auth()->user()->role === 'admin' && empty(auth()->user()->email))
                    <div class="bg-red-600 text-white px-4 py-3 shadow-md relative z-50">
                        <div class="max-w-7xl mx-auto flex justify-between items-center sm:px-6 lg:px-8">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="font-bold">PERHATIAN:</span>
                                <span class="text-sm">Anda belum mengatur Email Pengingat Stok. Notifikasi stok habis tidak dapat dikirim.</span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="bg-white text-red-600 text-sm font-bold px-3 py-1.5 rounded hover:bg-red-50 transition">
                                Atur Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        {{-- Gunakan asset() untuk memanggil file dari folder public --}}
        <audio id="stockAlertSound" src="{{ asset('sounds/Alert_Stock.mp3') }}" preload="auto"></audio>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('play_alert_sound'))
                    const audio = document.getElementById("stockAlertSound");
                    if(audio) {
                        // Promise catch untuk menangani jika browser memblokir autoplay
                        var playPromise = audio.play();

                        if (playPromise !== undefined) {
                            playPromise.then(_ => {
                                // Audio berhasil diputar
                                console.log("Audio played successfully");
                            })
                            .catch(error => {
                                // Jika diblokir browser, kita paksa tampilkan alert visual tambahan
                                console.log("Audio play blocked by browser: ", error);
                                alert("PERINGATAN KRITIS: Stok Barang Habis! (Audio diblokir browser)");
                            });
                        }
                    }
                @endif
            });
        </script>
    </body>
</html>
