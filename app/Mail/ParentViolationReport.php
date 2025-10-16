<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParentViolationReport extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $month;
    public $violationCount;

    public function __construct($studentName, $month, $violationCount)
    {
        $this->studentName = $studentName;
        $this->month = $month;
        $this->violationCount = $violationCount;
    }

    public function build()
    {
        return $this->subject('Monthly Behavior Report: Violation Notice')
            ->view('emails.parent_violation_report');
    }
}
