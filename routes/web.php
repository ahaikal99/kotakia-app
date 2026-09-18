<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvitationShareController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionBannerController;
use App\Http\Controllers\ReceiptController;
use App\Http\Middleware\RequireManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/design', CatalogController::class)->name('catalog');
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', ['siteUrl' => rtrim(config('seo.url'), '/')])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');
Route::get('/jemputan/{token}', [InvitationShareController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{40}')
    ->name('invitations.public');

Route::middleware(['auth', RequireManager::class])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/designs', [DesignController::class, 'index'])->name('designs');
    Route::get('/designs/create', [DesignController::class, 'create'])->name('designs.create');
    Route::post('/designs', [DesignController::class, 'store'])->middleware('throttle:20,1')->name('designs.store');
    Route::patch('/designs/{design}/status', [DesignController::class, 'status'])->whereNumber('design')->name('designs.status');
    Route::get('/', [ManagerController::class, 'index'])->name('index');
    Route::get('/banners', [PromotionBannerController::class, 'index'])->name('banners');
    Route::get('/banners/create', [PromotionBannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [PromotionBannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [PromotionBannerController::class, 'edit'])->whereNumber('banner')->name('banners.edit');
    Route::put('/banners/{banner}', [PromotionBannerController::class, 'update'])->whereNumber('banner')->name('banners.update');
    Route::patch('/banners/{banner}/deactivate', [PromotionBannerController::class, 'deactivate'])->whereNumber('banner')->name('banners.deactivate');
    Route::get('/orders', [ManagerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [ManagerController::class, 'order'])->whereNumber('order')->name('orders.show');
    Route::get('/users', [ManagerController::class, 'users'])->name('users');
    Route::get('/users/create-manager', [ManagerController::class, 'createManager'])->name('users.create');
    Route::post('/users/managers', [ManagerController::class, 'storeManager'])->middleware('throttle:10,1')->name('users.store');
    Route::get('/users/{user}', [ManagerController::class, 'user'])->whereNumber('user')->name('users.show');
    Route::patch('/users/{user}', [ManagerController::class, 'updateUser'])->whereNumber('user')->name('users.update');
    Route::get('/logs', [ManagerController::class, 'logs'])->name('logs');
    Route::get('/logs/{log}', [ManagerController::class, 'log'])->whereNumber('log')->name('logs.show');
    Route::get('/controls', [ManagerController::class, 'controls'])->name('controls');
    Route::put('/controls/{key}', [ManagerController::class, 'updateControl'])->name('controls.update');
});

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'send'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('register.store');
    Route::get('/signin', [AuthController::class, 'login'])->name('login');
    Route::post('/signin', [AuthController::class, 'authenticate'])->middleware('throttle:20,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/cards/{invitation}/receipt', [ReceiptController::class, 'show'])->whereNumber('invitation')->name('receipts.show');
    Route::post('/cards/{invitation}/receipt/email', [ReceiptController::class, 'send'])->whereNumber('invitation')->middleware('throttle:3,1')->name('receipts.send');
    Route::get('/promotions/{banner}/poster', [PromotionBannerController::class, 'poster'])->whereNumber('banner')->name('promotions.poster');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', [InvitationController::class, 'index'])->name('dashboard');
    Route::redirect('/order', '/cards/create');
    Route::get('/cards/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/cards', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/cards/{invitation}/edit', [InvitationController::class, 'edit'])->whereNumber('invitation')->name('invitations.edit');
    Route::put('/cards/{invitation}', [InvitationController::class, 'update'])->whereNumber('invitation')->name('invitations.update');
    Route::get('/cards/{invitation}/options', [InvitationController::class, 'options'])->whereNumber('invitation')->name('invitations.options');
    Route::put('/cards/{invitation}/options', [InvitationController::class, 'saveOptions'])->whereNumber('invitation')->name('invitations.options.save');
    Route::get('/cards/{invitation}/payment', [PaymentController::class, 'show'])->whereNumber('invitation')->name('payments.show');
    Route::post('/cards/{invitation}/payment', [PaymentController::class, 'simulate'])->whereNumber('invitation')->name('payments.simulate');
});
