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
  public function generateSend(): array
  {
    $client = (new Client())
      ->setTipoDoc('6')
      ->setNumDoc('20000000001')
      ->setRznSocial('EMPRESA X');

    $address = (new Address())
      ->setUbigueo('150101')
      ->setDepartamento('LIMA')
      ->setProvincia('LIMA')
      ->setDistrito('LIMA')
      ->setUrbanizacion('-')
      ->setDireccion('Av. Villa Nueva 221')
      ->setCodLocal('0000');

    $company = (new Company())
      ->setRuc('20123456789')
      ->setRazonSocial('GREEN SAC')
      ->setNombreComercial('GREEN')
      ->setAddress($address);

    $invoice = (new Invoice())
      ->setUblVersion('2.1')
      ->setTipoOperacion('0101') // Venta - Catalog. 51
      ->setTipoDoc('01') // Factura - Catalog. 01 
      ->setSerie('F001')
      ->setCorrelativo('1')
      ->setFechaEmision(new DateTime('2025-05-27 13:05:00-05:00')) // Zona horaria: Lima
      ->setFormaPago(new FormaPagoContado) // FormaPago: Contado
      ->setTipoMoneda('PEN') // Sol - Catalog. 02
      ->setCompany($company)
      ->setClient($client)
      ->setMtoOperGravadas(100.00)
      ->setMtoIGV(18.00)
      ->setTotalImpuestos(18.00)
      ->setValorVenta(100.00)
      ->setSubTotal(118.00)
      ->setMtoImpVenta(118.00);

    $item = (new SaleDetail())
      ->setCodProducto('P001')
      ->setUnidad('NIU') // Unidad - Catalog. 03
      ->setCantidad(2)
      ->setMtoValorUnitario(50.00)
      ->setDescripcion('PRODUCTO 1')
      ->setMtoBaseIgv(100)
      ->setPorcentajeIgv(18.00) // 18%
      ->setIgv(18.00)
      ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
      ->setTotalImpuestos(18.00) // Suma de impuestos en el detalle
      ->setMtoValorVenta(100.00)
      ->setMtoPrecioUnitario(59.00);
    $legend = (new Legend())
      ->setCode('1000') // Monto en letras - Catalog. 52
      ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');
    $invoice->setDetails([$item])
      ->setLegends([$legend]);
    // enviar sunat
    Log::info('enviado', [
      'serie' => $invoice->getSerie(),
      'correlativo' => $invoice->getCorrelativo(),
      'fechaEmision' => Carbon::parse($invoice->getFechaEmision())->format('Y-m-d H:i:s'),
    ]);
    $resulta = $this->see->send($invoice);

    if (!$resulta->isSuccess()) {
      return [
        'success' => false,
        'errors' => $resulta->getError(),
        'invoice' => $invoice,
      ];
    }
    // guardar el xml firmado 
    FacadesStorage::disk('local')->put(
      'facturas2/' . $invoice->getName() . '.xml',
      $this->see->getFactory()->getLastXml()
    );

    // cdr
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
