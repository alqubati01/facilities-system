<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $currencies = Currency::filter()->paginate(10);
    return view('content.currencies.index', ['currencies' => $currencies]);
  }

  public function create()
  {
    return view('content.currencies.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'name' => 'required|string|unique:currencies|max:30',
        'code' => 'required|string|max:3',
        'symbol' => 'required|string|max:5',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم العملة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم العملة نصاً.',
        'name.unique' => 'قيمة حقل اسم العملة مُستخدمة من قبل.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم العملة 30 حرفاً.',
        'code.required' => 'حقل الكود مطلوب.',
        'code.string' => 'يجب أن يكون حقل الكود نصاً.',
        'code.max' => 'يجب أن لا يتجاوز طول نّص حقل الكود 3 أحرف.',
        'symbol.required' => 'حقل الرمز مطلوب.',
        'symbol.string' => 'يجب أن يكون حقل الرمز نصاً.',
        'symbol.max' => 'يجب أن لا يتجاوز طول نّص حقل الرمز 5 أحرف.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة العملة إما true أو false.',
      ]);

    Currency::create($validated);

    return redirect()->route('currencies.index')
      ->with('success', 'تم إضافة العملة بنجاح');
  }

  public function show(string $id)
  {
    return view('content.currencies.show', ['currency' => Currency::findOrFail($id)]);
  }

  public function edit(string $id)
  {
    return view('content.currencies.edit', ['currency' => Currency::findOrFail($id)]);
  }

  public function update(Request $request, string $id)
  {
    $currency = Currency::findOrFail($id);

    $validated = $request->validate(
      [
        'name' => ['required', 'string', 'max:30', Rule::unique(Currency::class)->ignore(app('request')->segment(2))],
        'code' => 'required|string|max:3',
        'symbol' => 'required|string|max:5',
        'is_active' => 'boolean',
      ],
      [
        'name.required' => 'حقل اسم العملة مطلوب.',
        'name.string' => 'يجب أن يكون حقل اسم العملة نصاً.',
        'name.max' => 'يجب أن لا يتجاوز طول نّص حقل اسم العملة 30 حرفاً.',
        'name.unique' => 'قيمة حقل اسم العملة مُستخدمة من قبل.',
        'code.required' => 'حقل الكود مطلوب.',
        'code.string' => 'يجب أن يكون حقل الكود نصاً.',
        'code.max' => 'يجب أن لا يتجاوز طول نّص حقل الكود 3 أحرف.',
        'symbol.required' => 'حقل الرمز مطلوب.',
        'symbol.string' => 'يجب أن يكون حقل الرمز نصاً.',
        'symbol.max' => 'يجب أن لا يتجاوز طول نّص حقل الرمز 5 أحرف.',
        'is_active.boolean' => 'يجب أن تكون قيمة حقل حالة العملة إما true أو false.',
      ]);

    $currency->update($validated);

    return redirect()->route('currencies.index')
      ->with('success', 'تم تعديل بيانات العملة بنجاح');
  }

  public function destroy(string $id)
  {
    $currency = Currency::findOrFail($id);

    if ($currency->facilities()->withTrashed()->count() ||
      $currency->facilitiesByBranch()->withTrashed()->count()) {
      return redirect()->route('currencies.index')
        ->with('error', 'لا يمكن حذف هذا العملة، لارتباطه بسجلات في النظام.');
    } else {
      $currency->delete();

      return redirect()->route('currencies.index')
        ->with('success', 'تم حذف العملة بنجاح');
    }
  }
}
