<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
use Carbon\Carbon;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class FacturaBuilder
{
  protected $see;
  public function __construct(SunatInterface $see)
  {
    $this->see = $see->getSee();
  }
  private function buildCustomer(array $customerData): Client
  {
    return (new Client())
      ->setTipoDoc($customerData['tipo_doc'])
      ->setNumDoc($customerData['num_doc'])
      ->setRznSocial($customerData['razon_social']);
  }
  private function buildAddress(array $addressData): Address
  {

    return (new Address())
      ->setUbigueo($addressData['ubigueo'])
      ->setDepartamento($addressData['departamento'])
      ->setProvincia($addressData['provincia'])
      ->setDistrito($addressData['distrito'])
      ->setUrbanizacion($addressData['urbanizacion'])
      ->setDireccion($addressData['direccion'])
      ->setCodLocal($addressData['cod_local']);
  }
  private function buildCompany(array $companyData): Company
  {
    return (new Company())
      ->setRuc($companyData['ruc'])
      ->setRazonSocial($companyData['razon_social'])
      ->setNombreComercial($companyData['nombre_comercial'])
      ->setAddress($this->buildAddress($companyData['address']));
  }
  private function buildFormaPago(): FormaPagoContado
  {
    return (new FormaPagoContado());
  }
  private function buildDetails(array $items): array
  {
    $details = [];
    foreach ($items as $item) {
      $details[] = (new SaleDetail())
        ->setCodProducto($item['cod_producto'])
        ->setUnidad($item['unidad'])
        ->setCantidad($item['cantidad'])
        ->setMtoValorUnitario($item['mto_valor_unitario'])
        ->setDescripcion($item['descripcion'])
        ->setMtoBaseIgv($item['mto_base_igv'])
        ->setPorcentajeIgv($item['porcentaje_igv'])
        ->setIgv($item['igv'])
        ->setTipAfeIgv($item['tip_afe_igv'])
        ->setTotalImpuestos($item['total_impuestos'])
        ->setMtoValorVenta($item['mto_valor_venta'])
        ->setMtoPrecioUnitario($item['mto_precio_unitario']);
    }
    return $details;
  }
  private function buildLeyends(array $legends): array
  {
    return [
      (new Legend())
        ->setCode($legends['code'])
        ->setValue($legends['value'])
    ];
  }
  private function setInvoiceAmounts(Invoice $invoice, array $data): void
  {
    $invoice
      ->setMtoOperGravadas($data['mto_oper_gravadas'])
      ->setMtoIGV($data['mto_igv'])
      ->setTotalImpuestos($data['total_impuestos'])
      ->setValorVenta($data['valor_venta'])
      ->setSubTotal($data['sub_total'])
      ->setMtoImpVenta($data['mto_imp_venta']);
  }
  private function buildInvoice(array $data): Invoice
  {
    $invoice = (new Invoice())
      ->setUblVersion($data['ubl_version'])
      ->setTipoOperacion($data['tipo_operacion'])
      ->setTipoDoc($data['tipo_doc'])
      ->setSerie($data['serie'])
      ->setCorrelativo($data['correlativo'])
      ->setFechaEmision($data['fecha_emision'])
      ->setFormaPago($this->buildFormaPago())
      ->setTipoMoneda($data['tipo_moneda'])
      ->setCompany($this->buildCompany($data['company']))
      ->setClient($this->buildCustomer($data['client']))
      ->setDetails($this->buildDetails($data['items']))
      ->setLegends($this->buildLeyends($data['legends']));

    $this->setInvoiceAmounts($invoice, $data);

    return $invoice;
  }

  public function createInvoice(array $data): array
  {
    $invoice = $this->buildInvoice($data);
    Log::info('data', $data);
    Log::info('enviado', [
      'serie' => $invoice->getSerie(),
      'correlativo' => $invoice->getCorrelativo(),
      'fechaEmision' => Carbon::parse($invoice->getFechaEmision())->format('Y-m-d H:i:s'),
    ]);

    $resulta = $this->see->send($invoice);

    if (!$resulta->isSuccess()) {
      Log::error('Error al enviar la factura', [
        'serie' => $invoice->getSerie(),
        'correlativo' => $invoice->getCorrelativo(),
        'fechaEmision' => Carbon::parse($invoice->getFechaEmision())->format('Y-m-d H:i:s'),
        'error' => $resulta->getError(),
      ]);
      return [
        'success' => false,
        'errors' => $resulta->getError(),
        'invoice' => $invoice,
      ];
    }

    FacadesStorage::disk('local')->put(
      'facturas/' . $invoice->getName() . '.xml',
      $this->see->getFactory()->getLastXml()
    );

    FacadesStorage::disk('local')->put(
      'cdr/' . 'R-' . $invoice->getName() . '.zip',
      $resulta->getCdrZip()
    );

    return [
      'success' => true,
      'errors' => null,
      'invoice' => $invoice,
    ];
  }
}
