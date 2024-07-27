<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatusController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $statuses = Status::filter()->paginate(10);
    return view('content.statuses.index', ['statuses' => $statuses]);
  }

  public function create()
  {
    return view('content.statuses.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:statuses|max:30',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الحالة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الحالة نصاً.',
        'name.unique' => 'قيمة حقل اسم الحالة مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الحالة 30 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة التسهيل إما true أو false.',
      ]);

    Status::create($validated);

    return redirect()->route('statuses.index')
      ->with('success', 'تم إضافة الحالة بنجاح');
  }

  public function show(string $id)
  {
    return view('content.statuses.show', ['status' => Status::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.statuses.edit', ['status' => Status::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $status = Status::findOrFail($id);
    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:30', Rule::unique(Status::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الحالة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الحالة نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الحالة 30 حرفاً.',
        'name.unique' => 'قيمة حقل اسم الحالة مُستخدمة من قبل.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة التسهيل إما true أو false.',
      ]
    );

    $status->update($validated);

    return redirect()->route('statuses.index')
      ->with('success', 'تم تعديل بيانات الحالة بنجاح');
  }

  public function destroy(string $id)
  {
    $status = Status::findOrFail($id);

    if ($status->facilities()->withTrashed()->count() ||
      $status->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('statuses.index')
        ->with('error', 'لا يمكن حذف هذا الحالة، لارتباطه بسجلات في النظام.');
    } else {
      $status->delete();

      return redirect()->route('statuses.index')
        ->with('success', 'تم حذف الحالة بنجاح');
    }
  }
}
