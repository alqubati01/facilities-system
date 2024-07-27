<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $branches = Branch::filter()->paginate(10);
    return view('content.branches.index', ['branches' => $branches]);
  }

  public function create()
  {
    return view('content.branches.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:branches|max:30',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الفرع مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الفرع نصاً.',
        'name.unique' => 'قيمة حقل اسم الفرع مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الفرع 30 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الفرع إما true أو false.',
      ]);

    $branch = Branch::create($validated);

    return redirect()->route('branches.index')
      ->with('success', 'تم إضافة الفرع بنجاح');
  }

  public function show(string $id)
  {
    return view('content.branches.show', ['branch' => Branch::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.branches.edit', ['branch' => Branch::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $branch = Branch::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:30', Rule::unique(Branch::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الفرع مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الفرع نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الفرع 30 حرفاً.',
        'name.unique' => 'قيمة حقل اسم الفرع مُستخدمة من قبل.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الفرع إما true أو false.',
      ]
    );

    $branch->update($validated);

    return redirect()->route('branches.index')
      ->with('success', 'تم تعديل بيانات الفرع بنجاح');
  }

  public function destroy(string $id)
  {
    $branch = Branch::findOrFail($id);

    if ($branch->facilities()->withTrashed()->count() ||
      $branch->facilitiesByBranch()->withTrashed()->count() ||
      $branch->users()->withTrashed()->count()) {
      return redirect()->route('branches.index')
        ->with('error', 'لا يمكن حذف هذا الفرع، لارتباطه بسجلات في النظام.');
    } else {
      $branch->delete();

      return redirect()->route('branches.index')
        ->with('success', 'تم حذف الفرع بنجاح');
    }
  }
}
