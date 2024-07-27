<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $value = request()->input('identify');
        $field = filter_var($value, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $value]);
        return $field;
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
          $this->username() => 'حقل اسم المستخدم مطلوب.',
          'password.required' => 'حقل كلمة السرر مطلوب.',
        ]);

        $user = User::where($this->username(), '=', $request->input($this->username()))->first();

        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([$this->username() => 'هذا الحساب غير مفعل.']);
        }
    }

  protected function sendFailedLoginResponse(Request $request)
  {
    throw ValidationException::withMessages([
      $this->username() => 'بيانات الاعتماد هذه غير متطابقة مع البيانات المسجلة لدينا.',
    ]);
  }

  protected function authenticated(Request $request, $user)
  {
    if (is_null($user->password_change_at)) {
      return redirect()->route('profile.show', ['profile' => $user->id]);
    }
  }
}
