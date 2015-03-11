<?php

namespace mata\base;

/**
 * ValidationException represents an exception caused by failed validation.
 *
 */
class ValidationException extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Validation Failed';
    }
}
