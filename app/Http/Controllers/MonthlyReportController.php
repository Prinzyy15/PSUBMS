<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\MonthlyReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParentViolationReport;

class MonthlyReportController extends Controller
{
    // AJAX endpoint to get all students (with course and block info)
    public function allStudents()
    {
        $students = DB::table('students')
            ->leftJoin('courses', 'students.student_course', '=', 'courses.course_id')
            ->leftJoin('blocks', 'students.student_block', '=', 'blocks.block_id')
            ->select('students.student_id', 'students.student_number', 'students.student_fname', 'students.student_lname', 'students.student_year', 'courses.course_name', 'blocks.block_name')
            ->get();
        return response()->json(['students' => $students]);
    }
    public function index()
    {
        $reports = MonthlyReport::orderBy('month', 'desc')->get();
        return view('monthly_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('monthly_reports.create');
    }

    public function store(Request $request)
    {
        Log::info('MonthlyReportController@store: Entered store method.');

        $request->validate([
            'month' => 'required|date_format:Y-m',
            'selected_students' => 'required|array|min:1',
        ], [
            'selected_students.required' => 'Please select at least one student to send the report to.'
        ]);

        $month = $request->month;
        $adminId = Auth::id();
        $selectedStudents = $request->input('selected_students', []);

        Log::info('MonthlyReportController@store: Validation passed.', ['month' => $month, 'admin_id' => $adminId, 'selected_students' => $selectedStudents]);

        // Check if a report for this month already exists
        $existingReport = MonthlyReport::where('month', $month)->where('admin_id', $adminId)->first();
        if ($existingReport) {
            Log::warning('MonthlyReportController@store: Report for this month already exists.');
            return redirect()->route('monthly-reports.index')->with('warning', 'A monthly behavior report for this month has already been submitted. Please review existing reports before submitting again.');
        }

        // Save the monthly report
        MonthlyReport::create([
            'admin_id' => $adminId,
            'month' => $month,
            'report' => '', // No report text
        ]);

        Log::info('MonthlyReportController@store: Monthly report created.');

        // For each selected student, check for violations and notify parent if any
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        foreach ($selectedStudents as $studentId) {
            Log::info('MonthlyReportController@store: Processing student.', ['student_id' => $studentId]);

            $violationCount = DB::table('violations')
                ->where('student_id', $studentId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();

            // Log violation count (may be zero)
            Log::info('MonthlyReportController@store: Student violation count.', ['violation_count' => $violationCount]);

            $student = DB::table('students')->where('student_id', $studentId)->first();

            if ($student) {
                Log::info('MonthlyReportController@store: Student found.', ['student' => $student]);

                $parent = DB::table('parents')->where('parent_id', $student->student_parent)->first();

                $studentName = $student->student_fname . ' ' . $student->student_lname;

                // Send email only when there are violations
                if ($violationCount > 0 && $parent && !empty($parent->parent_email)) {
                    Log::info('MonthlyReportController@store: Parent has email and student has violations.', ['parent' => $parent]);
                    $mail = new ParentViolationReport($studentName, $month, $violationCount);
                    Mail::to($parent->parent_email)->send($mail);
                }

                // Always send SMS to all registered contacts for this student when configured
                try {
                    if (config('smsgate.enabled')) {
                        // fetch unique contact numbers for the student
                        $contactNumbers = DB::table('contacts')
                            ->where('contact_student_id', $studentId)
                            ->pluck('contact_number')
                            ->unique()
                            ->filter()
                            ->values()
                            ->toArray();

                        if (!empty($contactNumbers)) {
                            $svc = new \App\Services\SmsGateService();
                            // Use configurable template with placeholders
                            $template = config('notifications.monthly_report_sms');
                            $appName = config('app.name', 'School');
                            $replacements = [
                                '{app}' => $appName,
                                '{student}' => $studentName,
                                '{month}' => $month,
                                '{count}' => $violationCount,
                            ];
                            $message = strtr($template, $replacements);
                            foreach ($contactNumbers as $number) {
                                try {
                                    $svc->send($number, $message, ['student_id' => $studentId, 'parent_id' => $parent->parent_id ?? null]);
                                    Log::info('MonthlyReportController@store: SMS dispatched', ['student_id' => $studentId, 'number' => $number]);
                                } catch (\Throwable $e) {
                                    // log but don't stop processing other numbers or students
                                    Log::error('MonthlyReportController@store: SMS send failed for contact', ['number' => $number, 'error' => $e->getMessage()]);
                                }
                            }
                        } else {
                            Log::info('MonthlyReportController@store: No contact numbers found for student', ['student_id' => $studentId]);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('MonthlyReportController@store: SMS send failed (top-level)', ['error' => $e->getMessage()]);
                }
            }
        }

        Log::info('MonthlyReportController@store: Finished processing students.');

        return redirect()->route('monthly-reports.index')->with('success', 'Report submitted and sent to parents!');
    }
    // AJAX endpoint to get students with violations for a given month
    public function studentsWithViolations(Request $request)
    {
        $month = $request->input('month');
        if (!$month) {
            return response()->json(['students' => []]);
        }
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $studentIds = DB::table('violations')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->pluck('student_id')
            ->unique()
            ->toArray();
        if (empty($studentIds)) {
            return response()->json(['students' => []]);
        }
        $students = DB::table('students')
            ->whereIn('student_id', $studentIds)
            ->leftJoin('courses', 'students.student_course', '=', 'courses.course_id')
            ->leftJoin('blocks', 'students.student_block', '=', 'blocks.block_id')
            ->select('students.student_id', 'students.student_number', 'students.student_fname', 'students.student_lname', 'students.student_year', 'courses.course_name', 'blocks.block_name')
            ->get();
        return response()->json(['students' => $students]);
    }
}
