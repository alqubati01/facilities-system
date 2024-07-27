<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $categories = Category::filter()->paginate(10);
    return view('content.categories.index', ['categories' => $categories]);
  }

  public function create()
  {
    return view('content.categories.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:categories|max:50',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الفئة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الفئة نصاً.',
        'name.unique' => 'قيمة حقل اسم الفئة مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الفئة 50 حرفاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الفئة إما true أو false.',
      ]);

    Category::create($validated);

    return redirect()->route('categories.index')
      ->with('success', 'تم إضافة الفئة بنجاح');
  }

  public function show(string $id)
  {
    return view('content.categories.show', ['category' => Category::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.categories.edit', ['category' => Category::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $category = Category::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:50', Rule::unique(Category::class)->ignore(app('request')->segment(2))],
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الفئة مطلوب.',
        'name.unique' => 'قيمة حقل اسم الفئة مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الفئة 50 حرفاً.',
        'name.string' => 'يجب أن يكون حقل اسم الفئة نصاً.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الفئة إما true أو false.',
      ]
    );

    $category->update($validated);

    return redirect()->route('categories.index')
      ->with('success', 'تم تعديل بيانات الفئة بنجاح');
  }

  public function destroy(string $id)
  {
    $category = Category::findOrFail($id);

    if ($category->facilities()->withTrashed()->count() ||
      $category->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('categories.index')
        ->with('error', 'لا يمكن حذف هذا الفئة، لارتباطه بسجلات في النظام.');
    } else {
      $category->delete();

      return redirect()->route('categories.index')
        ->with('success', 'تم حذف الفئة بنجاح');
    }
  }
}
