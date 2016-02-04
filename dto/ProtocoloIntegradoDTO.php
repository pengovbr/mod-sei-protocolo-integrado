<?

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoDTO extends InfraDTO {
	
  public function getStrNomeTabela() {
  	 return 'protocolo_integrado';
  }

  public function montar() {
  	
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegrado', 'id_protocolo_integrado');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTarefa', 'id_tarefa');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPublicar', 'sin_publicar');
	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'MensagemPublicacao', 'mensagem_publicacao');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeTarefa','nome','tarefa');
    $this->configurarPK('IdProtocoloIntegrado',InfraDTO::$TIPO_PK_INFORMADO );  
	$this->configurarFK('IdTarefa', 'tarefa', 'id_tarefa', InfraDTO::$TIPO_FK_OBRIGATORIA);  
  }
}
?>