@extends('layouts.admin')
@section('title', 'Edit Pengguna')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        Edit Pengguna
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Ubah data pengguna termasuk nama, email, password, dan role.
    </p>

    {{-- Error Validasi --}}
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 p-6">
        <form action="{{ route('admin.users.update', $user->id) }}"
              method="POST"
              class="space-y-4">

            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                    Nama
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       required>

                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                       required>

                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                    Password (kosongkan jika tidak ganti)
                </label>

                <input type="password"
                       name="password"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm">

                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                    Konfirmasi Password
                </label>

                <input type="password"
                       name="password_confirmation"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                              focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            {{-- Role --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                    Role
                </label>

                <select name="role"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        required>
                    <option value="user"  @selected(old('role', $user->role) === 'user')>User</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>

                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Update
                </button>

                <a href="{{ route('admin.users.index') }}"
                   class="text-gray-600 hover:underline text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
