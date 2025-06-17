<?php

namespace App\Services\Sunat\Consultas;

use Greenter\See;

class ConsultarCDR
{
  protected See $see;

  public function __construct($see)
  {
    $this->see = $see;
  }

  public function statusCDR(string $ruc, string $tipo, string $serie, int $numero)
  {
    try {
      $result = $this->see->getStatus($ruc, $tipo, $serie, $numero);
      return [
        'success' => true,
        'data' => $result
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }
}
