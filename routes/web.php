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

Route::get('/', function () {
    return view('welcome');
});

Route::get('vendor/', function () {
    if(Auth::check()){
        return view('vendor.home');
    }
    return view('welcome', [
        'name'=>'Vendor'
    ]);
});

Route::get('customer/', function () {
    if(Auth::check()){
        return view('customer.home');
    }
    return view('welcome', [
        'name'=>'Customer'
    ]);
});

// Route::get('auth/social', 'Auth\LoginController@show')->name('social.login');
// Route::get('oauth/{driver}', 'Auth\LoginController@redirectToProvider')->name('social.oauth');
// Route::get('oauth/{driver}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');
Route::get('auth/social', [App\Http\Controllers\LoginController::class, 'index'])->name('social.login');
Route::get('oauth/{driver}', [App\Http\Controllers\LoginController::class, 'redirectToProvider'])->name('social.oauth');
Route::get('oauth/{driver}/callback', [App\Http\Controllers\LoginController::class, 'handleProviderCallback'])->name('social.callback');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'authenticate'])->name('login-authenticate');
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

Route::group(['prefix' => 'vendor'], function () {
   Route::get('/register', [App\Http\Controllers\Vendor\RegisterController::class, 'index'])->name('venodr-create');
   Route::post('/register', [App\Http\Controllers\Vendor\RegisterController::class, 'store'])->name('venodr-register');

   
     
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('vendor-dashboard');

        Route::get('/password-change/{id}/', [App\Http\Controllers\Vendor\VendorController::class, 'changePassword'])->name('vendor-password-change');
        Route::get('/edit/{id}', [App\Http\Controllers\Vendor\VendorController::class, 'edit'])->name('vendor-edit');

        Route::PATCH('/edit/{id}', [App\Http\Controllers\Vendor\VendorController::class, 'update'])->name('vendor-update');
        Route::PATCH('/change-password/{id}', [App\Http\Controllers\Vendor\VendorController::class, 'updatePassword'])->name('vendor-password-update');
   });

});

Route::group(['prefix' => 'customer'], function () {
   Route::get('/register', [App\Http\Controllers\Customer\RegisterController::class, 'index'])->name('customer-create');
   Route::post('/register', [App\Http\Controllers\Customer\RegisterController::class, 'store'])->name('customer-register');

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer-dashboard');

        Route::get('/password-change/{id}/', [App\Http\Controllers\Customer\CustomerController::class, 'changePassword'])->name('customer-password-change');
        Route::get('/edit/{id}', [App\Http\Controllers\Customer\CustomerController::class, 'edit'])->name('customer-edit');

        Route::PATCH('/edit/{id}', [App\Http\Controllers\Customer\CustomerController::class, 'update'])->name('customer-update');
        Route::PATCH('/change-password/{id}', [App\Http\Controllers\Customer\CustomerController::class, 'updatePassword'])->name('customer-password-update');
    });
   
});


Route::group(['prefix' => 'admin'], function () {
    
    Route::get('/', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('admin-login');
    
    Route::post('/login', [App\Http\Controllers\Admin\LoginController::class, 'authenticate'])->name('admin-login-authenticate');
    Route::post('/logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('admin-logout');

    Route::get('/forgot/password', function () {
        return view('admin.email');
    })->middleware('guest')->name('password.request');

    Route::get('/reset-password/{token}', 'ResetPasswordController@getPassword');
    Route::post('/reset-password', 'ResetPasswordController@updatePassword');

    Route::post('/forget-password', [App\Http\Controllers\Admin\ForgotPasswordController::class, 'postEmail'])->name('password.email');

    Route::group(['middleware' => 'auth'], function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin-dashboard');

        Route::get('/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('admin-edit');

        Route::get('/password-change/{id}/', [App\Http\Controllers\Admin\AdminController::class, 'changePassword'])->name('admin-password-change');

        Route::PATCH('/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('admin-update');
        Route::PATCH('/change-password/{id}', [App\Http\Controllers\Admin\AdminController::class, 'updatePassword'])->name('admin-password-update');

        

        

        Route::group(['prefix' => 'customers'], function () {
            Route::get('/', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin-customers');

            Route::get('/customers/list', [App\Http\Controllers\Admin\CustomerController::class, 'list'])->name('admin.customer.list');

            Route::get('/add', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin-customers-add');

             Route::get('/edit/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin-customers-edit');

            Route::post('/add', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('admin-customers-store');
            Route::PATCH('/update/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin-customers-update');

            Route::delete('/delete/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('admin-customers-delete');

            
        });


        Route::group(['prefix' => 'vendors'], function () {
            Route::get('/', [App\Http\Controllers\Admin\VendorsController::class, 'index'])->name('admin-vendors');
            Route::get('/vendors/list', [App\Http\Controllers\Admin\VendorsController::class, 'list'])->name('admin.vendor.list');


            Route::get('/add', [App\Http\Controllers\Admin\VendorsController::class, 'create'])->name('admin-vendors-add');

             Route::get('/edit/{id}', [App\Http\Controllers\Admin\VendorsController::class, 'edit'])->name('admin-vendors-edit');

            Route::post('/add', [App\Http\Controllers\Admin\VendorsController::class, 'store'])->name('admin-vendors-store');
            Route::PATCH('/update/{id}', [App\Http\Controllers\Admin\VendorsController::class, 'update'])->name('admin-vendors-update');

            Route::delete('/delete/{id}', [App\Http\Controllers\Admin\VendorsController::class, 'destroy'])->name('admin-vendors-delete');

        });

    });
   
});

