<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $users = User::filter()->orderBy('id')->paginate(10);
    $data = $request->all();
    $roles = Role::all();

    return view('content.users.index', [
      'users' => $users,
      'data' => $data,
      'roles' => $roles,
    ]);
  }

  public function create()
  {
    $roles = Role::all();

    return view('content.users.create', ['roles' => $roles]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users'],
        'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
        'job_title' => ['nullable', 'string', 'max:255'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
      ],
      [
        'name.required' => 'حقل الاسم مطلوب.',
        'name.string' => 'يجب أن يكون حقل الاسم نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل الاسم 255 حرفاً.',
        'username.required' => 'حقل اسم المستخدم مطلوب.',
        'username.string' => 'يجب أن يكون حقل اسم المستخدم نصاً.',
        'username.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم المستخدم 255 حرفاً.',
        'username.unique' => 'قيمة حقل اسم المستخدم مُستخدمة من قبل.',
        'email.string' => 'يجب أن يكون حقل البريد الإلكتروني نصاً.',
        'email.email' => 'يجب أن يكون حقل الإيميل عنوان بريد إلكتروني صحيح البُنية.',
        'email.max' => 'يجب أن لا يتجاوز طول نّص حقل البريد الإلكتروني 255 حرفاً.',
        'email.unique' => 'قيمة حقل البريد الإلكتروني مُستخدمة من قبل.',
        'job_title.string' => 'يجب أن يكون حقل المسمى الوظيفي نصاً.',
        'job_title.max' => 'يجب أن لا يتجاوز طول نّص حقل المسمى الوظيفي 255 حرفاً.',
        'password.required' => 'حقل كلمة المرور مطلوب.',
        'password.string' => 'يجب أن يكون حقل كلمة المرور نصاً.',
        'password.min' => 'يجب أن لا يقل طول نّص حقل كلمة المرور 8 أحرف.',
        'password.confirmed' => 'حقل التأكيد غير مُطابق للحقل كلمة المرور.',
      ]);

    $user = new User();
    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->job_title = $request->job_title;
    $user->password = Hash::make($request->password);
    $user->save();
    $user->roles()->sync($request->role);

    return redirect()->route('users.index')
      ->with('success', 'تم أضافة المستخدم بنجاح');
  }

  public function show(string $id)
  {
    return view('content.users.show', ['user' => User::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    $user = User::with('branches')->findOrFail($id);
    $roles = Role::all();
    $branches = Branch::where('is_active', 1)->get();

    return view('content.users.edit', [
      'user' => $user,
      'roles' => $roles,
      'branches' => $branches,
    ]);
  }

  public function update(Request $request, string $id)
  {
    $user = User::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore(app('request')->segment(2))],
        'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore(app('request')->segment(2))],
        'job_title' => ['nullable', 'string', 'max:255'],
        'is_active' => ['required', 'boolean'],
      ],
      [
        'name.required' => 'حقل الاسم مطلوب.',
        'name.string' => 'يجب أن يكون حقل الاسم نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل الاسم 255 حرفاً.',
        'username.required' => 'حقل اسم المستخدم مطلوب.',
        'username.string' => 'يجب أن يكون حقل اسم المستخدم نصاً.',
        'username.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم المستخدم 255 حرفاً.',
        'username.unique' => 'قيمة حقل اسم المستخدم مُستخدمة من قبل.',
        'email.string' => 'يجب أن يكون حقل البريد الإلكتروني نصاً.',
        'email.email' => 'يجب أن يكون حقل الإيميل عنوان بريد إلكتروني صحيح البُنية.',
        'email.max' => 'يجب أن لا يتجاوز طول نّص حقل البريد الإلكتروني 255 حرفاً.',
        'email.unique' => 'قيمة حقل البريد الإلكتروني مُستخدمة من قبل.',
        'job_title.string' => 'يجب أن يكون حقل المسمى الوظيفي نصاً.',
        'job_title.max' => 'يجب أن لا يتجاوز طول نّص حقل المسمى الوظيفي 255 حرفاً.',
        'is_active.required' => 'حقل حالة المستخدم مطلوب.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة المستخدم 1 أو 0.',
      ]);

    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->job_title = $request->job_title;
    $user->is_active = $request->is_active;
    $user->save();

    $user->roles()->sync($request->role);

    return redirect()->route('users.index')
      ->with('success', 'تم تعديل بيانات المستخدم بنجاح');
  }

  public function updatePass(Request $request, string $id)
  {
    $user = User::findOrFail($id);

    $validated = $request->validate(
      [
        'password' => ['required', 'string', 'min:8', 'confirmed',],
      ],
      [
        'password.required' => 'حقل كلمة المرور مطلوب.',
        'password.string' => 'يجب أن يكون حقل كلمة المرور نصاً.',
        'password.confirmed' => 'حقل التأكيد غير مُطابق للحقل كلمة المرور.',
        'password.min' => 'يجب أن لا يقل طول نّص حقل كلمة المرور 8 أحرف.',
      ]);

    $user->password = Hash::make($request->password);
    $user->password_change_at = null;
    $user->save();

    return redirect()->route('users.index')
      ->with('success', 'تم تعديل كلمة مرور المستخدم بنجاح');
  }

  public function userBranches(Request $request, string $id)
  {
    $user = User::findOrFail($id);
    $user->branches()->sync($request->branches);

    return redirect()->route('users.index')
      ->with('success', 'تم تعديل بيانات الفروع للمستخدم بنجاح');
  }

  public function destroy(string $id)
  {
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')
      ->with('success', 'تم حذف المستخدم بنجاح');
  }
}
