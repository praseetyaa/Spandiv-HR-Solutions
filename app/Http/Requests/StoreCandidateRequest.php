<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasAnyRole(['company_owner', 'hr_admin', 'recruiter']); }

    public function rules(): array
    {
        return [
            'job_id'     => 'required|exists:job_postings,id',
            'name'       => 'required|string|max:200',
            'email'      => 'required|email|max:200',
            'phone'      => 'nullable|string|max:20',
            'resume'     => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'source'     => 'nullable|in:website,linkedin,referral,job_fair,other',
            'notes'      => 'nullable|string|max:1000',
        ];
    }
}
