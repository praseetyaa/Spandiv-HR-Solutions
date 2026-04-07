<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasAnyRole(['company_owner', 'hr_admin', 'employee']); }

    public function rules(): array
    {
        $id = $this->route('employee') ?? $this->input('id');
        return [
            'first_name'       => 'sometimes|string|max:100',
            'last_name'        => 'nullable|string|max:100',
            'email'            => "sometimes|email|unique:employees,email,{$id}",
            'department_id'    => 'sometimes|exists:departments,id',
            'position_id'     => 'sometimes|exists:job_positions,id',
            'employment_type'  => 'sometimes|in:permanent,contract,probation,intern',
            'phone'            => 'nullable|string|max:20',
        ];
    }
}
