<?php

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

  
   protected function listarConectado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO) {
    try {  
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->listar($protocoloIntegradoParametrosDTO);
  	
      if(count($ret)==1){		
			  return $ret[0];
      }
  	  	
      
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Parâmetros.',$e);
    }
  }
  
  protected function consultarControlado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO) {
    try {
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->consultar($protocoloIntegradoParametrosDTO);
  
      return $ret;
  
    }catch(Exception $e){
      throw new InfraException('Erro consultando Parâmetros.',$e);
    }
  }
  protected function alterarControlado(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO){
    try {
      //Regras de Negocio
      $objInfraException = new InfraException();
      $objInfraException->lancarValidacoes();
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $objProtocoloBD->alterar($protocoloIntegradoParametrosDTO);
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicação no Protocolo Integrado.',$e);
    }
  }
}
?>