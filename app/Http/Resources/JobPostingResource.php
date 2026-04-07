<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'slug'                 => \Illuminate\Support\Str::slug($this->title),
            'department'           => $this->department?->name,
            'position'             => $this->position?->title,
            'description'          => $this->description,
            'requirements'         => $this->requirements,
            'employment_type'      => $this->employment_type,
            'employment_type_label'=> $this->employment_type_label,
            'salary_range'         => $this->formatSalaryRange(),
            'openings'             => $this->openings,
            'close_date'           => $this->close_date?->format('Y-m-d'),
            'published_at'         => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format salary range into a human-readable string.
     */
    private function formatSalaryRange(): ?string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return null;
        }

        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') .
                   ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        }

        if ($this->salary_min) {
            return 'Mulai Rp ' . number_format($this->salary_min, 0, ',', '.');
        }

        return 'Hingga Rp ' . number_format($this->salary_max, 0, ',', '.');
    }
}
