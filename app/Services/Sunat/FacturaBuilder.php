<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
use App\Models\Sale;
use Carbon\Carbon;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class FacturaBuilder
{
  protected $see;
  protected Company $companyData;
  protected SunatInterface $seeService;
  public function __construct(SunatInterface $see)
  {
    $this->see = $see->getSee();
    $this->companyData = $see->getCompanyData();
    $this->seeService = $see;
  }
  private function buildCustomer(array $customerData): Client
  {
    return $this->seeService->getClientData($customerData);
  }
  private function buildFormaPago(): FormaPagoContado
  {
    return $this->seeService->getFormaPagoData();
  }
  private function buildDetails(array $items): array
  {
    $details = [];
    foreach ($items as $item) {
      $details[] = $this->seeService->getSaleDetail($item);
    }
    return $details;
  }
  private function buildLeyends(array $legends): array
  {
    return [
      $this->seeService->getLegend($legends)
    ];
  }
  private function buildInvoice(array $data): Invoice
  {
    $invoice = $this->seeService->getInvoice($data, $this->companyData, $this->buildCustomer($data['client']), $this->buildDetails($data['items']), $this->buildLeyends($data['legends']), $this->buildFormaPago());
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
