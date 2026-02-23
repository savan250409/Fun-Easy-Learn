<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource routes for the modules will go here later
    Route::resource('categories', 'App\Http\Controllers\CategoryController');
    Route::resource('subcategories', 'App\Http\Controllers\SubCategoryController');
    Route::resource('child-categories', 'App\Http\Controllers\ChildCategoryController');
    Route::resource('items', 'App\Http\Controllers\ItemController');

    Route::get('/get-subcategories/{categoryId}', 'App\Http\Controllers\ChildCategoryController@getSubcategories');
    Route::get('/get-child-categories/{subCategoryId}', 'App\Http\Controllers\ItemController@getChildCategories');

    // AJAX Routes for status toggling
    Route::post('/categories/{id}/toggle-status', 'App\Http\Controllers\CategoryController@toggleStatus')->name('categories.toggle-status');
    Route::post('/subcategories/{id}/toggle-status', 'App\Http\Controllers\SubCategoryController@toggleStatus')->name('subcategories.toggle-status');
    Route::post('/child-categories/{id}/toggle-status', 'App\Http\Controllers\ChildCategoryController@toggleStatus')->name('child-categories.toggle-status');
    Route::post('/items/{id}/toggle-status', 'App\Http\Controllers\ItemController@toggleStatus')->name('items.toggle-status');
});
