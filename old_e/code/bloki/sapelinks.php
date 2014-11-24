<?php
if (!defined('_SAPE_USER')){
        define('_SAPE_USER', '6ef2af095974b67d5b1322d0a54a9940');
     }
     require_once(realpath($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php'));
     //$o['force_show_code'] = true;
     $o['multi_site'] = true;
     $sape = new SAPE_client($o);
     
    $out['{SAPE_LINKS}']=$sape->return_links(10);
    //$out['{SAPE_LINKS}']="test";