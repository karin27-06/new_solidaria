<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalePipelineRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Pipelines\General\UpdateStock;
use App\Pipelines\General\validateProducts;
use App\Pipelines\Sales\CalculateTotals;
use App\Pipelines\Sales\CodeSale;
use App\Pipelines\Sales\CreateSale;
use App\Pipelines\Sales\CreateSaleDetails;
use App\Pipelines\Sales\SendSunat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;
use Inertia\Inertia;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $sales = Sale::with('user', 'customer', 'local', 'doctor', 'typeVoucher', 'typePayment')
        //     ->orderBy('id', 'asc')
        //     ->paginate(10);
        // return response()->json([
        //     'sales' => SaleResource::collection($sales),
        //     'pagination' => [
        //         'total' => $sales->total(),
        //         'current_page' => $sales->currentPage(),
        //         'per_page' => $sales->perPage(),
        //         'last_page' => $sales->lastPage(),
        //         'from' => $sales->firstItem(),
        //         'to' => $sales->lastItem(),
        //     ],
        // ])->setStatusCode(200, 'Sales retrieved successfully', [
        //     'Content-Type' => 'application/json',
        // ]);
        return Inertia::render('panel/sale/SaleIndex');
    }

    public function viewSale()
    {
        return Inertia::render('panel/sale/SaleIndex');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $validated = $request->validated();
        $sale = Sale::create($validated);
        return response()->json([
            'status' => true,
            'message' => 'Sale created successfully',
            'sale' => new SaleResource($sale),
        ], 201, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function sendSale(SalePipelineRequest $request)
    {
        $saleData = $request->validated();
        $sale = Pipeline::send($saleData)
            ->through([
                validateProducts::class,
                CalculateTotals::class,
                CodeSale::class,
                CreateSale::class,
                CreateSaleDetails::class,
                UpdateStock::class,
                SendSunat::class,
            ])
            ->thenReturn();
        return response()->json([
            'status' => true,
            'message' => 'Venta creado correctamente',
            'sale' => $sale,
        ])->setStatusCode(201);
    }


    public function viewSaleList()
    {
        return Inertia::render('panel/saleList/saleListIndex');
    }


    public function getSalesList()
    {
        $sales = Sale::with('user', 'customer', 'local', 'doctor', 'typeVoucher', 'typePayment')
            ->orderBy('id', 'asc')
            ->paginate(12);
        return response()->json([
            'sales' => SaleResource::collection($sales),
            'pagination' => [
                'total' => $sales->total(),
                'current_page' => $sales->currentPage(),
                'per_page' => $sales->perPage(),
                'last_page' => $sales->lastPage(),
                'from' => $sales->firstItem(),
                'to' => $sales->lastItem(),
            ],
        ])->setStatusCode(200, 'Sales retrieved successfully', [
            'Content-Type' => 'application/json',
        ]);
    }
}
