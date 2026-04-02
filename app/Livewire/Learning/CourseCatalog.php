<?php

namespace App\Livewire\Learning;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\CourseSection;
use App\Models\Employee;
use Livewire\Component;

class CourseCatalog extends Component
{
    public string $search = '';
    public string $categoryFilter = '';
    public string $levelFilter = '';
    public ?int $selectedCourseId = null;

    // Course Form
    public bool $showCourseForm = false;
    public ?int $editingCourseId = null;
    public string $courseTitle = '';
    public string $courseDescription = '';
    public string $courseCategory = '';
    public string $courseLevel = 'beginner';
    public int $courseDuration = 60;
    public bool $courseIsMandatory = false;

    // Section Form
    public bool $showSectionForm = false;
    public ?int $editingSectionId = null;
    public string $sectionTitle = '';
    public int $sectionOrder = 1;
    public int $sectionDuration = 30;

    // Enrollment
    public bool $showEnrollForm = false;
    public ?int $enrollEmployeeId = null;

    public function getCoursesProperty()
    {
        return Course::with('enrollments')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->when($this->levelFilter, fn ($q) => $q->where('level', $this->levelFilter))
            ->latest()
            ->get();
    }

    public function getCategoriesProperty(): array
    {
        return Course::distinct()->pluck('category')->filter()->toArray();
    }

    public function getSelectedCourseProperty()
    {
        if (!$this->selectedCourseId) return null;
        return Course::with(['sections.materials', 'enrollments.employee'])->find($this->selectedCourseId);
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function selectCourse(int $id): void
    {
        $this->selectedCourseId = $id;
    }

    // ── Course CRUD ──
    public function openCourseForm(?int $id = null): void
    {
        $this->resetCourseForm();
        if ($id) {
            $c = Course::findOrFail($id);
            $this->editingCourseId = $c->id;
            $this->courseTitle = $c->title;
            $this->courseDescription = $c->description ?? '';
            $this->courseCategory = $c->category;
            $this->courseLevel = $c->level;
            $this->courseDuration = $c->duration_minutes;
            $this->courseIsMandatory = $c->is_mandatory;
        }
        $this->showCourseForm = true;
    }

    public function saveCourse(): void
    {
        $this->validate([
            'courseTitle'    => 'required|string|max:255',
            'courseCategory' => 'required|string|max:100',
            'courseLevel'    => 'required|in:beginner,intermediate,advanced',
            'courseDuration' => 'required|integer|min:1',
        ]);

        $data = [
            'title'            => $this->courseTitle,
            'description'      => $this->courseDescription ?: null,
            'category'         => $this->courseCategory,
            'level'            => $this->courseLevel,
            'duration_minutes' => $this->courseDuration,
            'is_mandatory'     => $this->courseIsMandatory,
        ];

        if ($this->editingCourseId) {
            Course::findOrFail($this->editingCourseId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['created_by'] = auth()->id();
            Course::create($data);
        }

        $this->showCourseForm = false;
        $this->resetCourseForm();
    }

    public function deleteCourse(int $id): void
    {
        Course::findOrFail($id)->delete();
        if ($this->selectedCourseId === $id) $this->selectedCourseId = null;
    }

    private function resetCourseForm(): void
    {
        $this->editingCourseId = null;
        $this->courseTitle = '';
        $this->courseDescription = '';
        $this->courseCategory = '';
        $this->courseLevel = 'beginner';
        $this->courseDuration = 60;
        $this->courseIsMandatory = false;
    }

    // ── Section CRUD ──
    public function openSectionForm(?int $id = null): void
    {
        $this->resetSectionForm();
        if ($id) {
            $s = CourseSection::findOrFail($id);
            $this->editingSectionId = $s->id;
            $this->sectionTitle = $s->title;
            $this->sectionOrder = $s->order_number;
            $this->sectionDuration = $s->duration_minutes;
        } else {
            $this->sectionOrder = ($this->selectedCourse?->sections->count() ?? 0) + 1;
        }
        $this->showSectionForm = true;
    }

    public function saveSection(): void
    {
        $this->validate([
            'sectionTitle'    => 'required|string|max:255',
            'sectionOrder'    => 'required|integer|min:1',
            'sectionDuration' => 'required|integer|min:1',
        ]);

        $data = [
            'course_id'       => $this->selectedCourseId,
            'title'           => $this->sectionTitle,
            'order_number'    => $this->sectionOrder,
            'duration_minutes' => $this->sectionDuration,
        ];

        if ($this->editingSectionId) {
            CourseSection::findOrFail($this->editingSectionId)->update($data);
        } else {
            CourseSection::create($data);
        }

        $this->showSectionForm = false;
        $this->resetSectionForm();
    }

    public function deleteSection(int $id): void
    {
        CourseSection::findOrFail($id)->delete();
    }

    private function resetSectionForm(): void
    {
        $this->editingSectionId = null;
        $this->sectionTitle = '';
        $this->sectionOrder = 1;
        $this->sectionDuration = 30;
    }

    // ── Enrollment ──
    public function openEnrollForm(): void { $this->showEnrollForm = true; $this->enrollEmployeeId = null; }

    public function enrollEmployee(): void
    {
        $this->validate(['enrollEmployeeId' => 'required|exists:employees,id']);

        CourseEnrollment::firstOrCreate([
            'course_id'   => $this->selectedCourseId,
            'employee_id' => $this->enrollEmployeeId,
        ], [
            'tenant_id' => auth()->user()->tenant_id,
            'status'    => 'enrolled',
        ]);

        $this->showEnrollForm = false;
        $this->enrollEmployeeId = null;
    }

    public function removeEnrollment(int $id): void
    {
        CourseEnrollment::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.learning.course-catalog')
            ->layout('layouts.app', ['pageTitle' => 'Katalog Kursus']);
    }
}
