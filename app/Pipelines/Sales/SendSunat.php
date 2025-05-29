<?php

namespace App\Pipelines\Sales;

use App\Models\Customer;
use App\Services\Sunat\borradoFacturaBuilder;
use App\Services\Sunat\FacturaBuilder;
use Closure;
use Illuminate\Support\Facades\Log;

class SendSunat
{

  protected $facturaBuilder;
  public function __construct(borradoFacturaBuilder $facturaBuilder)
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

  private function getCustomer(int $customer_id)
  {
    $customer = Customer::find($customer_id);
    if (!$customer) {
      Log::error("Customer with ID {$customer_id} not found.");
      throw new \Exception("Customer not found");
    }
    return [
      'tipo_doc' => $customer->tipo_doc,
      'num_doc' => $customer->num_doc,
      'razon_social' => $customer->razon_social,
    ];
  }
}
