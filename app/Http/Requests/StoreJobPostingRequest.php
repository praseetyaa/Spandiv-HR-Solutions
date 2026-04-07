<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasAnyRole(['company_owner', 'hr_admin', 'recruiter']); }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:200',
            'department_id'  => 'required|exists:departments,id',
            'position_id'   => 'required|exists:job_positions,id',
            'type'           => 'required|in:full_time,part_time,contract,internship',
            'location'       => 'nullable|string|max:255',
            'salary_min'     => 'nullable|numeric|min:0',
            'salary_max'     => 'nullable|numeric|gte:salary_min',
            'description'    => 'required|string',
            'requirements'   => 'required|string',
            'deadline'       => 'required|date|after:today',
            'max_candidates' => 'nullable|integer|min:1',
        ];
    }
}
