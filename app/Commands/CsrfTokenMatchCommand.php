<?php

namespace Veer\Commands;

use Veer\Commands\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Symfony\Component\Security\Core\Util\StringUtils; 

class CsrfTokenMatchCommand extends Command implements SelfHandling
{

    public function handle() {

        $token = app('request')->input('_token') ?: app('request')->header('X-CSRF-TOKEN');

        if ( ! $token && $header = app('request')->header('X-XSRF-TOKEN'))
        {
                $token = app('encrypter')->decrypt($header);
        }

        if(StringUtils::equals(app('request')->session()->token(), $token)) return true;

        return false;
    }
}
