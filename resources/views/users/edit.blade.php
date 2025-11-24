<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengguna: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($user->id === 1)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-yellow-700">
                    <p class="font-bold">Akun Super Admin</p>
                    <p>Role akun ini dikunci dan tidak dapat diubah demi keamanan sistem.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" class="block mt-1 w-full bg-gray-100" type="text" name="username" :value="old('username', $user->username)" required />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Peran (Role)')" />
                            
                            @if($user->id === 1)
                                <input type="text" value="Super Admin (Terkunci)" class="block mt-1 w-full border-gray-300 bg-gray-200 text-gray-500 rounded-md shadow-sm cursor-not-allowed" disabled>
                                <input type="hidden" name="role" value="admin"> @else
                                <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Cashier (Kasir)</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                                </select>
                            @endif
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <hr class="my-6 border-gray-200">
                        <p class="text-sm text-gray-500 mb-4 font-italic">* Kosongkan password jika tidak ingin menggantinya.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="password" :value="__('Password Baru (Opsional)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Ulangi Password Baru')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Perbarui User') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>