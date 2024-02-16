<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\tempImagesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Row;

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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('shop.home');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-cart',[CartController::class,'deleteItem'])->name('front.deleteItem');
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/processCheckout',[CartController::class,'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');


Route::group(['prefix' => 'account'],function(){
    Route::group(['middleware' => 'guest'],function(){
        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account.authenticate');
        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');

    });
    
    Route::group(['middleware' => 'auth'],function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/my-orders',[AuthController::class,'orders'])->name('account.orders');
        Route::get('/order-detail/{orderId}',[AuthController::class,'orderDetail'])->name('account.orderDetail');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');
        
    });
});







Route::group(['prefix' => 'admin'],function(){
    
    Route::group(['middleware' => 'admin.guest'],function(){

        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');
        
        
    });
    
    Route::group(['middleware' => 'admin.auth'],function(){
        
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');
        
        //category routes
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}/',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}/',[CategoryController::class,'destroy'])->name('categories.delete');
        Route::post('/categories/bulkDelete', [CategoryController::class, 'bulkDelete'])->name('categories.bulkDelete');
        Route::post('/categories/bulkPublish', [CategoryController::class, 'bulkPublish'])->name('categories.bulkPublish');
        Route::post('/categories/bulkUnpublish', [CategoryController::class, 'bulkUnpublish'])->name('categories.bulkUnpublish');


        
        //sub category routes
        // Route::get('/sub-categories',[SubCategoryController::class, 'index'])->name('sub-categories.index');
        // Route::get('sub-categories/create',[SubCategoryController::class, 'create'])->name('sub-categories.create');
        // Route::post('/sub-categories',[SubCategoryController::class, 'store'])->name('sub-categories.store');
        // Route::get('/sub-categories/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        // Route::put('/sub-categories/{subCategory}/',[SubCategoryController::class,'update'])->name('sub-categories.update');
        // Route::delete('/sub-categories/{subCategory}/',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');
        
        //brands routes
        Route::get('/brands',[BrandsController::class, 'index'])->name('brands.index');
        Route::get('brands/create',[BrandsController::class, 'create'])->name('brands.create');
        Route::post('/brands',[BrandsController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit',[BrandsController::class,'edit'])->name('brands.edit');
        Route::put('/brands/{brand}/',[BrandsController::class,'update'])->name('brands.update');
        Route::delete('/brands/{brand}/',[BrandsController::class,'destroy'])->name('brands.delete');
        Route::post('/brands/bulkDelete', [BrandsController::class, 'bulkDelete'])->name('brands.bulkDelete');
        Route::post('/brands/bulkPublish', [BrandsController::class, 'bulkPublish'])->name('brands.bulkPublish');
        Route::post('/brands/bulkUnpublish', [BrandsController::class, 'bulkUnpublish'])->name('brands.bulkUnpublish');
        
        //Products routes
        Route::get('/products',[ProductController::class, 'index'])->name('products.index');
        Route::get('products/create',[ProductController::class, 'create'])->name('products.create');
        Route::post('/products',[ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}/',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}/',[ProductController::class,'destroy'])->name('products.delete');
        Route::post('/products/bulkDelete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');
        Route::post('/products/bulkPublish', [ProductController::class, 'bulkPublish'])->name('products.bulkPublish');
        Route::post('/products/bulkUnpublish', [ProductController::class, 'bulkUnpublish'])->name('products.bulkUnpublish');

        Route::get('/get-product',[ProductController::class,'getProducts'])->name('products.getProducts');

        
        
        Route::get('/products-subcategories',[ProductSubCategoryController::class, 'index'])->name('products-subcategories.index');
        
        Route::post('/product-images/update',[ProductImageController::class, 'update'])->name('product-images.update');
        Route::delete('/product-images/delete',[ProductImageController::class, 'destroy'])->name('product-images.destroy');

        //export products
        Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');

        //shipping routes
        Route::get('/shipping/create',[ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping',[ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/{id}',[ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}',[ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{id}/',[ShippingController::class,'destroy'])->name('shipping.delete');

        

        //temp-images.create
        Route::post('/upload-temp-image',[tempImagesController::class,'create'])->name('temp-images.create');



        //settings routes
        Route::get('/change-password',[SettingController::class,'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('/process-change-password',[SettingController::class,'processchangePassword'])->name('admin.processchangePassword');
        Route::get('/profile',[SettingController::class,'showProfileForm'])->name('admin.profile');
        Route::post('/profile-update',[SettingController::class,'profileUpdate'])->name('admin.profileUpdate');

        // this is for slug generate
        Route::get('/getSlug',function(Request $request){
            $slug = '';
            if (!empty($request->title)) {
               $slug =  Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                
                'slug' => $slug
            ]);
        })->name('getSlug');


    });

});