<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunPayrollRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasAnyRole(['company_owner', 'finance_admin']); }

    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:payroll_periods,id',
        ];
    }
}
