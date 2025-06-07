<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;

class SunatServices implements SunatInterface
{


  protected See $see;

  public function __construct()
  {
    $this->see = new See();
    $this->see->setCertificate(file_get_contents(config('greenter.certificate_path')));
    $this->see->setService(config('greenter.endpoint'));
    $this->see->setClaveSOL(config('greenter.credentials.ruc'), config('greenter.credentials.usuario'), config('greenter.credentials.password'));
  }


  /**
   * Get the See instance.
   *
   * @return \Greenter\See
   */
  public function getSee(): \Greenter\See
  {
    return $this->see;
  }


  /**
   * Get the company data.
   *
   * @return Company
   */
  public function getCompanyData(): Company
  {
    return (new Company())
      ->setRuc(config('greenter.company.ruc'))
      ->setRazonSocial(config('greenter.company.razon_social'))
      ->setNombreComercial(config('greenter.company.nombre_comercial'))
      ->setAddress(
        (new Address())
          ->setUbigueo(config('greenter.company.address.ubigueo'))
          ->setDepartamento(config('greenter.company.address.departamento'))
          ->setProvincia(config('greenter.company.address.provincia'))
          ->setDistrito(config('greenter.company.address.distrito'))
          ->setUrbanizacion(config('greenter.company.address.urbanizacion'))
          ->setDireccion(config('greenter.company.address.direccion'))
          ->setCodLocal(config('greenter.company.address.cod_local'))
      );
  }

  /**
   * Get the address data.
   *
   * @return Address
   */
  public function getAddressData(): Address
  {
    return (new Address())
      ->setUbigueo(config('greenter.company.address.ubigueo'))
      ->setDepartamento(config('greenter.company.address.departamento'))
      ->setProvincia(config('greenter.company.address.provincia'))
      ->setDistrito(config('greenter.company.address.distrito'))
      ->setUrbanizacion(config('greenter.company.address.urbanizacion'))
      ->setDireccion(config('greenter.company.address.direccion'))
      ->setCodLocal(config('greenter.company.address.cod_local'));
  }

  /**
   * Get the client data.
   * @param array $clientData
   * @return Client
   */
  public function getClientData(array $clientData): Client
  {
    return (new Client())
      ->setTipoDoc($clientData['tipo_doc'])
      ->setNumDoc($clientData['num_doc'])
      ->setRznSocial($clientData['razon_social']);
  }

  /**
   * Get the payment method data.
   * @return FormaPagoContado
   */

  public function getFormaPagoData(): FormaPagoContado
  {
    return (new FormaPagoContado());
  }

  /**
   * Get the sale detail data.
   * @param array $item
   * @return SaleDetail
   */
  public function getSaleDetail(array $item): SaleDetail
  {
    return (new SaleDetail())
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
  /**
   * Get the legend data.
   * @param array $legends
   * @return Legend
   */
  public function getLegend(array $legends): Legend
  {
    return (new Legend())
      ->setCode($legends['code'])
      ->setValue($legends['value']);
  }
  /**
   * Get the invoice data.
   * @param array $data
   * @param Company $company
   * @param Client $client
   * @param array $details
   * @param array $legends
   * @param FormaPagoContado $formaPago
   * @return Invoice
   */
  public function getInvoice(array $data, Company $company, Client $client, array $detalis, array $legends, FormaPagoContado $formaPago): Invoice
  {
    return (new Invoice())
      ->setUblVersion($data['ubl_version'])
      ->setTipoOperacion($data['tipo_operacion'])
      ->setTipoDoc($data['tipo_doc'])
      ->setSerie($data['serie'])
      ->setCorrelativo($data['correlativo'])
      ->setFechaEmision($data['fecha_emision'])
      ->setFormaPago($formaPago)
      ->setTipoMoneda($data['tipo_moneda'])
      ->setCompany($company)
      ->setClient($client)
      ->setDetails($detalis)
      ->setLegends($legends)
      ->setMtoOperGravadas($data['mto_oper_gravadas'])
      ->setMtoIGV($data['mto_igv'])
      ->setTotalImpuestos($data['total_impuestos'])
      ->setValorVenta($data['valor_venta'])
      ->setSubTotal($data['sub_total'])
      ->setMtoImpVenta($data['mto_imp_venta']);
  }
}
