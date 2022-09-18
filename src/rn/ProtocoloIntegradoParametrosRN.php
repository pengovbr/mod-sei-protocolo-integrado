<?php

// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoParametrosRN extends InfraRN {
  
  public static  $NUM_MAX_ANDAMENTOS_POR_VEZ = 500000; 
  public static  $CHAVE_MODULO_PI = '123456789abcdefg';
   public static $NUM_CARACTERES_CHAVE_PI = 16;

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  /*
  * Encripta senha de acesso ao PI atravйs da operaзгo XOR entre as variбveis $CHAVE_MODULO_PI e a senha de acesso ao PI 
  */
  function encriptaSenha($senha) {

      // Chave do mуdulo definida para encriptar a senha de acesso ao WS do PI
       $chave = ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI;
       
       if(strlen(ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI)!=ProtocoloIntegradoParametrosRN::$NUM_CARACTERES_CHAVE_PI){
       
        throw new InfraException ("Erro: Chave de encriptaзгo do mуdulo nгo possui ".ProtocoloIntegradoParametrosRN::$NUM_CARACTERES_CHAVE_PI." caracteres");
       }
       if(ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI == $senha){
       
         //throw new InfraException ("Erro: Senha informada nгo deve ser igual a chave de encriptaзгo");
       }
       

       // Senha que serб salva na base
    for ($senhaIncriptada = $senha, $x = 0, $y = 0; $x < strlen($senhaIncriptada); $x++){
      
    $senhaIncriptada{$x} = chr(ord($senhaIncriptada{$x}) ^ ord($chave{$y}));
    $y = ($y >= (strlen($chave) - 1)) ? 0 : ++$y;
    
  }
       
       return $senhaIncriptada;
 }
   protected function listarConectado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO) {
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_configurar_parametros',__METHOD__,$protocoloIntegradoParametrosDTO);
  
      //Regras de Negocio
      //$objInfraException = new InfraException();
  
      //$objInfraException->lancarValidacoes();
  
  
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->listar($protocoloIntegradoParametrosDTO);
  	
      if(count($ret)==1){
      			
			return $ret[0];
      }
  	  	
      
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Parвmetros.',$e);
    }
  }
  
  protected function consultarControlado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO) {
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_configurar_parametros',__METHOD__,$protocoloIntegradoParametrosDTO);
  
      //Regras de Negocio
      //$objInfraException = new InfraException();
  
      //$objInfraException->lancarValidacoes();
  
  
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->consultar($protocoloIntegradoParametrosDTO);
  
  
      return $ret;
  
    }catch(Exception $e){
      throw new InfraException('Erro consultando Parвmetros.',$e);
    }
  }
  protected function alterarControlado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO){
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_configurar_parametros',__METHOD__,$protocoloIntegradoParametrosDTO);
  
      //Regras de Negocio
      $objInfraException = new InfraException();
      $objInfraException->lancarValidacoes();
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $objProtocoloBD->alterar($protocoloIntegradoParametrosDTO);
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicaзгo no Protocolo Integrado.',$e);
    }
  }
}
?>