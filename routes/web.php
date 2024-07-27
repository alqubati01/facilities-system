<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['firewall.all', 'checkStatus'])->group(function () {
//Auth::routes();
  Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
  Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Home and Profile Routes
  Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
  Route::get('/profile/{profile}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
  Route::put('/profile/{profile}', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.changePassword');


// Settings Routes
  Route::group(['middleware' => 'role:admin'], function () {
    Route::resource('branches', \App\Http\Controllers\BranchController::class);
    Route::resource('types', \App\Http\Controllers\TypeController::class);
    Route::resource('currencies', \App\Http\Controllers\CurrencyController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('specializations', \App\Http\Controllers\SpecializationController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('units', \App\Http\Controllers\UnitController::class);
    Route::resource('statuses', \App\Http\Controllers\StatusController::class);

    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::put('/role-permissions/{role}', [\App\Http\Controllers\RoleController::class, 'rolePermissions'])->name('role.permissions');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::put('/users-updatePass/{user}', [\App\Http\Controllers\UserController::class, 'updatePass'])->name('users.updatePass');
    Route::put('/users-branches/{user}', [\App\Http\Controllers\UserController::class, 'userBranches'])->name('users.branches');

    Route::get('firewalls', [\App\Http\Controllers\FirewallController::class, 'index'])->name('firewalls.index');
    Route::put('firewalls-blocked/{firewall}', [\App\Http\Controllers\FirewallController::class, 'blocked'])->name('firewalls.blocked');
    Route::put('firewalls-unblocked/{firewall}', [\App\Http\Controllers\FirewallController::class, 'unblocked'])->name('firewalls.unblocked');

    // Route::get('backup', function () {
    //   var_dump(openssl_get_cert_locations());

    //   \Illuminate\Support\Facades\Storage::disk('google')->put('hello.txt', 'Hello World');
    // });

  });

// Facilities Routes
  Route::get('facilities/export', [\App\Http\Controllers\FacilityController::class, 'export'])->name('facilities.export');
  Route::get('facilities/{facility}/export', [\App\Http\Controllers\FacilityController::class, 'exportFacility'])->name('facilities.exportFacility');
  Route::resource('facilities', \App\Http\Controllers\FacilityController::class);
  Route::put('facilitiesUpdateStatus/{facility}', [\App\Http\Controllers\FacilityController::class, 'updateStatus'])->name('facilities.updateStatus');

// Facilities By Branch Routes
  Route::get('facilitiesByBranch/export', [\App\Http\Controllers\FacilityByBranchController::class, 'export'])->name('facilitiesByBranch.export');
  Route::get('facilitiesByBranch/{facilitiesByBranch}/export', [\App\Http\Controllers\FacilityByBranchController::class, 'exportFacility'])->name('facilitiesByBranch.exportFacility');
  Route::resource('facilitiesByBranch', \App\Http\Controllers\FacilityByBranchController::class);
  Route::put('facilitiesByBranchUpdateStatus/{facilitiesByBranch}', [\App\Http\Controllers\FacilityByBranchController::class, 'updateStatus'])->name('facilitiesByBranch.updateStatus');

// Reports Facilities Routes
  Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
  Route::get('reports/facilities-by-branch', [\App\Http\Controllers\ReportController::class, 'facilitiesByBranch'])->name('reports.facilitiesByBranch');
  Route::get('reports/facilities-by-branch/export', [\App\Http\Controllers\ReportController::class, 'facilitiesByBranchExport'])->name('reports.facilitiesByBranchExport');
  Route::get('reports/facilities-by-unit', [\App\Http\Controllers\ReportController::class, 'facilitiesByUnit'])->name('reports.facilitiesByUnit');
  Route::get('reports/facilities-by-unit/export', [\App\Http\Controllers\ReportController::class, 'facilitiesByUnitExport'])->name('reports.facilitiesByUnitExport');
  Route::get('reports/facilities-by-specialization', [\App\Http\Controllers\ReportController::class, 'facilitiesBySpecialization'])->name('reports.facilitiesBySpecialization');
  Route::get('reports/facilities-by-specialization/export', [\App\Http\Controllers\ReportController::class, 'facilitiesBySpecializationExport'])->name('reports.facilitiesBySpecializationExport');
  Route::get('reports/facilities-by-category', [\App\Http\Controllers\ReportController::class, 'facilitiesByCategory'])->name('reports.facilitiesByCategory');
  Route::get('reports/facilities-by-category/export', [\App\Http\Controllers\ReportController::class, 'facilitiesByCategoryExport'])->name('reports.facilitiesByCategoryExport');
  Route::get('reports/facilities-by-product', [\App\Http\Controllers\ReportController::class, 'facilitiesByProduct'])->name('reports.facilitiesByProduct');
  Route::get('reports/facilities-by-product/export', [\App\Http\Controllers\ReportController::class, 'facilitiesByProductExport'])->name('reports.facilitiesByProductExport');

// Reports By Branch Facilities Routes
  Route::get('reportsByBranch', [\App\Http\Controllers\ReportByBranchController::class, 'index'])->name('reportsByBranch.index');
  Route::get('reportsByBranch/facilities-by-branch', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByBranch'])->name('reportsByBranch.facilitiesByBranch');
  Route::get('reportsByBranch/facilities-by-branch/export', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByBranchExport'])->name('reportsByBranch.facilitiesByBranchExport');
  Route::get('reportsByBranch/facilities-by-unit', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByUnit'])->name('reportsByBranch.facilitiesByUnit');
  Route::get('reportsByBranch/facilities-by-unit/export', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByUnitExport'])->name('reportsByBranch.facilitiesByUnitExport');
  Route::get('reportsByBranch/facilities-by-specialization', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesBySpecialization'])->name('reportsByBranch.facilitiesBySpecialization');
  Route::get('reportsByBranch/facilities-by-specialization/export', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesBySpecializationExport'])->name('reportsByBranch.facilitiesBySpecializationExport');
  Route::get('reportsByBranch/facilities-by-category', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByCategory'])->name('reportsByBranch.facilitiesByCategory');
  Route::get('reportsByBranch/facilities-by-category/export', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByCategoryExport'])->name('reportsByBranch.facilitiesByCategoryExport');
  Route::get('reportsByBranch/facilities-by-product', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByProduct'])->name('reportsByBranch.facilitiesByProduct');
  Route::get('reportsByBranch/facilities-by-product/export', [\App\Http\Controllers\ReportByBranchController::class, 'facilitiesByProductExport'])->name('reportsByBranch.facilitiesByProductExport');

});
