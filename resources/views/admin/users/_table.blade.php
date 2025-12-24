<div class="overflow-x-auto">
  <table class="w-full border-collapse border border-gray-300">
    <thead class="bg-indigo-100 text-left">
      <tr>
        {{-- Checkbox master --}}
        <th class="border p-2 w-10 text-center">
          @if($users->count())
            <input type="checkbox" id="select-all-users">
          @endif
        </th>
        <th class="border p-2 w-16 text-center">ID</th>
        <th class="border p-2">Nama</th>
        <th class="border p-2">Email</th>
        <th class="border p-2 w-28 text-center">Role</th>
        <th class="border p-2 w-40 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody>
      @forelse($users as $u)
        <tr class="hover:bg-gray-50">

          {{-- Checkbox per baris (ikut ke form bulk lewat form="...") --}}
          <td class="border p-2 text-center">
            <input
              type="checkbox"
              form="bulk-delete-users-form"
              name="user_ids[]"
              value="{{ $u->id }}"
              class="row-checkbox-user"
              {{ $u->id === auth()->id() ? 'disabled' : '' }} {{-- tidak bisa pilih akun sendiri --}}
            >
          </td>

          {{-- ID --}}
          <td class="border p-2 text-center">
            {{ $u->id }}
          </td>

          {{-- Nama --}}
          <td class="border p-2">
            {{ $u->name }}
          </td>

          {{-- Email --}}
          <td class="border p-2">
            {{ $u->email }}
          </td>

          {{-- Role --}}
          <td class="border p-2 text-center">
            {{ ucfirst($u->role) }}
          </td>

          {{-- Aksi --}}
          <td class="border p-2 text-center">
            <a href="{{ route('admin.users.edit',$u->id) }}"
               class="text-blue-600 hover:underline">
              Edit
            </a>
            |
            <form action="{{ route('admin.users.destroy',$u->id) }}"
                  method="POST"
                  class="inline"
                  onsubmit="return confirm('Hapus pengguna ini?')">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="text-red-600 hover:underline"
                      {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                Hapus
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="border p-4 text-center text-gray-500">
            Belum ada pengguna.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if(method_exists($users,'links'))
  <div class="mt-4">
    {{ $users->links() }}
  </div>
@endif
