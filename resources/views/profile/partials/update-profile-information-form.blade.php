<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Perbarui informasi akun, email notifikasi, dan username Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- INPUT NAMA --}}
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="dark:text-gray-300" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- INPUT USERNAME --}}
        <div>
            <x-input-label for="username" :value="__('Username')" class="dark:text-gray-300" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        {{-- INPUT EMAIL --}}
        <div>
            <x-input-label for="email" :value="__('Email (Untuk Notifikasi Stok)')" class="dark:text-gray-300" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" :value="old('email', $user->email)" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            {{-- Cek Verifikasi Email --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail() && isset($user->email) && $user->email)
                <div class="mt-2 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm text-yellow-800 dark:text-yellow-300">
                        {{ __('Email belum diverifikasi.') }}

                        <button form="send-verification" class="underline font-bold text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klik di sini untuk kirim ulang verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- INPUT ROLE (Read Only) --}}
        <div>
            <x-input-label for="role" :value="__('Role / Jabatan')" class="dark:text-gray-300" />
            <x-text-input id="role" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600 cursor-not-allowed" :value="ucfirst($user->role)" disabled />
            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Hubungi Admin untuk mengubah role.</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 border-0 shadow-lg shadow-indigo-500/30">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>