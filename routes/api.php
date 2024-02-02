<?php

use App\Http\Controllers\Api\Public\PublicBannerController;
use App\Http\Controllers\Api\Public\PublicCategoryController;
use App\Http\Controllers\Api\Public\PublicProductController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeValueController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NusantaraController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "api" middleware group. Make something great!
 * |
 */

Route::prefix('v1')->group(
    function () {
        Route::middleware('auth:api')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('user', 'index');
                Route::post('user/create', 'store');
                Route::get('user/{id}', 'show');
                Route::put('user/update/{id}', 'update');
                Route::delete('user/delete/{id}', 'destroy');
            });

            Route::controller(UserAddressController::class)->group(function () {
                Route::get('address', 'index');
                Route::post('address/create', 'store');
                Route::get('address/{id}', 'show');
                Route::put('address/update/{id}', 'update');
                Route::delete('address/delete/{id}', 'destroy');
            });

            // rajaongkir
            Route::controller(NusantaraController::class)->group(function () {
                Route::get('nusantara/provinsi', 'province');
                Route::get('nusantara/kota', 'kota');
                Route::post('nusantara/cost', 'getCost');
            });

            Route::controller(CategoryController::class)->prefix('cat')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/create', 'store');
                Route::post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
            });

            // router for attribute data
            Route::controller(AttributeController::class)->prefix('attribute')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/create', 'store');
                Route::put('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'destroy');
            });

            // router for attribute values
            Route::controller(AttributeValueController::class)->prefix('attribute-values')->group(function () {
                Route::get('/', 'index');
                Route::get('/show-by-attribute/{id}', 'showByAttribute');
                Route::post('/create', 'store');
                Route::put('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'destroy');
            });

            // router for product
            Route::controller(ProductController::class)->prefix('product')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/create', 'store');
                Route::post('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'destroy');
            });

            // route banner
            Route::controller(BannerController::class)->prefix('banner')->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store');
                Route::get('/show/{id}', 'show');
                Route::post('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'destroy');
            });

            // CART TEMPORARY
            Route::controller(CartController::class)->prefix('cart')->group(function () {
                Route::get('/', 'index');
            });
            Route::post('auth/logout', [SessionController::class, 'destroy']);

            // user address
            Route::get('/user-address', [UserAddressController::class,         'getUserAddress']);
            Route::post('/set-default-address', [UserAddressController::class, 'setDefaultAddress']);

            Route::controller(OrdersController::class)->prefix('order')->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store');
                Route::get('/detail/{code}', 'show');
            });
        });

        Route::post('auth/login', [SessionController::class, 'create']);

        Route::prefix('public')->group(function () {
            Route::get('/banner', [PublicBannerController::class,     'index']);
            Route::get('/category', [PublicCategoryController::class, 'index']);

            Route::get('/all-product', [ProductController::class,                  'allProduct']);
            Route::get('/featured-product', [ProductController::class,             'featuredProduct']);
            Route::get('/product-by-categories/{slug}', [ProductController::class, 'productByCategories']);
            Route::get('/product-by-slug/{slug}', [ProductController::class,       'productBySlug']);

            Route::get('/categories/{slug}', [CategoryController::class, 'getCategoryBySlug']);

            Route::get('/shopping-cart/{user_id}', [CartController::class, 'index']);
            Route::post('/add-to-cart', [CartController::class,            'createTemp']);
            Route::post('/update-cart', [CartController::class,            'updateCart']);
            Route::get('/delete-cart/{id}', [CartController::class,        'deleteCart']);

            Route::get('/download-invoice', [OrdersController::class, 'downloadInvoice']);
        });
    }
);
