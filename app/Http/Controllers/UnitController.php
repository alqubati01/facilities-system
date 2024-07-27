<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $units = Unit::filter()->paginate(10);
    return view('content.units.index', ['units' => $units]);
  }

  public function create()
  {
    return view('content.units.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:units|max:50',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الوحدة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الوحدة نصاً.',
        'name.unique' => 'قيمة حقل اسم الوحدة مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الوحدة 50 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الوحدة إما true أو false.',
      ]);

    Unit::create($validated);

    return redirect()->route('units.index')
      ->with('success', 'تم إضافة الوحدة بنجاح');
  }

  public function show(string $id)
  {
    return view('content.units.show', ['unit' => Unit::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.units.edit', ['unit' => Unit::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $unit = Unit::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:50', Rule::unique(Unit::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الوحدة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الوحدة نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الوحدة 50 حرفاً.',
        'name.unique' => 'قيمة حقل اسم الوحدة مُستخدمة من قبل.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الوحدة إما true أو false.',
      ]
    );

    $unit->update($validated);

    return redirect()->route('units.index')
      ->with('success', 'تم تعديل بيانات الوحدة بنجاح');
  }

  public function destroy(string $id)
  {
    $unit = Unit::findOrFail($id);

    if ($unit->facilities()->withTrashed()->count() ||
      $unit->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('units.index')
        ->with('error', 'لا يمكن حذف هذا الوحدة، لارتباطه بسجلات في النظام.');
    } else {
      $unit->delete();

      return redirect()->route('units.index')
        ->with('success', 'تم حذف الوحدة بنجاح');
    }
  }
}
