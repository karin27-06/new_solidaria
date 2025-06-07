<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
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
   * @return array
   */
  public function getCompanyData(): array
  {
    return [
      'ruc' => config('greenter.company.ruc'),
      'razon_social' => config('greenter.company.razon_social'),
      'nombre_comercial' => config('greenter.company.nombre_comercial'),
      'address' => $this->getAddressData(),
    ];
  }
  /**
   * Get the address data.
   *
   * @return array
   */
  public function getAddressData(): array
  {
    return [
      'ubigueo' => config('greenter.address.ubigueo'),
      'departamento' => config('greenter.address.departamento'),
      'provincia' => config('greenter.address.provincia'),
      'distrito' => config('greenter.address.distrito'),
      'urbanizacion' => config('greenter.address.urbanizacion'),
      'direccion' => config('greenter.address.direccion'),
      'cod_local' => config('greenter.address.cod_local'),
    ];
  }
}
