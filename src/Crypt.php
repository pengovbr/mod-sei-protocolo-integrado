<?php

// require_once dirname(__FILE__).'/../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';

class Crypt {
    
    static $chave = "xy";
    
  static function StringXor($a, $b) {
    if ($a=='') { return '';
    }
      $retorno = "";
    $i = strlen($a)-1;
    $j = strlen($b);
    do {
        $retorno .= ($a{$i} ^ $b{$i % $j});
    } while ($i--);
      return strrev($retorno);
  }
    
  static function Encrypt($string) {
      return base64_encode(Crypt::StringXor($string, Crypt::$chave));
  }
    
  static function Decrypt($string) {
      return Crypt::StringXor(base64_decode($string), Crypt::$chave);
  }
    
}

?>