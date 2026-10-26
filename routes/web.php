<?php
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LoanReportController;
use App\Http\Controllers\Admin\LoanRequestController;
use App\Http\Controllers\Admin\MemberAccountController;
use App\Http\Controllers\Member\AuthController as MemberAuthController;
use App\Http\Controllers\Member\CatalogController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\FineController as MemberFineController;
use App\Http\Controllers\Member\LoanRequestController as MemberLoanRequestController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\RegistrationController;
use App\Models\Catalog\Category;
use App\Models\Catalog\Item;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = \Illuminate\Support\Facades\Schema::hasTable('categories')
        ? Category::where('is_archived', false)->get()
        : collect();
    $items = \Illuminate\Support\Facades\Schema::hasTable('items')
        ? Item::with('categories')
            ->where('status', '!=', Item::ARCHIVED)
            ->orderBy('id', 'asc')
            ->take(6)
            ->get()
        : collect();
    return view('welcome', compact('categories', 'items'));
})->name('home');

Route::prefix('admin')->name('admin.')->group(function (): void {
 Route::get('/', fn () => redirect()->route('admin.dashboard'));
 Route::get('/login',[AuthController::class,'showLoginForm'])->name('login.view');
 Route::post('/login',[AuthController::class,'login'])->middleware('throttle:10,1')->name('login');
 Route::match(['get','post'],'/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
 Route::middleware(['auth','admin'])->group(function (): void {
  Route::get('/dashboard',DashboardController::class)->name('dashboard');
  Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
  Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
  Route::get('/categories/{category}',[CategoryController::class,'show'])->name('categories.show');
  Route::patch('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
  Route::patch('/categories/{category}/archive',[CategoryController::class,'archive'])->name('categories.archive');
  Route::get('/items',[ItemController::class,'index'])->name('items.index');
  Route::post('/items',[ItemController::class,'store'])->name('items.store');
  Route::get('/items/{item}',[ItemController::class,'show'])->name('items.show');
  Route::patch('/items/{item}',[ItemController::class,'update'])->name('items.update');
  Route::patch('/items/{item}/archive',[ItemController::class,'archive'])->name('items.archive');
  Route::get('/members',[MemberAccountController::class,'index'])->name('members.index');
  Route::post('/members',[MemberAccountController::class,'store'])->name('members.store');
  Route::get('/members/{member}',[MemberAccountController::class,'show'])->name('members.show');
  Route::patch('/members/{member}',[MemberAccountController::class,'update'])->name('members.update');
  Route::get('/loan-requests',[LoanRequestController::class,'index'])->name('loan-requests.index');
  Route::post('/loan-requests/{loan}/approve',[LoanRequestController::class,'approve'])->name('loan-requests.approve');
  Route::post('/loan-requests/{loan}/reject',[LoanRequestController::class,'reject'])->name('loan-requests.reject');
  Route::post('/loan-requests/{loan}/handover',[LoanRequestController::class,'handover'])->name('loan-requests.handover');
  Route::post('/loan-requests/{loan}/confirm-return',[LoanRequestController::class,'confirmReturn'])->name('loan-requests.confirm-return');
  Route::get('/fines',[FineController::class,'index'])->name('fines.index');
  Route::post('/fines/{fine}/confirm-payment',[FineController::class,'confirmPayment'])->name('fines.confirm-payment');
  Route::get('/reports/loans',[LoanReportController::class,'index'])->name('reports.loans.index');
  Route::get('/reports/loans/export',[LoanReportController::class,'export'])->name('reports.loans.export');
  Route::get('/reports/loans/print',[LoanReportController::class,'print'])->name('reports.loans.print');
 });
});

Route::prefix('member')->name('member.')->group(function (): void {
 Route::get('/', fn () => redirect()->route('member.dashboard'));
 Route::get('/register', [RegistrationController::class, 'create'])->name('register');
 Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
 Route::get('/login',[MemberAuthController::class,'showLoginForm'])->name('login.view');
 Route::post('/login',[MemberAuthController::class,'login'])->middleware('throttle:10,1')->name('login');
 Route::match(['get','post'],'/logout',[MemberAuthController::class,'logout'])->middleware('auth')->name('logout');
 Route::middleware(['auth','member'])->group(function (): void {
  Route::get('/dashboard',MemberDashboardController::class)->name('dashboard');
  Route::get('/profile',[ProfileController::class,'show'])->name('profile.show');
  Route::patch('/profile',[ProfileController::class,'update'])->name('profile.update');
  Route::get('/catalog/categories',[CatalogController::class,'categories'])->name('catalog.categories');
  Route::get('/catalog/items',[CatalogController::class,'index'])->name('catalog.items.index');
  Route::get('/catalog/items/{item}',[CatalogController::class,'show'])->name('catalog.items.show');
  Route::get('/loan-requests',[MemberLoanRequestController::class,'index'])->name('loan-requests.index');
  Route::post('/loan-requests',[MemberLoanRequestController::class,'store'])->name('loan-requests.store');
  Route::get('/loan-requests/{loan}',[MemberLoanRequestController::class,'show'])->name('loan-requests.show');
  Route::get('/fines',[MemberFineController::class,'index'])->name('fines.index');
 });
});
