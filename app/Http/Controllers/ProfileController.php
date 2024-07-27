<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function show(string $id)
  {
    $user = User::with('roles', 'branches')->findOrFail($id);

    if (auth()->user()->id !== $user->id) {
      abort(403);
    }

    return view('content.profile.show', ['user' => $user]);
  }

  public function changePassword(Request $request, string $id)
  {
    $user = User::findOrFail($id);

    $validated = $request->validate(
      [
        'old_password' => ['required',
          function ($attribute, $value, $fail) use ($user) {
            if (!Hash::check($value, $user->password)) {
              $fail('كلمة المرور غير متطابقة معا الحالية.');
            }
          }
        ],
        'new_password' => ['required', 'string', 'min:8', 'confirmed', 'different:old_password'],
      ],
      [
        'old_password.required' => 'قم بإدخال كلمة السر',
        'new_password.required' => 'قم بإدخال كلمة السر الجديدة',
        'new_password.string' => 'كلمة السر لا بد أن يتكون من الأحرف فقط',
        'new_password.min' => 'يجب أن لا تكون القيمة أقل من 8',
        'new_password.confirmed' => 'غير مطابق لكلمة السر.',
        'new_password.different' => 'نفس كلمة المرور الحالية.',
      ]
    );

    $user->password_change_at = now();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('profile.show', ['profile' => $user->id])
      ->with('success', 'تم تعديل كلمة المرور بنجاح');
  }
}
