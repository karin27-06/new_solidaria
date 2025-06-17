<?php

namespace App\Contracts;

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;

interface SunatInterface
{
  public function getSee(): See;
  public function getCompanyData(): Company;
  public function getAddressData(): Address;
  public function getClientData(array $clientData): Client;
  public function getFormaPagoData(): FormaPagoContado;
  public function getSaleDetail(array $item): SaleDetail;
  public function getLegend(array $legends): Legend;
  public function getInvoice(array $data, Company $company, Client $client, array $detalis, array $legends, FormaPagoContado $formaPago): Invoice;
}
