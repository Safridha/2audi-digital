@extends('layouts.admin')
@section('title','Tambah Pengguna')

@section('content')
  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Tambah Pengguna</h2>

    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf

      <div class="mb-4">
        <label class="block mb-1 text-sm">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="mb-4">
        <label class="block mb-1 text-sm">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="mb-4">
        <label class="block mb-1 text-sm">Password</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="mb-4">
        <label class="block mb-1 text-sm">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
      </div>

      <div class="mb-6">
        <label class="block mb-1 text-sm">Role</label>
        <select name="role" class="w-full border rounded px-3 py-2" required>
          <option value="user"  {{ old('role')==='user'?'selected':'' }}>User</option>
          <option value="admin" {{ old('role')==='admin'?'selected':'' }}>Admin</option>
        </select>
        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" type="submit">
          Simpan
        </button>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Batal</a>
      </div>
    </form>
  </div>
@endsection
