<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ClientType;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Pipelines\FilterByCode;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Customer::class);
        return Inertia::render('panel/customer/indexCustomer');
    }

    public function listarCustomers(Request $request)
    {
        // authorization so you can access the method

        Gate::authorize('viewAny', Customer::class);

        try {
            $code = $request->get('code');
            $customers = Pipeline::send($code)
                ->send(Customer::query())
                ->through([
                    new FilterByCode($code)
                ])
                ->thenReturn()->orderBY('id', 'asc')->paginate(10);
            return response()->json([
                'customers' => CustomerResource::collection($customers),
                'pagination' => [
                    'total' => $customers->total(),
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'last_page' => $customers->lastPage(),
                    'from' => $customers->firstItem(),
                    'to' => $customers->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al cargar los datos del cliente',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $client_type = ClientType::select('id', 'name')
            ->orderBy('id')
            ->get();

        return Inertia::render('panel/customer/components/formCustomer', [
            'client_types' => $client_type,
        ]);
    }
    public function store(StoreCustomerRequest $request)
    {
        Gate::authorize('create', Customer::class);
        $validated = $request->validated();
        $customer = Customer::create($validated);
        return redirect()->route('panel.customers.index')->with('message', 'Cliente creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        Gate::authorize('view', $customer);
        return response()->json([
            'message' => 'Cliente encontrado',
            'customer' => new CustomerResource($customer),
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        Gate::authorize('update', $customer);
        $validated = $request->validated();
        $customer->update($validated);
        return response()->json([
            'message' => 'Cliente actualizado de manera correcta',
            'customer' => new CustomerResource($customer->refresh()),
        ]);
    }


}
