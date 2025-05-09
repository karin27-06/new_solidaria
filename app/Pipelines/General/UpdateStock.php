<?php

namespace App\Pipelines\General;

use Closure;
use Illuminate\Support\Facades\Log;

class UpdateStock
{
  public function __invoke($saleData, Closure $next)
  {
    Log::info('Actualizando el stock del los productos en el local');
    return $next($saleData);
  }
}
