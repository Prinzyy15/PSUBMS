<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillParentLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backfill:parent-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill students.student_parent using parents.parent_student_id where possible';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting backfill of student->parent links...');

        $parents = DB::table('parents')
            ->whereNotNull('parent_student_id')
            ->get();

        $updated = 0;
        foreach ($parents as $p) {
            $student = DB::table('students')->where('student_id', $p->parent_student_id)->first();
            if ($student) {
                DB::table('students')
                    ->where('student_id', $p->parent_student_id)
                    ->update(['student_parent' => $p->parent_id]);
                $updated++;
                Log::info('Backfilled student_parent', ['student_id' => $p->parent_student_id, 'parent_id' => $p->parent_id]);
            }
        }

        $this->info("Backfill complete. Updated {$updated} students.");
        return 0;
    }
}
