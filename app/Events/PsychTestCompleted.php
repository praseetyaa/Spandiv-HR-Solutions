<?php
namespace App\Events;
use App\Models\CandidateTestSession;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PsychTestCompleted { use Dispatchable, SerializesModels; public function __construct(public CandidateTestSession $session) {} }
