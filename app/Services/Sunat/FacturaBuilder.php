<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
use App\Models\Sale;
use Carbon\Carbon;
use Greenter\Model\Client\Client;
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
  protected Company $companyData;
  public function __construct(SunatInterface $see)
  {
    $this->see = $see->getSee();
    $this->companyData = $see->getCompanyData();
  }
  private function buildCustomer(array $customerData): Client
  {
    return (new Client())
      ->setTipoDoc($customerData['tipo_doc'])
      ->setNumDoc($customerData['num_doc'])
      ->setRznSocial($customerData['razon_social']);
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
      ->setCompany($this->companyData)
      ->setClient($this->buildCustomer($data['client']))
      ->setDetails($this->buildDetails($data['items']))
      ->setLegends($this->buildLeyends($data['legends']));

    $this->setInvoiceAmounts($invoice, $data);

    return $invoice;
  }

  public function createInvoice(array $data, int $id_sale): array
  {
    // contruyo el comprobante
    $invoice = $this->buildInvoice($data);
    // envio a SUNAT
    $resulta = $this->see->send($invoice);
    // si hubo error
    if (!$resulta->isSuccess()) {
      $this->logInvoiceError($invoice, $resulta);
      return $this->buildErrorResponse($resulta, $invoice);
    }
    // guarda los archivos
    $this->storeInvoiceFiles($invoice, $resulta);
    // actualiza el estado de la venta
    $this->updateSaleStatus($id_sale, true);
    return $this->buildSuccessResponse($invoice);
  }


  /**
   * Registra el error de la factura en los logs
   */
  protected function logInvoiceError($invoice, $result): void
  {
    Log::error('Error al enviar la factura', [
      'serie' => $invoice->getSerie(),
      'correlativo' => $invoice->getCorrelativo(),
      'fechaEmision' => Carbon::parse($invoice->getFechaEmision())->format('Y-m-d H:i:s'),
      'error' => $result->getError()->getMessage(),
    ]);
  }

  /**
   * Almacena el xml y cdr para boletas y facturas
   */
  protected function storeInvoiceFiles($invoice, $result): void
  {
    $folder = $invoice->getTipoDoc() === '03' ? 'boletas' : 'facturas';
    FacadesStorage::disk('local')->put(
      $folder . '/' . $invoice->getName() . '.xml',
      $this->see->getFactory()->getLastXml()
    );
    FacadesStorage::disk('local')->put(
      'cdr/' . $folder . '/R-' . $invoice->getName() . '.zip',
      $result->getCdrZip()
    );
  }

  /**
   * Actualiza el estado de la venta en SUNAT
   */
  protected function updateSaleStatus(int $id_sale, bool $status): void
  {
    Sale::where('id', $id_sale)->update(['state_sunat' => $status]);
  }

  /**
   * Construye la respuesta de error
   */
  protected function buildErrorResponse($result, $invoice): array
  {
    return [
      'success' => false,
      'errors' => $result->getError()->getMessage(),
      'invoice' => $invoice,
    ];
  }

  /**
   * Construye la respuesta exitosa
   */
  protected function buildSuccessResponse($invoice): array
  {
    return [
      'success' => true,
      'errors' => null,
      'invoice' => $invoice,
    ];
  }
}
