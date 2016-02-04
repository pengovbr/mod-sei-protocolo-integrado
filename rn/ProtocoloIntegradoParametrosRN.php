<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4Є REGIГO
*
* 13/10/2009 - criado por mga
*
* Versгo do Gerador de Cуdigo: 1.29.1
*
* Versгo no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoParametrosRN extends InfraRN {
  
  public static $NUM_MAX_ANDAMENTOS_POR_VEZ = 500000;   
  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
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
  	  
	  if ($protocoloIntegradoParametrosDTO->isSetStrUrlWebservice()){
        $this->validarStrUrlWebservice($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      if ($protocoloIntegradoParametrosDTO->isSetStrLoginWebservice()){
        $this->validarStrLoginWebservice($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      
      if ($protocoloIntegradoParametrosDTO->isSetStrSenhaWebservice()){
        $this->validarStrSenhaWebservice($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      
	  if ($protocoloIntegradoParametrosDTO->isSetNumQuantidadeTentativas()){
        $this->validarNumQuantidadeTentativas($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      if ($protocoloIntegradoParametrosDTO->isSetNumAtividadesCarregar()){
        $this->validarNumAtividadesCarregar($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      
      if ($protocoloIntegradoParametrosDTO->isSetStrEmailAdministrador()){
        $this->validarStrEmailAdministrador($protocoloIntegradoParametrosDTO, $objInfraException);
      }
      $objInfraException->lancarValidacoes();
  
      $objProtocoloBD = new ProtocoloIntegradoParametrosBD($this->getObjInfraIBanco());
      $objProtocoloBD->alterar($protocoloIntegradoParametrosDTO);
  
  
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicaзгo no Protocolo Integrado.',$e);
    }
  }
  private function validarStrUrlWebservice(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getStrUrlWebservice())){
      $objInfraException->adicionarValidacao('URL do WebService nгo informada');
    }else{
      $protocoloIntegradoParametrosDTO->setStrUrlWebservice(trim($protocoloIntegradoParametrosDTO->getStrUrlWebservice()));
      
    }
  }
  private function validarStrLoginWebservice(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getStrLoginWebservice())){
      $objInfraException->adicionarValidacao('Login de acesso ao WebService nгo informado');
    }else{
      if(strlen($protocoloIntegradoParametrosDTO->getStrLoginWebservice())>10){
      	
		$objInfraException->adicionarValidacao('Login de acesso ao WebService deve ter 10 caracteres no mбximo');
      }else{
   			
		 $protocoloIntegradoParametrosDTO->setStrLoginWebservice(trim($protocoloIntegradoParametrosDTO->getStrLoginWebservice()));
      }	
      
    }
  }	
  private function validarStrSenhaWebservice(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getStrSenhaWebservice())){
      $objInfraException->adicionarValidacao('Senha de acesso ao WebService nгo informada');
    }else{
      
	  if(strlen($protocoloIntegradoParametrosDTO->getStrLoginWebservice())>20){
      	
		$objInfraException->adicionarValidacao('Senha de acesso ao WebService deve ter 20 caracteres no mбximo');
      }else{
      		
		$protocoloIntegradoParametrosDTO->setStrSenhaWebservice(trim($protocoloIntegradoParametrosDTO->getStrSenhaWebservice()));
      	
      }	
      
    }
  }
  private function validarNumQuantidadeTentativas(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getNumQuantidadeTentativas())){
      $objInfraException->adicionarValidacao('Quantidade de tentativas nгo informada');
    }else{
      if(!is_numeric($protocoloIntegradoParametrosDTO->getNumQuantidadeTentativas())){
      		
			$objInfraException->adicionarValidacao('Quantidade de tentativas deve ser um nъmero inteiro');
      }else{
		 	
			$protocoloIntegradoParametrosDTO->setNumQuantidadeTentativas(intval($protocoloIntegradoParametrosDTO->getNumQuantidadeTentativas()));
      }	
     
    }
  }
  private function validarNumAtividadesCarregar(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getNumAtividadesCarregar())){
      $objInfraException->adicionarValidacao('Quantidade mбxima de andamentos por vez nгo informada');
    }else{
      if(!is_numeric($protocoloIntegradoParametrosDTO->getNumAtividadesCarregar())){
      		
			$objInfraException->adicionarValidacao('Quantidade mбxima de andamentos por vez  deve ser um nъmero inteiro');
      
	  }else{
	  	 if($protocoloIntegradoParametrosDTO->getNumAtividadesCarregar()>ProtocoloIntegradoParametrosRN::$NUM_MAX_ANDAMENTOS_POR_VEZ){
      	
			$objInfraException->adicionarValidacao('Quantidade mбxima de andamentos por vez  nгo deve ser maior que '.ProtocoloIntegradoParametrosRN::$NUM_MAX_ANDAMENTOS_POR_VEZ);
		 }else{
		 	
			$protocoloIntegradoParametrosDTO->setNumAtividadesCarregar(intval($protocoloIntegradoParametrosDTO->getNumAtividadesCarregar()));
      	
      	 }
		 			
      }	
	} 
    
  }
  private function validarStrEmailAdministrador(ProtocoloIntegradoParametrosDTO $protocoloIntegradoParametrosDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($protocoloIntegradoParametrosDTO->getStrEmailAdministrador())){
      $objInfraException->adicionarValidacao('Email do administrador da Integraзгo nгo informado');
    }else{
      $protocoloIntegradoParametrosDTO->setStrEmailAdministrador(trim($protocoloIntegradoParametrosDTO->getStrEmailAdministrador()));
      
    }
  }		
}
?>