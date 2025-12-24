<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        // menampilkan data user dengan urutan terbaru, dibatasi 10 data per halaman menggunakan pagination.
        $users = $query->orderByDesc('id')
                       ->paginate(10)
                       ->withQueryString();

        if ($request->ajax()) {
            return view('admin.users._table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role'     => ['required', Rule::in(['admin','user'])],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success','Pengguna dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => [
                'required','email','max:255',
                Rule::unique('users','email')->ignore($user->id)
            ],
            'password' => ['nullable','string','min:8','confirmed'],
            'role'     => ['required', Rule::in(['admin','user'])],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success','Pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success','Pengguna dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('user_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pengguna yang dipilih.');
        }

        // pastikan array integer
        $ids = array_map('intval', $ids);

        $authId = $request->user()->id;

        $ids = array_filter($ids, fn ($id) => $id !== $authId);

        if (empty($ids)) {
            return back()->with('error', 'Tidak boleh menghapus akun yang sedang login.');
        }

        User::whereIn('id', $ids)->delete();

        return back()->with('success', 'Pengguna terpilih berhasil dihapus.');
    }
}
