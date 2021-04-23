<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/fetch-locations',
        '/api/charge-calculator',
        'api/v1/*',
        'api/v2/*',
        'api/v3/*',
        'lpapi/*',
    ];
}
