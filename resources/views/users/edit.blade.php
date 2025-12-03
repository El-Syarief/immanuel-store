<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pengguna: ') }} 
            <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT KHUSUS SUPER ADMIN --}}
            @if($user->id === 1)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-500 p-5 mb-8 rounded-r-xl flex gap-4 items-start shadow-sm">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex-shrink-0 text-yellow-600 dark:text-yellow-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-yellow-800 dark:text-yellow-400 uppercase tracking-wider mb-1 text-xs">Akun Super Admin</p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 leading-relaxed">Role akun ini dikunci dan tidak dapat diubah demi keamanan sistem.</p>
                    </div>
                </div>
            @endif

            {{-- CARD CONTAINER --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Data Akun</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui informasi profil dan akses pengguna.</p>
                    </div>

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            {{-- Nama Lengkap --}}
                            <div>
                                <x-input-label for="name" :value="__('Nama Lengkap')" class="dark:text-gray-300" />
                                <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="text" name="name" :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Username --}}
                            <div>
                                <x-input-label for="username" :value="__('Username')" class="dark:text-gray-300" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <x-text-input id="username" class="block w-full pl-10 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="text" name="username" :value="old('username', $user->username)" required />
                                </div>
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            {{-- Role --}}
                            <div>
                                <x-input-label for="role" :value="__('Peran (Role)')" class="dark:text-gray-300" />
                                
                                @if($user->id === 1)
                                    <input type="text" value="Super Admin (Terkunci)" class="block mt-1 w-full border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md shadow-sm cursor-not-allowed italic" disabled>
                                    <input type="hidden" name="role" value="admin"> 
                                @else
                                    <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Cashier (Kasir)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                                    </select>
                                @endif
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                        </div>

                        {{-- DIVIDER PASSWORD --}}
                        <div class="relative py-6">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white dark:bg-gray-800 px-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Ganti Password (Opsional)</span>
                            </div>
                        </div>

                        <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700/50 space-y-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic mb-2">* Kosongkan jika tidak ingin mengganti password.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru')" class="dark:text-gray-300" />
                                    <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100" type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Ulangi Password')" class="dark:text-gray-300" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100" type="password" name="password_confirmation" placeholder="Konfirmasi password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('users.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4 font-bold text-sm transition">
                                Batal
                            </a>
                            <x-primary-button class="ml-4 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-500/30 border-0">
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>