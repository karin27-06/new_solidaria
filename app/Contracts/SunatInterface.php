<?php

namespace App\Contracts;

use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\See;

interface SunatInterface
{
  public function getSee(): See;
  public function getCompanyData(): Company;
  public function getAddressData(): Address;
  // public function getCustomerData(int $customer_id): array;
}
