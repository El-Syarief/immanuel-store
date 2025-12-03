<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Bagian 1: Update Info Profil --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Bagian 2: Update Password --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Bagian 3: Delete Account (Jika diaktifkan nanti) --}}
            {{-- 
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div> 
            --}}
        </div>
    </div>
</x-app-layout>