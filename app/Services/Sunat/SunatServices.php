<?php

namespace App\Services\Sunat;

use App\Contracts\SunatInterface;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
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
}
