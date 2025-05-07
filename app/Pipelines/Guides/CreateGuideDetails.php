<?php

namespace App\Pipelines\Guides;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CreateGuideDetails
{
  public function __invoke($request, Closure $next)
  {
    $guide_id = $request['guide_model']['origin_local_id'];
    Log::info('CreateGuideDetails pipeline started', [
      'guide_id' => $guide_id,
    ]);
    return $next($request);
  }
}
