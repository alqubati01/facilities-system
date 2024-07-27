<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $products = Product::with('specialization')->filter()->paginate(10);

    $data = $request->all();
    $specializations = Specialization::where('is_active', 1)->get();

    return view('content.products.index', [
      'products' => $products,
      'data' => $data,
      'specializations' => $specializations,
    ]);
  }

  public function create()
  {
    $specializations = Specialization::where('is_active', 1)->get();

    return view('content.products.create', ['specializations' => $specializations]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:products|max:100',
        'specialization_id' => 'integer',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الصنف مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الصنف نصاً.',
        'name.unique' => 'قيمة حقل اسم الصنف مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الصنف 100 حرفاً.',
        'specialization_id.integer' => 'يجب أن يكون حقل تخصص الصنف عددًا صحيحًا.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الصنف إما true أو false.',
      ]
    );

    Product::create($validated);

    return redirect()->route('products.index')
      ->with('success', 'تم إضافة الصنف بنجاح');
  }

  public function show(string $id)
  {
    return view('content.products.show', [
      'product' => Product::with('specialization')->findOrFail($id)
    ]);
  }

  public function edit(string $id)
  {
    $specializations = Specialization::where('is_active', 1)->get();

    return view('content.products.edit', [
      'product' => Product::with('specialization')->findOrFail($id),
      'specializations' => $specializations
    ]);
  }

  public function update(Request $request, string $id)
  {
    $product = Product::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:100', Rule::unique(Product::class)->ignore(app('request')->segment(2))],
        'specialization_id' => 'integer',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم الصنف مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم الصنف نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم الصنف 100 حرفاً.',
        'name.unique' => 'قيمة حقل اسم الصنف مُستخدمة من قبل.',
        'specialization_id.integer' => 'يجب أن يكون حقل تخصص الصنف عددًا صحيحًا.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة الصنف إما true أو false.',
      ]);

    $product->update($validated);

    return redirect()->route('products.index')
      ->with('success', 'تم تعديل بيانات الصنف بنجاح');
  }

  public function destroy(string $id)
  {
    $product = Product::findOrFail($id);

    if ($product->facilities()->withTrashed()->count() ||
      $product->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('products.index')
        ->with('error', 'لا يمكن حذف هذا الصنف، لارتباطه بسجلات في النظام.');
    } else {
      $product->delete();

      return redirect()->route('products.index')
        ->with('success', 'تم حذف الصنف بنجاح');
    }
  }
}
