<?php

use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Inputs\ComboboxController;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\ProductMovementController;
use App\Http\Controllers\Panel\ZoneController;
use App\Http\Controllers\Panel\DoctorController;
use App\Http\Controllers\Panel\SupplierController;
use App\Http\Controllers\Panel\ProductController;
use App\Http\Controllers\Panel\LocalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inputs\SelectController;
use App\Http\Controllers\Panel\ClientTypeController;
use App\Http\Controllers\Panel\GuideController;
use App\Http\Controllers\Panel\InventoryController;
use App\Http\Controllers\Panel\LaboratoryController;
use App\Http\Controllers\Panel\MovementController;
use App\Http\Controllers\Panel\ProductPriceController;
use App\Http\Controllers\Panel\RoleController;
use App\Http\Controllers\Panel\PermissionController;
use App\Http\Controllers\Panel\SaleController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\Panel\CustomerController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');



# rutes no auth

Route::resource('guides', GuideController::class);


# route group for panel
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        # module suppliers
        Route::resource('suppliers', SupplierController::class)->except('edit');
        # list suppliers
        Route::get('listar-suppliers', [SupplierController::class, 'listarProveedor'])->name('suppliers.listar');

        # module categories
        Route::resource('categories', CategoryController::class);
        # list categories
        Route::get('listar-categories', [CategoryController::class, 'listarCategories'])->name('categories.listar');
        # module Doctors
        Route::resource('doctors', DoctorController::class)->except('edit');
        # list doctors
        Route::get('listar-doctors', [DoctorController::class, 'listarDoctors'])->name('doctors.listar');
        # module laboratories
        Route::resource('laboratories', LaboratoryController::class);
        # list laboratories
        Route::get('listar-laboratories', [LaboratoryController::class, 'listarLaboratories'])->name('laboratories.listar');
        # module zones
        Route::resource('zones', ZoneController::class);
        # list zones
        Route::get('listar-zones', [ZoneController::class, 'listarZones'])->name('zones.listar');
        # module Client Types
        Route::resource('clientTypes', ClientTypeController::class);
        # list Client Types
        Route::get('listar-clientTypes', [ClientTypeController::class, 'listarClientTypes'])->name('clientTypes.listar');
        # module locals
        Route::resource('locals', LocalController::class);
        # list locals
        Route::get('listar-locals', [LocalController::class, 'listarLocals'])->name('locals.listar');
        # module Products
        Route::resource('products', ProductController::class);
        # list Products
        Route::get('listar-products', [ProductController::class, 'listarProducts'])->name('products.listar');
        # module Customer
        Route::resource('customers', CustomerController::class);
        # list Customers
        Route::get('listar-customers', [CustomerController::class, 'listarCustomers'])->name('customers.listar');  
        # module Products
        Route::resource('product_prices', ProductPriceController::class);
        # list Products
        Route::get('listar-product_prices', [ProductPriceController::class, 'listarProductsprice'])->name('product_prices.listar');
        Route::get('listar-products', [ProductController::class, 'listarProducts'])->name('products.listar');
        # module Movements
        Route::resource('movements', MovementController::class);
        # list Movements
        Route::get('listar-movements', [MovementController::class, 'listMovements'])->name('movements.listar');
        #Finalize Movement
        Route::post('movements/{id}/finalize', [MovementController::class, 'finalize'])->name('panel.movements.finalize');
        # module role
        Route::resource('roles', RoleController::class);
        # list roles
        Route::get('listar-roles', [RoleController::class, 'listarRoles'])->name('roles.listar');
        # module permission
        Route::resource('permissions', PermissionController::class);
        # list permissions
        Route::get('listar-permissions', [PermissionController::class, 'listarPermissions'])->name('permissions.listar');
        # Module Users
        Route::resource('users', UserController::class);
        # list users
        Route::get('listar-users', [UserController::class, 'listarUsers'])->name('users.listar');
        # module Inventory
        Route::resource('inventory', InventoryController::class);
        # list Inventory
        Route::get('listar-inventory', [InventoryController::class, 'listInventory'])->name('inventory.listar');
        # module DetailsProductMovements 
        Route::resource('product-movements', ProductMovementController::class);
        Route::delete('product-movements/delete', [ProductMovementController::class, 'destroy']);
        # list ProductMovements
        Route::get('listar-product-movements', [ProductMovementController::class, 'listProductMovements']);
        # module sale
        // Route::resource('sales', SaleController::class);
        #Print ProductoMovement
        Route::get('/movements/{movement}/print', [MovementController::class, 'print'])->name('movements.print');
        # Route group for inputs, selects and autocomplete
      
        # module sale
        Route::resource('sales', SaleController::class);
      
      
        Route::prefix('inputs')->name('inputs.')->group(function () {
            # get product list
            Route::get('product_list', [SelectController::class, 'getProducts'])->name('product_list');
            # get laboratory list
            Route::get('laboratory_list', [SelectController::class, 'getLaboratoryList'])->name('laboratory_list');
            # get category list
            Route::get('category_list', [SelectController::class, 'getCategoryList'])->name('category_list');
            Route::get('local_list', [SelectController::class, 'getLocalList'])->name('local_list');
            Route::get('role_list', [SelectController::class, 'getRoleList'])->name('role_list');
            # get supplier list
            Route::get('suppliers', [SelectController::class, 'getSuppliers'])->name('suppliers_list');
            # get user list
            Route::get('users', [SelectController::class, 'getUsers'])->name('users_list');
            #get movements list
            Route::get('movement-types', [SelectController::class, 'getMovementTypes'])->name('movement-types_list');
            # get type PAYMENTS LIST
            Route::get('type-payments', [SelectController::class, 'getTypePaymentList'])->name('type-payments_list');
            # get client_type list
            Route::get('client_type_list', [SelectController::class, 'getClient_typeList'])->name('client_type_list');
        });

        # Route group for reports
        Route::prefix('reports')->name('reports.')->group(function () {

            # Exports to Excel
            Route::get('/export-excel-laboratories', [LaboratoryController::class, 'exportExcel'])->name('laboratories.excel');
            Route::get('/export-excel-categories', [CategoryController::class, 'exportExcel'])->name('categories.excel');
            Route::get('/export-excel-products', [ProductController::class, 'exportExcel'])->name('products.excel');

            # Excel imports
            Route::post('/import-excel-laboratories', [LaboratoryController::class, 'importExcel'])->name('reports.laboratories.import');
            Route::post('/import-excel-categories', [CategoryController::class, 'importExcel'])->name('reports.categories.import');
            Route::post('/import-excel-products', [ProductController::class, 'importExcel'])->name('reports.products.import');
            # Exports to PDF
        });


        Route::prefix('combobox')->name('combobox.')->group(function () {
            Route::get('customer', [ComboboxController::class, 'comboBoxCustomer'])->name('customer');
            Route::get('doctor', [ComboboxController::class, 'comboBoxDoctor'])->name('doctor');
        });
    });
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
