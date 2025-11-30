<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Pengguna') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm transition">
                + Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1">Cari User</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau Username..." class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1">Role</label>
                        <select name="role" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier (Kasir)</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Cari</button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bergabung</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role == 'admin')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">ADMIN</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">KASIR</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            @php
                                                $isSuperAdmin = auth()->id() === 1;
                                                $targetIsAdmin = $user->role === 'admin';
                                                $isSelf = $user->id === auth()->id();
                                                
                                                // Logika Gabungan: 
                                                // Boleh akses (Edit/Hapus) jika: SAYA Super Admin ATAU Target BUKAN Admin
                                                $canManage = $isSuperAdmin || !$targetIsAdmin;
                                            @endphp

                                            {{-- TOMBOL EDIT --}}
                                            @if(!$isSelf && $canManage)
                                                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 p-2 rounded hover:bg-yellow-100" title="Edit">✏️</a>
                                            @endif

                                            {{-- TOMBOL HAPUS --}}
                                            @if(!$isSelf && $canManage) 
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin nonaktifkan user ini?');">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded hover:bg-red-100" title="Hapus">🗑️</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>