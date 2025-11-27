<section class="space-y-6">
    
    @if(auth()->id() === 1)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p class="font-bold text-yellow-700">Akun Super Admin</p>
            <p class="text-sm text-yellow-600">Akun utama tidak dapat dihapus demi keamanan sistem.</p>
        </div>
    @else
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Hapus Akun') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Setelah akun dihapus, Anda tidak akan bisa login kembali. Harap unduh data penting sebelum melanjutkan.') }}
            </p>
        </header>

        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Hapus Akun') }}</x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Apakah Anda yakin ingin menghapus akun ini?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan password Anda untuk mengonfirmasi penghapusan akun secara permanen.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Hapus Akun') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endif
</section>