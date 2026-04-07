<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasAnyRole(['company_owner', 'hr_admin']); }

    public function rules(): array
    {
        return [
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'nullable|string|max:100',
            'email'            => 'required|email|unique:employees,email',
            'employee_number'  => 'required|string|max:50|unique:employees,employee_number',
            'department_id'    => 'required|exists:departments,id',
            'position_id'     => 'required|exists:job_positions,id',
            'join_date'        => 'required|date',
            'employment_type'  => 'required|in:permanent,contract,probation,intern',
            'gender'           => 'nullable|in:male,female',
            'birth_date'       => 'nullable|date|before:today',
            'phone'            => 'nullable|string|max:20',
        ];
    }
}
