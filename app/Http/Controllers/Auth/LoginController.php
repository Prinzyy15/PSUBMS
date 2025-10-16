<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class LoginController extends Controller
{
    /**
     * Override the username field used for authentication.
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     * Run a safe repair for parent<->student links so parents see their students on first login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        try {
            if (isset($user->role) && $user->role === 'parent') {
                // Find parent record by matching users.name to parents.parent_username
                $parent = DB::table('parents')
                    ->join('users', 'users.name', '=', DB::raw('CAST(parents.parent_username AS CHAR)'))
                    ->where('users.name', $user->name)
                    ->select('parents.*')
                    ->first();

                if ($parent && !empty($parent->parent_student_id)) {
                    $studentId = (int)$parent->parent_student_id;
                    $student = DB::table('students')->where('student_id', $studentId)->first();
                    if ($student && (empty($student->student_parent) || (int)$student->student_parent === 0)) {
                        DB::table('students')->where('student_id', $studentId)->update(['student_parent' => $parent->parent_id]);
                        Log::info('LoginController: repaired student_parent on parent login', ['parent_id' => $parent->parent_id, 'student_id' => $studentId]);
                    }

                    // Auto-create contact if parent_phone exists and contact not present
                    if (!empty($parent->parent_phone)) {
                        $rawPhone = trim($parent->parent_phone);
                        $normalized = null;
                        try {
                            $phoneUtil = PhoneNumberUtil::getInstance();
                            if (strpos($rawPhone, '+') === 0) {
                                $proto = $phoneUtil->parse($rawPhone, null);
                            } else {
                                // use default region from config (fallback to PH)
                                $defaultRegion = config('phone.default_region', 'PH');
                                $proto = $phoneUtil->parse($rawPhone, $defaultRegion);
                            }
                            if ($phoneUtil->isValidNumber($proto)) {
                                $normalized = $phoneUtil->format($proto, PhoneNumberFormat::E164);
                            }
                        } catch (NumberParseException $e) {
                            Log::warning('LoginController: phone parse failed', ['phone' => $rawPhone, 'error' => $e->getMessage()]);
                        }

                        if ($normalized) {
                            $exists = DB::table('contacts')
                                ->where('contact_student_id', $studentId)
                                ->where('contact_number', $normalized)
                                ->exists();
                            if (!$exists) {
                                DB::table('contacts')->insert([
                                    'contact_number_label' => 'Parent (auto)',
                                    'contact_number' => $normalized,
                                    'contact_student_id' => $studentId,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                Log::info('LoginController: created auto contact on parent login', ['parent_id' => $parent->parent_id, 'student_id' => $studentId, 'phone' => $normalized]);
                            }
                        } else {
                            Log::info('LoginController: parent phone not normalized or invalid; skipping contact creation', ['parent_id' => $parent->parent_id, 'raw' => $rawPhone]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('LoginController: auto-repair failed', ['error' => $e->getMessage(), 'user' => $user->id ?? null]);
        }

        return null; // continue with normal redirect
    }
}
