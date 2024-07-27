<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $types = Type::filter()->paginate(10);
    return view('content.types.index', ['types' => $types]);
  }

  public function create()
  {
    return view('content.types.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:types|max:30',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم نوع التسهيل مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم نوع التسهيل نصاً.',
        'name.unique' => 'قيمة حقل اسم نوع التسهيل مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم نوع التسهيل 30 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة نوع التسهيل إما true أو false.',
      ]);

    $type = Type::create($validated);

    return redirect()->route('types.index')
      ->with('success', 'تم إضافة نوع التسهيل بنجاح');
  }

  public function show(string $id)
  {
    return view('content.types.show', ['type' => Type::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.types.edit', ['type' => Type::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $type = Type::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:30', Rule::unique(Type::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم نوع التسهيل مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم نوع التسهيل نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم نوع التسهيل 30 حرفاً.',
        'name.unique' => 'قيمة حقل اسم نوع التسهيل مُستخدمة من قبل.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة نوع التسهيل إما true أو false.',
      ]
    );

    $type->update($validated);

    return redirect()->route('types.index')
      ->with('success', 'تم تعديل بيانات نوع التسهيل بنجاح');
  }

  public function destroy(string $id)
  {
    $type = Type::findOrFail($id);

    if ($type->facilities()->withTrashed()->count() ||
      $type->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('types.index')
        ->with('error', 'لا يمكن حذف هذا النوع، لارتباطه بسجلات في النظام.');
    } else {
      $type->delete();

      return redirect()->route('types.index')
        ->with('success', 'تم حذف نوع التسهيل بنجاح');
    }
  }
}
