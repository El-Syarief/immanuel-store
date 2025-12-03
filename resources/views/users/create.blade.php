<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Formulir Pendaftaran</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Buat akun baru untuk Admin atau Kasir.</p>
                    </div>
                
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        {{-- SEKSI 1: DATA DIRI --}}
                        <div class="space-y-6 mb-8">
                            
                            {{-- Nama Lengkap --}}
                            <div>
                                <x-input-label for="name" :value="__('Nama Lengkap')" class="dark:text-gray-300" />
                                <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 shadow-sm" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Budi Santoso" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Username --}}
                            <div>
                                <x-input-label for="username" :value="__('Username (Login)')" class="dark:text-gray-300" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <x-text-input id="username" class="block w-full pl-10 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 shadow-sm" type="text" name="username" :value="old('username')" required placeholder="Tanpa spasi, cth: budi123" />
                                </div>
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            {{-- Role --}}
                            <div>
                                <x-input-label for="role" :value="__('Peran (Role)')" class="dark:text-gray-300" />
                                <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier (Kasir)</option>
                                    
                                    @if(auth()->id() === 1)
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                
                                @if(auth()->id() !== 1)
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Hanya Super Admin yang dapat menambahkan Admin baru.
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- SEKSI 2: KEAMANAN (Password) --}}
                        <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700/50 space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Keamanan Akun</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="password" :value="__('Password')" class="dark:text-gray-300" />
                                    <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Ulangi Password')" class="dark:text-gray-300" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-indigo-500" type="password" name="password_confirmation" required placeholder="Konfirmasi password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('users.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4 font-bold text-sm transition">Batal</a>
                            <x-primary-button class="ml-4 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-500/30 border-0">
                                {{ __('Simpan User') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>