<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'api\UserController@login');
Route::post('register', 'api\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
	Route::post('verify/email', 'api\UserController@verifyemail');
	Route::post('verify/mobile', 'api\UserController@verifymobile');
	Route::post('details', 'api\UserController@details');
	Route::get('user/{id}', 'api\UserController@detailsbyid')->name('User.By.Id');
	Route::post('user/password/change', 'api\UserController@changePassword')->name('User.By.Id');
	
	Route::post('roles', 'api\RoleController@getRole');
	Route::post('assign/role', 'api\RoleController@AssignRole');
	
	// Address API
	Route::post('address/all', 'api\AddressController@getAllAddress');
	Route::post('address', 'api\AddressController@getAddressById');
	Route::post('address/store', 'api\AddressController@store');
	Route::post('address/delete', 'api\AddressController@delete');
	
	Route::post('category/add', 'api\CategoryController@addCategories');
	Route::post('category/edit', 'api\CategoryController@editCategories');
	Route::post('category/delete', 'api\CategoryController@deleteCategories');
	
	Route::post('upload/image', 'api\UtilityController@upload')->name('Upload');
	
	//Product Add
	Route::post('product/add', 'api\ProductController@addProduct');
	Route::post('product/update', 'api\ProductController@updateProduct');
	Route::post('product/delete', 'api\ProductController@deleteProduct');
	Route::post('product/master/add', 'api\ProductController@addProductMaster');
	Route::post('product/master/update', 'api\ProductController@editProductMaster');
	Route::post('master/product/delete', 'api\ProductController@productMasterDelete');
	//Route::post('product/variant/delete', 'api\ProductController@productVariantDelete');
	
	//Order
	Route::post('order/place', 'api\OrderController@placeOrder');
	Route::post('order/details', 'api\OrderController@getOrderDetails');
	Route::post('order/list/all', 'api\OrderController@getAllOrders');
	Route::post('order/list/notaccepted', 'api\OrderController@getOrdersNotAccepted');
	Route::post('order/list/accepted', 'api\OrderController@getOrdersAccepted');
	Route::post('order/list/rejected', 'api\OrderController@getOrdersRejected');
	Route::post('order/list/by/vendor', 'api\OrderController@getOrdersByVendor');
	Route::post('order/list/accepted/notassigned', 'api\OrderController@getOrdersAcceptedNotAssigned');
	Route::post('order/accept', 'api\OrderController@orderAccept');
	Route::post('order/reject', 'api\OrderController@orderReject');
	Route::post('order/assign', 'api\OrderController@orderAssignment');
	Route::post('order/delivery/update', 'api\OrderController@orderStatusUpdate');
	Route::post('order/status/update', 'api\OrderController@orderPaymentStatusUpdate');
	Route::post('user/order/list', 'api\OrderController@getAllOrdersForUser');
	Route::post('order/recent', 'api\OrderController@getAllRecentOrders');
	Route::get('order/export', 'api\OrderController@export');
	
	// Delivery Boy
	Route::post('carrier/available', 'api\CarrierController@getAvailableCarrier');
	Route::post('carrier/all', 'api\CarrierController@getAllCarrier');
	Route::post('carrier', 'api\CarrierController@getCarrierById');
	Route::post('carrier/delete', 'api\CarrierController@delete');
	Route::post('carrier/restore', 'api\CarrierController@restore');
	Route::post('carrier/order/details', 'api\CarrierController@orderDetails');
	Route::put('carrier/location', 'api\CarrierController@setLocation');
	Route::get('carrier/{id}/location', 'api\CarrierController@getLocation');
	Route::post('carrier/documents', 'api\CarrierController@addDocuments');
	Route::post('carrier/report', 'api\CarrierController@addReport');
	
	//vendor sales
	Route::post('vendors/sales', 'api\VendorController@getSales');

	//pickups
	Route::get('pickup', 'api\PickupController@index');
	Route::get('pickup/{id}', 'api\PickupController@show');
	Route::post('pickup', 'api\PickupController@store');
	Route::post('pickup/accept', 'api\PickupController@accept');
	Route::post('pickup/complete', 'api\PickupController@complete');
	Route::post('pickup/create', 'api\PickupController@create');//by admin
	Route::post('pickup/assign', 'api\PickupController@assign');//by admin

	//add vendor by admin
	Route::post('vendor/registerbyadmin', 'api\VendorController@registerByAdmin');
	Route::post('vendor/updatebyadmin/{user_id}', 'api\VendorController@updateByAdmin');
	
	//Coupon API
	Route::post('coupon/add', 'api\CouponController@addCoupon');	
	Route::post('coupon/delete', 'api\CouponController@deleteCoupons');
	Route::post('coupon/manage/visible', 'api\CouponController@visibleCoupons');
	Route::post('apply/coupon', 'api\CouponController@applyCoupons');

	//notification
	Route::put('notification/open', 'api\NotificationController@openNotification');
	Route::get('notification/admin', 'api\NotificationController@getAdminRecentNotification');	

	//app config
	Route::get('config', 'api\AppConfigController@index');
	Route::put('config', 'api\AppConfigController@update');
});

// Banner API
Route::post('banner/all', 'api\BannerController@getAllBanners');
Route::post('banner/active', 'api\BannerController@getActiveBanners');
Route::post('banner', 'api\BannerController@getBannerById');
Route::post('banner/add', 'api\BannerController@addBanner');
Route::post('banner/delete', 'api\BannerController@delete');
Route::post('banner/update', 'api\BannerController@updateBanner');

Route::post('category/all', 'api\CategoryController@getAllCategories');
Route::post('categories', 'api\CategoryController@getCategories');
Route::post('parent-categories', 'api\CategoryController@getParentCategories');
Route::post('categorybyid', 'api\CategoryController@getCategoryById');
Route::post('productbycategoryid', 'api\ProductController@getProductByCatId');
Route::post('productdetailsbyid', 'api\ProductController@getProductDetailsById');
Route::post('productvariantbyid', 'api\ProductController@getProductVariantById');
Route::post('productvariantbyskuid', 'api\ProductController@getProductVariantBySkuId');
Route::post('productdetailsbyvendor', 'api\ProductController@getProductDetailsByVendor');
Route::post('menu', 'api\MenuController@getAllMenu');
Route::post('product/home/categories', 'api\ProductController@productforhome');
Route::post('productbyvendorid', 'api\ProductController@getProductByVendorId');
Route::get('productlist', 'api\ProductController@getPoductsByCategoryVendor');

// Coupon
Route::post('active/coupon', 'api\CouponController@getActiveCoupons');

// Vendor API
Route::post('vendors/all', 'api\VendorController@getAllVendors');
Route::post('vendor', 'api\VendorController@getVendorById');
Route::post('vendor/delete', 'api\VendorController@delete');
Route::post('vendor/add/mobile', 'api\VendorController@addMobile');
Route::post('vendor/verify/mobile', 'api\VendorController@verifymobile');
Route::post('vendor/add/address', 'api\VendorController@addAddress');
Route::post('vendor/register', 'api\VendorController@register');
Route::post('get/nearest/seller', 'api\VendorController@getNearestSeller');
Route::post('vendorbycatid', 'api\VendorController@getVendorByCategoryID');

// Delivery API
Route::post('carrier/add/mobile', 'api\CarrierController@addMobile');
Route::post('carrier/verify/mobile', 'api\CarrierController@verifymobile');
Route::post('carrier/register', 'api\CarrierController@register');
Route::post('carrier/manage/availablity', 'api\CarrierController@availablity');

// Cart
Route::post('cart/details', 'api\CartController@getCartItems');
Route::post('cart/add', 'api\CartController@setCartItems');
Route::post('cart/update', 'api\CartController@updateCartItems');
Route::post('cart/empty', 'api\CartController@emptyCart');

// Tracker API
Route::post('position/get', 'api\TrackerController@getPosition');
Route::post('position/set', 'api\TrackerController@setPosition');

// Search Product
Route::post('product/search', 'api\ProductController@search');

