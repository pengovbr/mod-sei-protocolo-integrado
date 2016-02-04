<?

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoMonitoramentoProcessosDTO extends InfraDTO {
	
  public function getStrNomeTabela() {
  	 return 'protocolo_integrado_monitoramento_processos';
  }

  public function montar() {
  	
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegradoMonitoramentoProcessos', 'id_protocolo_integrado_monitoramento_processos');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdAtividade', 'id_atividade');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro', 'dth_cadastro');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdProtocolo','id_protocolo','atividade');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdUnidade','id_unidade','atividade');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdTarefa','id_tarefa','atividade');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH,'DataAbertura','dth_abertura','atividade');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdPacote', 'id_protocolo_integrado_pacote_envio');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'MensagemPublicacao','id_tarefa','protocolo_integrado');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'IdProtocolo','id_protocolo','protocolo_integrado_pacote_envio');
    $this->configurarPK('IdProtocoloIntegradoMonitoramentoProcessos',InfraDTO::$TIPO_PK_INFORMADO );  
	$this->configurarFK('IdAtividade', 'atividade', 'id_atividade', InfraDTO::$TIPO_FK_OBRIGATORIA);  
	$this->configurarFK('IdTarefa', 'protocolo_integrado', 'id_tarefa', InfraDTO::$TIPO_FK_OBRIGATORIA);  
	$this->configurarFK('IdPacote', 'protocolo_integrado_pacote_envio', 'id_protocolo_integrado_pacote_envio', InfraDTO::$TIPO_FK_OBRIGATORIA);  
  }
}
?>