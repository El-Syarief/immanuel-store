<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // ... (index, create, store TETAP SAMA seperti sebelumnya) ...
    // Biar tidak kepanjangan, saya tulis ulang yang penting saja

    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Tindakan tidak diizinkan.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        $users = $query->latest()->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        if ($request->role === 'admin' && auth()->id() !== 1) {
             abort(403, 'AKSES DITOLAK: Hanya Super Admin yang diizinkan menambah Admin baru.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,cashier'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $new = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Audit record
        Audit::create([
            'actor_id' => auth()->id(),
            'type' => 'user.create',
            'reference_id' => $new->id,
            'payload' => json_encode($new->toArray()),
            'reason' => 'Tambah Pengguna ' . $new->username,
        ]);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();

        // 1. JIKA EDIT DIRI SENDIRI -> LEMPAR KE HALAMAN PROFIL
        if ($user->id === auth()->id()) {
            // Asumsi kamu pakai route profile.edit bawaan Breeze/Jetstream
            return redirect()->route('profile.edit'); 
        }

        // 2. PROTEKSI HIERARKI
        // Jika saya BUKAN Super Admin (ID 1), TAPI saya mencoba mengedit user yang role-nya ADMIN
        if (auth()->id() !== 1 && $user->role === 'admin') {
            abort(403, 'AKSES DITOLAK: Admin biasa tidak diizinkan mengedit sesama Admin.');
        }

        // 3. Proteksi Super Admin (sudah ada sebelumnya, tapi dipertegas)
        if ($user->id === 1 && auth()->id() !== 1) {
             abort(403, 'Hanya Super Admin yang bisa mengedit akun Utama.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();
        
        // CEK DIRI SENDIRI
        if ($user->id === auth()->id()) {
             return redirect()->route('profile.edit')->with('status', 'Silakan update data melalui halaman profil.');
        }

        // CEK HIERARKI (Sama seperti edit)
        if (auth()->id() !== 1 && $user->role === 'admin') {
            abort(403, 'AKSES DITOLAK: Admin biasa tidak diizinkan mengubah data sesama Admin.');
        }

        // PROTEKSI 2: Cek lagi saat update
        if ($user->id === 1 && auth()->id() !== 1) {
            return back()->withErrors(['error' => 'Dilarang mengubah data Super Admin!']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'role' => ['required', 'in:admin,cashier'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // PROTEKSI 3: Jangan biarkan User ID 1 diubah role-nya jadi Cashier
        if ($user->id === 1 && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Super Admin harus tetap menjadi Admin!']);
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $old = $user->only(['name', 'username', 'role']);

        $user->update($data);

        Audit::create([
            'actor_id' => auth()->id(),
            'type' => 'user.update',
            'reference_id' => $user->id,
            'payload' => json_encode(['old' => $old, 'new' => $user->only(['name','username','role'])]),
            'reason' => 'Edit Pengguna ' . $user->username,
        ]);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        // PROTEKSI 4: User ID 1 SAKRAL (Tidak boleh dihapus siapapun)
        if ($user->id === 1) {
            return back()->withErrors(['error' => 'AKSES DITOLAK: Akun Utama (Super Admin) tidak dapat dihapus.']);
        }

        // Proteksi diri sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri!']);
        }

        if ($user->role === 'admin' && auth()->id() !== 1) {
             return back()->withErrors(['error' => 'AKSES DITOLAK: Anda tidak memiliki wewenang menghapus sesama Admin.']);
        }

        $user->delete();

        Audit::create([
            'actor_id' => auth()->id(),
            'type' => 'user.delete',
            'reference_id' => $user->id,
            'payload' => json_encode($user->toArray()),
            'reason' => 'Hapus Pengguna ' . $user->username,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dinonaktifkan.');
    }
}