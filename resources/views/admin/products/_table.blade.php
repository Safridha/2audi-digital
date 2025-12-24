@php
    use Illuminate\Support\Str;

    $startNumber = method_exists($products, 'firstItem')
        ? $products->firstItem()
        : 1;

    $q = request('q');
@endphp

<div class="overflow-x-auto">
    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead class="bg-indigo-100 text-left">
            <tr>
                <th class="border p-2 w-10 text-center">
                    @if($products->count())
                        <input type="checkbox" id="select-all-products">
                    @endif
                </th>
                <th class="border p-2 w-16 text-center">No</th>
                <th class="border p-2 w-16 text-center">ID</th>
                <th class="border p-2 text-center">Gambar</th>
                <th class="border p-2">Nama Produk</th>
                <th class="border p-2 w-40">Kategori</th>
                <th class="border p-2 w-40 text-right">Harga</th>
                <th class="border p-2">Bahan</th>
                <th class="border p-2 w-40 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="border p-2 text-center align-top">
                        <input type="checkbox"
                               form="bulk-delete-products-form"
                               name="product_ids[]"
                               value="{{ $product->id }}"
                               class="row-checkbox-product">
                    </td>

                    <td class="border p-2 text-center align-top">
                        {{ $startNumber + $loop->index }}
                    </td>

                    <td class="border p-2 text-center align-top">
                        {{ $product->id }}
                    </td>

                    <td class="border p-2 text-center align-top">
                        @if ($product->image)
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="Gambar {{ $product->name }}"
                                class="h-12 w-12 object-cover rounded mx-auto"
                            >
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>

                    <td class="border p-2 align-top">
                        <div class="font-semibold text-gray-900">
                            {{ $product->name }}
                        </div>

                        @if($product->description)
                            <div class="text-xs text-gray-600 mt-1">
                                {{ Str::limit(strip_tags($product->description), 80) }}
                            </div>
                        @endif
                    </td>

                    <td class="border p-2 align-top">
                        {{ $product->category->name ?? '-' }}
                    </td>

                    <td class="border p-2 text-right align-top">
                        Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                    </td>

                    <td class="border p-2 align-top">
                        @php $bahans = $product->bahans ?? collect(); @endphp

                        @if($bahans->count())
                            <ul class="space-y-1">
                                @foreach($bahans as $bahan)
                                    <li class="text-xs text-gray-700">
                                        • {{ $bahan->nama_bahan }}
                                        <span class="text-gray-500">
                                            ({{ $bahan->pivot->qty_per_unit }})
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>

                    <td class="border p-2 text-center align-top">
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                           class="text-blue-600 hover:underline">
                            Edit
                        </a>
                        |
                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Yakin hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="border p-4 text-center text-gray-500">
                        @if($q)
                            Tidak ada produk yang cocok dengan
                            <span class="font-semibold">"{{ $q }}"</span>.
                        @else
                            Belum ada produk.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (method_exists($products, 'links'))
    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endif
