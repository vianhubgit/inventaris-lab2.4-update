<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('kelas')?->id;

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kelas', 'nama')->ignore($id)],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama kelas',
            'keterangan' => 'keterangan',
        ];
    }
}
