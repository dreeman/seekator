<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;

class ResponsableException extends Exception  implements Responsable
{
    public function toResponse($request, $statusCode = 400)
    {
        return response()->json([
            'message' => 'error',
            'error' => $this->getMessage(),
        ], $statusCode);
    }
}
