<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Immanuel Store - Internal System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
        
        <div class="w-full max-w-4xl bg-white shadow-2xl rounded-3xl overflow-hidden flex flex-col md:flex-row">
            
            <div class="md:w-1/2 bg-indigo-900 p-12 text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-20">
                    <svg class="absolute top-0 left-0 w-64 h-64 -mt-12 -ml-12 text-indigo-500" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                    <svg class="absolute bottom-0 right-0 w-64 h-64 -mb-12 -mr-12 text-indigo-700" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-white bg-opacity-20 backdrop-blur-lg rounded-xl flex items-center justify-center text-3xl font-bold mb-4">
                            IS
                        </div>
                        <h2 class="text-3xl font-bold leading-tight">IMMANUEL STORE</h2>
                        <p class="text-indigo-200 text-sm tracking-widest uppercase mt-1">Internal Management System</p>
                    </div>
                    
                    <div class="space-y-4 mt-8 border-t border-indigo-800 pt-8">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-indigo-800 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold">Sistem Terpusat</h4>
                                <p class="text-xs text-indigo-300">Manajemen stok dan transaksi satu pintu.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-indigo-800 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold">Akses Terbatas</h4>
                                <p class="text-xs text-indigo-300">Hanya untuk staf dan admin berwenang.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 text-xs text-indigo-400 text-center">
                    &copy; {{ date('Y') }} Immanuel Store. All rights reserved.
                </div>
            </div>

            <div class="md:w-1/2 p-12 flex flex-col justify-center bg-white">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Selamat Datang</h3>
                    <p class="text-gray-500 text-sm">Silakan login untuk mengakses sistem.</p>
                </div>

                @if (Route::has('login'))
                    <div class="space-y-4">
                        @auth
                            <div class="text-center">
                                <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-200 text-sm">
                                    Anda sudah login sebagai <strong>{{ Auth::user()->name }}</strong>
                                </div>
                                <a href="{{ url('/dashboard') }}" class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow-md">
                                    Ke Dashboard &rarr;
                                </a>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="group relative flex items-center justify-center w-full py-4 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition transform hover:-translate-y-0.5">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition ease-in-out duration-150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                      <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                Login Akun
                            </a>

                            <div class="text-center mt-6">
                                <p class="text-xs text-gray-400">
                                    Lupa password atau belum punya akun? <br>
                                    Hubungi <span class="text-gray-600 font-bold">Super Admin</span>.
                                </p>
                            </div>
                        @endauth
                    </div>
                @endif
            </div>

        </div>
    </div>

</body>
</html>