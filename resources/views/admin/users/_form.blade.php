    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php($user = $user ?? null)

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}" class="form-input" required>
        @error('name')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Username <span class="text-red-500">*</span></label>
        <input type="text" name="username" value="{{ old('username', $user?->username) }}" class="form-input" required>
        @error('username')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Kata Sandi @if(!$user)<span class="text-red-500">*</span>@endif</label>
        <input type="password" name="password" class="form-input" @if(!$user) required @endif
               placeholder="{{ $user ? 'Kosongkan jika tidak diubah' : '' }}">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation" class="form-input">
    </div>
    <div>
        <label class="form-label">Peran <span class="text-red-500">*</span></label>
        <select name="role" class="form-select" required>
            @foreach($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user?->role?->value) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user?->is_active ?? true))
                   class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm">Akun aktif</span>
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="btn-primary">{{ $user ? 'Simpan Perubahan' : 'Tambah Pengguna' }}</button>
    <a href="{{ url()->previous(route('admin.users.index')) }}" class="btn-secondary">Batal</a>
</div>
