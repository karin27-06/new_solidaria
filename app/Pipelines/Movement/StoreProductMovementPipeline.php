<?php

namespace App\Pipelines\Movement;

use App\Models\Product_Local;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreProductMovementPipeline
{
      /**
     * Ejecuta el pipeline para crear un movimiento de producto y actualizar el stock.
     *
     * @param array $data Datos del movimiento
     * @param int $movementId ID del movimiento asociado
     * @return array Respuesta con el resultado
     * @throws ValidationException
     * @throws \Exception
     */
    public function handle(array $data, int $movementId): array
    {
        // Validar datos
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'fraction_quantity' => 'required|integer|min:0',
            'total_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'batch' => 'required|string|max:15',
            'expiry_date' => 'required|date',
            'quantity_type' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Obtener el usuario autenticado y su local_id
        $user = Auth::user();
        if (!$user || !$user->local_id) {
            throw new \Exception('El usuario no está asociado a un local');
        }
        $localId = $user->local_id;

        try {
            DB::beginTransaction();

            // Crear el registro en product_movements
            $movement = ProductMovement::create([
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'fraction_quantity' => $data['fraction_quantity'],
                'total_price' => $data['total_price'],
                'unit_price' => $data['unit_price'],
                'fraction_price' => $data['fraction_quantity'] > 0 ? $data['unit_price'] : 0,
                'batch' => $data['batch'],
                'expiry_date' => $data['expiry_date'],
                'movement_id' => $movementId,
                'quantity_type' => $data['quantity_type'],
                'status' => 1,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Movimiento de producto creado y stock actualizado exitosamente',
                'data' => $movement,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}