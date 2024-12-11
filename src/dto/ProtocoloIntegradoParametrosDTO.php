<?php

// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoParametrosDTO extends InfraDTO {
    
  public function getStrNomeTabela() {
      return 'md_pi_parametros';
  }

  public function montar() {
      $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegradoParametros', 'id_md_pi_parametros');
      $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExecutandoPublicacao', 'sin_executando_publicacao');
      $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataUltimoProcessamento', 'dth_ultimo_processamento');
      $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'AtividadesCarregar', 'num_atividades_carregar');
      $this->configurarPK('IdProtocoloIntegradoParametros', InfraDTO::$TIPO_PK_INFORMADO);  
  }
}

?>
