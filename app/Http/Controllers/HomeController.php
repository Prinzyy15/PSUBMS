<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
// ...existing code...

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// MODELS
use \App\Students;
use \App\Violations;
use \App\ViolationType;
use \App\Blocks;
use \App\Courses;
use \App\Parents;
use \App\Contacts;
use \App\User;
use \App\Appointment;

class HomeController extends Controller
{
    // Parent Dashboard: Formal Messages for Violations
    public function parentMessages(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'parent') {
            return response()->json([], 403);
        }
        $parent = DB::table('parents')
            ->join('users', 'users.name', '=', DB::raw('CAST(parents.parent_username AS CHAR)'))
            ->where('users.name', $user->name)
            ->select('parents.*')
            ->first();
        if (!$parent) {
            return response()->json([]);
        }
        $students = DB::table('students')
            ->where('student_parent', $parent->parent_id)
            ->get();
        $messages = [];
        // Get the latest monthly report month
        $latestReport = DB::table('monthly_reports')->orderByDesc('month')->first();
        $currentMonth = $latestReport ? $latestReport->month : date('Y-m');
        $year = $latestReport ? date('Y', strtotime($currentMonth.'-01')) : date('Y');
        $monthNum = $latestReport ? date('m', strtotime($currentMonth.'-01')) : date('m');
        foreach ($students as $student) {
            $violations = DB::table('violations')
                ->leftJoin('violation_type', 'violations.violation_type_id', '=', 'violation_type.vt_id')
                ->where('student_id', $student->student_id)
                ->whereYear('violations.created_at', $year)
                ->whereMonth('violations.created_at', $monthNum)
                ->select('violation_type.vt_label', 'violations.violation_remarks', 'violations.created_at')
                ->get();
            $violationCount = $violations->count();
            if ($violationCount > 0) {
                $studentName = $student->student_fname . ' ' . $student->student_lname;
                $violationDetails = '';
                foreach ($violations as $v) {
                    $type = $v->vt_label ? $v->vt_label : 'Violation';
                    $remarks = $v->violation_remarks ? $v->violation_remarks : '';
                    $date = date('F j, Y', strtotime($v->created_at));
                    $violationDetails .= "<li><strong>Type:</strong> $type" . ($remarks ? " | <strong>Remarks:</strong> $remarks" : "") . " | <strong>Date:</strong> $date</li>";
                }
                $messages[] = [
                    'message' => "Dear Parent/Guardian, This is to formally inform you that your child, <strong>$studentName</strong>, has committed <strong>$violationCount</strong> violation(s) for the month of <strong>$currentMonth</strong>:<ul>$violationDetails</ul>Please review the details in your parent dashboard and support your child in improving their behavior. If you have any questions, feel free to contact the school guidance office. Thank you for your attention and cooperation. Sincerely, School Administration."
                ];
            }
        }
        return response()->json($messages);
    }
    // Parent Dashboard: Monthly Reports for Linked Students
    public function parentMonthlyReports(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'parent') {
            return response()->json([], 403);
        }
        $parent = DB::table('parents')
            ->join('users', 'users.name', '=', DB::raw('CAST(parents.parent_username AS CHAR)'))
            ->where('users.name', $user->name)
            ->select('parents.*')
            ->first();
        if (!$parent) {
            return response()->json([]);
        }
        $students = DB::table('students')
            ->where('student_parent', $parent->parent_id)
            ->get();
        $reports = [];
        $latestReport = DB::table('monthly_reports')->orderByDesc('month')->first();
        $currentMonth = $latestReport ? $latestReport->month : null;
        $year = $latestReport ? date('Y', strtotime($currentMonth.'-01')) : null;
        $monthNum = $latestReport ? date('m', strtotime($currentMonth.'-01')) : null;
        foreach ($students as $student) {
            $violationCount = null;
            if ($latestReport) {
                $violationCount = DB::table('violations')
                    ->where('student_id', $student->student_id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNum)
                    ->count();
            }
            $reports[] = [
                'student_name' => $student->student_fname . ' ' . $student->student_lname,
                'month' => $currentMonth,
                'violation_count' => $violationCount,
                'message' => $violationCount !== null
                    ? ($violationCount > 0
                        ? "There are $violationCount violation(s) for this month."
                        : "No violations recorded for this month.")
                    : "No monthly report available."
            ];
        }
        return response()->json($reports);
    }
    // Parent Dashboard: Linked Students
    public function parentLinkedStudents(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'parent') {
            return response()->json([], 403);
        }
        // Find parent by username (user.name)
        $parent = DB::table('parents')
            ->join('users', 'users.name', '=', DB::raw('CAST(parents.parent_username AS CHAR)'))
            ->where('users.name', $user->name)
            ->select('parents.*')
            ->first();

        if (!$parent) {
            return response()->json([]);
        }

        // Get all students linked to this parent
        $students = DB::table('students')
            ->where('student_parent', $parent->parent_id)
            ->leftJoin('courses', 'students.student_course', '=', 'courses.course_id')
            ->leftJoin('blocks', 'students.student_block', '=', 'blocks.block_id')
            ->select('students.*', 'courses.course_name', 'blocks.block_name')
            ->get();
        return response()->json($students);
    }
    // Parent Dashboard: Incident Alerts
    public function parentIncidents(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'parent') {
            return response()->json([], 403);
        }
        // Find the student linked to this parent user by username
        $student = DB::table('students')->where('student_number', $user->name)->first();
        if (!$student) {
            return response()->json([]);
        }
        // Find the parent record for this student
        $parent = DB::table('parents')->where('parent_student_id', $student->student_id)->first();
        if (!$parent) {
            return response()->json([]);
        }
        // Get incidents for this student, show if violation_remarks is not empty
        $incidents = DB::table('violations')
            ->leftJoin('violation_type', 'violations.violation_type_id', '=', 'violation_type.vt_id')
            ->where('violations.student_id', $student->student_id)
            ->orderByDesc('violations.created_at')
            ->select('violations.*', 'violation_type.vt_label')
            ->get();
        $result = [];
        $student_full_name = $student->student_fname . ' ' . $student->student_lname;
        foreach ($incidents as $incident) {
            if (!empty($incident->violation_remarks)) {
                $result[] = [
                    'summary' => "Incident: Dear Parent/Guardian, This is to inform you that your child, $student_full_name, has committed the following violation: '" . ($incident->vt_label ?? 'Violation') . "' with the following remarks: '" . $incident->violation_remarks . "' in " . date('F Y', strtotime($incident->created_at)) . ".",
                    'guidance' => 'Guidance: Please talk to your child and support them at home.',
                    'note' => '',
                    'note_visible' => false,
                ];
            }
        }
        return response()->json($result);
    }

    // Parent Dashboard: Behavior Insights (simple summary)
    public function parentBehaviorInsights(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'parent') {
            return response()->json(['summary' => ''], 403);
        }
    $parent = DB::table('parents')->where('parent_id', $user->parent_id ?? 0)->first();
        if (!$parent) {
            return response()->json(['summary' => '']);
        }
    $student = DB::table('students')->where('student_id', $parent->parent_student_id)->first();
        if (!$student) {
            return response()->json(['summary' => '']);
        }
        // Example: count incidents
        $incidentCount = DB::table('violations')
            ->where('student_id', $student->student_id)
            ->where('is_private', 0)
            ->count();
        $summary = $incidentCount > 0
            ? "There have been $incidentCount behavior incident(s) this term. Please review the details above."
            : "No behavior issues reported for your child.";
        return response()->json(['summary' => $summary]);
    }
    // Reset parent password to username (student number)
    public function resetParentPassword(Request $request)
    {
        $username = $request->input('username');
        $user = \App\User::where('name', $username)->orWhere('email', $username)->first();
        if ($user) {
            $user->password = bcrypt($username);
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Password reset to: ' . $username]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Parent user not found.'], 404);
        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role === 'parent') {
            return view('parent_dashboard');
        }
        switch ($request->input('get')) {
            case 'dropped':
                $student_status = 'dropped';
                break;
            case 'graduated':
                $student_status = 'graduated';
                break;
            case 'notactive':
                $student_status = 'notactive';
                break;
            default:
                $student_status = 'active';
                break;
        }
        $students = DB::table('students')
                ->where('student_status', $student_status)
                ->join('courses', 'courses.course_id', '=', 'students.student_course')
                ->join('blocks', 'blocks.block_id', '=', 'students.student_block')
                ->select('students.*', 'courses.*', 'blocks.*')
            ->get();

        $active_students = DB::table('students')
            ->where('student_status', $student_status)
                ->join('courses', 'courses.course_id', '=', 'students.student_course')
                ->join('blocks', 'blocks.block_id', '=', 'students.student_block')
                ->select('students.*', 'courses.*', 'blocks.*')
            ->get();

        $courses = DB::table('courses')
            ->where('course_status', 'active')
            ->get();

        $blocks = DB::table('blocks')
            ->where('block_status', 'active')
            ->get();

        return view('home',
            [
                'students' => $students,
                'courses' => $courses,
                'blocks' => $blocks,
                'active_students' => $active_students
            ]
        );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function messages()
    {
    if(Auth::user()->role === 'admin')
        {                
            $messages = DB::table('contact_us')->get();
            $students = DB::table('students')
                ->where('student_status', 'active')
                ->get();
            return view('messages',
                [
                    'messages' => $messages,
                    'students' => $students
                ]
            );
        }

        return redirect('/home');
    }

    public function addAppointment(Request $request)
    {
        $appointment = new Appointment;
        $data = $request->all();
        return response()->json($appointment->new($data));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function statistics()
    {

        $violations = DB::table('violations')
            ->select('violations.created_at')
            ->get();

        $violationType = DB::table('violation_type')->get();

        // Show only the current year and its months
        $currentYear = date('Y');
        $months = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];
        $labels = [];
        foreach ($months as $mnum => $mlabel) {
            $labels[] = $mlabel . ' ' . $currentYear;
        }

        $violationsDatasetsCntPerMonth = [];
        foreach ($violationType as $vt) {
            $month_array = [];
            foreach (array_keys($months) as $mnum) {
                $count = DB::table('violations')
                    ->where('violation_type_id', $vt->vt_id)
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $mnum)
                    ->count();
                $month_array[] = $count;
            }
            $violationsDatasetsCntPerMonth[] = [
                'label' => $vt->vt_label,
                'data' => $month_array,
                'backgroundColor' => $this->_getColor()
            ];
        }
        return view('statistics', [
            'violations' => $violations,
            'violationsDatasetsCntPerYear' => $violationsDatasetsCntPerMonth,
            'years' => $labels
        ]);
    }

    // Fixed color palette for statistics chart
    private $colorPalette = [
        '#2563eb', '#f59e42', '#e11d48', '#10b981', '#6366f1', '#fbbf24', '#f43f5e', '#14b8a6', '#a21caf', '#f472b6', '#0ea5e9', '#facc15', '#22d3ee', '#64748b', '#d97706', '#be185d', '#059669', '#b91c1c', '#7c3aed', '#eab308'
    ];

    private $colorIndex = 0;
    public function _getColor(){
        $color = $this->colorPalette[$this->colorIndex % count($this->colorPalette)];
        $this->colorIndex++;
        return $color;
    }


    public function _getViolationsPerYear($y){
        return DB::table('violations')
                ->whereYear('created_at', $y)
                ->count();
    }

    public function _getViolationTypePerYear($y, $type){
        $cnt = DB::table('violations')
                ->where('violation_type_id', $type)
                ->whereYear('created_at', $y)
                ->count();
        return $cnt;
    }

    public function addStudent(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'student_number' => 'required|string|unique:students,student_number',
            'student_fname' => 'required|string',
            'student_mname' => 'nullable|string',
            'student_lname' => 'required|string',
            'student_dob' => 'required|date',
            'student_address' => 'required|string',
            'student_course' => 'required|integer|exists:courses,course_id',
            'student_year' => 'required|integer',
            'student_block' => 'required|integer|exists:blocks,block_id',
            'student_status' => 'required|string',
            'student_gender' => 'required|string',
            'student_avatar' => 'nullable|string',
            'student_password' => 'nullable|string',
        ]);
        $student = new Students;
        $data = $request->all();
        // Handle file upload
        if ($request->hasFile('student_photo')) {
            $file = $request->file('student_photo');
            $filename = 'student_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/student_avatars', $filename);
            // Save the public path for display
            $data['student_avatar'] = asset('storage/student_avatars/' . $filename);
        }
        return response()->json($student->new($data));
    }

    public function checkStudent(Request $request)
    {
        $data = $request->all();

        $student = DB::table('students')
            ->where('student_number', $data['q'])
            ->count();

        return response()->json([
            'hit' => $student,
            'result' => $data,
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function student($id)
    {
        $student_status = 'active';
        $student = DB::table('students')
            ->where('student_id', $id)
            ->join('courses', 'courses.course_id', '=', 'students.student_course')
            ->join('blocks', 'blocks.block_id', '=', 'students.student_block')
            ->select('students.*', 'courses.*', 'blocks.*')
            ->first();

        $contacts = DB::table('contacts')
            ->where('contact_student_id', $id)
            ->get();

        // Fetch parent by student_parent if set, otherwise by parent_student_id
        $parents = collect();
        if ($student && isset($student->student_parent) && $student->student_parent > 0) {
            $parent = DB::table('parents')->where('parent_id', $student->student_parent)->get();
            $parents = $parent;
        } else {
            $parents = DB::table('parents')->where('parent_student_id', $id)->get();
        }

        $appointments = DB::table('appointments')
            ->where('appointment_student_id', $id)
            ->get();

        $violations = DB::table('violations')
            ->where('student_id', $id)
            ->join('violation_type', 'violation_type.vt_id', '=', 'violations.violation_type_id')
            ->join('users', 'users.id', '=', 'violations.violation_created_by')
            ->select('violations.id as violation_id', 'violations.*', 'users.*', 'violation_type.vt_id', 'violation_type.vt_label', 'violation_type.vt_code', 'violation_type.vt_desc')
            ->get();

        $violation_type = DB::table('violation_type')->get();
        return view('student',
            [
                'student' => $student,
                'appointments' => $appointments,
                'parents' => $parents,
                'contacts' => $contacts,
                'violations' => $violations,
                'vt' => $violation_type,
                'id' => $id,
            ]
        );
    }

    public function getStudentAppointment($id)
    {
        $appointment = DB::table('appointments')
            ->where('appointment_id', $id)
            ->first();
        return response()->json($appointment);
    }

    public function getStudent($id)
    {
        $student = DB::table('students')
            ->where('student_id', $id)
            ->first();
        return response()->json($student);
    }

    public function updateStudent(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,student_id',
            'student_avatar' => 'nullable|string',
            'student_number' => 'required|string',
            'student_fname' => 'required|string',
            'student_mname' => 'nullable|string',
            'student_lname' => 'required|string',
            'student_dob' => 'required|date',
            'student_address' => 'required|string',
            'student_course' => 'required|integer',
            'student_year' => 'required|integer',
            'student_block' => 'required|integer',
            'student_status' => 'required|string',
            'student_gender' => 'required|string',
            'student_password' => 'nullable|string',
        ]);
        $data = $request->all();
        $updateData = [
            'student_avatar' => $data['student_avatar'],
            'student_number' => $data['student_number'],
            'student_fname' => ucwords($data['student_fname']),
            'student_mname' => ucwords($data['student_mname']),
            'student_lname' => ucwords($data['student_lname']),
            'student_dob' => $data['student_dob'],
            'student_address' => ucwords($data['student_address']),
            'student_course' => $data['student_course'],
            'student_year' => $data['student_year'],
            'student_block' => $data['student_block'],
            'student_status' => $data['student_status'],
            'student_gender' => $data['student_gender']
        ];
        // Only update password if present
        if (isset($data['student_password']) && $data['student_password'] !== null && $data['student_password'] !== '') {
            $updateData['student_password'] = $data['student_password'];
        }
        try {
            $studentExists = DB::table('students')->where('student_id', $data['student_id'])->exists();
            if (!$studentExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student record not found.'
                ], 404);
            }
            $student = DB::table('students')
                ->where('student_id', $data['student_id'])
                ->update($updateData);
            if ($student) {
                $status = 'success';
                $message = 'Student has been updated!';
            } else {
                $status = 'error';
                $message = 'No changes made to student.';
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'An error occurred while updating student.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteStudent($id)
    {
        try {
            // Check if student exists
            $student = DB::table('students')->where('student_id', $id)->first();
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student record not found.'
                ], 404);
            }
            $deleted = DB::table('students')->where('student_id', $id)->delete();
            if ($deleted) {
                $status = 'success';
                $message = 'Student has been deleted.';
            } else {
                $status = 'error';
                $message = 'Failed to delete student.';
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'An error occurred while deleting student.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function addStudentViolation(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,student_id',
            'violation_type_id' => 'required|integer|exists:violation_type,vt_id',
            'violation_remarks' => 'nullable|string',
            'violation_actions' => 'nullable|string',
            'violation_status' => 'nullable|string',
            'violation_created_by' => 'required|integer|exists:users,id',
        ]);
        $violation = new Violations;
        $data = $request->all();
        $result = $violation->new($data);
        return response()->json($result);
    }

    public function getStudentViolation($id)
    {
        $violation = DB::table('violations')
            ->where('id', $id)
            ->first();
        return response()->json($violation);
    }

    public function updateStudentViolation(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'id' => 'required|integer|exists:violations,id',
            'violation_type_id' => 'required|integer',
            'violation_remarks' => 'nullable|string',
            'violation_actions' => 'nullable|string',
            'violation_status' => 'nullable|string',
            'violation_created_by' => 'required|integer',
        ]);
        $data = $request->all();
        $_status = (isset($data['violation_status'])) ? $data['violation_status'] : 'pending';
        try {
            $exists = DB::table('violations')->where('id', $data['id'])->exists();
            if (!$exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Violation record not found.'
                ], 404);
            }
            $violation = DB::table('violations')
                ->where('id', $data['id'])
                ->update([
                    'violation_type_id' => $data['violation_type_id'],
                    'violation_remarks' => $data['violation_remarks'],
                    'violation_actions' => $data['violation_actions'],
                    'violation_status' => $_status,
                    'violation_created_by' => $data['violation_created_by'],
                ]);
            if ($violation) {
                $status = 'success';
                $message = 'Violation has been updated!';
            } else {
                $status = 'error';
                $message = 'No changes made to violation.';
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'An error occurred while updating violation.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteStudentViolation($id)
    {
        try {
            // Check if violation exists
            $violation = DB::table('violations')->where('id', $id)->first();
            if (!$violation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Violation record not found.'
                ], 404);
            }
            $deleted = DB::table('violations')->where('violation_id', $id)->delete();
            if ($deleted) {
                $status = 'success';
                $message = 'Violation has been deleted.';
            } else {
                $status = 'error';
                $message = 'Failed to delete violation.';
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'An error occurred while deleting violation.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function checkViolationCode(Request $request)
    {
        $data = $request->all();

        $code = DB::table('violation_type')
            ->where('vt_code', '=', $data['q'])
            ->count();

        return response()->json([
            'hit' => $code,
            'result' => $data,
        ]);
    }

    public function addParent(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'parent_fname' => 'required|string',
            'parent_mname' => 'nullable|string',
            'parent_lname' => 'required|string',
            'parent_student_id' => 'required|integer|exists:students,student_id',
        ]);
        $parent = new Parents;
        $data = $request->all();
        try {
            $result = $parent->new($data);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding parent.'
            ], 500);
        }
    }

    public function getParent($id)
    {
        $parent = DB::table('parents')
            ->where('parent_id', $id)
            ->first();

        // Find the linked user by student number (parent_username)
        $student = DB::table('students')->where('student_id', $parent->parent_student_id)->first();
        $parentUsername = $student ? $student->student_number : null;
        $user = null;
        if ($parentUsername) {
            $user = DB::table('users')->where('name', $parentUsername)->first();
        }

        // For security, do not return the hashed password. Use username as default password for display.
        $parent->parent_username = $parentUsername;
        $parent->parent_password = $parentUsername;

        return response()->json($parent);
    }

    public function updateParent(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'parent_id' => 'required|integer|exists:parents,parent_id',
            'parent_fname' => 'required|string',
            'parent_mname' => 'nullable|string',
            'parent_lname' => 'required|string',
        ]);
        $data = $request->all();
        try {
            $parentExists = DB::table('parents')->where('parent_id', $data['parent_id'])->exists();
            if (!$parentExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parent record not found.'
                ], 404);
            }
            $parent = DB::table('parents')
                ->where('parent_id', $data['parent_id'])
                ->update([
                    'parent_fname' => $data['parent_fname'],
                    'parent_mname' => $data['parent_mname'],
                    'parent_lname' => $data['parent_lname'],
                ]);
            if ($parent) {
                $status = 'success';
                $message = 'Parent has been updated!';
            } else {
                $status = 'error';
                $message = 'No changes made to parent.';
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'An error occurred while updating parent.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteParent($id)
    {
        try {
            // Check if parent exists
            $parent = DB::table('parents')->where('parent_id', $id)->first();
            if (!$parent) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parent record not found.'
                ], 404);
            }
            $deleted = DB::table('parents')->where('parent_id', $id)->delete();
            if ($deleted) {
                Log::info('deleteParent: Parent deleted', ['parent_id' => $id]);
                $status = 'success';
                $message = 'Parent has been deleted.';
            } else {
                Log::error('deleteParent: Delete failed', ['parent_id' => $id]);
                $status = 'error';
                $message = 'Failed to delete parent.';
            }
        } catch (\Exception $e) {
            Log::error('deleteParent error', ['error' => $e->getMessage(), 'parent_id' => $id]);
            $status = 'error';
            $message = 'An error occurred while deleting parent.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function addContact(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'contact_number_label' => 'required|string',
            'contact_number' => 'required|string',
            'contact_student_id' => 'required|integer|exists:students,student_id',
        ]);
        $contact = new Contacts;
        $data = $request->all();
        try {
            Log::debug('addContact called', ['request' => $data]);
            $result = $contact->new($data);
            Log::debug('addContact result', ['result' => $result]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('addContact error', ['error' => $e->getMessage(), 'data' => $data]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding contact.'
            ], 500);
        }
    }

    public function getContact($id)
    {
        $contact = DB::table('contacts')
            ->where('contact_id', $id)
            ->first();
        return response()->json($contact);
    }

    public function updateContact(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'contact_id' => 'required|integer|exists:contacts,contact_id',
            'contact_number_label' => 'required|string',
            'contact_number' => 'required|string',
        ]);
        $data = $request->all();
        try {
            $contactExists = DB::table('contacts')->where('contact_id', $data['contact_id'])->exists();
            if (!$contactExists) {
                Log::warning('updateContact: Contact not found', ['contact_id' => $data['contact_id']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Contact record not found.'
                ], 404);
            }
            $contact = DB::table('contacts')
                ->where('contact_id', $data['contact_id'])
                ->update([
                    'contact_number_label' => $data['contact_number_label'],
                    'contact_number' => $data['contact_number'],
                ]);
            if ($contact) {
                $status = 'success';
                $message = 'Contact has been updated!';
            } else {
                $status = 'error';
                $message = 'No changes made to contact.';
            }
        } catch (\Exception $e) {
            Log::error('updateContact error', ['error' => $e->getMessage(), 'data' => $data]);
            $status = 'error';
            $message = 'An error occurred while updating contact.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteContact($id)
    {
        try {
            // Check if contact exists
            $contact = DB::table('contacts')->where('contact_id', $id)->first();
            if (!$contact) {
                Log::warning('deleteContact: Contact not found', ['contact_id' => $id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Contact record not found.'
                ], 404);
            }
            $deleted = DB::table('contacts')->where('contact_id', $id)->delete();
            if ($deleted) {
                Log::info('deleteContact: Contact deleted', ['contact_id' => $id]);
                $status = 'success';
                $message = 'Contact has been deleted.';
            } else {
                Log::error('deleteContact: Delete failed', ['contact_id' => $id]);
                $status = 'error';
                $message = 'Failed to delete contact.';
            }
        } catch (\Exception $e) {
            Log::error('deleteContact error', ['error' => $e->getMessage(), 'contact_id' => $id]);
            $status = 'error';
            $message = 'An error occurred while deleting contact.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
    if(Auth::user()->role === 'admin')
        {                
            $users = DB::table('users')->get();
            return view('users',
                [
                    'users' => $users
                ]
            );
        }

        return redirect('/home');
    }

    public function addUser(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);
        $data = $request->all();
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']); 
        $user->role = $data['role']; 
        $user->status = $data['status']; 
        try {
            if($user->save()) {
                Log::info('addUser: User added', ['user_id' => $user->id, 'name' => $user->name]);
                $status = 'success';
                $message = 'User has been added!';
            } else {
                Log::error('addUser: Save failed', ['data' => $data]);
                $status = 'error';
                $message = 'Failed to add user.';
            }
        } catch (\Exception $e) {
            Log::error('addUser error', ['error' => $e->getMessage(), 'data' => $data]);
            $status = 'error';
            $message = 'An error occurred while adding user.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function getUser($id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->first();
        return response()->json($user);
    }

    public function updateUser(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|string',
            'status' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);
        $data = $request->all();
        try {
            $userExists = DB::table('users')->where('id', $data['id'])->exists();
            if (!$userExists) {
                Log::warning('updateUser: User not found', ['user_id' => $data['id']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User record not found.'
                ], 404);
            }
            if($data['password']) {
                $user = DB::table('users')
                    ->where('id', $data['id'])
                    ->update([
                        'name' => $data['name'],
                        'password' => Hash::make($data['password']),
                        'email' => $data['email'],
                        'role' => $data['role'],
                        'status' => $data['status'],
                    ]);
            } else {
                $user = DB::table('users')
                    ->where('id', $data['id'])
                    ->update([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role' => $data['role'],
                        'status' => $data['status'],
                    ]);   
            }
            if($user) {
                Log::info('updateUser: User updated', ['user_id' => $data['id']]);
                $status = 'success';
                $message = 'User has been updated!';
            } else {
                Log::error('updateUser: No changes made', ['user_id' => $data['id']]);
                $status = 'error';
                $message = 'No changes made to user.';
            }
        } catch (\Exception $e) {
            Log::error('updateUser error', ['error' => $e->getMessage(), 'data' => $data]);
            $status = 'error';
            $message = 'An error occurred while updating user.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteUser($id)
    {
        try {
            // Check if user exists
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                Log::warning('deleteUser: User not found', ['user_id' => $id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User record not found.'
                ], 404);
            }
            $deleted = DB::table('users')->where('id', $id)->delete();
            if ($deleted) {
                Log::info('deleteUser: User deleted', ['user_id' => $id]);
                $status = 'success';
                $message = 'User has been deleted.';
            } else {
                Log::error('deleteUser: Delete failed', ['user_id' => $id]);
                $status = 'error';
                $message = 'Failed to delete user.';
            }
        } catch (\Exception $e) {
            Log::error('deleteUser error', ['error' => $e->getMessage(), 'user_id' => $id]);
            $status = 'error';
            $message = 'An error occurred while deleting user.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function violations()
    {
    if(Auth::user()->role === 'admin')
        {     
            $violations = DB::table('violation_type')->get();

            return view('violations',
                [
                    'violations' => $violations
                ]
            );
        }

        return redirect('/home');
    }

    public function addViolation(Request $request)
    {
        $data = $request->all();
    Log::debug('addViolation request data:', $data);
        $violationType = [
            'vt_code' => $data['vt_code'],
            'vt_label' => $data['vt_label'],
            'vt_desc' => $data['vt_desc'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    Log::debug('addViolation insert array:', $violationType);
        try {
            $id = DB::table('violation_type')->insertGetId($violationType);
            Log::debug('addViolation insert ID:', ['id' => $id]);
            return response()->json(['status' => 'success', 'id' => $id]);
        } catch (\Exception $e) {
            Log::error('addViolation error:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getViolation($id)
    {
        $course = DB::table('violation_type')
            ->where('vt_id', $id)
            ->first();
        return response()->json($course);
    }

    public function updateViolation(Request $request)
    {
        $data = $request->all();

        $course = DB::table('violation_type')
            ->where('vt_id', $data['vt_id'])
            ->update([
                'vt_label' => $data['vt_label'],
                'vt_desc' => $data['vt_desc'] ?? '',
                'vt_code' => $data['vt_code'],
            ]);

        if($course)
        {
            $status = 'success';
            $message = 'Violation has been updated!';
        }
        else
        {
            $status = 'error';
            $message = 'Ooopps! Something went wrong!';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteViolation($id)
    {        
        $violation = DB::table('violation_type')
            ->where('vt_id', $id)
            ->delete();

        return response()->json($violation);
    }

    public function getViolators($id)
    {        
        $student_violators = [];
        $raw_violators = DB::table('violations')
            ->where('violations.violation_type_id', (int) $id)
            ->select('violations.*')
            ->get();
        $_new_violators = [];
        foreach ($raw_violators as $key => $value) {
            array_push($_new_violators, $value->student_id);
        }
        $unique = array_unique($_new_violators);

        foreach ($unique as $key => $value) {
            $student = DB::table('students')
                ->where('student_id', (int) $value)
                ->select('students.*')
                ->first();
            array_push($student_violators, $student);
        }
        $violation_type = DB::table('violation_type')
            ->where('vt_id', (int) $id)
            ->select('violation_type.*')
            ->first();
        return view('violators',
            [
                'violation_type' => $violation_type,
                'violators' => $student_violators
            ]
        );
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function course()
    {
    if(Auth::user()->role === 'admin')
        {     
            $courses = DB::table('courses')
                ->where('course_status', 'active')
                ->get();

            return view('course',
                [
                    'courses' => $courses
                ]
            );
        }

        return redirect('/home');
    }

    public function addCourse(Request $request)
    {
        $course = new Courses;
        $data = $request->all();
        return response()->json($course->new($data));
    }

    public function getCourse($id)
    {
        $course = DB::table('courses')
            ->where('course_id', $id)
            ->first();
        return response()->json($course);
    }

    public function updateCourse(Request $request)
    {
        $data = $request->all();
        $course_desc = isset($data['course_desc']) && $data['course_desc'] !== null ? $data['course_desc'] : '';
        $course = DB::table('courses')
            ->where('course_id', $data['course_id'])
            ->update([
                'course_name' => $data['course_name'],
                'course_desc' => $course_desc,
                'course_status' => $data['course_status'],
            ]);

        if($course)
        {
            $status = 'success';
            $message = 'Course has been updated!';
        }
        else
        {
            $status = 'error';
            $message = 'Ooopps! Something went wrong!';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteCourse($id)
    {
        try {
            // Check if course exists
            $course = DB::table('courses')->where('course_id', $id)->first();
            if (!$course) {
                Log::warning('deleteCourse: Course not found', ['course_id' => $id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Course record not found.'
                ], 404);
            }
            $deleted = DB::table('courses')->where('course_id', $id)->delete();
            if ($deleted) {
                Log::info('deleteCourse: Course deleted', ['course_id' => $id]);
                $status = 'success';
                $message = 'Course has been deleted.';
            } else {
                Log::error('deleteCourse: Delete failed', ['course_id' => $id]);
                $status = 'error';
                $message = 'Failed to delete course.';
            }
        } catch (\Exception $e) {
            Log::error('deleteCourse error', ['error' => $e->getMessage(), 'course_id' => $id]);
            $status = 'error';
            $message = 'An error occurred while deleting course.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function block()
    {
    if(Auth::user()->role === 'admin')
        {     
            $blocks = DB::table('blocks')->get();

            return view('block',
                [
                    'blocks' => $blocks
                ]
            );
        }

        return redirect('/home');
    }

    public function addBlock(Request $request)
    {
    Log::debug('addBlock request', $request->all());
        $block = new Blocks;
        $data = $request->all();
        $result = $block->new($data);
    Log::debug('addBlock result', $result);
        return response()->json([
            'request' => $data,
            'result' => $result
        ]);
    }

    public function getBlock($id)
    {
        $course = DB::table('blocks')
            ->where('block_id', $id)
            ->first();
        return response()->json($course);
    }

    public function updateBlock(Request $request)
    {
        $data = $request->all();

        $block = DB::table('blocks')
            ->where('block_id', $data['block_id'])
            ->update([
                'block_name' => $data['block_name'],
                'block_desc' => $data['block_desc'],
                'block_status' => $data['block_status'],
            ]);

        if($block)
        {
            $status = 'success';
            $message = 'Block has been updated!';
        }
        else
        {
            $status = 'error';
            $message = 'Ooopps! Something went wrong!';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteBlock($id)
    {
        try {
            // Check if block exists
            $block = DB::table('blocks')->where('block_id', $id)->first();
            if (!$block) {
                Log::warning('deleteBlock: Block not found', ['block_id' => $id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Block record not found.'
                ], 404);
            }
            $deleted = DB::table('blocks')->where('block_id', $id)->delete();
            if ($deleted) {
                Log::info('deleteBlock: Block deleted', ['block_id' => $id]);
                $status = 'success';
                $message = 'Block has been deleted.';
            } else {
                Log::error('deleteBlock: Delete failed', ['block_id' => $id]);
                $status = 'error';
                $message = 'Failed to delete block.';
            }
        } catch (\Exception $e) {
            Log::error('deleteBlock error', ['error' => $e->getMessage(), 'block_id' => $id]);
            $status = 'error';
            $message = 'An error occurred while deleting block.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
