<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 07/05/2013 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';
require_once dirname(__FILE__).'/../../../../sip/Sip.php';
require_once dirname(__FILE__).'/../../../../sip/BancoSip.php';



class ProtocoloIntegradoVersaoRN extends InfraRN {

  private $numSeg = 0;

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function inicializar($strTitulo){

    ini_set('max_execution_time','0');
    ini_set('memory_limit','-1');

    InfraDebug::getInstance()->setBolLigado(true);
    InfraDebug::getInstance()->setBolDebugInfra(true);
    InfraDebug::getInstance()->setBolEcho(true);
    InfraDebug::getInstance()->limpar();

    $this->numSeg = InfraUtil::verificarTempoProcessamento();

    $this->logar($strTitulo);
  }

  private function logar($strMsg){
    InfraDebug::getInstance()->gravar($strMsg);

    //para encher o buffer e fazer o flush
    //echo str_repeat(' ',64*1024);  flush();

    //LogSEI::getInstance()->gravar($strMsg);
  }

  private function finalizar($strMsg=null, $bolErro){

    if (!$bolErro) {
      $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
      $this->logar('TEMPO TOTAL DE EXECUCAO: ' . $this->numSeg . ' s');
    }else{
      $strMsg = 'ERRO: '.$strMsg;
    }

    if ($strMsg!=null){
      $this->logar($strMsg);
    }

    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->setBolEcho(false);
    $this->numSeg = 0;
    die;
  }


  /**
   * @throws InfraException
   */
  protected function atualizarVersaoConectado(){
  	
	
	InfraDebug::getInstance()->setBolLigado(true);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->setBolEcho(false);
	InfraDebug::getInstance()->limpar();
	  
	
	$strPiVersao = "1.1.2";
	try{
    	
		if (!(BancoSEI::getInstance() instanceof InfraMySql) && !(BancoSEI::getInstance() instanceof InfraSqlServer) && !(BancoSEI::getInstance() instanceof InfraOracle)){
			$this->finalizar('BANCO DE DADOS NAO SUPORTADO: '.get_parent_class(BancoSEI::getInstance()),true);
		}

		echo "<style>
			#msgSucesso, #msgErro{
				font-weight:bold;
			}
			#msgSucesso{
				color:blue;
			} 
			#msgErro{
				color:red;
			}
			#log{
				font-size:13px;
				font-style:italic;
			}
		  </style>";
		echo "<div id='msgSucesso'></div>";
		echo "<div id='msgErro'></div>";
		echo "<br /><br /><br />";
		echo "<span>Log com a lista de comandos executados:</span>";
		echo "<br /><br />";
		echo "<div id='log'>";

		$erros = null;

    	$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado_pacote_envio'");
		if ($bolExisteTabela==false){
			$erros = $this->executaSQLSEI("CREATE TABLE protocolo_integrado_pacote_envio (id_protocolo_integrado_pacote_envio bigint(20) NOT NULL AUTO_INCREMENT,id_protocolo bigint(20) NOT NULL,
	  			dth_metadados datetime DEFAULT NULL,dth_situacao datetime DEFAULT NULL,sta_integracao char(2) NOT NULL,arquivo_metadados MEDIUMBLOB,arquivo_erro blob,num_tentativas_envio int(11) DEFAULT '0',
	  			dth_agendamento_executado varchar(45) DEFAULT NULL,PRIMARY KEY (id_protocolo_integrado_pacote_envio),KEY fk_pacote_envio_protocolo_integrado_protocolo (id_protocolo),
	  			CONSTRAINT fk_pacote_envio_protocolo_integrado_protocolo FOREIGN KEY (id_protocolo) REFERENCES protocolo (id_protocolo) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;", $erros);
		}
		
		$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado_monitoramento_processos'");
		if ($bolExisteTabela==false){
			$erros = $this->executaSQLSEI("CREATE TABLE protocolo_integrado_monitoramento_processos (id_protocolo_integrado_monitoramento_processos bigint(20) NOT NULL AUTO_INCREMENT,
		  		id_atividade int(11) NOT NULL,dth_cadastro datetime DEFAULT NULL,id_protocolo_integrado_pacote_envio bigint(20) NOT NULL,PRIMARY KEY (id_protocolo_integrado_monitoramento_processos),
		  		KEY id_atividade_idx (id_atividade),KEY fk_protocolo_integrado_monitoramento_processos_pacote (id_protocolo_integrado_pacote_envio),
		  		CONSTRAINT fk_protocolo_integrado_monitoramento_processos_atividade FOREIGN KEY (id_atividade) REFERENCES atividade (id_atividade) ON DELETE CASCADE ON UPDATE CASCADE,
		  		CONSTRAINT fk_protocolo_integrado_monitoramento_processos_pacote_envio FOREIGN KEY (id_protocolo_integrado_pacote_envio) 
		  		REFERENCES protocolo_integrado_pacote_envio (id_protocolo_integrado_pacote_envio) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;", $erros);  
		}
		
		$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado'");
		if ($bolExisteTabela==false){
	      	$erros = $this->executaSQLSEI("CREATE TABLE protocolo_integrado (id_protocolo_integrado bigint(20) NOT NULL AUTO_INCREMENT,id_tarefa int(11) DEFAULT NULL, 
	      		sin_publicar char(1) NOT NULL DEFAULT 'N',mensagem_publicacao varchar(255) NOT NULL,PRIMARY KEY (id_protocolo_integrado),KEY fk_protocolo_integrado_tarefa_idx (id_tarefa), 
	      		CONSTRAINT fk_protocolo_integrado_tarefa FOREIGN KEY (id_tarefa) REFERENCES tarefa (id_tarefa) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;", $erros);
		}

		$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado'");
		if ($bolExisteTabela==true){
			$erros = $this->executaSQLSEI("delete from protocolo_integrado;", $erros);
			$erros = $this->executaSQLSEI("insert into protocolo_integrado (id_tarefa,sin_publicar,mensagem_publicacao) select id_tarefa,'N',nome from tarefa;", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_GERACAO_PROCEDIMENTO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_RELACIONAR_PROCEDIMENTO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_REMOCAO_RELACIONAMENTO_PROCEDIMENTO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_SOBRESTAMENTO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_REMOCAO_SOBRESTAMENTO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_SOBRESTANDO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_SOBRESTADO_AO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_REMOCAO_SOBRESTANDO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_REMOCAO_SOBRESTADO_AO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_PROCESSO_RECEBIDO_UNIDADE.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_GLOBAL.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_CONCLUSAO_PROCESSO_USUARIO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_PROCESSO_CIENCIA.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ANEXADO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ANEXADO_AO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_DESANEXADO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_DESANEXADO_DO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ALTERACAO_GRAU_SIGILO_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ALTERACAO_HIPOTESE_LEGAL_PROCESSO.";", $erros);
	      	$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_PROCESSO_ANEXADO_CIENCIA.";", $erros);
			$erros = $this->executaSQLSEI("update protocolo_integrado SET sin_publicar='S' where id_tarefa=".TarefaRN::$TI_ATUALIZACAO_ANDAMENTO.";", $erros);
		}
		
		$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado_parametros'");
		if ($bolExisteTabela==false){
			$erros = $this->executaSQLSEI("CREATE TABLE protocolo_integrado_parametros (id_protocolo_integrado_parametros bigint(20) NOT NULL AUTO_INCREMENT,url_webservice varchar(255) NOT NULL,
	  			quantidade_tentativas int(11) NOT NULL,email_administrador varchar(255) NOT NULL,dth_ultimo_processamento datetime DEFAULT NULL,login_webservice varchar(10) DEFAULT NULL,
	  			senha_webservice varchar(20) DEFAULT NULL,sin_executando_publicacao char(1) NOT NULL DEFAULT 'N',sin_publicacao_restritos char(1) NOT NULL DEFAULT 'S',num_atividades_carregar int(11) 
	  			DEFAULT NULL, PRIMARY KEY (id_protocolo_integrado_parametros)) ENGINE=InnoDB;", $erros);
		}
		
		$bolExisteTabela = BancoSEI::getInstance()->executarSql("show tables like 'protocolo_integrado_parametros'");
		if ($bolExisteTabela==true){
			$numCountElementos = BancoSEI::getInstance()->executarSql("select * from protocolo_integrado_parametros");
			if ($numCountElementos==0){
				$erros = $this->executaSQLSEI("INSERT INTO protocolo_integrado_parametros (id_protocolo_integrado_parametros,url_webservice,quantidade_tentativas,email_administrador,
		  			login_webservice,senha_webservice,sin_executando_publicacao,sin_publicacao_restritos,num_atividades_carregar) VALUES (1,'https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl'
		  			,15,'','','','N','S',100000);", $erros);
			}
		}
		
		
		BancoSIP::getInstance()->abrirConexao();
			
		// recursos
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_acesso_arquivo_metadados'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
			   (select max(s.id_recurso)+1 from recurso s), 'protocolo_integrado_acesso_arquivo_metadados', 'Visualização do arquivo XML que foi gerado para um pacote de envio ao PI', 
			   'controlador.php?acao=protocolo_integrado_acesso_arquivo_metadados', 'S');", $erros);
		}
	    
	    $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_configurar_parametros'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s), 'protocolo_integrado_configurar_parametros', 'Configuração dos Parametros Gerais do Modulo', 
			    'controlador.php?acao=protocolo_integrado_configurar_parametros', 'S');", $erros);
		}
			
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_configurar_publicacao'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_configurar_publicacao', 'Opção de configurar quais históricos sobem ou não pro PI', 
				'controlador.php?acao=protocolo_integrado_configurar_publicacao', 'S');", $erros);
		}    
			
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_forcar_reenvio'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_forcar_reenvio', 'Forçar reenvio de dados a partir da tela de monitoramento do PI', 
				'controlador.php?acao=protocolo_integrado_forcar_reenvio', 'S');", $erros);
		}  
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_mensagens_alterar'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_mensagens_alterar', 
				'Opção de configurar qual a mensagem que será traduzida ao PI, uma vez que ela já foi configurada para subir', 'controlador.php?acao=protocolo_integrado_mensagens_alterar', 'S');", $erros);
		}	  
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_mensagens_listar'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_mensagens_listar', 'Listagem dos Históricos que estão configurados para serem publicados no PI', 
				'controlador.php?acao=protocolo_integrado_mensagens_listar', 'S');", $erros);
		}
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_monitoramento'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'), 
				(select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_monitoramento', 'Monitoramento da Integração', 'controlador.php?acao=protocolo_integrado_monitoramento', 'S');", $erros);
		}	  
		
		// perfil_recursos
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_acesso_arquivo_metadados' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), 
				(select id_recurso from recurso where nome='protocolo_integrado_acesso_arquivo_metadados' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}	  
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_publicacao' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_configurar_publicacao' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}	  
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_forcar_reenvio' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_forcar_reenvio' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}	  
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_alterar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_mensagens_alterar' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}	  
			 
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where 
				nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}	  
			  		
		// item_menu
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_sistema=(select id_sistema from sistema where sigla='SEI')
		and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Administração' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and rotulo='Protocolo Integrado'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, rotulo, sequencia, sin_ativo, sin_nova_janela) VALUES 
				((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im), (select id_sistema 
				from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select im.id_item_menu from 
				item_menu im where im.rotulo='Administração' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), 'Protocolo Integrado', '0', 'S', 'N');", $erros);
		}
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_sistema=(select id_sistema from sistema where sigla='SEI')
		and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and rotulo='Parâmetros'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela) 
				VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im), 
				(select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select 
				im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_recurso 
				from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI')), 'Parâmetros', '10', 'S', 'N');", $erros);
		}      
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_sistema=(select id_sistema from sistema where sigla='SEI')
		and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and rotulo='Configuração das Mensagens'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela) 
				VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im), 
				(select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				'Configuração das Mensagens', '20', 'S', 'N');", $erros);
		}      
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_sistema=(select id_sistema from sistema where sigla='SEI')
		and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
		and rotulo='Monitoramento'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela) 
				VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im), 
				(select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI')), 
				'Monitoramento', '30', 'S', 'N');", $erros);
		}
		      
		// rel_perfil_item_menu
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Parâmetros' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil 
				where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu 
				from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from 
				sistema where sigla='SEI') and rotulo='Parâmetros' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and 
				im.id_sistema=(select id_sistema from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and 
				id_sistema=(select id_sistema from sistema where sigla='SEI')));", $erros);
		}
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Configuração das Mensagens' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil where 
				nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu 
				where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') 
				and rotulo='Configuração das Mensagens' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema 
				from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where 
				sigla='SEI')));", $erros);
		}      
		
		$numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_sistema=(select id_sistema from sistema where sigla='SEI')
			and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
			and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Monitoramento' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
			and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSIP("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil where 
				nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu 
				where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where 
				sigla='SEI') and rotulo='Monitoramento' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select 
				id_sistema from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from 
				sistema where sigla='SEI')));", $erros);
		}
		
		BancoSIP::getInstance()->fecharConexao(); 		
			 
		$numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSEI("INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, sin_ativo) 
				VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Processo de Publicação do PI', 'ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado', 
				'D', '2', 'S');", $erros);
		}
		
		$numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSEI("INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, 
				parametro, sin_ativo) VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Agendamento do alarme de e-mail disparado quando novos pacotes não 
				estão sendo gerados', 'ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados', 'D', '12', '2', 'S');", $erros);
		}      
		
		$numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado'");
		if ($numCountElementos==0){
			$erros = $this->executaSQLSEI("INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa,descricao,comando,sta_periodicidade_execucao,periodicidade_complemento,sin_ativo)
				VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat),'Agendamento do alarme de e-mail disparado quando há falha na publocação de pacotes',
				'ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado','D','17','S');", $erros);
		}      

		$bolExiste = BancoSEI::getInstance()->executarSql('select * from infra_parametro where nome=\'PI_VERSAO\'');
		if ($bolExiste==true){
			BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$strPiVersao.'\' where nome=\'PI_VERSAO\'');
		}
		else{
			BancoSEI::getInstance()->executarSql('insert into infra_parametro(nome,valor) values(\'PI_VERSAO\', \''.$strPiVersao.'\')');	
		}
		
		echo "</div>";
		if ($erros==null){			
			echo "<script>document.getElementById('msgSucesso').innerHTML='Atualização realizada com sucesso';</script>";
			
		}
		else{
			echo "<script>document.getElementById('msgErro').innerHTML='Erro atualizando protocolo integrado. ';</script>";
      		LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
		}
		LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
		InfraDebug::getInstance()->limpar();
		
	}catch(Exception $e){
      	InfraDebug::getInstance()->setBolLigado(false);
      	InfraDebug::getInstance()->setBolDebugInfra(false);
      	InfraDebug::getInstance()->setBolEcho(false);
		echo "</div>";
		echo "<script>document.getElementById('msgErro').innerHTML='Erro atualizando protocolo integrado. ".$e."';</script>";
      	LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
		InfraDebug::getInstance()->limpar();
		throw new InfraException('Erro atualizando protocolo integrado.', $e);
	}
  }

	private function executaSQLSEI($sql, $errosAnteriores){
		if ($errosAnteriores==null){
			echo "<label>SQL executado (SEI): ".$sql."</label><br /><br />";
			InfraDebug::getInstance()->gravar("SQL executado (SEI): ".$sql);
			try{			
				BancoSEI::getInstance()->executarSql($sql);
			}
			catch(Exception $e){
				echo $e;
				return $e;
			}
			return null;			
		}
		return $errosAnteriores;
	}
	
	private function executaSQLSIP($sql, $errosAnteriores){
		if ($errosAnteriores==null){
			echo "<label>SQL executado (SIP): ".$sql."</label><br /><br />";
			InfraDebug::getInstance()->gravar("SQL executado (SIP): ".$sql);
			try{
				BancoSIP::getInstance()->executarSql($sql);
			}
			catch(Exception $e){
				echo $e;
				return $e;
			}
		}
		return $errosAnteriores;
	}
	
	private function limparTagsCriticas($str){

	  //remove tags mas deixa conteúdo
	  $arrRemoverTags = array('html','body');
	  foreach ($arrRemoverTags as $tag) {
	    $str=preg_replace("%<".$tag."[^>]*>%si", "", $str);
	    $str=preg_replace("%</".$tag."[^>]*>%si", "", $str);
	  }

	  //remove tags e todo o seu conteúdo
	  $arrRemoverTags = array('img', 'script','iframe','frame','embed','object','param','video','audio','button','input','select','link','head','title');

	  foreach ($arrRemoverTags as $tag) {
	    $str = preg_replace("%<".$tag."[^>]*>(.*?)<\/".$tag.">%si", "", $str);
	    $str = preg_replace("%<".$tag."[^>]*\/>%si", "", $str);
	  }
	  return $str;
	}
}
?>