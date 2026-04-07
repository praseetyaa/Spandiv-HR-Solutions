<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date|after_or_equal:today',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string|max:500',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.after_or_equal'   => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
