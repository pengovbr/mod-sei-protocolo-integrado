<?php

// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoDTO extends InfraDTO {
    
  public function getStrNomeTabela() {
      return 'md_pi_mensagem';
  }

    public function montar() {
        //Adriano - MPOG - tratamento para identificadores de campos ficarem com até 30 posições.
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegrado', 'id_md_pi_mensagem');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTarefa', 'id_tarefa');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPublicar', 'sin_publicar');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'MensagemPublicacao', 'mensagem_publicacao');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeTarefa','nome','tarefa');
        $this->configurarPK('IdProtocoloIntegrado',InfraDTO::$TIPO_PK_INFORMADO);  
        $this->configurarFK('IdTarefa', 'tarefa', 'id_tarefa', InfraDTO::$TIPO_FK_OBRIGATORIA);  
    }
}

?>
