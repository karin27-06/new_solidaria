<?php

namespace App\Pipelines\Sales;

use App\Models\TypeVoucher;
use Closure;
use Illuminate\Support\Facades\Log;

class CodeSale
{
  public function __invoke($saleDate, Closure $next)
  {
    $typeVoucher = $saleDate['type_voucher_id'];
    $local_id = $saleDate['local_id'] ?? 1;
    // Obtener el modelo Voucher con su prefijo
    $voucher = TypeVoucher::findOrFail($typeVoucher);

    // Obtener la entrada en la tabla pivote
    $pivot = $voucher->localVouchers()
      ->where('local_id', $local_id)
      ->firstOrFail()
      ->pivot;

    // Incrementar el correlativo
    $pivot->correlative += 1;
    $pivot->save();

    Log::info('codeSale pipeline passed', [
      'type_voucher_id' => $typeVoucher,
      'local_id' => $local_id,
      'voucher_code' => $pivot->serie
    ]);
    // Agregar el código del voucher al array de datos de la venta
    $saleDate['voucher_code'] = $pivot->serie . '-' . str_pad($pivot->correlative + 1, 4, '0', STR_PAD_LEFT);
    $saleDate['serie']  = $pivot->serie;
    $saleDate['correlative'] =  str_pad($pivot->correlative + 1, 4, '0', STR_PAD_LEFT);
    return $next($saleDate);
  }
}
