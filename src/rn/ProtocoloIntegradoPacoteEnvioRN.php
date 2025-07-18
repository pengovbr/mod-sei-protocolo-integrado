<?php

require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoPacoteEnvioRN extends InfraRN { 
  
  public static $STA_NAO_INTEGRADO = 'NI'; 
  public static $STA_INTEGRADO = 'I';  
  public static $STA_ERRO_NEGOCIAL = 'EN';  
  public static $STA_FALHA_INFRA = 'FI';  
  
  public function __construct(){
      parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
      return BancoSEI::getInstance();
  }
  
  protected function listarConectado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
      
    try {
        
        //Valida Permissao
        SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoPacoteEnvioDTO);
            
        //Regras de Negocio
        //$objInfraException = new InfraException();
        //$objInfraException->lancarValidacoes();
            
        $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
        $ret = $objProtocoloBD->listar($protocoloIntegradoPacoteEnvioDTO);
            
        return $ret;
        
    } catch(Exception $e) {
        throw new InfraException('Mуdulo Protocolo Integrado: Erro listando Pacotes.', $e);
    }
    
  }
  
  protected function contarConectado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
    
    try {
        
        //Valida Permissao
        SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoPacoteEnvioDTO);
            
        //Regras de Negocio
        //$objInfraException = new InfraException();
        //$objInfraException->lancarValidacoes();
            
        $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
        $ret = $objProtocoloBD->contar($protocoloIntegradoPacoteEnvioDTO);
            
        return $ret;
            
    } catch(Exception $e) {
        throw new InfraException('Mуdulo Protocolo Integrado: Erro obtendo nъmero de atividades monitoradas.', $e);
    }
    
  }
  
  protected function consultarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
        
    try {
        
        //Valida Permissao
        SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoPacoteEnvioDTO);
            
        //Regras de Negocio
        //$objInfraException = new InfraException();
        //$objInfraException->lancarValidacoes();
            
        $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
        $ret = $objProtocoloBD->consultar($protocoloIntegradoPacoteEnvioDTO);
            
        //Auditoria
        return $ret;
        
    } catch(Exception $e) {
        throw new InfraException('Mуdulo Protocolo Integrado: Erro Consultando Pacote.', $e); 
    }
        
  }
  
  protected function cadastrarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
       
    try {
        
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoPacoteEnvioDTO);
        
      //Regras de Negocio
      $objInfraException = new InfraException();
        
      $objInfraException->lancarValidacoes();
      $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
        
      return $objProtocoloBD->cadastrar($protocoloIntegradoPacoteEnvioDTO);
        
    } catch(Exception $e) {
        throw new InfraException('Mуdulo Protocolo Integrado: Erro alterando Mensagens de Publicaзгo no Protocolo Integrado.', $e);
    }
        
  }

  protected function alterarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
    
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoPacoteEnvioDTO);
        
      //Regras de Negocio
      $objInfraException = new InfraException();
        
        
      $objInfraException->lancarValidacoes();
        
      $objPacoteBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
      $objPacoteBD->alterar($protocoloIntegradoPacoteEnvioDTO);
      
  }
  
}

?>