<?

require_once dirname(__FILE__).'/../../../../../../sip/web/Sip.php';
//require_once dirname(__FILE__).'/../../web/Sip.php'; 


class ProtocoloIntegradoVersaoSipRN extends InfraRN {

	private $numSeg = 0;
	private $versaoAtualDesteModulo = '2.0.0';
	private $nomeParametroModulo = 'PI_VERSAO';
	
	public function __construct(){
		parent::__construct();
		$this->inicializar(' SIP - INICIALIZAR ');
	}

	protected function inicializarObjInfraIBanco(){
		return BancoSip::getInstance();
	}

	private function inicializar($strTitulo){

		ini_set('max_execution_time','0');
		ini_set('memory_limit','-1');

		try {
			@ini_set('zlib.output_compression','0');
			@ini_set('implicit_flush', '1');
		}catch(Exception $e){}

		ob_implicit_flush();

		InfraDebug::getInstance()->setBolLigado(true);
		InfraDebug::getInstance()->setBolDebugInfra(true);
		InfraDebug::getInstance()->setBolEcho(true);
		InfraDebug::getInstance()->limpar();

		$this->numSeg = InfraUtil::verificarTempoProcessamento();

		$this->logar($strTitulo);
	}

	private function logar($strMsg){
		InfraDebug::getInstance()->gravar($strMsg);
		flush();
	}

	private function finalizar($strMsg=null, $bolErro){

		if (!$bolErro) {
			$this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
			$this->logar('TEMPO TOTAL DE EXECUÇÃO: ' . $this->numSeg . ' s');
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

	private function instalarv200(){

		$this->instalarv115();
		//Como não houve mudanças de banco no SIP desde a versão 1.1.5,apenas cria parâmetro PI_VERSAO com valor 2.0.0
		BancoSip::getInstance()->executarSql('insert into infra_parametro (valor, nome ) VALUES( \''. $this->versaoAtualDesteModulo .'\',  \''. $this->nomeParametroModulo .'\' )' );
		$this->logar('ATUALIZAÇÔES DO MÓDULO PROTOCOLO INTEGRADO NA BASE DO SIP REALIZADAS COM SUCESSO');
		 

	}

	private function instalarv115(){
		 
		$objSistemaRN = new SistemaRN();
		$objPerfilRN = new PerfilRN();
		$objMenuRN = new MenuRN();
		$objItemMenuRN = new ItemMenuRN();
		$objRecursoRN = new RecursoRN();
		 
		$objSistemaDTO = new SistemaDTO();
		$objSistemaDTO->retNumIdSistema();
		$objSistemaDTO->setStrSigla('SEI');
		 
		$objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);
		 
		if ($objSistemaDTO == null){
			throw new InfraException('Sistema SEI não encontrado.');
		}
		 
		$numIdSistemaSei = $objSistemaDTO->getNumIdSistema();
		 	 
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Administrador');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
		 
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
		}
		 
		$numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();
		 
		
		$objMenuDTO = new MenuDTO();
		$objMenuDTO->retNumIdMenu();
		$objMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objMenuDTO->setStrNome('Principal');
		$objMenuDTO = $objMenuRN->consultar($objMenuDTO);
		 
		if ($objMenuDTO == null){
			throw new InfraException('Menu do sistema SEI não encontrado.');
		}
		 
		$numIdMenuSei = $objMenuDTO->getNumIdMenu();
		
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Administração');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
		 
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
		}
		 
		$numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();
		 
			 
		//SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MÓDULO PROTOCOLO INTEGRADO NA BASE DO SIP...');

		//criando os recursos e vinculando-os aos perfil Administrador
		$objRecursoArquivoMetadadosDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_acesso_arquivo_metadados');
		$objRecursoConfigurarParametrosDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_parametros');
		$objRecursoConfiguracaoPublicacaoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_publicacao');
		$objRecursoForcarReenvioDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_forcar_reenvio');
		$objRecursoMensagensAlterarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_alterar');
		$objRecursoMensagensListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_listar');
		$objRecursoMonitoramentoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_monitoramento');
	 
		//criando Administração -> Administração do Módulo
		$objItemMenuDTOControleProcesso = $this->adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Protocolo Integrado', 0);
		
		//criando Protocolo Integrado -> Configuração de Mensagens
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOControleProcesso->getNumIdItemMenu() ,
				$objRecursoMensagensListarDTO->getNumIdRecurso(),
				'Configuração das mensagens',
				20);

		//criando Protocolo Integrado -> Configuração de Parâmetros
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOControleProcesso->getNumIdItemMenu() ,
				$objRecursoConfigurarParametrosDTO->getNumIdRecurso(),
				'Parâmetros',
				20);
		 
			$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOControleProcesso->getNumIdItemMenu() ,
				$objRecursoMonitoramentoDTO->getNumIdRecurso(),
				'Monitoramento',
				20);
		 
		//$this->removerRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiInformatica, 'atributo_consultar');
		 
		//$this->adicionarRecursoPerfil($numIdSistemaSei,$numIdPerfilSeiAdministrador,'usuario_alterar');

		//$this->removerRecurso($numIdSistemaSei,'rel_unidade_serie_alterar');
		 
		$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
		$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
		$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
		$objRegraAuditoriaDTO->setStrDescricao('Geral');
		 
		$objRegraAuditoriaRN = new RegraAuditoriaRN();
		$objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);
		 
		$rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
       \'protocolo_integrado_acesso_arquivo_metadados\',
      \'protocolo_integrado_configurar_parametros\',
      \'protocolo_integrado_configurar_publicacao\',
      \'protocolo_integrado_mensagens_alterar\',
      \'protocolo_integrado_mensagens_listar\',
      \'protocolo_integrado_monitoramento\' )'
				);
		 
		 
		//CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
		foreach($rs as $recurso){
			BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values ('.$objRegraAuditoriaDTO->getNumIdRegraAuditoria().', '.$numIdSistemaSei.', '.$recurso['id_recurso'].')');
		}
		 
		$objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
		$objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
		$objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
		 
		$objSistemaRN = new SistemaRN();
		$objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
		 
	}

	
	protected function atualizarVersaoConectado(){

		try{

			//checando BDs suportados
			if (!(BancoSip::getInstance() instanceof InfraMySql) &&
					!(BancoSip::getInstance() instanceof InfraSqlServer) &&
					!(BancoSip::getInstance() instanceof InfraOracle)){
						$this->finalizar('BANCO DE DADOS NAO SUPORTADO: '.get_parent_class(BancoSip::getInstance()),true);
	  		}

		  //checando permissoes na base de dados
		  $objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());
		   
		  /*if (count($objInfraMetaBD->obterTabelas('sip_teste'))==0){
		  	BancoSip::getInstance()->executarSql('CREATE TABLE sip_teste (id '.$objInfraMetaBD->tipoNumero().' null)');
		  }
		   
		  BancoSip::getInstance()->executarSql('DROP TABLE sip_teste');*/
		   
		  //checando qual versao instalar
		  $objInfraParametro = new InfraParametro(BancoSip::getInstance());

		  $strVersaoModuloPI = $objInfraParametro->getValor($this->nomeParametroModulo, false);
		   
		  if (InfraString::isBolVazia($strVersaoModuloPI)){
		    
		    $this->instalarv200();
		  	 	

		  }else if($strVersaoModuloPI=='1.1.5') {

		  		BancoSip::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\''.$this->nomeParametroModulo.'\'');
				$this->logar('ATUALIZAÇÔES DO MÓDULO PROTOCOLO INTEGRADO NA BASE DO SIP REALIZADAS COM SUCESSO');
		  }else if($strVersaoModuloPI==$this->versaoAtualDesteModulo){

		  	$this->finalizar('Erro instalando/atualizando Módulo Protocolo Integrado no SIP.Versão '.$strVersaoModuloPI.' já instalada',false);
		  }else{

		  	$this->finalizar('Erro instalando/atualizando Módulo Protocolo Integrado no SIP.Versão do módulo'.$this->versaoAtualDesteModulo.' inválida',false);
		
		  	
		  }
		  //BancoSip::getInstance()->executarSql('update infra_parametro set valor=\''.SIP_VERSAO.'\' where nome=\'SIP_VERSAO\'');
		  //$this->logar("SIP - FIM");
		  //$this->finalizar('Versão '.$strVersaoModuloPI.' já instalada',false);

		} catch(Exception $e){
				 
				InfraDebug::getInstance()->setBolLigado(false);
				InfraDebug::getInstance()->setBolDebugInfra(false);
				InfraDebug::getInstance()->setBolEcho(false);
				throw new InfraException('Erro atualizando versão.', $e);
				 
		}

	}
	private function getMaxIdRecurso(){

	  $objRecursoDTO = new RecursoDTO();
      $objRecursoRN  = new RecursoRN();
      $objRecursoDTO->retNumIdRecurso();
      $numMaxIdRecurso = 0;
      
      $arrRecursos = $objRecursoRN->listar($objRecursoDTO);
      foreach($arrRecursos as $key =>$value){

	      	$idRecurso = $value->getNumIdRecurso();
	      	if($idRecurso>$numMaxIdRecurso){

	          	$numMaxIdRecurso = $idRecurso;
	      	}
       }
       return $numMaxIdRecurso;

	}
	private function getMaxIdItemMenu(){

	  $objItemMenuDTO = new ItemMenuDTO();
      $objItemMenuRN  = new ItemMenuRN();
      $objItemMenuDTO->retNumIdItemMenu();
      $numMaxIdItemMenu = 0;
      
      $arrItemMenu = $objItemMenuRN->listar($objItemMenuDTO);
      foreach($arrItemMenu as $key =>$value){

	      	$idItemMenu = $value->getNumIdItemMenu();
	      	if($idItemMenu>$numMaxIdItemMenu){

	          	$numMaxIdItemMenu = $idItemMenu;
	      	}
       }
       return $numMaxIdItemMenu;

	}

	private function adicionarRecursoPerfil($numIdSistema, $numIdPerfil, $strNome, $strCaminho = null){

	 $objRecursoDTO = new RecursoDTO();
	 $objRecursoDTO->retNumIdRecurso();
	 $objRecursoDTO->setNumIdSistema($numIdSistema);
	 $objRecursoDTO->setStrNome($strNome);

	 $objRecursoRN = new RecursoRN();
	 $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

	 if ($objRecursoDTO==null){

	 	$objInfraSequencia = new InfraSequencia(BancoSip::getInstance());
	 	$objRecursoDTO = new RecursoDTO();
	 	$objRecursoDTO->setNumIdRecurso($objInfraSequencia->obterProximaSequencia('recurso'));
	 	$objRecursoDTO->setNumIdSistema($numIdSistema);
	 	$objRecursoDTO->setStrNome($strNome);
	 	$objRecursoDTO->setStrDescricao(null);

	 	if ($strCaminho == null){
	 		$objRecursoDTO->setStrCaminho('controlador.php?acao='.$strNome);
	 	}else{
	 		$objRecursoDTO->setStrCaminho($strCaminho);
	 	}

	 	$objRecursoDTO->setStrSinAtivo('S');
	 	$objRecursoDTO = $objRecursoRN->cadastrar($objRecursoDTO);
	 }

	 if ($numIdPerfil!=null){
	 	$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
	 	$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
	 	$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
	 	$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

	 	$objRelPerfilRecursoRN = new RelPerfilRecursoRN();

	 	if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO)==0){
	 		$objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
	 	}
	 }

	 return $objRecursoDTO;
	}

	private function removerRecursoPerfil($numIdSistema, $strNome, $numIdPerfil){

		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->setBolExclusaoLogica(false);
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());
			$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());
			$objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));
		}
	}

	private function desativarRecurso($numIdSistema, $strNome){
		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRecursoRN->desativar(array($objRecursoDTO));
		}
	}

	private function removerRecurso($numIdSistema, $strNome){

		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->setBolExclusaoLogica(false);
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objItemMenuDTO = new ItemMenuDTO();
			$objItemMenuDTO->retNumIdMenu();
			$objItemMenuDTO->retNumIdItemMenu();
			$objItemMenuDTO->setNumIdSistema($numIdSistema);
			$objItemMenuDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

			$objItemMenuRN = new ItemMenuRN();
			$arrObjItemMenuDTO = $objItemMenuRN->listar($objItemMenuDTO);

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

			foreach($arrObjItemMenuDTO as $objItemMenuDTO){
				$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
				$objRelPerfilItemMenuDTO->retTodos();
				$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
				$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

				$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));
			}

			$objItemMenuRN->excluir($arrObjItemMenuDTO);
			$objRecursoRN->excluir(array($objRecursoDTO));
		}
	}

	private function adicionarItemMenu($numIdSistema, $numIdPerfil, $numIdMenu, $numIdItemMenuPai, $numIdRecurso, $strRotulo, $numSequencia ){

		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdMenu($numIdMenu);

		if ($numIdItemMenuPai==null){
			$objItemMenuDTO->setNumIdMenuPai(null);
			$objItemMenuDTO->setNumIdItemMenuPai(null);
		}else{
			$objItemMenuDTO->setNumIdMenuPai($numIdMenu);
			$objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
		}

		$objItemMenuDTO->setNumIdSistema($numIdSistema);
		$objItemMenuDTO->setNumIdRecurso($numIdRecurso);
		if($numIdRecurso==null){

			$objItemMenuDTO->setStrRotulo($strRotulo);
		}
		$objItemMenuRN = new ItemMenuRN();
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

		if ($objItemMenuDTO==null){

			$objInfraSequencia = new InfraSequencia(BancoSip::getInstance());
			$objItemMenuDTO = new ItemMenuDTO();
			$objItemMenuDTO->setNumIdItemMenu($objInfraSequencia->obterProximaSequencia('item_menu'));
			$objItemMenuDTO->setNumIdMenu($numIdMenu);

			if ($numIdItemMenuPai==null){
				$objItemMenuDTO->setNumIdMenuPai(null);
				$objItemMenuDTO->setNumIdItemMenuPai(null);
			}else{
				$objItemMenuDTO->setNumIdMenuPai($numIdMenu);
				$objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
			}

			$objItemMenuDTO->setNumIdSistema($numIdSistema);
			$objItemMenuDTO->setNumIdRecurso($numIdRecurso);
			$objItemMenuDTO->setStrRotulo($strRotulo);
			$objItemMenuDTO->setStrDescricao(null);
			$objItemMenuDTO->setNumSequencia($numSequencia);
			$objItemMenuDTO->setStrSinNovaJanela('N');
			$objItemMenuDTO->setStrSinAtivo('S');
			$objItemMenuDTO = $objItemMenuRN->cadastrar($objItemMenuDTO);
		}


		if ($numIdPerfil!=null && $numIdRecurso!=null){

			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
			$objRelPerfilRecursoDTO->setNumIdRecurso($numIdRecurso);

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();

			if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO)==0){
				$objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
			}

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdRecurso($numIdRecurso);
			$objRelPerfilItemMenuDTO->setNumIdMenu($numIdMenu);
			$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

			if ($objRelPerfilItemMenuRN->contar($objRelPerfilItemMenuDTO)==0){
				$objRelPerfilItemMenuRN->cadastrar($objRelPerfilItemMenuDTO);
			}
		}

		return $objItemMenuDTO;
	}

	private function removerItemMenu($numIdSistema, $numIdMenu, $numIdItemMenu){

		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdMenu();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistema);
		$objItemMenuDTO->setNumIdMenu($numIdMenu);
		$objItemMenuDTO->setNumIdItemMenu($numIdItemMenu);

		$objItemMenuRN = new ItemMenuRN();
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

		if ($objItemMenuDTO!=null) {

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdMenu($objItemMenuDTO->getNumIdMenu());
			$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));

			$objItemMenuRN->excluir(array($objItemMenuDTO));
		}
	}

	private function removerPerfil($numIdSistema, $strNome){

		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistema);
		$objPerfilDTO->setStrNome($strNome);

		$objPerfilRN = new PerfilRN();
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

		if ($objPerfilDTO!=null){

			$objPermissaoDTO = new PermissaoDTO();
			$objPermissaoDTO->retNumIdSistema();
			$objPermissaoDTO->retNumIdUsuario();
			$objPermissaoDTO->retNumIdPerfil();
			$objPermissaoDTO->retNumIdUnidade();
			$objPermissaoDTO->setNumIdSistema($numIdSistema);
			$objPermissaoDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objPermissaoRN = new PermissaoRN();
			$objPermissaoRN->excluir($objPermissaoRN->listar($objPermissaoDTO));

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));

			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objCoordenadorPerfilDTO = new CoordenadorPerfilDTO();
			$objCoordenadorPerfilDTO->retTodos();
			$objCoordenadorPerfilDTO->setNumIdSistema($numIdSistema);
			$objCoordenadorPerfilDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objCoordenadorPerfilRN = new CoordenadorPerfilRN();
			$objCoordenadorPerfilRN->excluir($objCoordenadorPerfilRN->listar($objCoordenadorPerfilDTO));

			$objPerfilRN->excluir(array($objPerfilDTO));
		}
	}

}

?>