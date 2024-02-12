<?php

use App\Http\Controllers\WineController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CellarController;
use App\Http\Controllers\CellarHasWineController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/', function () {
    return Inertia::render('HomeView');
});

// Wine
Route::middleware(['auth'])->group(function () {
    Route::get('/wine-search', [WineController::class, 'searchResult'])->name('wine.search');
    Route::get('/wine/{wine}', [WineController::class, 'show'])->name('wine.show');
    Route::get('/wine-create', [WineController::class, 'create'])->name('wine.create');
    Route::post('/wine-create', [WineController::class, 'store'])->name('wine.store');
    Route::get('/wine-edit/{wine}', [WineController::class, 'edit'])->name('wine.edit');;
    Route::put('/wine-edit/{wine}', [WineController::class, 'update']);
    Route::delete('/wine-delete/{wine}', [WineController::class, 'destroy']);

    //cette route est pour tester seulement, ne sera pas présent dans le produit final
    Route::get('/wines', [WineController::class, 'index']);
});

//Cellar
//----------------------------------------------------
Route::get('/cellars', [CellarController::class, 'index'])->name('cellar.index');
Route::get('/cellar/{cellar}', [CellarController::class, 'show'])->name('cellar.show');
Route::get('/cellar-create', [CellarController::class, 'create'])->name('cellar.create');
Route::post('/cellar-create', [CellarController::class, 'store'])->name('cellar.store');
Route::get('/cellar-edit/{cellar}', [CellarController::class, 'edit'])->name('cellar.edit');
Route::put('/cellar-edit/{cellar}', [CellarController::class, 'update'])->name('cellar.update');
Route::delete('/cellar/{cellar}', [CellarController::class, 'destroy'])->name('cellar.delete');


// CellarHasWine
Route::post('/cellarwine-store', [CellarHasWineController::class, 'store'])->name('cellarwine.store')->middleware('auth');


// User
//----------------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/{user}', [UserController::class, 'show'])->name('profile.show');
    Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/users/edit', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
});

Route::middleware(['admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
});


require __DIR__.'/auth.php';
