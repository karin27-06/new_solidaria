<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\Product_Local;
use App\Models\ProductMovement;
use App\Pipelines\Movement\StoreProductMovementPipeline;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductMovementController extends Controller
{
    protected $pipeline;

    public function __construct(StoreProductMovementPipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public function listProductMovements(Request $request)
    {
        try {
            $request->validate([
                'movementId' => 'required|integer|exists:movements,id'
            ]);

            $movementId = $request->query('movementId');

            $movement = Movement::with('product_movements.product.laboratory')->findOrFail($movementId);

            $productMovements = $movement->product_movements ?? collect([]);

            $formattedMovements = $productMovements->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'productId' => $pm->product_id,
                    'quantity' => $pm->quantity,
                    'fractionQuantity' => $pm->fraction_quantity,
                    'unitPrice' => number_format($pm->unit_price, 2),
                    'unitPriceEx' => number_format($pm->unit_price, 2),
                    'fractionPrice' => number_format($pm->fraction_price, 2),
                    'totalPrice' => number_format($pm->total_price, 2),
                    'labName' => $pm->product->laboratory->name ?? 'N/A',
                    'productName' => $pm->product->name ?? 'Unknown',
                    'unitPrices' => number_format($pm->unit_price, 2) . ' - ' . number_format($pm->fraction_price, 2),
                    'batch' => $pm->batch,
                    'expiryDate' => $pm->expiry_date,
                    'expiryDateDisplay' => \Carbon\Carbon::parse($pm->expiry_date)->format('d-m-Y'),
                    'movementId' => $pm->movement_id,
                    'movementTypeId' => $pm->type_movement_id ?? 1,
                    'quantityStatus' => $pm->quantity_type,
                    'quantityType' => $pm->quantity_type === 1 ? 'Box' : ($pm->quantity_type === 0 ? 'Fraction' : 'Both'),
                    'totalQuantity' => (string) ($pm->quantity + $pm->fraction_quantity),
                    'generalPrice' => number_format($pm->unit_price, 2) . ' - ' . number_format($pm->fraction_price, 2),
                    'status' => $pm->status ?? 1,
                ];
            });

            $tasaIgv = 0.18;
            $totalSubtotal = 0;
            $totalIgv = 0;
            $totalTotal = 0;

            foreach ($productMovements as $pm) {
                $totalPrice = $pm->total_price ?? 0;

                if ($movement->igv_status == 1) {
                    $subtotal = $totalPrice / (1 + $tasaIgv);
                    $igv = $totalPrice - $subtotal;
                    $total = $totalPrice;
                } else {
                    $subtotal = $totalPrice;
                    $igv = $subtotal * $tasaIgv;
                    $total = $subtotal + $igv;
                }

                $totalSubtotal += $subtotal;
                $totalIgv += $igv;
                $totalTotal += $total;
            }

            return response()->json([
                'success' => true,
                'message' => 'Product movements fetched successfully',
                'data' => $formattedMovements,
                'subtotal' => number_format($totalSubtotal, 2),
                'tax' => number_format($totalIgv, 2),
                'total' => number_format($totalTotal, 2),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movement not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product movements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:0',
                'fraction_quantity' => 'required|integer|min:0',
                'total_price' => 'required|numeric|min:0',
                'unit_price' => 'required|numeric|min:0',
                'batch' => 'required|string',
                'expiry_date' => 'required|date',
                'quantity_type' => 'required|in:0,1,2',
                'movement_id' => 'required|integer|exists:movements,id',
            ]);

            $result = $this->pipeline->handle($validated, $validated['movement_id']);

            $movement = $result['data'];

            return response()->json([
                'success' => true,
                'message' => 'Product movement created successfully',
                'data' => [
                    'id' => $movement->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'fraction_quantity' => $validated['fraction_quantity'],
                    'total_price' => $validated['total_price'],
                    'unit_price' => $validated['unit_price'],
                    'batch' => $validated['batch'],
                    'expiry_date' => $validated['expiry_date'],
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product movement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:0',
                'fraction_quantity' => 'required|integer|min:0',
                'total_price' => 'required|numeric|min:0',
                'unit_price' => 'required|numeric|min:0',
                'batch' => 'required|string',
                'expiry_date' => 'required|date',
                'quantity_type' => 'required|in:0,1,2',
                'movement_id' => 'required|integer|exists:movements,id',
            ]);

            $productMovement = ProductMovement::findOrFail($id);

            $productMovement->update([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'fraction_quantity' => $validated['fraction_quantity'],
                'total_price' => $validated['total_price'],
                'unit_price' => $validated['unit_price'],
                'fraction_price' => $validated['unit_price'] / ($productMovement->product->fraction ?? 1),
                'batch' => $validated['batch'],
                'expiry_date' => $validated['expiry_date'],
                'quantity_type' => $validated['quantity_type'],
                'movement_id' => $validated['movement_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product movement updated successfully',
                'data' => [
                    'id' => $productMovement->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'fraction_quantity' => $validated['fraction_quantity'],
                    'total_price' => $validated['total_price'],
                    'unit_price' => $validated['unit_price'],
                    'batch' => $validated['batch'],
                    'expiry_date' => $validated['expiry_date'],
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product movement not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product movement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:product_movements,id',
            ]);

            $productMovement = ProductMovement::findOrFail($validated['id']);
            $productMovement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de producto eliminado exitosamente',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movimiento de producto no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el movimiento de producto',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}