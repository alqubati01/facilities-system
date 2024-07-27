<?php

namespace App\Http\Controllers;

use App\Exports\FacilitiesByBranchExport;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Currency;
use App\Models\FacilityByBranch;
use App\Models\Product;
use App\Models\Specialization;
use App\Models\Status;
use App\Models\Type;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class FacilityByBranchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    if (!auth()->user()->can('show facility')) {
      abort(403);
    }

    $facilities = FacilityByBranch::with('branch', 'unit', 'currency', 'status')
      ->whereHas('branch.users', function ($q) {
        $q->where('users.id', auth()->user()->id);
      })
      ->filter()
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    $data = $request->all();
    $branches = Branch::where('is_active', 1)
      ->whereHas('users', function ($q) {
        $q->where('users.id', auth()->user()->id);
      })->get();
    $units = Unit::where('is_active', 1)->get();
    $currencies = Currency::where('is_active', 1)->get();
    $specializations = Specialization::where('is_active', 1)->get();
    $categories = Category::where('is_active', 1)->get();
    $products = Product::where('is_active', 1)->get();
    $statuses = Status::where('is_active', 1)->get();

    return view('content.facilitiesByBranches.index', [
      'facilities' => $facilities,
      'data' => $data,
      'branches' => $branches,
      'units' => $units,
      'currencies' => $currencies,
      'specializations' => $specializations,
      'categories' => $categories,
      'products' => $products,
      'statuses' => $statuses,
    ]);
  }

  public function create()
  {
    if (!auth()->user()->can('create facility')) {
      abort(403);
    }

    $max_facility_number = FacilityByBranch::withTrashed()->where('year', now()->year)->max('facility_number');
    $branches = Branch::where('is_active', 1)
      ->whereHas('users', function ($q) {
        $q->where('users.id', auth()->user()->id);
      })->get();
    $units = Unit::where('is_active', 1)->get();
    $currencies = Currency::where('is_active', 1)->get();
    $types = Type::where('is_active', 1)->get();
    $specializations = Specialization::where('is_active', 1)->get();
    $categories = Category::where('is_active', 1)->get();
    $products = Product::where('is_active', 1)->get();

    return view('content.facilitiesByBranches.create', [
      'max_facility_number' => $max_facility_number,
      'branches' => $branches,
      'units' => $units,
      'currencies' => $currencies,
      'types' => $types,
      'specializations' => $specializations,
      'categories' => $categories,
      'products' => $products,
    ]);
  }

  public function store(Request $request)
  {
    if (!auth()->user()->can('create facility')) {
      abort(403);
    }

    $validated = $request->validate(
      [
//        'facility_number' => 'required|unique:facilities_by_branches|integer',
//        'facility_number' => ['required', 'integer',
//          Rule::unique(FacilityByBranch::class)->where(function ($query) use ($request) {
//            return $query->where('facility_number', $request->facility_number)
//              ->where('year', now()->year);
//          }),
//        ],
        'date' => 'required|date',
        'branch' => 'required|integer',
        'unit' => 'required|integer',
        'currency' => 'required|integer',
        'amount' => 'required|integer',
        'amount_in_writing' => 'required|string',
        'type' => 'required|integer',
        'details' => 'nullable|string|max:80',
        'recipient' => 'required|string|max:255',
        'specialization' => 'required|integer',
        'category' => 'required|integer',
        'reason_type' => 'required|integer',
        'reason' => 'nullable|string|max:255',
        'products' => 'array',
        'neighboring_customers' => 'nullable|string|max:255',
      ],
      [
//        'facility_number.required' => 'حقل رقم التسهيل مطلوب.',
//        'facility_number.unique' => 'قيمة حقل رقم التسهيل مُستخدمة من قبل.',
//        'facility_number.integer' => 'يجب أن يكون حقل رقم التسهيل عددًا صحيحًا.',
        'date.required' => 'حقل التاريخ مطلوب.',
        'date.date' => 'حقل التاريخ ليس تاريخًا صحيحًا.',
        'branch.required' => 'حقل الفرع مطلوب.',
        'branch.integer' => 'يجب أن يكون حقل الفرع عددًا صحيحًا.',
        'unit.required' => 'حقل الوحدة مطلوب.',
        'unit.integer' => 'يجب أن يكون حقل الوحدة عددًا صحيحًا.',
        'currency.required' => 'حقل العملة مطلوب.',
        'currency.integer' => 'يجب أن يكون حقل العملة عددًا صحيحًا.',
        'amount.required' => 'حقل المبلغ مطلوب.',
        'amount.integer' => 'يجب أن يكون حقل المبلغ عددًا صحيحًا.',
        'amount_in_writing.required' => 'حقل المبلغ كتابة مطلوب.',
        'amount_in_writing.string' => 'يجب أن يكون حقل المبلغ كتابة نصاً.',
        'type.required' => 'حقل النوع مطلوب.',
        'type.integer' => 'يجب أن يكون حقل النوع عددًا صحيحًا.',
        'details.string' => 'يجب أن يكون حقل التفصيل نصاً.',
        'details.max' => 'يجب أن لا يتجاوز طول نّص حقل التفصيل 80 حرفاً.',
        'recipient.required' => 'حقل المستفيدون مطلوب.',
        'recipient.string' => 'يجب أن يكون حقل المستفيدون نصاً.',
        'recipient.max' => 'يجب أن لا يتجاوز طول نّص حقل المستفيدون 255 حرفاً.',
        'specialization.required' => 'حقل التخصص مطلوب.',
        'specialization.integer' => 'يجب أن يكون حقل التخصص عددًا صحيحًا.',
        'category.required' => 'حقل الفئة مطلوب.',
        'category.integer' => 'يجب أن يكون حقل الفئة عددًا صحيحًا.',
        'reason_type.required' => 'حقل نوع السبب مطلوب.',
        'reason_type.integer' => 'يجب أن يكون حقل نوع السبب عددًا صحيحًا.',
        'reason.string' => 'يجب أن يكون حقل السبب نصاً.',
        'reason.max' => 'يجب أن لا يتجاوز طول نّص حقل السبب 255 حرفاً.',
        'products.array' => 'يجب أن يكون حقل الأصناف مصفوفة.',
        'neighboring_customers.string' => 'يجب أن يكون حقل العملاء المجاورين نصاً.',
        'neighboring_customers.max' => 'يجب أن لا يتجاوز طول نّص حقل العملاء المجاورين 255 حرفاً.',
      ]);

    $max_facility_number = FacilityByBranch::withTrashed()->where('year', now()->year)->max('facility_number');

    $facility = new FacilityByBranch();
    $facility->facility_number = $max_facility_number + 1;
    $facility->year = now()->year;
    $facility->date = $request->date;
    $facility->branch_id = $request->branch;
    $facility->unit_id = $request->unit;
    $facility->currency_id = $request->currency;
    $facility->amount = $request->amount;
    $facility->amount_in_writing = $request->amount_in_writing;
    $facility->type_id = $request->type;
    $facility->details = $request->details;
    $facility->recipient = $request->recipient;
    $facility->specialization_id = $request->specialization;
    $facility->category_id = $request->category;
    $facility->reason_type = $request->reason_type;
    if ($request->reason_type == '2') { // 2 => mean others
      $facility->reason = $request->reason;
    }
    $facility->neighboring_customers = $request->neighboring_customers;
    $facility->status_id = 3;
    $facility->created_by = auth()->user()->id;
    $facility->updated_at = null;
    $facility->save();

    if ($request->has('products')) {
      $facility->products()->sync($request->products);
    }

    return redirect()->route('facilitiesByBranch.show', ['facilitiesByBranch' => $facility->id])
      ->with('success', 'تم أضافة التسهيل بنجاح');
  }

  public function show(string $id)
  {
    $userBranches = Branch::whereHas('users', function ($q) {
      $q->where('users.id', auth()->user()->id);
    })->pluck('id')->toArray();

    $facilityBranch = FacilityByBranch::with('branch')->findOrFail($id);

    if (!in_array($facilityBranch->branch->id, $userBranches)) {
//      abort(403);
      return redirect()->route('facilitiesByBranch.index')
        ->with('error', 'لا يمكنك الوصول لهذا التسهيل.');
    }

    $facility = FacilityByBranch
      ::with('branch', 'unit', 'currency', 'type', 'specialization', 'category', 'products', 'status', 'createdBy', 'updatedBy')
      ->findOrFail($id);

    return view('content.facilitiesByBranches.show', ['facility' => $facility]);
  }

  public function edit(string $id)
  {
    if (!auth()->user()->can('edit facility')) {
      abort(403);
    }

    $userBranches = Branch::whereHas('users', function ($q) {
      $q->where('users.id', auth()->user()->id);
    })->pluck('id')->toArray();

    $facilityBranch = FacilityByBranch::with('branch')->findOrFail($id);

    if (!in_array($facilityBranch->branch->id, $userBranches)) {
//      abort(403);
      return redirect()->route('facilitiesByBranch.index')
        ->with('error', 'لا يمكنك الوصول لهذا التسهيل.');
    }

    $facility = FacilityByBranch::findOrFail($id);

    if ($facility->status_id == 1) {
      $branches = Branch::where('is_active', 1)
        ->whereHas('users', function ($q) {
          $q->where('users.id', auth()->user()->id);
        })->get();
      $units = Unit::where('is_active', 1)->get();
      $currencies = Currency::where('is_active', 1)->get();
      $types = Type::where('is_active', 1)->get();
      $specializations = Specialization::where('is_active', 1)->get();
      $categories = Category::where('is_active', 1)->get();
      $products = Product::where('is_active', 1)->get();
      $statuses = Status::where('is_active', 1)->get();

      return view('content.facilitiesByBranches.edit', [
        'facility' => $facility,
        'branches' => $branches,
        'units' => $units,
        'currencies' => $currencies,
        'types' => $types,
        'specializations' => $specializations,
        'categories' => $categories,
        'products' => $products,
        'statuses' => $statuses,
      ]);
    } else if (Auth::user()->roles[0]->id == 1) {
      $branches = Branch::where('is_active', 1)
        ->whereHas('users', function ($q) {
          $q->where('users.id', auth()->user()->id);
        })->get();
      $units = Unit::where('is_active', 1)->get();
      $currencies = Currency::where('is_active', 1)->get();
      $types = Type::where('is_active', 1)->get();
      $specializations = Specialization::where('is_active', 1)->get();
      $categories = Category::where('is_active', 1)->get();
      $products = Product::where('is_active', 1)->get();
      $statuses = Status::where('is_active', 1)->get();

      return view('content.facilitiesByBranches.edit', [
        'facility' => $facility,
        'branches' => $branches,
        'units' => $units,
        'currencies' => $currencies,
        'types' => $types,
        'specializations' => $specializations,
        'categories' => $categories,
        'products' => $products,
        'statuses' => $statuses,
      ]);
    } else if ($facility->status_id != 1) {
      return redirect()->route('facilitiesByBranch.show', ['facilitiesByBranch' => $id])
        ->with('error', 'لا يمكن تعديل هذا التسهيل.');
    }
  }

  public function update(Request $request, string $id)
  {
    if (!auth()->user()->can('edit facility')) {
      abort(403);
    }

    $facility = FacilityByBranch::findOrFail($id);

    $validated = $request->validate(
      [
        'facility_number' => [
          'required',
          'integer',
          Rule::unique(FacilityByBranch::class)->where(function ($query) use ($request) {
            return $query->where('facility_number', $request->facility_number)->where('year', now()->year);
          })->ignore(app('request')->segment(2)),
        ],
        'date' => 'required|date',
        'branch' => 'required|integer',
        'unit' => 'required|integer',
        'currency' => 'required|integer',
        'amount' => 'required|integer',
        'amount_in_writing' => 'required|string',
        'type' => 'required|integer',
        'details' => 'nullable|string|max:80',
        'recipient' => 'required|string|max:255',
        'specialization' => 'required|integer',
        'category' => 'required|integer',
        'reason_type' => 'required|integer',
        'reason' => 'nullable|string|max:255',
        'products' => 'array',
        'neighboring_customers' => 'nullable|string|max:255',
      ],
      [
        'facility_number.required' => 'حقل رقم التسهيل مطلوب.',
        'facility_number.integer' => 'يجب أن يكون حقل رقم التسهيل عددًا صحيحًا.',
        'facility_number.unique' => 'قيمة حقل رقم التسهيل مُستخدمة من قبل.',
        'date.required' => 'حقل التاريخ مطلوب.',
        'date.date' => 'حقل التاريخ ليس تاريخًا صحيحًا.',
        'branch.required' => 'حقل الفرع مطلوب.',
        'branch.integer' => 'يجب أن يكون حقل الفرع عددًا صحيحًا.',
        'unit.required' => 'حقل الوحدة مطلوب.',
        'unit.integer' => 'يجب أن يكون حقل الوحدة عددًا صحيحًا.',
        'currency.required' => 'حقل العملة مطلوب.',
        'currency.integer' => 'يجب أن يكون حقل العملة عددًا صحيحًا.',
        'amount.required' => 'حقل المبلغ مطلوب.',
        'amount.integer' => 'يجب أن يكون حقل المبلغ عددًا صحيحًا.',
        'amount_in_writing.required' => 'حقل المبلغ كتابة مطلوب.',
        'amount_in_writing.string' => 'يجب أن يكون حقل المبلغ كتابة نصاً.',
        'type.required' => 'حقل النوع مطلوب.',
        'type.integer' => 'يجب أن يكون حقل النوع عددًا صحيحًا.',
        'details.string' => 'يجب أن يكون حقل التفصيل نصاً.',
        'details.max' => 'يجب أن لا يتجاوز طول نّص حقل التفصيل 80 حرفاً.',
        'recipient.required' => 'حقل المستفيدون مطلوب.',
        'recipient.string' => 'يجب أن يكون حقل المستفيدون نصاً.',
        'recipient.max' => 'يجب أن لا يتجاوز طول نّص حقل المستفيدون 255 حرفاً.',
        'specialization.required' => 'حقل التخصص مطلوب.',
        'specialization.integer' => 'يجب أن يكون حقل التخصص عددًا صحيحًا.',
        'category.required' => 'حقل الفئة مطلوب.',
        'category.integer' => 'يجب أن يكون حقل الفئة عددًا صحيحًا.',
        'reason_type.required' => 'حقل نوع السبب مطلوب.',
        'reason_type.integer' => 'يجب أن يكون حقل نوع السبب عددًا صحيحًا.',
        'reason.string' => 'يجب أن يكون حقل السبب نصاً.',
        'reason.max' => 'يجب أن لا يتجاوز طول نّص حقل السبب 255 حرفاً.',
        'products.array' => 'يجب أن يكون حقل الأصناف مصفوفة.',
        'neighboring_customers.string' => 'يجب أن يكون حقل العملاء المجاورين نصاً.',
        'neighboring_customers.max' => 'يجب أن لا يتجاوز طول نّص حقل العملاء المجاورين 255 حرفاً.',
      ]);

    $facility->facility_number = $request->facility_number;
    $facility->year = now()->year;
    $facility->date = $request->date;
    $facility->branch_id = $request->branch;
    $facility->unit_id = $request->unit;
    $facility->currency_id = $request->currency;
    $facility->amount = $request->amount;
    $facility->amount_in_writing = $request->amount_in_writing;
    $facility->type_id = $request->type;
    $facility->details = $request->details;
    $facility->recipient = $request->recipient;
    $facility->specialization_id = $request->specialization;
    $facility->category_id = $request->category;
    $facility->reason_type = $request->reason_type;
    if ($request->reason_type == '2') { // 2 => mean others
      $facility->reason = $request->reason;
    } else {
      $facility->reason = null;
    }
    $facility->neighboring_customers = $request->neighboring_customers;
    $facility->status_id = 3;
    $facility->updated_by = auth()->user()->id;
    $facility->save();

    if ($request->reason_type == '1') { // 1 => mean others
      if ($request->has('products')) {
        $facility->products()->sync($request->products);
      }
    } else {
      $facility->products()->detach($request->products);
    }

    return redirect()->route('facilitiesByBranch.show', ['facilitiesByBranch' => $facility->id])
      ->with('success', 'تم تعديل بيانات التسهيل بنجاح');
  }

  public function updateStatus(Request $request, string $id)
  {
    if (!auth()->user()->can('edit facility status')) {
      abort(403);
    }

    $facility = FacilityByBranch::findOrFail($id);

    $validated = $request->validate([
      'status' => 'required|integer',
    ], [
      'status.required' => 'حقل الحالة مطلوب.',
      'status.integer' => 'يجب أن يكون حقل الحالة عددًا صحيحًا.',
    ]);

    $facility->status_id = $request->status;
    $facility->save();

    return redirect()->route('facilitiesByBranch.show', ['facilitiesByBranch' => $facility->id])
      ->with('success', 'تم تعديل حالة التسهيل بنجاح');
  }

  public function destroy(string $id)
  {
    if (!auth()->user()->can('delete facility')) {
      abort(403);
    }

    $facility = FacilityByBranch::findOrFail($id);
    $facility->delete();

    return redirect()->route('facilitiesByBranch.index')
      ->with('success', 'تم حذف التسهيل بنجاح');
  }

  public function export(Request $request)
  {
    if (!auth()->user()->can('export facilities')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $userBranches = Branch::where('is_active', 1)
      ->whereHas('users', function ($q) {
        $q->where('users.id', auth()->user()->id);
      })->pluck('id')->toArray();

    $facilities = FacilityByBranch::with('branch', 'unit', 'currency', 'type', 'specialization', 'category', 'products', 'status')
      ->whereIn('branch_id', $userBranches)
      ->filterExport()
      ->get();

    return Excel::download(new FacilitiesByBranchExport($facilities), 'facilitiesByBranch.xlsx');
  }

  public function exportFacility(string $id)
  {
    $userBranches = Branch::whereHas('users', function ($q) {
      $q->where('users.id', auth()->user()->id);
    })->pluck('id')->toArray();

    $facilityByBranch = FacilityByBranch::with('branch', 'currency', 'type', 'specialization', 'products')->findOrFail($id);

    if (!in_array($facilityByBranch->branch->id, $userBranches)) {
      abort(403);
    }

    $fileName = Str::limit($facilityByBranch->recipient, 30);
    $fileBranch = $facilityByBranch->branch->name;
    $pdf = PDF::loadView('content.facilitiesByBranches.exportFacility', ['facility' => $facilityByBranch]);

    // return $pdf->stream($fileName . ' - ' . $fileBranch . '.pdf');
    return $pdf->download($fileName . ' - ' . $fileBranch . '.pdf');
  }

}
