<?php

namespace App\Livewire\PsychTest;

use App\Models\PsychTest;
use App\Models\PsychTestSection;
use App\Models\Question;
use App\Models\QuestionOption;
use Livewire\Component;

class TestManager extends Component
{
    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';

    // Test form
    public bool $showTestForm = false;
    public ?int $editingTestId = null;
    public string $testName = '';
    public string $testCode = '';
    public string $testCategory = 'personality';
    public string $testType = '';
    public string $testDescription = '';
    public string $testInstructions = '';
    public int $testDuration = 60;
    public int $testTotalQuestions = 0;
    public ?string $testPassingScore = null;
    public bool $testRandomizeQ = false;
    public bool $testRandomizeOpt = false;

    // Question builder
    public ?int $selectedTestId = null;
    public bool $showQuestionForm = false;
    public ?int $editingQuestionId = null;
    public ?int $questionSectionId = null;
    public string $questionType = 'multiple_choice';
    public string $questionContent = '';
    public int $questionOrder = 1;
    public string $questionPoints = '1';
    public string $questionDimensionKey = '';
    public array $questionOptions = [];

    // ── Computed ──
    public function getTestsProperty()
    {
        return PsychTest::withCount(['questions', 'sections', 'assignments'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getSelectedTestProperty()
    {
        if (!$this->selectedTestId) return null;
        return PsychTest::with(['sections.questions.options'])->find($this->selectedTestId);
    }

    public function getStatsProperty(): array
    {
        return [
            'total'    => PsychTest::count(),
            'active'   => PsychTest::where('status', 'active')->count(),
            'draft'    => PsychTest::where('status', 'draft')->count(),
            'questions' => Question::count(),
        ];
    }

    // ── Select ──
    public function selectTest(int $id): void
    {
        $this->selectedTestId = $id;
    }

    // ── Test CRUD ──
    public function openTestForm(?int $id = null): void
    {
        $this->reset(['editingTestId', 'testName', 'testCode', 'testCategory', 'testType', 'testDescription', 'testInstructions', 'testDuration', 'testTotalQuestions', 'testPassingScore', 'testRandomizeQ', 'testRandomizeOpt']);
        $this->testCategory = 'personality';
        $this->testDuration = 60;
        if ($id) {
            $t = PsychTest::findOrFail($id);
            $this->editingTestId = $t->id;
            $this->testName = $t->name;
            $this->testCode = $t->code;
            $this->testCategory = $t->category;
            $this->testType = $t->test_type;
            $this->testDescription = $t->description ?? '';
            $this->testInstructions = $t->instructions;
            $this->testDuration = $t->duration_minutes;
            $this->testTotalQuestions = $t->total_questions;
            $this->testPassingScore = $t->passing_score;
            $this->testRandomizeQ = $t->is_randomize_q;
            $this->testRandomizeOpt = $t->is_randomize_opt;
        }
        $this->showTestForm = true;
    }

    public function saveTest(): void
    {
        $this->validate([
            'testName'         => 'required|string|max:255',
            'testCode'         => 'required|string|max:50',
            'testCategory'     => 'required',
            'testInstructions' => 'required|string',
            'testDuration'     => 'required|integer|min:1',
        ]);

        $data = [
            'name'             => $this->testName,
            'code'             => $this->testCode,
            'category'         => $this->testCategory,
            'test_type'        => $this->testType ?: $this->testCategory,
            'description'      => $this->testDescription ?: null,
            'instructions'     => $this->testInstructions,
            'duration_minutes' => $this->testDuration,
            'total_questions'  => $this->testTotalQuestions,
            'passing_score'    => $this->testPassingScore ?: null,
            'is_randomize_q'   => $this->testRandomizeQ,
            'is_randomize_opt' => $this->testRandomizeOpt,
        ];

        if ($this->editingTestId) {
            PsychTest::findOrFail($this->editingTestId)->update($data);
        } else {
            PsychTest::create([
                'tenant_id' => auth()->user()->tenant_id,
                'status'    => 'draft',
                ...$data,
            ]);
        }
        $this->showTestForm = false;
    }

    public function updateTestStatus(int $id, string $status): void
    {
        PsychTest::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteTest(int $id): void
    {
        PsychTest::findOrFail($id)->delete();
        if ($this->selectedTestId === $id) $this->selectedTestId = null;
    }

    // ── Question CRUD ──
    public function openQuestionForm(?int $id = null): void
    {
        $this->reset(['editingQuestionId', 'questionSectionId', 'questionType', 'questionContent', 'questionOrder', 'questionPoints', 'questionDimensionKey', 'questionOptions']);
        $this->questionType = 'multiple_choice';
        $this->questionPoints = '1';
        $this->questionOrder = Question::where('test_id', $this->selectedTestId)->max('order_number') + 1;
        $this->questionOptions = [
            ['content' => '', 'is_correct' => false, 'score_value' => '0'],
            ['content' => '', 'is_correct' => false, 'score_value' => '0'],
        ];
        if ($id) {
            $q = Question::with('options')->findOrFail($id);
            $this->editingQuestionId = $q->id;
            $this->questionSectionId = $q->section_id;
            $this->questionType = $q->type;
            $this->questionContent = $q->content;
            $this->questionOrder = $q->order_number;
            $this->questionPoints = (string) $q->points;
            $this->questionDimensionKey = $q->dimension_key ?? '';
            $this->questionOptions = $q->options->map(fn ($o) => [
                'content' => $o->content,
                'is_correct' => $o->is_correct,
                'score_value' => (string) $o->score_value,
            ])->toArray();
        }
        $this->showQuestionForm = true;
    }

    public function addOption(): void
    {
        $this->questionOptions[] = ['content' => '', 'is_correct' => false, 'score_value' => '0'];
    }

    public function removeOption(int $idx): void
    {
        unset($this->questionOptions[$idx]);
        $this->questionOptions = array_values($this->questionOptions);
    }

    public function saveQuestion(): void
    {
        $this->validate([
            'questionContent' => 'required|string',
            'questionType'    => 'required',
        ]);

        $data = [
            'test_id'       => $this->selectedTestId,
            'section_id'    => $this->questionSectionId ?: null,
            'type'          => $this->questionType,
            'content'       => $this->questionContent,
            'order_number'  => $this->questionOrder,
            'points'        => $this->questionPoints ?: 1,
            'dimension_key' => $this->questionDimensionKey ?: null,
        ];

        if ($this->editingQuestionId) {
            $question = Question::findOrFail($this->editingQuestionId);
            $question->update($data);
            $question->options()->delete();
        } else {
            $question = Question::create($data);
        }

        // Save options
        foreach ($this->questionOptions as $idx => $opt) {
            if (empty($opt['content'])) continue;
            QuestionOption::create([
                'question_id'  => $question->id,
                'content'      => $opt['content'],
                'is_correct'   => $opt['is_correct'] ?? false,
                'score_value'  => $opt['score_value'] ?? 0,
                'order_number' => $idx + 1,
            ]);
        }

        // Update total questions count
        PsychTest::findOrFail($this->selectedTestId)->update([
            'total_questions' => Question::where('test_id', $this->selectedTestId)->count(),
        ]);

        $this->showQuestionForm = false;
    }

    public function deleteQuestion(int $id): void
    {
        Question::findOrFail($id)->delete();
        PsychTest::findOrFail($this->selectedTestId)->update([
            'total_questions' => Question::where('test_id', $this->selectedTestId)->count(),
        ]);
    }

    public function render()
    {
        return view('livewire.psych-test.test-manager')
            ->layout('layouts.app', ['pageTitle' => 'Tes Psikologi']);
    }
}
