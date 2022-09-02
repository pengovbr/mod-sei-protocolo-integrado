<?php

// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoMonitoramentoProcessosDTO extends InfraDTO {
	
    public function getStrNomeTabela() {
        //ADRIANO - MPOG - Adequando nome de identificadores para ate 30 posicoes
        return 'md_pi_monitora_processos';
    }

    public function montar() {
        //ADRIANO - MPOG - Adequando nome de identificadores para ate 30 posicoes
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProtocoloIntegradoMonitoramentoProcessos', 'id_md_pi_monitora_processos');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdAtividade', 'id_atividade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro', 'dth_cadastro');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdProtocolo','id_protocolo','atividade');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdUnidade','id_unidade','atividade');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdTarefa','id_tarefa','atividade');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH,'DataAbertura','dth_abertura','atividade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdPacote', 'id_md_pi_pacote_envio');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'MensagemPublicacao','id_tarefa','md_pi_mensagem');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'IdProtocolo','id_protocolo','md_pi_pacote_envio');
        $this->configurarPK('IdProtocoloIntegradoMonitoramentoProcessos',InfraDTO::$TIPO_PK_NATIVA);  
        $this->configurarFK('IdAtividade', 'atividade', 'id_atividade', InfraDTO::$TIPO_FK_OBRIGATORIA);  
        $this->configurarFK('IdTarefa', 'md_pi_mensagem', 'id_tarefa', InfraDTO::$TIPO_FK_OBRIGATORIA);  
        $this->configurarFK('IdPacote', 'md_pi_pacote_envio', 'id_md_pi_pacote_envio', InfraDTO::$TIPO_FK_OBRIGATORIA);  
    }
    
}

?>
