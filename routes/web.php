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
    ->middleware(['auth:manager,salesman'])
    ->name('dashboard');

Route::get('/dashboard/data', [App\Http\Controllers\DashboardController::class, 'getChartData'])
    ->middleware(['auth:manager,salesman'])
    ->name('dashboard.data');

Route::middleware('auth:manager,salesman')->group(function () {
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::get('/promotions/{promotion}/bundle-items', [App\Http\Controllers\SaleController::class, 'getBundleItems'])->name('promotions.bundle-items');
    Route::resource('promotions', App\Http\Controllers\PromotionController::class);
    Route::get('/reports/salesman/{id}', [App\Http\Controllers\ReportController::class, 'salesmanFinanceReport'])->name('reports.salesman');
    Route::get('/reports/salesman/{id}/export', [App\Http\Controllers\ReportController::class, 'exportSalesmanReport'])->name('reports.salesman.export');
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
    Route::get('/analysis/weka', [App\Http\Controllers\AnalysisController::class, 'wekaIndex'])->name('analysis.weka');
    Route::post('/analysis/weka/run', [App\Http\Controllers\AnalysisController::class, 'runApriori'])->name('analysis.weka.run');
    Route::get('/analysis/weka/all-rules', [App\Http\Controllers\AnalysisController::class, 'allRules'])->name('analysis.weka.allRules');

    // Reports routes
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales/export', [App\Http\Controllers\ReportController::class, 'exportSales'])->name('reports.sales.export');
    Route::get('/reports/promotions/export', [App\Http\Controllers\ReportController::class, 'exportPromotions'])->name('reports.promotions.export');
    Route::get('/reports/apriori/export', [App\Http\Controllers\ReportController::class, 'exportApriori'])->name('reports.apriori.export');
    Route::post('/reports/salesman/{id}/approve-pending', [App\Http\Controllers\ReportController::class, 'approvePendingSales'])->name('reports.salesman.approve-pending');
    Route::post('/reports/salesman/{id}/approve-selected', [App\Http\Controllers\ReportController::class, 'approveSelectedSales'])->name('reports.salesman.approve-selected');
});

// Salesman profile routes
Route::middleware('auth:salesman')->group(function () {
    Route::get('/salesman/profile', [App\Http\Controllers\SalesmanProfileController::class, 'edit'])->name('salesman.profile.edit');
    Route::patch('/salesman/profile', [App\Http\Controllers\SalesmanProfileController::class, 'update'])->name('salesman.profile.update');
    Route::delete('/salesman/profile', [App\Http\Controllers\SalesmanProfileController::class, 'destroy'])->name('salesman.profile.destroy');

    // Staff read-only items catalogue
    Route::get('/salesman/items', [App\Http\Controllers\SalesmanItemController::class, 'index'])->name('salesman.items.index');

    // Personal sales report routes
    Route::get('/reports/salesman-personal', [App\Http\Controllers\SalesmanReportController::class, 'index'])->name('reports.salesman_personal.index');
    Route::get('/reports/salesman-personal/export', [App\Http\Controllers\SalesmanReportController::class, 'export'])->name('reports.salesman_personal.export');
});

require __DIR__.'/auth.php';
