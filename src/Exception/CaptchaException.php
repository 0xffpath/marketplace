<?php
namespace App\Exception;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class CaptchaException extends BadCredentialsException
{
    public function getMessageKey()
    {
        return 'Invalid captcha.';
    }
}
