<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityByBranch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
      if (!auth()->user()->can('manage dashboard')) {
        return redirect()->route('facilities.create');
      }

      // basics data
      $number_of_facilities_by_management = Facility::count();
      $number_of_facilities_by_branches = FacilityByBranch::count();
      $new_facilities = Facility::where('created_at', '>=', now()->subDays(7))->count();
      $products_count = Product::count();

      // facilities chart by management
      $facilities_by_management = DB::table('facilities')
        ->join('branches', 'facilities.branch_id', '=', 'branches.id')
        ->selectRaw('count(facilities.id) as number_of_facilities, branches.name')
        ->where('facilities.created_at', '>=', now()->subDays(120))
        ->orderBy('branch_id', 'asc')
        ->groupBy('branch_id')
        ->get();

      $facilities_by_management_branch_name = [];
      $facilities_by_management_branch_count = [];
      foreach ($facilities_by_management as $by_management) {
        $facilities_by_management_branch_name[] = $by_management->name;
        $facilities_by_management_branch_count[] = $by_management->number_of_facilities;
      }

      // facilities chart by currencies
      $number_of_facilities_by_date = Facility::where('created_at', '>=', now()->subDays(120))->count();
      $facilities_by_management_currencies = DB::table('facilities')
        ->join('currencies', 'facilities.currency_id', '=', 'currencies.id')
        ->selectRaw('count(facilities.id) as number_of_facilities, currencies.name')
        ->where('facilities.created_at',  '>=', now()->subDays(120))
        ->orderBy('currency_id', 'asc')
        ->groupBy('currency_id')
        ->get();

      $facilities_by_management_currency_name = [];
      $facilities_by_management_currency_count = [];
      foreach ($facilities_by_management_currencies as $by_currency) {
        $facilities_by_management_currency_name[] = $by_currency->name;
        $facilities_by_management_currency_count[] = $by_currency->number_of_facilities;
      }

      // facilities chart by branch
      $facilities_by_branches = DB::table('facilities_by_branches')
        ->join('branches', 'facilities_by_branches.branch_id', '=', 'branches.id')
        ->selectRaw('count(facilities_by_branches.id) as number_of_facilities, branches.name')
        ->where('facilities_by_branches.created_at', '>=', now()->subDays(120))
        ->orderBy('branch_id', 'asc')
        ->groupBy('branch_id')
        ->get();

      $facilities_by_branches_branch_name = [];
      $facilities_by_branches_branch_count = [];
      foreach ($facilities_by_branches as $by_branch) {
        $facilities_by_branches_branch_name[] = $by_branch->name;
        $facilities_by_branches_branch_count[] = $by_branch->number_of_facilities;
      }

      // new facilities of last 7 days
      $facilities = Facility::with('branch', 'unit', 'currency', 'type', 'specialization', 'category', 'products', 'status')
        ->where('created_at', '>=', now()->subDays(7))
        ->paginate(7);

      return view('home', [
        'number_of_facilities_by_management' => $number_of_facilities_by_management,
        'number_of_facilities_by_branches' => $number_of_facilities_by_branches,
        'new_facilities' => $new_facilities,
        'products_count' => $products_count,
        'facilities_by_management_branch_name' => $facilities_by_management_branch_name,
        'facilities_by_management_branch_count' => $facilities_by_management_branch_count,
        'facilities_by_management_currency_name' => $facilities_by_management_currency_name,
        'facilities_by_management_currency_count' => $facilities_by_management_currency_count,
        'number_of_facilities_by_date' => $number_of_facilities_by_date,
        'facilities_by_branches_branch_name' => $facilities_by_branches_branch_name,
        'facilities_by_branches_branch_count' => $facilities_by_branches_branch_count,
        'facilities' => $facilities,
      ]);
    }
}
