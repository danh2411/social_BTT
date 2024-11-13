<?php

namespace App\Constants;

use Symfony\Component\HttpFoundation\Response;

class BaseResponseTypeConstant
{
    public const RESPONSE_TYPE_SUCCESS = 1;
    public const RESPONSE_TYPE_ERROR_OTHER = 2;
    public const RESPONSE_TYPE_ERROR_PARAMS_INVALID = 3;
    public const RESPONSE_TYPE_ERROR_NOT_FOUND = 4;
    public const RESPONSE_TYPE_ERROR_UNAUTHENTICATED = 5;
    public const RESPONSE_TYPE_ERROR_FORBIDDEN = 6;
}