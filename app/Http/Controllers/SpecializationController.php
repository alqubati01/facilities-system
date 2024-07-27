<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecializationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $specializations = Specialization::filter()->paginate(10);
    return view('content.specializations.index', ['specializations' => $specializations]);
  }

  public function create()
  {
    return view('content.specializations.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:specializations|max:50',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم التخصص مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم التخصص نصاً.',
        'name.unique' => 'قيمة حقل اسم التخصص مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم التخصص 50 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة التخصص إما true أو false.',
      ]);

    Specialization::create($validated);

    return redirect()->route('specializations.index')
      ->with('success', 'تم إضافة التخصص بنجاح');
  }

  public function show(string $id)
  {
    return view('content.specializations.show', ['specialization' => Specialization::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.specializations.edit', ['specialization' => Specialization::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $specialization = Specialization::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:50', Rule::unique(Specialization::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم التخصص مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم التخصص نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم التخصص 50 حرفاً.',
        'name.unique' => 'قيمة حقل اسم التخصص مُستخدمة من قبل.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة التخصص إما true أو false.',
      ]
    );

    $specialization->update($validated);

    return redirect()->route('specializations.index')
      ->with('success', 'تم تعديل بيانات التخصص بنجاح');
  }

  public function destroy(string $id)
  {
    $specialization = Specialization::findOrFail($id);

   if ($specialization->facilities()->withTrashed()->count() ||
      $specialization->facilitiesByBranch()->withTrashed()->count() ||
      $specialization->products()->count()) {
      return redirect()->route('specializations.index')
        ->with('error', 'لا يمكن حذف هذا التخصص، لارتباطه بسجلات في النظام.');
    } else {
     $specialization->delete();

     return redirect()->route('specializations.index')
       ->with('success', 'تم حذف التخصص بنجاح');
   }
  }
}
