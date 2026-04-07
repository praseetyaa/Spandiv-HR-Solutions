<?php
namespace App\Events;
use App\Models\CandidateTestAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PsychTestAssigned { use Dispatchable, SerializesModels; public function __construct(public CandidateTestAssignment $assignment) {} }
