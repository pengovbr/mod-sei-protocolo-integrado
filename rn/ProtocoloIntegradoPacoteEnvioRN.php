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
  
  public function listarProtocoloIntegradoEnviar(){
  	try{
	  $sql = "select pipe.id_protocolo id_protocolo, pipe.sta_integracao sta_integracao, pipe.dth_situacao dth_situacao, pipe.dth_metadados dth_metadados, ". 
	  			"pipe.num_tentativas_envio num_tentativas_envio, pipe.id_protocolo_integrado_pacote_envio id_protocolo_integrado_pacote_envio ". 
	  		 "FROM  protocolo_integrado_pacote_envio pipe ".
			 "WHERE sta_integracao<>'".ProtocoloIntegradoPacoteEnvioRN::$STA_INTEGRADO."' limit 1000";
    	$resultadoProtocoloIntegradoPacoteEnvio = $this->getObjInfraIBanco()->consultarSql($sql);
		$arrProtocoloIntegradoPacoteEnvioDTO = array();
		foreach($resultadoProtocoloIntegradoPacoteEnvio as $item){
			$objProtocoloIntegradoPacoteEnvioDTO = new ProtocoloIntegradoPacoteEnvioDTO();
			
			$objProtocoloIntegradoPacoteEnvioDTO->setNumIdProtocolo($item['id_protocolo']);
			$objProtocoloIntegradoPacoteEnvioDTO->setStrStaIntegracao($item['sta_integracao']);
			$objProtocoloIntegradoPacoteEnvioDTO->setDthDataSituacao($item['dth_situacao']);
			$objProtocoloIntegradoPacoteEnvioDTO->setDthDataMetadados($item['dth_metadados']);
			$objProtocoloIntegradoPacoteEnvioDTO->setNumTentativasEnvio($item['num_tentativas_envio']);
			$objProtocoloIntegradoPacoteEnvioDTO->setNumIdProtocoloIntegradoPacoteEnvio($item['id_protocolo_integrado_pacote_envio']);
			
			array_push($arrProtocoloIntegradoPacoteEnvioDTO,$objProtocoloIntegradoPacoteEnvioDTO);
	
  		}
		return $arrProtocoloIntegradoPacoteEnvioDTO;
      
    }catch(Exception $e){
      throw new InfraException('Erro ao carregar atividades.',$e);
    }
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
  	  	
      
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Tarefas.',$e);
    }
  }
  protected function consultarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO) {
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
  
      //Regras de Negocio
      //$objInfraException = new InfraException();
  
      //$objInfraException->lancarValidacoes();
  
  
      $objProtocoloBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->consultar($protocoloIntegradoPacoteEnvioDTO);
  
      //Auditoria
  
      return $ret;
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Tarefas.',$e); 
    }
  }
   protected function cadastrarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
  
      //Regras de Negocio
      $objInfraException = new InfraException();
  
      /*if ($objTarefaDTO->isSetStrNome()){
        $this->validarStrNome($objTarefaDTO, $objInfraException);
      }
      
      if ($objTarefaDTO->isSetStrSinHistoricoResumido()){
        $this->validarStrSinHistoricoResumido($objTarefaDTO, $objInfraException);
      }
      */
      $objInfraException->lancarValidacoes();
  
      $objProtocoloBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
      return $objProtocoloBD->cadastrar($protocoloIntegradoPacoteEnvioDTO);
  
  
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicaзгo no Protocolo Integrado.',$e);
    }
  }
  
 
  protected function alterarControlado(ProtocoloIntegradoPacoteEnvioDTO $protocoloIntegradoPacoteEnvioDTO){
  	
		 //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_monitoramento',__METHOD__,$protocoloIntegradoPacoteEnvioDTO);
  
      //Regras de Negocio
      $objInfraException = new InfraException();
  
      /*if ($objTarefaDTO->isSetStrNome()){
        $this->validarStrNome($objTarefaDTO, $objInfraException);
      }
      
      if ($objTarefaDTO->isSetStrSinHistoricoResumido()){
        $this->validarStrSinHistoricoResumido($objTarefaDTO, $objInfraException);
      }
      */
      $objInfraException->lancarValidacoes();
  
      $objPacoteBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
      $objPacoteBD->alterar($protocoloIntegradoPacoteEnvioDTO);
  }
  
  

 
}
?>