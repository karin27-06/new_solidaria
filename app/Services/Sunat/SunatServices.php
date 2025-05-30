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
}
