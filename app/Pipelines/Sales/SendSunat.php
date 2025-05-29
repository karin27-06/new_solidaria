<?php

namespace App\Pipelines\Sales;

use App\Models\Customer;
use App\Services\Sunat\borradoFacturaBuilder;
use App\Services\Sunat\FacturaBuilder;
use Closure;
use DateTime;
use Illuminate\Support\Facades\Log;

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
    $respuesta = $this->facturaBuilder->createInvoice(
      $this->getInvoiceData($saleData['customer_id'])
    );
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
      'tipo_doc' => $customer->clientType->tipo_doc,
      'num_doc' => $customer->code,
      'razon_social' => $customer->firstname . ' ' . $customer->lastname,
    ];
  }
  private function getAddress(): array
  {
    return [
      'ubigueo' => '150101',
      'departamento' => 'LIMA',
      'provincia' => 'LIMA',
      'distrito' => 'LIMA',
      'urbanizacion' => '-',
      'direccion' => 'Av. Ejemplo 123',
      'cod_local' => '0000',
    ];
  }
  private function getCompany(): array
  {
    return [
      'ruc' => '20000000001',
      'razon_social' => 'SOLIDARIA SAC',
      'nombre_comercial' => 'BOTICA SOLIDARIA',
      'address' => $this->getAddress(),
    ];
  }
  private function getItems(): array
  {
    return [
      [
        'cod_producto' => 'P001',
        'unidad' => 'NIU',
        'cantidad' => 2,
        'mto_valor_unitario' => 50.00,
        'descripcion' => 'Producto A',
        'mto_base_igv' => 100.00,
        'porcentaje_igv' => 18.00,
        'igv' => 18.00,
        'tip_afe_igv' => '10',
        'total_impuestos' => 18.00,
        'mto_valor_venta' => 100.00,
        'mto_precio_unitario' => 59.00,
      ],
      // Agregar más productos según sea necesario
    ];
  }
  private function getLegends(): array
  {
    return [
      'code' => '1000',
      'value' => 'SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES',
    ];
  }

  private function getInvoiceAmounts(): array
  {
    return [
      'mtoOperGravadas' => 100.00,
      'mtoIGV' => 18.00,
      'totalImpuestos' => 18.00,
      'valorVenta' => 100.00,
      'subTotal' => 118.00,
      'mtoImpVenta' => 118.00,
    ];
  }
  public function getInvoiceData(int $customer_id): array
  {
    return [
      'ubl_version' => '2.1',
      'tipo_operacion' => '0101', // Venta - Catalog. 51
      'tipo_doc' => '01', // Factura - Catalog. 01
      'serie' => 'F001',
      'correlativo' => '1',
      'fecha_emision' => (new DateTime('2025-05-27 13:05:00-05:00')),
      // 'formaPago' => 'Contado', // FormaPago: Contado
      'tipo_moneda' => 'PEN', // Sol - Catalog. 02
      'company' => $this->getCompany(),
      'client' => $this->getCustomer($customer_id),
      'items' => $this->getItems(),
      'legends' => $this->getLegends(),
      'mto_oper_gravadas' => 100.00,
      'mto_igv' => 18.00,
      'total_impuestos' => 18.00,
      'valor_venta' => 100.00,
      'sub_total' => 118.00,
      'mto_imp_venta' => 118.00,
    ];
  }
}
