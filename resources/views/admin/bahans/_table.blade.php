<div class="overflow-x-auto">
  <table class="w-full border-collapse border border-gray-300 text-sm">
    <thead class="bg-indigo-100 text-left">
      <tr>
        {{-- Checkbox master --}}
        <th class="border p-2 w-10 text-center">
          @if($bahans->count())
            <input type="checkbox" id="select-all-bahans">
          @endif
        </th>
        <th class="border p-2 w-14 text-center">No</th>
        <th class="border p-2 w-16 text-center">ID</th>
        <th class="border p-2">Nama Bahan</th>
        <th class="border p-2 w-32">Satuan</th>
        <th class="border p-2 w-32 text-right">Minimal Stok</th>
        <th class="border p-2 w-32 text-center">Dibuat</th>
        <th class="border p-2 w-40 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody>
      @forelse($bahans as $index => $bahan)
        <tr class="hover:bg-gray-50">
          {{-- Checkbox per baris --}}
          <td class="border p-2 text-center align-top">
            <input type="checkbox"
                   name="ids[]"
                   value="{{ $bahan->id }}"
                   class="row-checkbox-bahan">
          </td>

          {{-- No --}}
          <td class="border p-2 text-center align-top">
            {{ $bahans->firstItem() + $index }}
          </td>

          {{-- ID --}}
          <td class="border p-2 text-center align-top">
            {{ $bahan->id }}
          </td>

          {{-- Nama Bahan --}}
          <td class="border p-2 align-top">
            {{ $bahan->nama_bahan }}
          </td>

          {{-- Satuan --}}
          <td class="border p-2 align-top">
            {{ $bahan->satuan }}
          </td>

          {{-- Minimal Stok --}}
          <td class="border p-2 text-right align-top">
            {{ $bahan->minimal_stock }}
          </td>

          {{-- Dibuat --}}
          <td class="border p-2 text-center align-top">
            {{ $bahan->created_at?->format('d-m-Y') ?? '-' }}
          </td>

          {{-- Aksi --}}
          <td class="border p-2 text-center align-top">
            <a href="{{ route('admin.bahans.edit', $bahan) }}"
               class="text-indigo-600 hover:underline">
              Edit
            </a>
            |
            <form action="{{ route('admin.bahans.destroy', $bahan) }}"
                  method="POST"
                  class="inline"
                  onsubmit="return confirm('Yakin hapus bahan ini?')">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="text-red-600 hover:underline ml-1">
                Hapus
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="border p-4 text-center text-gray-500">
            Belum ada data bahan.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($bahans instanceof \Illuminate\Pagination\AbstractPaginator)
  <div class="mt-4">
    {{ $bahans->links() }}
  </div>
@endif
