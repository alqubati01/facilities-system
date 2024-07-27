<?php

namespace App\Http\Controllers;

use App\Exports\ReportsByBranch\FacilitiesByBranchExport;
use App\Exports\ReportsByBranch\FacilitiesByCategoryExport;
use App\Exports\ReportsByBranch\FacilitiesByProductExport;
use App\Exports\ReportsByBranch\FacilitiesBySpecializationExport;
use App\Exports\ReportsByBranch\FacilitiesByUnitExport;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Specialization;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportByBranchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    return redirect()->route('reportsByBranch.facilitiesByBranch', [
      'date' => now()->startOfMonth()->toDateString() . ' - ' . now()->endOfMonth()->toDateString()
    ]);
  }

  public function facilitiesByBranch(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $date = explode(' - ', $request->date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $branchesReq = [];
    if ($request->branch) {
      $branchesReq = [$request->branch];
    } else {
      $branchesReq = Branch::pluck('id');
    }

    $currenciesReq = [];
    if ($request->currency) {
      $currenciesReq = [$request->currency];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = ($request->groupBy) ?? '';

    $results = DB::table('facilities_by_branches')
      ->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.branch_id', 'branches.name as branch_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.branch_id', $branchesReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.branch_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.branch_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->paginate(10);

    $data = $request->all();
    $branches = Branch::get();
    $currencies = Currency::get();

    return view('content.reportsByBranch.facilitiesByBranch', [
      'results' => $results,
      'data' => $data,
      'branches' => $branches,
      'currencies' => $currencies,
    ]);
  }

  public function facilitiesByBranchExport(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $date = $queryArray['date'];
    $date  = explode(' - ', $date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $branchesReq = [];
    if (array_key_exists('branch', $queryArray)) {
      $branchesReq = [$queryArray['branch']];
    } else {
      $branchesReq = Branch::pluck('id');
    }

    $currenciesReq = [];
    if (array_key_exists('currency', $queryArray)) {
      $currenciesReq = [$queryArray['currency']];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = '';
    if (array_key_exists('groupBy', $queryArray)) {
      $groupByReq = $queryArray['groupBy'];
    }

    $results = DB::table('facilities_by_branches')
      ->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.branch_id', 'branches.name as branch_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.branch_id', $branchesReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.branch_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.branch_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->get();

    return Excel::download(new FacilitiesByBranchExport($results, $groupByReq), 'facilitiesByBranchReport.xlsx');
  }

  public function facilitiesByUnit(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $date = explode(' - ', $request->date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $unitsReq = [];
    if ($request->unit) {
      $unitsReq = [$request->unit];
    } else {
      $unitsReq = Unit::pluck('id');
    }

    $currenciesReq = [];
    if ($request->currency) {
      $currenciesReq = [$request->currency];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = ($request->groupBy) ?? '';

    $results = DB::table('facilities_by_branches')
      ->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.unit_id', 'units.name as unit_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.unit_id', $unitsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.unit_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.unit_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->paginate(10);

    $data = $request->all();
    $units = Unit::get();
    $currencies = Currency::get();

    return view('content.reportsByBranch.facilitiesByUnit', [
      'results' => $results,
      'data' => $data,
      'units' => $units,
      'currencies' => $currencies,
    ]);
  }

  public function facilitiesByUnitExport(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $date = $queryArray['date'];
    $date  = explode(' - ', $date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $unitsReq = [];
    if (array_key_exists('unit', $queryArray)) {
      $unitsReq = [$queryArray['unit']];
    } else {
      $unitsReq = Unit::pluck('id');
    }

    $currenciesReq = [];
    if (array_key_exists('currency', $queryArray)) {
      $currenciesReq = [$queryArray['currency']];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = '';
    if (array_key_exists('groupBy', $queryArray)) {
      $groupByReq = $queryArray['groupBy'];
    }

    $results = DB::table('facilities_by_branches')
      ->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.unit_id', 'units.name as unit_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.unit_id', $unitsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.unit_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.unit_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->get();

    return Excel::download(new FacilitiesByUnitExport($results, $groupByReq), 'facilitiesByUnitReport.xlsx');
  }

  public function facilitiesBySpecialization(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $date  = explode(' - ', $request->date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $specializationsReq = [];
    if ($request->specialization) {
      $specializationsReq = [$request->specialization];
    } else {
      $specializationsReq = Specialization::pluck('id');
    }

    $currenciesReq = [];
    if ($request->currency) {
      $currenciesReq = [$request->currency];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = ($request->groupBy) ?? '';

    $results = DB::table('facilities_by_branches')
      ->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.specialization_id', 'specializations.name as specialization_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.specialization_id', $specializationsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.specialization_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.specialization_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->paginate(10);

    $data = $request->all();
    $specializations = Specialization::get();
    $currencies = Currency::get();

    return view('content.reportsByBranch.facilitiesBySpecialization', [
      'results' => $results,
      'data' => $data,
      'specializations' => $specializations,
      'currencies' => $currencies,
    ]);
  }

  public function facilitiesBySpecializationExport(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $date = $queryArray['date'];
    $date  = explode(' - ', $date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $specializationsReq = [];
    if (array_key_exists('specialization', $queryArray)) {
      $specializationsReq = [$queryArray['specialization']];
    } else {
      $specializationsReq = Specialization::pluck('id');
    }

    $currenciesReq = [];
    if (array_key_exists('currency', $queryArray)) {
      $currenciesReq = [$queryArray['currency']];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = '';
    if (array_key_exists('groupBy', $queryArray)) {
      $groupByReq = $queryArray['groupBy'];
    }

    $results = DB::table('facilities_by_branches')
      ->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.specialization_id', 'specializations.name as specialization_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else if($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.specialization_id', $specializationsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.specialization_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.specialization_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->get();

    return Excel::download(new FacilitiesBySpecializationExport($results, $groupByReq), 'facilitiesBySpecializationReport.xlsx');
  }

  public function facilitiesByCategory(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $date  = explode(' - ', $request->date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $categoriesReq = [];
    if ($request->category) {
      $categoriesReq = [$request->category];
    } else {
      $categoriesReq = Category::pluck('id');
    }

    $currenciesReq = [];
    if ($request->currency) {
      $currenciesReq = [$request->currency];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = ($request->groupBy) ?? '';

    $results = DB::table('facilities_by_branches')
      ->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.category_id', 'categories.name as category_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.category_id', $categoriesReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.category_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.category_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->paginate(10);

    $data = $request->all();
    $categories = Category::get();
    $currencies = Currency::get();

    return view('content.reportsByBranch.facilitiesByCategory', [
      'results' => $results,
      'data' => $data,
      'categories' => $categories,
      'currencies' => $currencies,
    ]);
  }

  public function facilitiesByCategoryExport(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $date = $queryArray['date'];
    $date  = explode(' - ', $date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $categoriesReq = [];
    if (array_key_exists('category', $queryArray)) {
      $categoriesReq = [$queryArray['category']];
    } else {
      $categoriesReq = Category::pluck('id');
    }

    $currenciesReq = [];
    if (array_key_exists('currency', $queryArray)) {
      $currenciesReq = [$queryArray['currency']];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = '';
    if (array_key_exists('groupBy', $queryArray)) {
      $groupByReq = $queryArray['groupBy'];
    }

    $results = DB::table('facilities_by_branches')
      ->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('facilities_by_branches.category_id', 'categories.name as category_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'products') {
          return $q->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
            ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
            ->addSelect('products.id', 'products.name as product_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('facilities_by_branches.category_id', $categoriesReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('facilities_by_branches.category_id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'products') {
          return $q->groupBy('products.id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('facilities_by_branches.category_id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->get();

    return Excel::download(new FacilitiesByCategoryExport($results, $groupByReq), 'facilitiesByCategoryReport.xlsx');
  }

  public function facilitiesByProduct(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $date  = explode(' - ', $request->date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $productsReq = [];
    if ($request->product) {
      $productsReq = [$request->product];
    } else {
      $productsReq = Product::pluck('id');
    }

    $currenciesReq = [];
    if ($request->currency) {
      $currenciesReq = [$request->currency];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = ($request->groupBy) ?? '';

    $results = DB::table('facilities_by_branches')
      ->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
      ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('products.id', 'products.name as product_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('products.id', $productsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('products.id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('products.id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->paginate(10);

    $data = $request->all();
    $products = Product::get();
    $currencies = Currency::get();

    return view('content.reportsByBranch.facilitiesByProduct', [
      'results' => $results,
      'data' => $data,
      'products' => $products,
      'currencies' => $currencies,
    ]);
  }

  public function facilitiesByProductExport(Request $request)
  {
    if (!auth()->user()->can('show reports')) {
      abort(403);
    }

    $queryString = $request->query('data');
    parse_str($queryString, $queryArray);

    $date = $queryArray['date'];
    $date  = explode(' - ', $date);
    $dateFrom = $date[0];
    $dateTo = $date[1];

    $productsReq = [];
    if (array_key_exists('product', $queryArray)) {
      $productsReq = [$queryArray['product']];
    } else {
      $productsReq = Product::pluck('id');
    }

    $currenciesReq = [];
    if (array_key_exists('currency', $queryArray)) {
      $currenciesReq = [$queryArray['currency']];
    } else {
      $currenciesReq = Currency::pluck('id');
    }

    $groupByReq = '';
    if (array_key_exists('groupBy', $queryArray)) {
      $groupByReq = $queryArray['groupBy'];
    }

    $results = DB::table('facilities_by_branches')
      ->join('facility_by_branch_product', 'facilities_by_branches.id', '=', 'facility_by_branch_product.facility_id')
      ->join('products', 'facility_by_branch_product.product_id', '=', 'products.id')
      ->join('currencies', 'facilities_by_branches.currency_id', '=', 'currencies.id')
      ->select('products.id', 'products.name as product_name',
        'facilities_by_branches.currency_id', 'currencies.name as currency_name', 'currencies.symbol', DB::raw('SUM(amount) as total')
      )->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
            ->addSelect('facilities_by_branches.branch_id', 'branches.name as branch_name');
        }
        else if ($groupByReq === 'units') {
          return $q->join('units', 'facilities_by_branches.unit_id', '=', 'units.id')
            ->addSelect('facilities_by_branches.unit_id', 'units.name as unit_name');
        }
        else if ($groupByReq === 'specializations') {
          return $q->join('specializations', 'facilities_by_branches.specialization_id', '=', 'specializations.id')
            ->addSelect('facilities_by_branches.specialization_id', 'specializations.name as specialization_name');
        }
        else if ($groupByReq === 'categories') {
          return $q->join('categories', 'facilities_by_branches.category_id', '=', 'categories.id')
            ->addSelect('facilities_by_branches.category_id', 'categories.name as category_name');
        }
        else {
          return $q;
        }
      })
      ->whereIn('products.id', $productsReq)
      ->whereIn('facilities_by_branches.currency_id', $currenciesReq)
      ->where('facilities_by_branches.status_id', '=', 3)
      ->whereNull('facilities_by_branches.deleted_at')
      ->where('facilities_by_branches.date', '>=', $dateFrom)
      ->where('facilities_by_branches.date', '<=', $dateTo)
      ->groupBy('products.id', 'facilities_by_branches.currency_id')
      ->when($groupByReq, function ($q, $groupByReq) {
        if ($groupByReq === 'branches') {
          return $q->groupBy('facilities_by_branches.branch_id');
        }
        else if ($groupByReq === 'units') {
          return $q->groupBy('facilities_by_branches.unit_id');
        }
        else if ($groupByReq === 'specializations') {
          return $q->groupBy('facilities_by_branches.specialization_id');
        }
        else if ($groupByReq === 'categories') {
          return $q->groupBy('facilities_by_branches.category_id');
        }
        else {
          return $q;
        }
      })
      ->orderBy('products.id', 'asc')
      ->orderBy('facilities_by_branches.currency_id', 'asc')
      ->get();

    return Excel::download(new FacilitiesByProductExport($results, $groupByReq), 'facilitiesByProductReport.xlsx');
  }
}
