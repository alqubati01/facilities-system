<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
  public function index()
  {
    $roles = Role::paginate(10);

    return view('content.roles.index', ['roles' => $roles]);
  }

  public function create()
  {
    return view('content.roles.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:roles|max:50',
        'slug' => 'required|string|unique:roles|max:50',
      ],
      [
        'name.required' => 'حقل اسم الدور مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الدور نصاً.',
        'name.unique' => 'قيمة حقل اسم الدور مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الدور 50 حرفاً.',
        'slug.required' => 'حقل تسمية الدور مطلوب.',
        'slug.string' => 'يجب أن يكون حقل تسمية الدور نصاً.',
        'slug.unique' => 'قيمة حقل تسمية الدور مُستخدمة من قبل.',
        'slug.max' => 'يجب أن لا يتجاوز طول نّص حقل تسمية الدور 50 حرفاً.',
      ]);

    Role::create($validated);

    return redirect()->route('roles.index')
      ->with('success', 'تم إضافة الدور بنجاح');
  }

  public function show(string $id)
  {
    $role = Role::with('permission')->findOrFail($id);
    $permissions = Permission::all();

    return view('content.roles.show', ['role' => $role, 'permissions' => $permissions]);
  }

  public function edit(string $id)
  {
    return view('content.roles.edit', ['role' => Role::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $role = Role::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:50', Rule::unique(Role::class)->ignore(app('request')->segment(2))],
        'slug' => ['required', 'string', 'max:50', Rule::unique(Role::class)->ignore(app('request')->segment(2))],
      ],
      [
        'name.required' => 'حقل اسم الدور مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الدور نصاً.',
        'name.unique' => 'قيمة حقل اسم الدور مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الدور 50 حرفاً.',
        'slug.required' => 'حقل تسمية الدور مطلوب.',
        'slug.string' => 'يجب أن يكون حقل تسمية الدور نصاً.',
        'slug.unique' => 'قيمة حقل تسمية الدور مُستخدمة من قبل.',
        'slug.max' => 'يجب أن لا يتجاوز طول نّص حقل تسمية الدور 50 حرفاً.',
      ]
    );

    $role->update($validated);

    return redirect()->route('roles.index')
      ->with('success', 'تم تعديل بيانات الدور بنجاح');
  }

  public function destroy(string $id)
  {
    $role = Role::findOrFail($id);

    if ($role->users()->withTrashed()->count()) {
      return redirect()->route('roles.index')
        ->with('error', 'لا يمكن حذف هذا الدور، لارتباطه بسجلات في النظام.');
    } else {
      $role->delete();

      return redirect()->route('roles.index')
        ->with('success', 'تم حذف الدور بنجاح');
    }
  }

  public function rolePermissions(Request $request, string $id)
  {
    $role = Role::findOrFail($id);
    $role->permission()->sync($request->permissions);

    return redirect()->route('roles.index')
      ->with('success', 'تم تعديل بيانات صلاحية الدور بنجاح');
  }
}
