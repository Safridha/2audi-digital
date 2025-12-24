@php
    $startNumber = method_exists($categories, 'firstItem')
        ? $categories->firstItem()
        : 1;

    $q = request('q');
@endphp

<div class="overflow-x-auto">
    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead class="bg-indigo-100 text-left">
            <tr>
                <th class="border p-2 w-10 text-center">
                    @if($categories->count())
                        <input type="checkbox" id="select-all-categories">
                    @endif
                </th>
                <th class="border p-2 w-16 text-center">No</th>
                <th class="border p-2 w-16 text-center">ID</th>
                <th class="border p-2 text-center">Gambar</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Deskripsi</th>
                <th class="border p-2 w-40 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($categories as $category)
                <tr class="hover:bg-gray-50">
                    {{-- checkbox per kategori, diarahkan ke form bulk --}}
                    <td class="border p-2 text-center align-top">
                        <input type="checkbox"
                               form="bulk-delete-categories-form"
                               name="category_ids[]"
                               value="{{ $category->id }}"
                               class="row-checkbox-category">
                    </td>

                    <td class="border p-2 text-center align-top">
                        {{ $startNumber + $loop->index }}
                    </td>

                    <td class="border p-2 text-center align-top">
                        {{ $category->id }}
                    </td>

                    {{-- Kolom gambar --}}
                    <td class="border p-2 text-center align-top">
                        @if ($category->image)
                            <img
                                src="{{ asset('storage/' . $category->image) }}"
                                alt="Gambar {{ $category->name }}"
                                class="h-12 w-12 object-cover rounded mx-auto"
                            >
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>

                    <td class="border p-2 align-top">
                        {{ $category->name }}
                    </td>

                    <td class="border p-2 align-top">
                        {{ $category->description ?? '-' }}
                    </td>

                    <td class="border p-2 text-center align-top">
                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                           class="text-blue-600 hover:underline">
                            Edit
                        </a>
                        |
                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border p-4 text-center text-gray-500">
                        @if($q)
                            Tidak ada kategori yang cocok dengan
                            <span class="font-semibold">"{{ $q }}"</span>.
                        @else
                            Belum ada kategori.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (method_exists($categories, 'links'))
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endif
