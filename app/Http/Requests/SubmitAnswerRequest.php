<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'session_id'        => 'required|exists:candidate_test_sessions,id',
            'question_id'       => 'required|exists:questions,id',
            'selected_option_id'=> 'nullable|exists:question_options,id',
            'answer_text'       => 'nullable|string|max:5000',
            'number_input'      => 'nullable|numeric',
            'time_spent_sec'    => 'nullable|integer|min:0',
        ];
    }
}
