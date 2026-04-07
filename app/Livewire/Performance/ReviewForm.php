<?php

namespace App\Livewire\Performance;

use App\Models\PerformanceReview;
use App\Models\ReviewCycle;
use App\Models\Employee;
use Livewire\Component;

class ReviewForm extends Component
{
    public ?int $reviewId = null;
    public ?int $cycleId = null;
    public ?int $employeeId = null;
    public array $scores = [];
    public string $strengths = '';
    public string $improvements = '';
    public string $overallComment = '';
    public float $overallScore = 0;
    public string $status = 'draft';

    protected array $criteria = [
        'quality_of_work'  => 'Kualitas Kerja',
        'productivity'     => 'Produktivitas',
        'initiative'       => 'Inisiatif',
        'teamwork'         => 'Kerja Tim',
        'communication'    => 'Komunikasi',
        'attendance'       => 'Kehadiran',
        'leadership'       => 'Kepemimpinan',
    ];

    public function mount(?int $reviewId = null): void
    {
        if ($reviewId) {
            $review = PerformanceReview::findOrFail($reviewId);
            $this->reviewId        = $review->id;
            $this->cycleId         = $review->cycle_id;
            $this->employeeId      = $review->employee_id;
            $this->scores          = $review->scores ?? [];
            $this->strengths       = $review->strengths ?? '';
            $this->improvements    = $review->improvements ?? '';
            $this->overallComment  = $review->overall_comment ?? '';
            $this->overallScore    = (float) $review->overall_score;
            $this->status          = $review->status;
        }

        if (empty($this->scores)) {
            foreach ($this->criteria as $key => $label) {
                $this->scores[$key] = 0;
            }
        }
    }

    public function updatedScores(): void
    {
        $filled = array_filter($this->scores, fn ($v) => $v > 0);
        $this->overallScore = count($filled) > 0 ? round(array_sum($filled) / count($filled), 2) : 0;
    }

    public function saveDraft(): void
    {
        $this->saveReview('draft');
    }

    public function submit(): void
    {
        $this->saveReview('submitted');
    }

    private function saveReview(string $status): void
    {
        $data = [
            'tenant_id'       => auth()->user()->tenant_id,
            'cycle_id'        => $this->cycleId,
            'employee_id'     => $this->employeeId,
            'reviewer_id'     => auth()->id(),
            'scores'          => $this->scores,
            'strengths'       => $this->strengths,
            'improvements'    => $this->improvements,
            'overall_comment' => $this->overallComment,
            'overall_score'   => $this->overallScore,
            'status'          => $status,
        ];

        if ($status === 'submitted') {
            $data['submitted_at'] = now();
        }

        PerformanceReview::updateOrCreate(
            ['id' => $this->reviewId],
            $data
        );

        session()->flash('success', $status === 'submitted' ? 'Review berhasil disubmit.' : 'Draft tersimpan.');
    }

    public function render()
    {
        $employee = $this->employeeId ? Employee::with(['department', 'position'])->find($this->employeeId) : null;
        $cycles   = ReviewCycle::where('tenant_id', auth()->user()->tenant_id)->where('status', 'active')->get();

        return view('livewire.performance.review-form', [
            'employee'     => $employee,
            'cycles'       => $cycles,
            'criteriaList' => $this->criteria,
        ]);
    }
}
