<?php

namespace App\Livewire\Engagement;

use App\Models\PulseSurvey;
use App\Models\PulseSurveyQuestion;
use Livewire\Component;

class SurveyManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    // Survey form
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $surveyTitle = '';
    public string $surveyDescription = '';
    public bool $surveyAnonymous = true;
    public ?string $surveyStartDate = null;
    public ?string $surveyEndDate = null;

    // Question form
    public bool $showQuestionForm = false;
    public ?int $selectedSurveyId = null;
    public string $questionContent = '';
    public string $questionType = 'rating';
    public bool $questionRequired = true;

    // Selected survey for detail
    public ?int $detailSurveyId = null;

    public function getSurveysProperty()
    {
        return PulseSurvey::withCount(['questions', 'responses'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getDetailSurveyProperty()
    {
        if (!$this->detailSurveyId) return null;
        return PulseSurvey::with(['questions' => fn ($q) => $q->orderBy('order_number'), 'questions.responses'])->find($this->detailSurveyId);
    }

    public function selectSurvey(int $id): void { $this->detailSurveyId = $id; }

    // ── Survey CRUD ──
    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'surveyTitle', 'surveyDescription', 'surveyAnonymous', 'surveyStartDate', 'surveyEndDate']);
        $this->surveyAnonymous = true;
        if ($id) {
            $s = PulseSurvey::findOrFail($id);
            $this->editingId = $s->id;
            $this->surveyTitle = $s->title;
            $this->surveyDescription = $s->description ?? '';
            $this->surveyAnonymous = $s->is_anonymous;
            $this->surveyStartDate = $s->start_date->format('Y-m-d');
            $this->surveyEndDate = $s->end_date->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function saveSurvey(): void
    {
        $this->validate([
            'surveyTitle' => 'required|string|max:255',
            'surveyStartDate' => 'required|date',
            'surveyEndDate' => 'required|date|after:surveyStartDate',
        ]);

        $data = [
            'title' => $this->surveyTitle,
            'description' => $this->surveyDescription ?: null,
            'is_anonymous' => $this->surveyAnonymous,
            'start_date' => $this->surveyStartDate,
            'end_date' => $this->surveyEndDate,
        ];

        if ($this->editingId) {
            PulseSurvey::findOrFail($this->editingId)->update($data);
        } else {
            PulseSurvey::create([
                'tenant_id' => auth()->user()->tenant_id,
                'created_by' => auth()->id(),
                'status' => 'draft',
                ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function updateStatus(int $id, string $status): void
    {
        PulseSurvey::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteSurvey(int $id): void
    {
        PulseSurvey::findOrFail($id)->delete();
        if ($this->detailSurveyId === $id) $this->detailSurveyId = null;
    }

    // ── Questions ──
    public function openQuestionForm(int $surveyId): void
    {
        $this->selectedSurveyId = $surveyId;
        $this->reset(['questionContent', 'questionType', 'questionRequired']);
        $this->questionType = 'rating';
        $this->questionRequired = true;
        $this->showQuestionForm = true;
    }

    public function saveQuestion(): void
    {
        $this->validate(['questionContent' => 'required|string']);
        $nextOrder = PulseSurveyQuestion::where('survey_id', $this->selectedSurveyId)->max('order_number') + 1;
        PulseSurveyQuestion::create([
            'survey_id' => $this->selectedSurveyId,
            'type' => $this->questionType,
            'content' => $this->questionContent,
            'order_number' => $nextOrder ?: 1,
            'is_required' => $this->questionRequired,
        ]);
        $this->showQuestionForm = false;
    }

    public function deleteQuestion(int $id): void
    {
        PulseSurveyQuestion::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.engagement.survey-manager')
            ->layout('layouts.app', ['pageTitle' => 'Pulse Survey']);
    }
}
