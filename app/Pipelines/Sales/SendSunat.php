<?php

namespace App\Pipelines\Sales;

use App\Services\Sunat\FacturaBuilder;
use Closure;
use Illuminate\Support\Facades\Log;
use Laravel\Pail\ValueObjects\Origin\Console;

class SendSunat
{

  protected $facturaBuilder;
  public function __construct(FacturaBuilder $facturaBuilder)
  {
    $this->facturaBuilder = $facturaBuilder;
  }

  public function __invoke($saleData, Closure $next)
  {
    Log::info('Enviando a sunat');
    $respuesta = $this->facturaBuilder->generateSend();
    Log::info('Respuesta de sunat: ' . json_encode($respuesta));
    return $next($saleData);
  }
}
