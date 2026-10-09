@extends('skema.aplikasi')
@section('konten')
<div x-data="{
        bukaModal: false, editId: null,
        form: { name: '', username: '', role: 'admin_logistik', dapur_id: '', password: '' },
        tambah() { this.editId = null; this.form = { name: '', username: '', role: 'admin_logistik', dapur_id: '', password: '' }; this.bukaModal = true; },
        ubah(a) { this.editId = a.id; this.form = { name: a.name, username: a.username, role: a.role, dapur_id: a.dapur_id ?? '', password: '' }; this.bukaModal = true; },
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Manajemen Akun</h1>
            <p class="text-xs text-muted mt-1">Buat &amp; kelola akun Admin Logistik dan Admin Dapur. Hanya Super Admin yang dapat mengakses menu ini.</p>
        </div>
        <button @click="tambah()" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun
        </button>
    </header>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @include('komponen.hint-geser')
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto">
        <table class="w-full min-w-160 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Username</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Dapur</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarAkun as $i => $a)
                <tr class="border-b border-paperline">
                    <td class="p-3">{{ $i + 1 }}</td>
                    <td class="p-3 font-semibold">{{ $a->name }}@if($a->id === auth()->id()) <span class="text-[10px] text-muted font-normal">(Anda)</span>@endif</td>
                    <td class="p-3 font-mono text-xs">{{ $a->username }}</td>
                    <td class="p-3">
                        <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded
                            {{ $a->isSuperAdmin() ? 'bg-mustard/20 text-[#7a5410]' : ($a->isAdminDapur() ? 'bg-rust/10 text-rust' : 'bg-green/10 text-green-deep') }}">
                            {{ $a->labelRole() }}
                        </span>
                    </td>
                    <td class="p-3 text-xs">{{ $a->dapur->nama_dapur ?? '-' }}</td>
                    <td class="p-3">
                        @if($a->isSuperAdmin())
                            <span class="text-[11px] text-muted">Terkunci</span>
                        @else
                            <div class="flex gap-1.5">
                                <button type="button"
                                    @click="ubah({{ Js::from(['id' => $a->id, 'name' => $a->name, 'username' => $a->username, 'role' => $a->role, 'dapur_id' => $a->dapur_id]) }})"
                                    class="px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Edit</button>
                                <form action="{{ route('akun.hapus', $a->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ e($a->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-[11px] font-bold text-white bg-rust rounded-lg hover:bg-rust/85 transition-colors">Hapus</button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-4 text-center text-xs text-muted">Belum ada akun.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit Akun --}}
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="editId ? 'Edit Akun' : 'Tambah Akun'"></h3>
            <form :action="editId ? `{{ url('manajemen-akun') }}/${editId}` : '{{ route('akun.simpan') }}'" method="POST" class="space-y-3">
                @csrf
                <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama <span class="text-rust">*</span></label>
                    <input type="text" name="name" x-model="form.name" required class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Username <span class="text-rust">*</span></label>
                    <input type="text" name="username" x-model="form.username" required placeholder="mis. admin_sukamaju" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Role <span class="text-rust">*</span></label>
                    <select name="role" x-model="form.role" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="admin_logistik">Admin Logistik</option>
                        <option value="admin_dapur">Admin Dapur</option>
                    </select>
                </div>
                <div x-show="form.role === 'admin_dapur'" x-cloak>
                    <label class="block text-xs text-muted uppercase mb-1">Dapur <span class="text-rust">*</span></label>
                    <select name="dapur_id" x-model="form.dapur_id" :required="form.role === 'admin_dapur'" :disabled="form.role !== 'admin_dapur'" class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih dapur --</option>
                        @foreach($daftarDapur as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_dapur }}</option>
                        @endforeach
                    </select>
                    @if($daftarDapur->isEmpty())
                        <p class="text-[11px] text-rust mt-1">Belum ada dapur. Tambahkan dulu di menu Dapur (Admin Logistik).</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Password <span x-show="!editId" class="text-rust">*</span></label>
                    <input type="text" name="password" x-model="form.password" :required="!editId" minlength="6" autocomplete="new-password"
                        :placeholder="editId ? 'Kosongkan jika tidak diganti' : 'Minimal 6 karakter'" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModal = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
