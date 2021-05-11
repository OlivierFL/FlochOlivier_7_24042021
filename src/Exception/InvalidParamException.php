<?php

namespace App\Exception;

use Exception;

class InvalidParamException extends Exception
{
    public static function nullPassword()
    {
        return new self('Error : password can not be null !');
    }
}
