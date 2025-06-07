<?php

namespace App\Contracts;

use Greenter\See;

interface SunatInterface
{
  public function getSee(): See;
  public function getCompanyData(): array;
  public function getAddressData(): array;
  // public function getCustomerData(int $customer_id): array;
}
