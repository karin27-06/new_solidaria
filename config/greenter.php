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
];
