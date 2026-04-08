<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EvidenceExportController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InspectionController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\PayableController;
use App\Http\Controllers\Admin\POSController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReceivableController;
use App\Http\Controllers\Admin\ReturnCaseController;
use App\Http\Controllers\Admin\ReturnsRuleController;
use App\Http\Controllers\Admin\StocklimitController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\TransectionController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\OrderController;

Route::group(['namespace'=>'Admin', 'as' => 'admin.', 'prefix'=>'admin'] ,function(){
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function(){
        Route::controller(LoginController::class)->group(function() {
            Route::get('/code/captcha/{tmp}', 'captcha')->name('default-captcha');
            Route::get('login', 'login')->name('login');
            Route::post('login', 'submit');
            Route::get('logout', 'logout')->name('logout');
        });
    });

    Route::group(['middleware' => ['admin']], function(){
        Route::controller(DashboardController::class)->group(function() {
            Route::get('/', 'dashboard')->name('dashboard');
            Route::post('account-status', 'accountStats')->name('account-status');
        });

        Route::controller(SystemController::class)->group(function() {
            Route::get('settings', 'settings')->name('settings');
            Route::post('settings', 'settingsUpdate');
            Route::get('settings-password', 'settings')->name('settings.password');
            Route::post('settings-password', 'settingsPasswordUpdate')->name('settings-password');
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:employee_role_section']], function () {
            Route::controller(CustomRoleController::class)->group(function() {
                Route::get('create', 'create')->name('create');
                Route::post('create', 'store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'distroy')->name('delete');
                Route::post('search', 'search')->name('search');
                Route::get('status/{id}/{status}', 'status')->name('status');
                Route::get('export-employee-role', 'employee_role_export')->name('export-employee-role');
            });
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee_section']], function () {
            Route::controller(EmployeeController::class)->group(function() {
                Route::get('add-new', 'add_new')->name('add-new');
                Route::post('add-new', 'store');
                Route::get('list', 'list')->name('list');
                Route::get('update/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'distroy')->name('delete');
                Route::get('export-employee', 'employee_list_export')->name('export-employee');
                Route::get('export', 'export')->name('export');
            });
        });

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:category_section']], function () {
            Route::controller(CategoryController::class)->group(function() {
                Route::get('add', 'index')->name('add');
                Route::get('add-sub-category', 'subIndex')->name('add-sub-category');
                Route::post('store', 'store')->name('store');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('update-sub/{id}', 'updateSub')->name('update-sub');
                Route::post('store', 'store')->name('store');
                Route::get('status/{id}/{status}', 'status')->name('status');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('subcategories', 'getSubCategories')->name('subcategories');
                Route::get('export', 'exportCategory')->name('export');
                Route::get('render-edit-canvas', 'renderEditCanvas')->name('render-edit-canvas');
                Route::get('render-view-canvas', 'renderViewCanvas')->name('render-view-canvas');
                Route::get('delete-after-shifting-modal', 'deleteAfterShiftingModal')->name('delete-after-shifting-modal');
                Route::get('sub-category/render-edit-canvas', 'renderSubCategoryEditCanvas')->name('sub-category.render-edit-canvas');
                Route::get('sub-category/render-view-canvas', 'renderSubCategoryViewCanvas')->name('sub-category.render-view-canvas');
                Route::get('sub-category/delete-after-shifting-modal', 'deleteSubCategoryAfterShiftingModal')->name('sub-category.delete-after-shifting-modal');
            });
        });

        Route::group(['prefix' => 'brand', 'as' => 'brand.', 'middleware' => ['module:brand_section']], function () {
            Route::controller(BrandController::class)->group(function() {
                Route::get('add', 'index')->name('add');
                Route::post('store', 'store')->name('store');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('status/{id}/{status}', 'status')->name('status');
                Route::get('export', 'export')->name('export');
                Route::get('render-edit-canvas', 'renderEditCanvas')->name('render-edit-canvas');
                Route::get('render-view-canvas', 'renderViewCanvas')->name('render-view-canvas');
                Route::get('delete-after-shifting-modal', 'deleteAfterShiftingModal')->name('delete-after-shifting-modal');
            });
        });
        //unit
        Route::group(['prefix' => 'unit', 'as' => 'unit.', 'middleware' => ['module:unit_section']], function () {
            Route::controller(UnitController::class)->group(function() {
                Route::get('index', 'index')->name('index');
                Route::post('store', 'store')->name('store');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('status/{id}/{status}', 'status')->name('status');
                Route::get('export', 'export')->name('export');
                Route::get('render-edit-canvas', 'renderEditCanvas')->name('render-edit-canvas');
                Route::get('render-view-canvas', 'renderViewCanvas')->name('render-view-canvas');
                Route::get('delete-after-shifting-modal', 'deleteAfterShiftingModal')->name('delete-after-shifting-modal');
            });
        });

        Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => ['module:product_section']], function () {
            Route::controller(ProductController::class)->group(function() {
                Route::get('add', 'index')->name('add');
                Route::post('store', 'store')->name('store');
                Route::get('list', 'list')->name('list');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::get('details/{id}', 'show')->name('show');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{product}', 'delete')->name('delete');
                Route::get('export', 'export')->name('export');
                Route::get('barcode-generate/{id}', 'barcodeGenerate')->name('barcode-generate');
                Route::get('barcode/{id}', 'barcode')->name('barcode');
                Route::get('bulk-import', 'bulkImportIndex')->name('bulk-import');
                Route::post('bulk-import', 'bulkImportData');
                Route::get('bulk-export', 'bulkExportData')->name('bulk-export');
                Route::get('status/{id}/{status}', 'status')->name('status');

                //ajax request
                Route::get('get-categories', 'getCategories')->name('get-categories');
                Route::get('remove-image/{id}/{name}', 'remove_image')->name('remove-image');
            });
        });

        Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => ['module:pos_section']], function () {
            Route::controller(POSController::class)->group(function() {
                Route::get('/', 'index')->name('index');
                Route::get('quick-view', 'quickView')->name('quick-view');
                Route::post('variant_price', 'variant_price')->name('variant_price');
                Route::post('add-to-cart', 'addToCart')->name('add-to-cart');
                Route::post('add-to-cart-data', 'addToCartData')->name('add-to-cart-data');
                Route::post('remove-from-cart', 'removeFromCart')->name('remove-from-cart');
                Route::post('cart-items', 'cartItems')->name('cart_items');
                Route::post('update-quantity', 'updateQuantity')->name('updateQuantity');
                Route::post('empty-cart', 'emptyCart')->name('emptyCart');
                Route::post('tax', 'updateTax')->name('tax');
                Route::post('discount', 'updateDiscount')->name('discount');
                Route::get('customers', 'getCustomers')->name('customers');
                Route::get('customer-balance', 'customerBalance')->name('customer-balance');
                Route::post('order', 'placeOrder')->name('order');
                Route::get('orders', 'orderList')->name('orders');
                Route::get('order-details/{id}', 'order_details')->name('order-details');
                Route::get('invoice/{id}', 'generateInvoice')->name('invoice');
                Route::get('search-products', 'searchProduct')->name('search-products');
                Route::get('search-by-add', 'searchByAddProduct')->name('search-by-add');

                Route::post('coupon-discount', 'couponDiscount')->name('coupon-discount');
                Route::post('remove-coupon', 'removeCoupon')->name('remove-coupon');
                Route::get('change-cart', 'changeCart')->name('change-cart');
                Route::get('new-cart-id', 'newCartId')->name('new-cart-id');
                Route::get('clear-cart-ids', 'clearCartIds')->name('clear-cart-ids');
                Route::get('get-cart-ids', 'getCartIds')->name('get-cart-ids');
                Route::post('change-counter', 'changeCounter')->name('change-counter');
                Route::get('/get-subcategories', 'getSubcategories')->name('subcategories');
                Route::get('cancel-hold-order', 'cancelHoldOrder')->name('cancel-hold-order');
                Route::get('selected-customer', 'selectedCustomer')->name('selected-customer');

                Route::get('get-coupon', 'getCoupon')->name('get-coupon');
            });

        });

        Route::group(['prefix' => 'returns', 'as' => 'returns.'], function () {
            Route::group(['middleware' => ['module:returns_playbooks_section']], function () {
                Route::controller(ReturnsRuleController::class)->group(function () {
                    Route::get('rules', 'index')->name('rules.index');
                    Route::post('rules', 'store')->name('rules.store');
                    Route::post('rules/{id}', 'update')->name('rules.update');
                });
            });

            Route::group(['middleware' => ['module:returns_inspect_section']], function () {
                Route::controller(InspectionController::class)->group(function () {
                    Route::get('inspect', 'create')->name('inspect');
                    Route::post('inspect', 'store')->name('inspect.store');
                });
            });

            Route::group(['middleware' => ['module:returns_cases_section']], function () {
                Route::controller(ReturnCaseController::class)->group(function () {
                    Route::get('cases', 'index')->name('cases.index');
                    Route::get('cases/{id}', 'show')->name('cases.show');
                });
            });

            Route::group(['middleware' => ['module:returns_queue_section']], function () {
                Route::controller(ReturnCaseController::class)->group(function () {
                    Route::post('cases/{id}/refund-decision', 'updateRefundDecision')->name('cases.refund-decision');
                    Route::get('queue', 'queue')->name('queue.index');
                    Route::post('queue/refund-decision', 'batchUpdateRefundDecision')->name('queue.refund-decision');
                });
            });

            Route::group(['middleware' => ['module:returns_ops_board_section']], function () {
                Route::controller(ReturnCaseController::class)->group(function () {
                    Route::get('dashboard', 'dashboard')->name('dashboard.index');
                });
            });

            Route::group(['middleware' => ['module:returns_cases_section']], function () {
                Route::controller(EvidenceExportController::class)->group(function () {
                    Route::get('cases/{id}/export', 'show')->name('cases.export');
                });
            });
        });

        //account
        Route::group(['prefix' => 'account', 'as' => 'account.', 'middleware' => ['module:account_section']], function () {
            Route::controller(AccountController::class)->group(function() {
                Route::get('add', 'add')->name('add');
                Route::post('store', 'store')->name('store');
                Route::get('list', 'list')->name('list');
                Route::get('view/{id}', 'view')->name('view');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('export', 'export')->name('export');
            });

            //expense
            Route::controller(ExpenseController::class)->group(function() {
                Route::get('add-expense', 'add')->name('add-expense');
                Route::post('store-expense', 'store')->name('store-expense');
                Route::get('export-expense', 'exportExpense')->name('export-expense');
            });

            //income
            Route::controller(IncomeController::class)->group(function() {
                Route::get('add-income', 'add')->name('add-income');
                Route::post('store-income', 'store')->name('store-income');
                Route::get('export-income', 'exportIncome')->name('export-income');
            });

            //transfer
            Route::controller(TransferController::class)->group(function() {
                Route::get('add-transfer', 'add')->name('add-transfer');
                Route::post('store-transfer', 'store')->name('store-transfer');
                Route::get('export-transfer', 'exportTransfer')->name('export-transfer');
            });

            //transaction
            Route::controller(TransectionController::class)->group(function() {
                Route::get('list-transaction', 'list')->name('list-transaction');
                Route::get('transaction-export', 'export')->name('transaction-export');
            });

            //payable
            Route::controller(PayableController::class)->group(function() {
                Route::get('add-payable', 'add')->name('add-payable');
                Route::post('store-payable', 'store')->name('store-payable');
                Route::post('payable-transfer', 'transfer')->name('payable-transfer');
            });

            //receivable
            Route::controller(ReceivableController::class)->group(function() {
                Route::get('add-receivable', 'add')->name('add-receivable');
                Route::post('store-receivable', 'store')->name('store-receivable');
                Route::post('receivable-transfer', 'transfer')->name('receivable-transfer');
            });
        });

        //customer
        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:customer_section']], function () {
            Route::controller(CustomerController::class)->group(function() {
                Route::get('add', 'index')->name('add');
                Route::post('store', 'store')->name('store');
                Route::get('list', 'list')->name('list');
                Route::get('view/{id}', 'view')->name('view');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::post('update-balance', 'updateBalance')->name('update-balance');
                Route::get('transaction-list/{id}', 'transactionList')->name('transaction-list');
                Route::get('export', 'export')->name('export');
            });
        });
        //supplier
        Route::group(['prefix' => 'supplier', 'as' => 'supplier.', 'middleware' => ['module:supplier_section']], function () {
            Route::controller(SupplierController::class)->group(function() {
                Route::get('add', 'index')->name('add');
                Route::post('store', 'store')->name('store');
                Route::get('list', 'list')->name('list');
                Route::get('view/{id}', 'view')->name('view');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('products/{id}', 'productList')->name('products');
                Route::get('transaction-list/{id}', 'transactionList')->name('transaction-list');
                Route::post('add-new-purchase', 'addNewPurchase')->name('add-new-purchase');
                Route::post('pay-due', 'payDue')->name('pay-due');
                Route::get('export', 'export')->name('export');
            });
        });
        //stock limit
        Route::group(['prefix' => 'stock', 'as' => 'stock.', 'middleware' => ['module:stock_section']], function () {
            Route::controller(StocklimitController::class)->group(function() {
                Route::get('stock-limit', 'stockLimit')->name('stock-limit');
                Route::get('render-update-quantity-modal', 'renderUpdateQuantityModal')->name('render-update-quantity-modal');
                Route::post('update-quantity/{product}', 'updateQuantity')->name('update-quantity');
                Route::get('export', 'export')->name('export');
            });
        });
        //business settings
        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.','middleware'=>['actch','module:setting_section']], function () {
            Route::controller(BusinessSettingsController::class)->group(function() {
                Route::get('shop-setup', 'shopIndex')->name('shop-setup');
                Route::post('update-setup', 'shopSetup')->name('update-setup');
                Route::get('shortcut-keys', 'shortcutKey')->name('shortcut-keys');
                Route::get('recaptcha-index', 'recaptchaIndex')->name('recaptcha-index');
                Route::post('recaptcha-update', 'recaptchaUpdate')->name('recaptcha-update');
            });
        });

        //coupon
        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:coupon_section']], function () {
            Route::controller(CouponController::class)->group(function() {
                Route::get('add-new', 'addNew')->name('add-new');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::get('status/{id}/{status}', 'status')->name('status');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('export', 'export')->name('export');
            });
        });

        //coupon
        Route::group(['prefix' => 'counter', 'as' => 'counter.', 'middleware' => ['module:counter_section']], function () {
            Route::controller(CounterController::class)->group(function() {
                Route::get('index',  'index')->name('index');
                Route::get('details/{id}', 'details')->name('details');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}',  'update')->name('update');
                Route::delete('delete/{id}', 'delete')->name('delete');
                Route::get('status/{id}/{status}',  'changeStatus')->name('status');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('details/{id}/export', 'export')->name('export');
                Route::get('export-list', 'exportList')->name('export-list');
                Route::get('export-counter-details/{id}', 'exportCounterDetails')->name('export-counter-details');
            });
        });


        Route::group(['prefix' => 'order', 'as' => 'order.', 'middleware' => ['module:order_section']], function() {
            Route::controller(OrderController::class)->group(function() {
                Route::get('list', 'list')->name('list');
                Route::get('order-items-menu/{id}', 'orderItemsMenu')->name('order-items-menu');
                Route::get('search', 'search')->name('search');
                Route::get('export', 'export')->name('export');
                Route::get('details/{id}', 'details')->name('details');
                Route::post('refund/{id}', 'refund')->name('refund');
            });
        });

        Route::get('component', function(){
            return view('admin-views.component');
        });

    });
});
