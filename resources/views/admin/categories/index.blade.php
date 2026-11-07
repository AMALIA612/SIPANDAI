@extends('layouts.app')

@section('title', 'Kelola Kategori Pengaduan - Admin UPT Gandul')

@section('content')
<div class="w-full space-y-6">

    <!-- Header & Tombol Tambah -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kelola Kategori Pengaduan</h1>
            <p class="text-sm text-slate-500">Atur kategori yang dapat dipilih pegawai saat membuat laporan pengaduan</p>
        </div>
        <button onclick="openCreateCategoryModal()" 
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow transition">
            <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Kategori Baru
        </button>
    </div>

    <!-- Tabel Kategori -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-5">Nama Kategori</th>
                        <th class="py-3.5 px-5">Keterangan</th>
                        <th class="py-3.5 px-5 text-center">Jumlah Pengaduan</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-center w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5 font-bold text-slate-800">
                                {{ $cat->name }}
                            </td>
                            <td class="py-3.5 px-5 text-sm text-slate-500 max-w-sm">
                                {{ $cat->description ?: '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-sm font-bold bg-slate-100 text-slate-700">
                                    {{ $cat->complaints_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($cat->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-200 text-slate-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-center whitespace-nowrap w-44">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}', {{ $cat->is_active ? 1 : 0 }})"
                                            class="px-2.5 py-1 rounded-lg text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        <i class="fa-solid fa-pen mr-1"></i> Edit
                                    </button>
                                    
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-sm font-semibold bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                                            <i class="fa-solid fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-sm">Belum ada kategori pengaduan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah Kategori -->
<div id="modalCreateCategory" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 animate-in fade-in zoom-in duration-150">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-800">Tambah Kategori Baru</h3>
            <button onclick="closeCreateCategoryModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 uppercase mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required placeholder="Contoh: Kendala Fasilitas GI"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
            </div>
            <div>
                <label for="description" class="block text-sm font-bold text-slate-700 uppercase mb-1">Keterangan</label>
                <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat kategori..."
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeCreateCategoryModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-pln-blue text-white rounded-xl text-sm font-bold shadow hover:bg-pln-hover">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditCategory" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-800">Edit Kategori Pengaduan</h3>
            <button onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditCategory" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_name" class="block text-sm font-bold text-slate-700 uppercase mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
            </div>
            <div>
                <label for="edit_description" class="block text-sm font-bold text-slate-700 uppercase mb-1">Keterangan</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none"></textarea>
            </div>
            <div>
                <label for="edit_is_active" class="block text-sm font-bold text-slate-700 uppercase mb-1">Status Kategori</label>
                <select name="is_active" id="edit_is_active" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="1">Aktif (Dapat dipilih pegawai)</option>
                    <option value="0">Nonaktif (Disembunyikan)</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-pln-blue text-white rounded-xl text-sm font-bold shadow hover:bg-pln-hover">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCreateCategoryModal() {
    document.getElementById('modalCreateCategory').classList.remove('hidden');
}
function closeCreateCategoryModal() {
    document.getElementById('modalCreateCategory').classList.add('hidden');
}
function openEditCategoryModal(id, name, desc, isActive) {
    document.getElementById('formEditCategory').action = '{{ url("admin/kategori") }}/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = desc;
    document.getElementById('edit_is_active').value = isActive;
    document.getElementById('modalEditCategory').classList.remove('hidden');
}
function closeEditCategoryModal() {
    document.getElementById('modalEditCategory').classList.add('hidden');
}
</script>
@endpush
@endsection