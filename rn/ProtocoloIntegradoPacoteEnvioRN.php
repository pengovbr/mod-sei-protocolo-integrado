<?php
/**
* TRIBUNAL REGIONAL FEDERAL DA 4Є REGIГO
*
* 13/10/2009 - criado por mga
*
* Versгo do Gerador de Cуdigo: 1.29.1
*
* Versгo no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

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
            SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
            
            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();
            
            $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
            $ret = $objProtocoloBD->listar($protocoloIntegradoPacoteEnvioDTO);
            
            return $ret;
        
        } catch(Exception $e) {
            throw new InfraException('Erro listando Pacotes.',$e);
        }
    
    }
  
    protected function contarConectado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
    
        try {
        
            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
            
            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();
            
            $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
            $ret = $objProtocoloBD->contar($protocoloIntegradoPacoteEnvioDTO);
            
            return $ret;
            
        } catch(Exception $e) {
            throw new InfraException('Erro obtendo nъmero de atividades monitoradas.',$e);
        }
    
    }
  
    protected function consultarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
        
        try {
        
            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
            
            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();
            
            $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
            $ret = $objProtocoloBD->consultar($protocoloIntegradoPacoteEnvioDTO);
            
            //Auditoria
            return $ret;
        
        } catch(Exception $e) {
            throw new InfraException('Erro Consultando Pacote.',$e); 
        }
        
    }
  
    protected function cadastrarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
       
        try {
        
        //Valida Permissao
        SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
        
        //Regras de Negocio
        $objInfraException = new InfraException();
        
        $objInfraException->lancarValidacoes();
        $objProtocoloBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
        
        return $objProtocoloBD->cadastrar($protocoloIntegradoPacoteEnvioDTO);
        
        } catch(Exception $e) {
            throw new InfraException('Erro alterando Mensagens de Publicaзгo no Protocolo Integrado.',$e);
        }
        
    }

    protected function alterarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
    
        //Valida Permissao
        SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
        
        //Regras de Negocio
        $objInfraException = new InfraException();
        
        
        $objInfraException->lancarValidacoes();
        
        $objPacoteBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
        $objPacoteBD->alterar($protocoloIntegradoPacoteEnvioDTO);
      
    }
  
}

?>