<?

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoPacoteEnvioDTO extends InfraDTO {
	
  public function getStrNomeTabela() {
  	 //ADRIANO - MPOG - Adequando nome de identificadores para até 30 posições
  	 return 'md_pi_pacote_envio';
  }

  public function montar() {
  	//ADRIANO - MPOG - Adequando nome de identificadores para até 30 posições
  	
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegradoPacoteEnvio', 'id_md_pi_pacote_envio');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaIntegracao', 'sta_integracao');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataSituacao', 'dth_situacao');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataMetadados', 'dth_metadados');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocolo', 'id_protocolo');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'TentativasEnvio', 'num_tentativas_envio');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'ArquivoMetadados', 'arquivo_metadados');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'ArquivoErro', 'arquivo_erro');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataAgendamentoExecutado', 'dth_agendamento_executado');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'ProtocoloFormatado','protocolo_formatado','protocolo');
	$this->configurarPK('IdProtocoloIntegradoPacoteEnvio',InfraDTO::$TIPO_PK_NATIVA); 
	$this->configurarFK('IdProtocolo', 'protocolo', 'id_protocolo', InfraDTO::$TIPO_FK_OBRIGATORIA); 
	$this->setOrd('IdProtocoloIntegradoPacoteEnvio', InfraDTO::$TIPO_ORDENACAO_ASC);
  
  }
}
?>
