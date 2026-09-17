<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $wajib = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'category_id' => [$wajib, 'string', 'exists:categories,id'],
            'name' => [$wajib, 'string', 'max:255'],
            'sku' => [$wajib, 'string', 'max:50', Rule::unique('items', 'sku')->ignore($this->route('item'))],
            'description' => ['nullable', 'string'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ada.',
            'name.required' => 'Nama barang wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU itu sudah dipakai barang lain.',
            'stock.min' => 'Stok tidak boleh minus.',
            'price.min' => 'Harga tidak boleh minus.',
        ];
    }
}
