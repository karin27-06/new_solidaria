<?php

namespace App\Contracts;

use ParseError;

class NumeroALetrasController
{
  /**
   * @var array
   */
  private $unidades = [
    '',
    'UNO ',
    'DOS ',
    'TRES ',
    'CUATRO ',
    'CINCO ',
    'SEIS ',
    'SIETE ',
    'OCHO ',
    'NUEVE ',
    'DIEZ ',
    'ONCE ',
    'DOCE ',
    'TRECE ',
    'CATORCE ',
    'QUINCE ',
    'DIECISÉIS ',
    'DIECISIETE ',
    'DIECIOCHO ',
    'DIECINUEVE ',
    'VEINTE ',
  ];
  /**
   * @var array
   */
  private $decenas = [
    'VEINTI',
    'TREINTA ',
    'CUARENTA ',
    'CINCUENTA ',
    'SESENTA ',
    'SETENTA ',
    'OCHENTA ',
    'NOVENTA ',
    'CIEN ',
  ];
  /**
   * @var array
   */
  private $centenas = [
    'CIENTO ',
    'DOSCIENTOS ',
    'TRESCIENTOS ',
    'CUATROCIENTOS ',
    'QUINIENTOS ',
    'SEISCIENTOS ',
    'SETECIENTOS ',
    'OCHOCIENTOS ',
    'NOVECIENTOS ',
  ];
  /**
   * @var array
   */
  private $acentosExcepciones = [
    'VEINTIDOS'  => 'VEINTIDÓS ',
    'VEINTITRES' => 'VEINTITRÉS ',
    'VEINTISEIS' => 'VEINTISÉIS ',
  ];
  /**
   * @var string
   */
  public $conector = 'CON';
  /**
   * @var bool
   */
  public $apocope = false;
  /**
   * Formatea y convierte un número a letras en formato facturación electrónica.
   *
   * @param int|float $number
   * @param int       $decimals
   * @param string    $currency
   *
   * @return string
   */
  public function toInvoice($number, $decimals = 2, $currency = '')
  {
    $this->checkApocope();

    $number = number_format($number, $decimals, '.', '');

    $splitNumber = explode('.', $number);

    $splitNumber[0] = $this->wholeNumber($splitNumber[0]);

    if (!empty($splitNumber[1])) {
      $splitNumber[1] .= '/100 ';
    } else {
      $splitNumber[1] = '00/100 ';
    }

    return $this->glue($splitNumber) . mb_strtoupper($currency, 'UTF-8');
  }
  private function checkApocope()
  {
    if ($this->apocope === true) {
      $this->unidades[1] = 'UN ';
    }
  }
  private function wholeNumber($number)
  {
    if ($number == '0') {
      $number = 'CERO ';
    } else {
      $number = $this->convertNumber($number);
    }

    return $number;
  }
  private function convertNumber($number)
  {
    $converted = '';

    if (($number < 0) || ($number > 999999999)) {
      throw new ParseError('Wrong parameter number');
    }

    $numberStrFill = str_pad($number, 9, '0', STR_PAD_LEFT);
    $millones = substr($numberStrFill, 0, 3);
    $miles = substr($numberStrFill, 3, 3);
    $cientos = substr($numberStrFill, 6);

    if (intval($millones) > 0) {
      if ($millones == '001') {
        $converted .= 'UN MILLÓN ';
      } elseif (intval($millones) > 0) {
        $converted .= sprintf('%sMILLONES ', $this->convertGroup($millones));
      }
    }

    if (intval($miles) > 0) {
      if ($miles == '001') {
        $converted .= 'MIL ';
      } elseif (intval($miles) > 0) {
        $converted .= sprintf('%sMIL ', $this->convertGroup($miles));
      }
    }

    if (intval($cientos) > 0) {
      if ($cientos == '001') {
        $this->apocope === true ? $converted .= 'UN ' : $converted .= 'UNO ';
      } elseif (intval($cientos) > 0) {
        $converted .= sprintf('%s ', $this->convertGroup($cientos));
      }
    }

    return trim($converted);
  }
  private function convertGroup($n)
  {
    $output = '';

    if ($n == '100') {
      $output = 'CIEN ';
    } elseif ($n[0] !== '0') {
      $output = $this->centenas[$n[0] - 1];
    }

    $k = intval(substr($n, 1));

    if ($k <= 20) {
      $unidades = $this->unidades[$k];
    } else {
      if (($k > 30) && ($n[2] !== '0')) {
        $unidades = sprintf('%sY %s', $this->decenas[intval($n[1]) - 2], $this->unidades[intval($n[2])]);
      } else {
        $unidades = sprintf('%s%s', $this->decenas[intval($n[1]) - 2], $this->unidades[intval($n[2])]);
      }
    }

    $output .= array_key_exists(trim($unidades), $this->acentosExcepciones) ?
      $this->acentosExcepciones[trim($unidades)] : $unidades;

    return $output;
  }
  private function glue($splitNumber)
  {
    return implode(' ' . mb_strtoupper($this->conector, 'UTF-8') . ' ', array_filter($splitNumber));
  }
}
