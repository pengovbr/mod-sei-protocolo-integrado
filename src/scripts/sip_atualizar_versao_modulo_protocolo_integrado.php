<?
require_once dirname(__FILE__) . '/../../web/Sip.php';

class ProtocoloIntegradoVersaoSipRN extends InfraRN
{

	private $numSeg = 0;
	private $versaoAtualDesteModulo = '3.0.0';
	const PARAMETRO_VERSAO_MODULO = 'PI_VERSAO';

	public function __construct()
	{
		parent::__construct();
		$this->inicializar(' SIP - INICIALIZAR ');
	}

	protected function inicializarObjInfraIBanco()
	{
		return BancoSip::getInstance();
	}

	private function inicializar($strTitulo)
	{
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		try {
			@ini_set('zlib.output_compression', '0');
			@ini_set('implicit_flush', '1');
		} catch (Exception $e) {
		}

		ob_implicit_flush();

		InfraDebug::getInstance()->setBolLigado(true);
		InfraDebug::getInstance()->setBolDebugInfra(true);
		InfraDebug::getInstance()->setBolEcho(true);
		InfraDebug::getInstance()->limpar();

		$this->numSeg = InfraUtil::verificarTempoProcessamento();

		$this->logar($strTitulo);
	}



	protected function atualizarVersaoConectado()
	{
		try {
			$this->inicializar('INICIANDO ATUALIZACAO DO MODULO PROTOCOLO INTEGRADO NO SIP');

			//testando se esta usando BDs suportados
			if (
				!(BancoSip::getInstance() instanceof InfraMySql) &&
				!(BancoSip::getInstance() instanceof InfraSqlServer) &&
				!(BancoSip::getInstance() instanceof InfraOracle)
			) {
				$this->finalizar('BANCO DE DADOS NAO SUPORTADO: ' . get_parent_class(BancoSip::getInstance()), true);
			}

			//testando permissoes de cria��es de tabelas
			$objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());
			if (count($objInfraMetaBD->obterTabelas('pen_sip_teste')) == 0) {
				BancoSip::getInstance()->executarSql('CREATE TABLE pen_sip_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
			}
			BancoSip::getInstance()->executarSql('DROP TABLE pen_sip_teste');

			$objInfraParametro = new InfraParametro(BancoSip::getInstance());

			// Aplica��o de scripts de atualiza��o de forma incremental
			// Aus�ncia de [break;] proposital para realizar a atualiza��o incremental de vers�es
			$strVersaoModuloPI = $objInfraParametro->getValor(self::PARAMETRO_VERSAO_MODULO, false);

			switch ($strVersaoModuloPI) {
					//case '' - Nenhuma vers�o instalada
				case '': $this->instalarV112();
				case '1.1.2': $this->instalarv115();
				case '1.1.5': $this->instalarv200();
				case '2.0.0': $this->instalarv300();
					break;

				default:
					$this->finalizar('VERSAO DO M�DULO J� CONSTA COMO ATUALIZADA', true);
			}

			$this->finalizar('FIM');
		} catch (Exception $e) {

			InfraDebug::getInstance()->setBolLigado(false);
			InfraDebug::getInstance()->setBolDebugInfra(false);
			InfraDebug::getInstance()->setBolEcho(false);
			throw new InfraException('Erro atualizando VERSAO.', $e);
		}
	}



	private function logar($strMsg)
	{
		InfraDebug::getInstance()->gravar($strMsg);
		flush();
	}

	private function finalizar($strMsg, $bolErro = false)
	{

		if (!$bolErro) {
			$this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
			$this->logar('TEMPO TOTAL DE EXECU��O: ' . $this->numSeg . ' s');
		} else {
			$strMsg = 'ERRO: ' . $strMsg;
		}

		if ($strMsg != null) {
			$this->logar($strMsg);
		}

		InfraDebug::getInstance()->setBolLigado(false);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->setBolEcho(false);
		$this->numSeg = 0;
		die;
	}

	private function atualizarNumeroVersao($parStrNumeroVersao)
	{
		$objInfraParametroDTO = new InfraParametroDTO();
		$objInfraParametroDTO->setStrNome(self::PARAMETRO_VERSAO_MODULO);
		$objInfraParametroDTO->retTodos();
		$objInfraParametroBD = new InfraParametroBD(BancoSip::getInstance());
		$objInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTO);
		$objInfraParametroDTO->setStrValor($parStrNumeroVersao);
		$objInfraParametroBD->alterar($objInfraParametroDTO);
	}

	private function instalarV112()
	{
		// Defini��o de par�metro de vers�o para o m�dulo de Protocolo Integrado
		$objInfraParametro = new InfraParametro(BancoSip::getInstance());
		$objInfraParametro->setValor('PI_VERSAO', '');

		$numIdSistemaSei = ScriptSip::obterIdSistema('SEI');
		$numIdPerfilSeiBasico = ScriptSip::obterIdPerfil($numIdSistemaSei, 'B�sico');
		$numIdPerfilSeiAdministrador = ScriptSip::obterIdPerfil($numIdSistemaSei, 'Administrador');

		// recursos
		ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_acesso_arquivo_metadados', 'controlador.php?acao=protocolo_integrado_acesso_arquivo_metadados');
		$objRecursoParametrosDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_parametros', 'controlador.php?acao=protocolo_integrado_configurar_parametros');
		ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_publicacao', 'controlador.php?acao=protocolo_integrado_configurar_publicacao');
		ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_forcar_reenvio', 'controlador.php?acao=protocolo_integrado_forcar_reenvio');
		ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_alterar', 'controlador.php?acao=protocolo_integrado_mensagens_alterar');
		$objRecursoMensagensDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_listar', 'controlador.php?acao=protocolo_integrado_mensagens_listar');
		$objRecursoMonitoramentoDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_monitoramento', 'controlador.php?acao=protocolo_integrado_monitoramento');

		// item_menu
		$numIdMenuSei = ScriptSip::obterIdMenu($numIdSistemaSei, 'Principal');
		$numIdItemMenuSeiAdministracao = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Administra��o');
		$numIdItemMenuProtocoloIntegrado = ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Protocolo Integrado', 0);
		ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoParametrosDTO->getNumIdRecurso(), 'Par�metros', 10);
		ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoMensagensDTO->getNumIdRecurso(), 'Configura��o das Mensagens', 20);
		ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoMonitoramentoDTO->getNumIdRecurso(), 'Monitoramento', 30);

		$this->atualizarNumeroVersao("1.1.2");
	}


	private function instalarv115()
	{
		$objPerfilRN = new PerfilRN();
		$objMenuRN = new MenuRN();
		$objItemMenuRN = new ItemMenuRN();
		$objRecursoRN = new RecursoRN();

		$numIdSistemaSei = ScriptSip::obterIdSistema('SEI');
		$numIdPerfilSeiAdministrador = ScriptSip::obterIdPerfil($numIdSistemaSei, 'Administrador');
		$numIdMenuSei = ScriptSip::obterIdMenu($numIdSistemaSei, 'Principal');
		$numIdItemMenuSeiAdministracao = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Administra��o');

		$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO M�DULO PROTOCOLO INTEGRADO NA BASE DO SIP...');

		//criando os recursos e vinculando-os aos perfil Administrador
		$objRecursoArquivoMetadadosDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_acesso_arquivo_metadados');
		$objRecursoConfigurarParametrosDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_parametros');
		$objRecursoConfiguracaoPublicacaoDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_publicacao');
		$objRecursoForcarReenvioDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_forcar_reenvio');
		$objRecursoMensagensAlterarDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_alterar');
		$objRecursoMensagensListarDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_listar');
		$objRecursoMonitoramentoDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_monitoramento');

		//criando Administra��o -> Administra��o do M�dulo
		$objItemMenuDTOControleProcesso = ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Protocolo Integrado', 0);

		//criando Protocolo Integrado -> Configura��o de Par�metros
		ScriptSip::adicionarItemMenu(
			$numIdSistemaSei,
			$numIdPerfilSeiAdministrador,
			$numIdMenuSei,
			$objItemMenuDTOControleProcesso->getNumIdItemMenu(),
			$objRecursoConfigurarParametrosDTO->getNumIdRecurso(),
			'Par�metros',
			10
		);

		//criando Protocolo Integrado -> Configura��o de Mensagens
		ScriptSip::adicionarItemMenu(
			$numIdSistemaSei,
			$numIdPerfilSeiAdministrador,
			$numIdMenuSei,
			$objItemMenuDTOControleProcesso->getNumIdItemMenu(),
			$objRecursoMensagensListarDTO->getNumIdRecurso(),
			'Configura��o das mensagens',
			20
		);

		ScriptSip::adicionarItemMenu(
			$numIdSistemaSei,
			$numIdPerfilSeiAdministrador,
			$numIdMenuSei,
			$objItemMenuDTOControleProcesso->getNumIdItemMenu(),
			$objRecursoMonitoramentoDTO->getNumIdRecurso(),
			'Monitoramento',
			30
		);


		ScriptSip::adicionarAuditoria($numIdSistemaSei, "Geral", array(
			'protocolo_integrado_acesso_arquivo_metadados',
			'protocolo_integrado_configurar_parametros',
			'protocolo_integrado_configurar_publicacao',
			'protocolo_integrado_mensagens_alterar',
			'protocolo_integrado_mensagens_listar',
			'protocolo_integrado_monitoramento'
		));


		$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
		$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
		$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
		$objRegraAuditoriaDTO->setStrDescricao('Geral');
		$objRegraAuditoriaRN = new RegraAuditoriaRN();
		$objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

		if($objRegraAuditoriaDTO){
			$objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
			$objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
			$objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
	
			$objSistemaRN = new SistemaRN();
			$objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
		}

		$this->atualizarNumeroVersao('1.1.5');
	}


	private function instalarv200()
	{
		$this->atualizarNumeroVersao('2.0.0');
	}

	private function instalarV300()
	{
		$this->atualizarNumeroVersao('3.0.0');
	}
}
try {
	session_start();
	SessaoSip::getInstance(false);
	$objProtocoloIntegradoVersaoSipRN = new ProtocoloIntegradoVersaoSipRN();
	$objProtocoloIntegradoVersaoSipRN->atualizarVersao();
} catch (Exception $e) {
	echo (InfraException::inspecionar($e));
	try {
		LogSip::getInstance()->gravar(InfraException::inspecionar($e));
	} catch (Exception $e) {
	}
}
