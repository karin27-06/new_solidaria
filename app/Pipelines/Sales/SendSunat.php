<?php

namespace App\Pipelines\Sales;

use App\Models\Customer;
use App\Models\Product;
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
      $this->getInvoiceData(
        $saleData['customer_id'],
        $saleData['products'],
        $saleData['serie'],
        $saleData['correlative']
      )
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
      'departamento' => 'PIURA',
      'provincia' => 'PIURA',
      'distrito' => 'PIURA',
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
  private function getItems(array $productos): array
  {
    $products = [];
    foreach ($productos as $producto) {
      $product = Product::find($producto['product_local_id']);
      if (!$product) {
        Log::error("Product with ID {$producto['id']} not found.");
        throw new \Exception("Product not found");
      }
      $cantidad =  $producto['quantity_fraction'] > 0 ? ($producto['quantity_box'] * $product->fraction) + $producto['quantity_fraction'] : $producto['quantity_box'];
      $precio_unitario = $producto['quantity_fraction'] > 0 ? $producto['price_fraction'] : $producto['price_box'];
      $totalBase = round($cantidad * $precio_unitario, 2);
      $igv = round($totalBase * 0.18, 2);
      $precio_unitario_final = round(($totalBase + $igv) / $cantidad, 2);

      $products[] = [
        'cod_producto' => 'P' . $product->id, // Código del producto
        'unidad' => 'NIU', // Unidad de medida - Catalog. 03
        'cantidad' => $cantidad,
        'mto_valor_unitario' => round($precio_unitario, 2),
        'descripcion' => $product->name,
        'mto_base_igv' => $totalBase,
        'porcentaje_igv' => 18.00, // Porcentaje IGV - Catalog. 07
        'igv' => $igv,
        'tip_afe_igv' => '10', // Afectación IGV - Catalog. 08
        'total_impuestos' => $igv,
        'mto_valor_venta' => $totalBase,
        'mto_precio_unitario' => $precio_unitario_final,
      ];
    }
    return $products;
  }
  private function getLegends(): array
  {
    return [
      'code' => '1000',
      'value' => 'SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES',
    ];
  }
  public function getInvoiceData(int $customer_id, array $productos, string $serie, string $correlativo): array
  {
    $items = $this->getItems($productos);

    // Calculate totals from items
    $mto_oper_gravadas = 0;
    $mto_igv = 0;
    $total_impuestos = 0;
    $valor_venta = 0;

    foreach ($items as $item) {
      $mto_oper_gravadas += $item['mto_valor_venta'];
      $mto_igv += $item['igv'];
      $total_impuestos += $item['total_impuestos'];
      $valor_venta += $item['mto_valor_venta'];
    }

    // Round all totals to 2 decimal places
    $mto_oper_gravadas = round($mto_oper_gravadas, 2);
    $mto_igv = round($mto_igv, 2);
    $total_impuestos = round($total_impuestos, 2);
    $valor_venta = round($valor_venta, 2);
    $sub_total = round($mto_oper_gravadas + $mto_igv, 2);
    return [
      'ubl_version' => '2.1',
      'tipo_operacion' => '0101', // Venta - Catalog. 51
      'tipo_doc' => '01', // Factura - Catalog. 01
      'serie' => $serie,
      'correlativo' => $correlativo,
      'fecha_emision' => (new DateTime('2025-05-29 13:05:00-05:00')),
      'tipo_moneda' => 'PEN', // Sol - Catalog. 02
      'company' => $this->getCompany(),
      'client' => $this->getCustomer($customer_id),
      'items' => $items,
      'legends' => $this->getLegends(),
      'mto_oper_gravadas' => $mto_oper_gravadas,
      'mto_igv' => $mto_igv,
      'total_impuestos' => $total_impuestos,
      'valor_venta' => $valor_venta,
      'sub_total' => $sub_total,
      'mto_imp_venta' => $sub_total,
    ];
  }
}
