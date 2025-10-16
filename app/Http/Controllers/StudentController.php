<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// MODELS
use \App\Students;
use \App\Violations;
use \App\ViolationType;
use \App\Message;

class StudentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function addMessage(Request $request)
    {
        $message = new Message;
        $data = $request->all();
        return response()->json($message->new($data));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function inquiry()
    {
        return view('inquiry');
    }

    public function inquiryViolation(Request $request)
    {
        $data = $request->all();
        $_return = [];

        $student = DB::table('students')
            ->where('student_number', $data['student_number'])
            ->where('student_password', $data['student_password'])
            ->first();

        if($student){

            $violations = DB::table('violations')
                ->where('violation_student_id', $student->student_id)
                ->join('violation_type', 'violation_type.vt_id', '=', 'violations.violation_type')
                ->select('violations.*', 'violation_type.vt_id', 'violation_type.vt_label', 'violation_type.vt_code', 'violation_type.vt_desc')
                ->get();

            $_return['status'] = 'success';
            $_return['message'] = 'You have an account!';
            $_return['records'] = $violations;
        }
        else
        {
            $_return['status'] = 'error';
            $_return['message'] = 'Oops! Something went wrong.';
            $_return['records'] = [];
        }

        return response()->json($_return);
    }
}