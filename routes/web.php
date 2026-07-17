<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth:manager,salesmen'])
    ->name('dashboard');

Route::get('/dashboard/data', [App\Http\Controllers\DashboardController::class, 'getChartData'])
    ->middleware(['auth:manager,salesmen'])
    ->name('dashboard.data');

Route::middleware('auth:manager,salesmen')->group(function () {
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::get('/api/promotions', [App\Http\Controllers\PromotionController::class, 'apiIndex'])->name('api.promotions');
    Route::get('/promotions/{promotion}/bundle-items', [App\Http\Controllers\SaleController::class, 'getBundleItems'])->name('promotions.bundle-items');
    Route::resource('promotions', App\Http\Controllers\PromotionController::class);
    Route::get('/reports/salesmen/{id}', [App\Http\Controllers\ReportController::class, 'salesmenFinanceReport'])->name('reports.salesmen');
    Route::get('/reports/salesman/{id}', [App\Http\Controllers\ReportController::class, 'salesmenFinanceReport'])->name('reports.salesman');
    Route::get('/reports/salesmen/{id}/export', [App\Http\Controllers\ReportController::class, 'exportSalesmenReport'])->name('reports.salesmen.export');
    Route::get('/reports/salesman/{id}/export', [App\Http\Controllers\ReportController::class, 'exportSalesmenReport'])->name('reports.salesman.export');
});

// Manager profile routes
Route::middleware('auth:manager')->group(function () {
    Route::get('/manager/profile', [App\Http\Controllers\ManagerProfileController::class, 'edit'])->name('manager.profile.edit');
    Route::patch('/manager/profile', [App\Http\Controllers\ManagerProfileController::class, 'update'])->name('manager.profile.update');
    Route::delete('/manager/profile', [App\Http\Controllers\ManagerProfileController::class, 'destroy'])->name('manager.profile.destroy');

    Route::resource('accounts', App\Http\Controllers\AccountController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    
    Route::post('/promotions/{promotion}/approve', [App\Http\Controllers\PromotionController::class, 'approve'])->name('promotions.approve');
    Route::post('/promotions/{promotion}/reject', [App\Http\Controllers\PromotionController::class, 'reject'])->name('promotions.reject');

    Route::post('/sales/{sale}/approve', [App\Http\Controllers\SaleController::class, 'approve'])->name('sales.approve');
    Route::post('/sales/{sale}/reject', [App\Http\Controllers\SaleController::class, 'reject'])->name('sales.reject');
    
    Route::get('/analysis', [App\Http\Controllers\AnalysisController::class, 'index'])->name('analysis.index');
    Route::post('/promotions/ajax-store', [App\Http\Controllers\PromotionController::class, 'ajaxStore'])->name('promotions.ajax-store');


    // Reports routes
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales/export', [App\Http\Controllers\ReportController::class, 'exportSales'])->name('reports.sales.export');
    Route::get('/reports/promotions/export', [App\Http\Controllers\ReportController::class, 'exportPromotions'])->name('reports.promotions.export');
    Route::get('/reports/apriori/export', [App\Http\Controllers\ReportController::class, 'exportApriori'])->name('reports.apriori.export');
    Route::post('/reports/salesmen/{id}/approve-pending', [App\Http\Controllers\ReportController::class, 'approvePendingSales'])->name('reports.salesmen.approve-pending');
    Route::post('/reports/salesmen/{id}/approve-selected', [App\Http\Controllers\ReportController::class, 'approveSelectedSales'])->name('reports.salesmen.approve-selected');
});

// Salesmen profile routes
Route::middleware('auth:salesmen')->group(function () {
    Route::get('/salesmen/profile', [App\Http\Controllers\SalesmenProfileController::class, 'edit'])->name('salesmen.profile.edit');
    Route::patch('/salesmen/profile', [App\Http\Controllers\SalesmenProfileController::class, 'update'])->name('salesmen.profile.update');
    Route::delete('/salesmen/profile', [App\Http\Controllers\SalesmenProfileController::class, 'destroy'])->name('salesmen.profile.destroy');

    // Staff read-only items catalogue
    Route::get('/salesmen/items', [App\Http\Controllers\SalesmenItemController::class, 'index'])->name('salesmen.items.index');

    // Personal sales report routes
    Route::get('/reports/salesmen-personal', [App\Http\Controllers\SalesmenReportController::class, 'index'])->name('reports.salesmen_personal.index');
    Route::get('/reports/salesmen-personal/export', [App\Http\Controllers\SalesmenReportController::class, 'export'])->name('reports.salesmen_personal.export');
});

require __DIR__.'/auth.php';
