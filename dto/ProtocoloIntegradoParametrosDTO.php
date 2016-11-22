<?

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoParametrosDTO extends InfraDTO {
	
  public function getStrNomeTabela() {
  	 //ADRIANO - MPOG - Adequando nome de identificadores para até 30 posições
  	 return 'md_pi_parametros';
  }

  public function montar() {
  	//ADRIANO - MPOG - Adequando nome de identificadores para até 30 posições
  	
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegradoParametros', 'id_md_pi_parametros');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'UrlWebservice', 'url_webservice');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'LoginWebservice', 'login_webservice');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SenhaWebservice', 'senha_webservice');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExecutandoPublicacao', 'sin_executando_publicacao');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'QuantidadeTentativas', 'quantidade_tentativas');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'EmailAdministrador', 'email_administrador');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataUltimoProcessamento', 'dth_ultimo_processamento');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPublicacaoRestritos', 'sin_publicacao_restritos');
      	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'AtividadesCarregar', 'num_atividades_carregar');
        $this->configurarPK('IdProtocoloIntegradoParametros',InfraDTO::$TIPO_PK_INFORMADO);  
	
  }
}
?>
