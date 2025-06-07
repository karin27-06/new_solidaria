<?php

use Greenter\Ws\Services\SunatEndpoints;

return [
  'credentials' => [
    'ruc' => env('SUNAT_RUC', '20000000001'),
    'usuario' => env('SUNAT_USER', 'MODDATOS'),
    'password' => env('SUNAT_PASS', 'moddatos'),
  ],

  'endpoint' => env('SUNAT_ENV', 'beta') === 'production'
    ? SunatEndpoints::FE_PRODUCCION
    : SunatEndpoints::FE_BETA,

  'certificate_path' => storage_path('app/public/Certificados/' . env('SUNAT_CERT_NAME', 'certificate.pem')),

  'company' => [
    'ruc' => env('SUNAT_RUC', '20000000001'),
    'razon_social' => env('SUNAT_RAZON_SOCIAL', 'Mi Empresa S.A.C.'),
    'nombre_comercial' => env('SUNAT_NOMBRE_COMERCIAL', 'Mi Empresa'),
    'address' => [
      'ubigueo' => env('SUNAT_UBIGEO', '150101'),
      'departamento' => env('SUNAT_DEPARTAMENTO', 'Lima'),
      'provincia' => env('SUNAT_PROVINCIA', 'Lima'),
      'distrito' => env('SUNAT_DISTRITO', 'Lima'),
      'urbanizacion' => env('SUNAT_URBANIZACION', ''),
      'direccion' => env('SUNAT_DIRECCION', 'Av. Ejemplo 123'),
      'cod_local' => env('SUNAT_COD_LOCAL', ''),
    ],
  ],
];
