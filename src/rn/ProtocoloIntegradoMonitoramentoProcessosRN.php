<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoMonitoramentoProcessosRN extends InfraRN {

	public function __construct() {
		parent::__construct();
	}

	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance();
	}

	protected function listarConectado(ProtocoloIntegradoMonitoramentoProcessosDTO $protocoloIntegradoMonitoramentoProcessosDTO) {
		
	    try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();
			//$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($protocoloIntegradoMonitoramentoProcessosDTO);

			return $ret;
			
		} catch(Exception $e) {
			throw new InfraException('Erro ao listar atividades monitoradas para publicação no Protocolo Integrado.', $e);
		}
		
	}

	protected function listarAtividadesPublicacaoConectado(ProtocoloIntegradoMonitoramentoProcessosDTO $protocoloIntegradoMonitoramentoProcessosDTO) {
		
	    try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();
			//$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$ret = $objBD->consultarAtividadesPublicacao($protocoloIntegradoMonitoramentoProcessosDTO->getNumIdPacote());

			return $ret;
			
		} catch(Exception $e) {
			throw new InfraException('Erro ao listar atividades monitoradas para publicação no Protocolo Integrado.', $e);
		}
		
	} 

	protected function consultarConectado(ProtocoloIntegradoMonitoramentoProcessosDTO $protocoloIntegradoMonitoramentoProcessosDTO) {
		
	    try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();
			//$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar($protocoloIntegradoMonitoramentoProcessosDTO);
			
			return $ret;

		} catch(Exception $e) {
			throw new InfraException('Erro Consultando Atividades monitoradas para publicação no Protocolo Integrado.', $e);
		}
		
	}

	protected function contarConectado(ProtocoloIntegradoMonitoramentoProcessosDTO $protocoloIntegradoMonitoramentoProcessosDTO) {
		
	    try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();
			//$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$ret = $objBD->contar($protocoloIntegradoMonitoramentoProcessosDTO);
			
			return $ret;

		} catch(Exception $e) {
			throw new InfraException('Erro Consultando Atividades monitoradas para publicação no Protocolo Integrado.', $e);
		}
		
	}

	protected function publicarProcessosConectado(ProtocoloIntegradoMonitoramentoProcessosDTO $objProtocoloIntegradoParametrosDTO) {
		
		$tempoInicial = time();
		try {
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $objProtocoloIntegradoParametrosDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();
			$objInfraException->lancarValidacoes();
			
			$objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
			$objProtocoloIntegradoParametrosDTO->retTodos();
	
			InfraDebug::getInstance()->gravar('Buscando Configuração de Publicação no Protocolo Integrado');
			$objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
			$objRetorno = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
	
			$objRetorno->setStrSinExecutandoPublicacao('S');
			$objProtocoloIntegradoParametrosRN->alterar($objRetorno);
			
			$this->cadastrarAtividadesIntegracao($objRetorno->getNumAtividadesCarregar());
			
			InfraDebug::getInstance()->gravar('Publicando Metadados e Operações dos Processos no Protocolo Integrado');
			$arrParam = array();
			$arrParam[0] = $objRetorno;
			$arrParam[1] = null;
			$this->publicarProcessosMonitorados($arrParam);
			
			$objRetorno->setDthDataUltimoProcessamento(date('d/m/Y H:i:s'));
			$objRetorno->setStrSinExecutandoPublicacao('N');
			$objProtocoloIntegradoParametrosRN->alterar($objRetorno);
	
			$tempoFinal = time();
			$tempo = $tempoFinal - $tempoInicial;
			
		} catch (Exception $e) {
			throw new InfraException('Erro acontecido', $e);
		}
		
	}

	protected function cadastrarControlado(ProtocoloIntegradoMonitoramentoProcessosDTO $protocoloIntegradoMonitoramentoProcessosDTO) {
		
	    try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();

            /*if ($objTarefaDTO->isSetStrNome()) {
                $this->validarStrNome($objTarefaDTO, $objInfraException);
            }
            if ($objTarefaDTO->isSetStrSinHistoricoResumido()) {
                $this->validarStrSinHistoricoResumido($objTarefaDTO, $objInfraException);
            }
            */
			
			$objInfraException->lancarValidacoes();
			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$objBD->cadastrar($protocoloIntegradoMonitoramentoProcessosDTO);

		} catch(Exception $e) {
			throw new InfraException('Erro Cadastrando atividades monitoradas para publicação no Protocolo Integrado.', $e);
		}
		
	}

	protected function consultarParticipantesDocumentosAssinadosProcessoConectado(ProtocoloDTO $protocoloDTO) {
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento', __METHOD__, $protocoloDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$ret = $objBD->consultarParticipantesDocumentosAssinadosProcesso($protocoloDTO->getDblIdProtocolo());

			return $ret;

		} catch(Exception $e) {
			throw new InfraException('Erro consultando Primeiro Documento Assinadodo Processo.', $e);
		}
	}

    protected function cadastrarAtividadesIntegracaoConectado($numMaxAtividades) {
		
		$tempo1 = time();
		$numUnidadeTeste = null;
		try {
			$objInfraParametroDTO = new InfraParametroDTO();
			$objParametroBD = new InfraParametroBD($this->getObjInfraIBanco());
			$objInfraParametroDTO->setStrNome('ID_UNIDADE_TESTE');
			$objInfraParametroDTO->retTodos();
	    	$ret = $objParametroBD->listar($objInfraParametroDTO);
			if (count($ret)>0) {
		 		$objInfra = $ret[0];
				$numUnidadeTeste = $objInfra->getStrValor();
			}
		} catch(Exception $e) {}
		
		try {
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('md_pi_monitoramento',__METHOD__,$protocoloIntegradoMonitoramentoProcessosDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();
			$objInfraException->lancarValidacoes();

			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			$arrProtocolos = array();

			$numTotal = 0;
			$numRodada = 0;
			$numCarregarPorRodada = 30000;
			while (true) {
				$numRodada = $numRodada+1;
				
				$qtCarregar = $numCarregarPorRodada;
				if ($numTotal>=$numMaxAtividades) {
					break;
				}
				if ($numTotal+$numCarregarPorRodada>$numMaxAtividades) {
					$qtCarregar = $numMaxAtividades-$numTotal;
				}
				$arrAtividadesMonitoradasDTO = $objBD->consultarNovasOperacoesProcesso($qtCarregar, $numUnidadeTeste);
				
				$numTotalRodada = count($arrAtividadesMonitoradasDTO);
				if ($numTotalRodada==0) {
					break;
				}
				$numTotal = $numTotal+$numTotalRodada;
				
				$arrParam = array();
				$arrParam[0] = $numTotalRodada;
				$arrParam[1] = $arrAtividadesMonitoradasDTO;
				$arrParam[2] = $arrProtocolos;
				$this->cadastrarAtividadesBatch($arrParam);
				
				unset($arrAtividadesMonitoradasDTO);
				
				if ($numTotalRodada<$numCarregarPorRodada) {
					break;
				}
			}

		} catch(Exception $e) {
			throw new InfraException('Erro ao cadastrar Atividades que serão enviadas ao Protocolo Integrado .', $e);
		}
		$tempo2 = time();
		
	}

	protected function cadastrarAtividadesBatchControlado($arrParam) {
	    
		$numTotalRodada = $arrParam[0];
		$arrAtividadesMonitoradasDTO = $arrParam[1];
		$arrProtocolos = $arrParam[2];
		
		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		for ($i = 0; $i < $numTotalRodada; $i++) {
			$objProtocoloIntegradoMonitoramentoDTO = $arrAtividadesMonitoradasDTO[$i];
			$idProtocolo = $objProtocoloIntegradoMonitoramentoDTO->getNumIdProtocolo();
	
			if (!in_array($idProtocolo, $arrProtocolos)) {
	
				$objPacoteExistenteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
				$objPacoteExistenteDTO->retTodos();
				$objPacoteExistenteDTO->setNumIdProtocolo($idProtocolo);
				$objPacoteExistenteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO);
				$objPacoteExistenteDTO->setNumMaxRegistrosRetorno(1);
				$arrPacoteRetorno = $objPacoteRN->listar($objPacoteExistenteDTO);
				$objPacoteRetorno = null;
				
				if(count($arrPacoteRetorno)>0) {
					$objPacoteRetorno = $arrPacoteRetorno[0];
				}
				if ($objPacoteRetorno == null) {

					$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();

					
					$objPacoteDTO->setNumIdProtocolo($idProtocolo);
					$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO);
					$objPacoteRN->cadastrar($objPacoteDTO);
	
					$objPacoteDTO->retTodos();
					$ret = $objPacoteRN->consultar($objPacoteDTO);
					$idPacote = $ret->getNumIdProtocoloIntegradoPacoteEnvio();
	
					$arrProtocolos[$i] = $idProtocolo;
					$arrIdPacote[$idProtocolo] = $idPacote;
				} else {
	
					$arrProtocolos[$i] = $idProtocolo;
					$arrIdPacote[$idProtocolo] = $objPacoteRetorno->getNumIdProtocoloIntegradoPacoteEnvio();
				}
	
			}
	
			$objProtocoloIntegradoMonitoramentoDTO->setNumIdPacote($arrIdPacote[$idProtocolo]);
			$this->cadastrar($objProtocoloIntegradoMonitoramentoDTO);

		}
	}

	protected function alterarControlado($arrParam) {}

	public function getSituacoesIntegracao() {
		$strItensSelSituacoesIntegracoes = array('' => 'Todos', ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO => 'Não Integrado', ProtocoloIntegradoPacoteEnvioRN::$STA_INTEGRADO => 'Integrado', ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL => 'Erro Negocial', ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA => 'Falha Infra');
        return $strItensSelSituacoesIntegracoes;
	}
	
	public function getUnidadesGeradoras() {

		$objInfraSip = new InfraSip(SessaoSEI::getInstance());

		$ret = $objInfraSip->carregarUnidades(SessaoSEI::getInstance()->getNumIdSistema());
		$arrUnidadesSip = array();

		$srtSeparador = ":UNI:";
		$strItensUnidadesCompacto = array();
		foreach ($ret as $uni) {
			$numIdUnidade = $uni[InfraSip::$WS_UNIDADE_ID];
			if ($numIdUnidade!='') {
				$strItensUnidadesCompacto[$numIdUnidade]=$uni[InfraSip::$WS_UNIDADE_SIGLA].$srtSeparador.$numIdUnidade;		
			}
		}
		sort($strItensUnidadesCompacto, SORT_STRING);
		
		$strItensUnidades = array();
		$strItensUnidades[0]='*';
		foreach ($strItensUnidadesCompacto as $uni=>$uni2) {
			$strFragmentos = explode($srtSeparador, $uni2);
			$strItensUnidades[$strFragmentos[1]] = $strFragmentos[0]; 
		}
		
		return $strItensUnidades;
	}

	public function listarProcessosMonitoradosControlado($filtro = array()) {
	    
		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		$objProtocoloIntegradoDTO = new ProtocoloIntegradoDTO();
		$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
		$objPacoteDTO->retNumIdProtocolo();
		$objPacoteDTO->retStrStaIntegracao();
		$objPacoteDTO->retDthDataSituacao();
		$objPacoteDTO->retDthDataMetadados();
		$objPacoteDTO->retNumTentativasEnvio();
		$objPacoteDTO->retStrProtocoloFormatado();
		$objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();

		$objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
		$strSqlNativo = '';
		
		if (isset($filtro['filtroCodProtocolo']) && $filtro['filtroCodProtocolo'] != '') {
			$strProtocoloFormatadoLimpo = InfraUtil::retirarFormatacao($filtro['filtroCodProtocolo']);
			$objProtocolo = new ProtocoloDTO();
			$objProtocolo->retDblIdProtocolo();
			$objProtocoloRN = new ProtocoloRN();
			$objProtocolo->setStrProtocoloFormatadoPesquisa($strProtocoloFormatadoLimpo . '%', InfraDTO::$OPER_LIKE);
			$arrProtocolosRetornados = $objProtocoloRN->listarRN0668($objProtocolo);

			$arrIdProtocolo = array();
			for ($k = 0; $k < count($arrProtocolosRetornados); $k++) {
				array_push($arrIdProtocolo, $arrProtocolosRetornados[$k]->getDblIdProtocolo());
			}
			if (count($arrIdProtocolo) > 0) {
				$objPacoteDTO->setNumIdProtocolo($arrIdProtocolo, InfraDTO::$OPER_IN);
			} else {
				$objPacoteDTO->setNumIdProtocolo(-1);
			}
		}
		
		if (isset($filtro['filtroSelSitucaoIntegracao']) && $filtro['filtroSelSitucaoIntegracao'] != '') {

			$objPacoteDTO->setStrStaIntegracao($filtro['filtroSelSitucaoIntegracao']);
		} else if (!isset($filtro['filtroSelSitucaoIntegracao'])) {
			$strSqlNativo .= "sta_integracao<>'NI' AND ";
		}
		
		if (isset($filtro['filtroSelUnidade']) && $filtro['filtroSelUnidade'] != '' && $filtro['filtroSelUnidade'] != 0) {
			$strUnidades = $filtro['filtroSelUnidade'];
			if (isset($filtro['filtroIncluirUnidadesFilhas']) && $filtro['filtroIncluirUnidadesFilhas']=='on') {
				$objInfraSip = new InfraSip(SessaoSEI::getInstance());
				$ret = $objInfraSip->carregarUnidades(SessaoSEI::getInstance()->getNumIdSistema());
				$arrUnidadesSip = array();				
				
				$numUnidade = $filtro['filtroSelUnidade'];
				foreach ($ret as $uni) {
					$numIdUnidade = $uni[InfraSip::$WS_UNIDADE_ID];
					if ($numIdUnidade!='' && $numIdUnidade==$numUnidade) {
						$numIdUnidadesInferor = $uni[InfraSip::$WS_UNIDADE_SUBUNIDADES];
						
						foreach ($numIdUnidadesInferor as $numIdUnidadeInferor) {
							$strUnidades = $strUnidades.",".$numIdUnidadeInferor;
						}
					}
				}
			}
            // Adriano MPOG - tratando novos IDs de tamanho máximo de 30 posições
			$strSqlNativo .= " md_pi_pacote_envio.id_protocolo IN (select id_protocolo from protocolo p where p.id_unidade_geradora IN (".$strUnidades.")) AND ";
		} 
		//Adriano -MPOG - fazendo alterações para ficar multibancos o tratamento do formato de data
		//Se campo inicial da data de geração do processo está preenchido
		if (isset($filtro['filtroTxtPeriodoGeracaoDe']) && $filtro['filtroTxtPeriodoGeracaoDe'] != '') {

			$strDataInicio = $filtro['filtroTxtPeriodoGeracaoDe'];
			
			//Código provisório para tratar unificação dos fontes
			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			
			$strDataInicialFormatada = $strDataInicio . " 00:00:00";
			$strNovaDataInicial = $objBD->retornarFormatoData($strDataInicialFormatada);
			
			//Se campo final da data de geração do processo está preenchido
			if (isset($filtro['filtroTxtPeriodoGeracaoA']) && $filtro['filtroTxtPeriodoGeracaoA'] != '') {
				$strDataFim = $filtro['filtroTxtPeriodoGeracaoA'];
				$strDataFinalFormatada = $strDataFim . " 23:59:59";
				$strNovaDataFinal = $objBD->retornarFormatoData($strDataFinalFormatada);
				
				//Trata SQL nativo para que considere apenas protocolos produzidos dentro daquele intervalo
				$strSqlNativo .= " md_pi_pacote_envio.id_protocolo IN (select p.id_protocolo from protocolo p where p.dta_geracao>= ".$strNovaDataInicial." AND p.dta_geracao<= ".$strNovaDataFinal.") AND ";
			} else {
		        //Trata SQL nativo para que considere apenas protocolos produzidos a partir da data inicial informada.
				$strSqlNativo .=  " md_pi_pacote_envio.id_protocolo IN (select p.id_protocolo from protocolo p where p.dta_geracao>= ".$strNovaDataInicial. ") AND ";
			}
			
		} else if (isset($filtro['filtroTxtPeriodoGeracaoA']) && $filtro['filtroTxtPeriodoGeracaoA'] != '') {
			//Se apenas o segundo campo de data de geração do processo está preenchido, considera apenas os processos produzidos até aquela data
			$strDataFim = $filtro['filtroTxtPeriodoGeracaoA'];

			//Código provisório para tratar unificação dos fontes
			$objBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
				
			$strDataFinalFormatada = $strDataFim . " 23:59:59";
			$strNovaDataFinal = $objBD->retornarFormatoData($strDataFinalFormatada);
			
			$strSqlNativo .=  " pi_pacote_envio.id_protocolo IN (select p.id_protocolo from protocolo p where p.dta_geracao<= ". $dthNovaDataFinal. ") AND ";
		}
		
		if (isset($filtro['filtroTxtPeriodoDe']) && $filtro['filtroTxtPeriodoDe'] != '') {
			$objPacoteDTO->adicionarCriterio(array('DataSituacao'), array(InfraDTO::$OPER_MAIOR_IGUAL), array($filtro['filtroTxtPeriodoDe'] . ' 00:00:00'));
		}
		if (isset($filtro['filtroTxtPeriodoA']) && $filtro['filtroTxtPeriodoA'] != '') {
			$objPacoteDTO->adicionarCriterio(array('DataSituacao'), array(InfraDTO::$OPER_MENOR_IGUAL), array($filtro['filtroTxtPeriodoA'] . ' 23:59:59'));
		}
		if ($strSqlNativo!='') {
			$strSqlNativo = trim($strSqlNativo);	
			$strSqlNativo = substr($strSqlNativo, 0,strlen($strSqlNativo)-3);
			$objPacoteDTO->setStrCriterioSqlNativo($strSqlNativo);
		}
		
		if (isset($filtro['paginacao']) && $filtro['paginacao'] == true) {
			PaginaSEI::getInstance()->prepararOrdenacao($objPacoteDTO, 'IdProtocoloIntegradoPacoteEnvio', InfraDTO::$TIPO_ORDENACAO_ASC);
			
			if (isset($filtro['filtroNumQuantidadeRegistrosPorPagina']) && $filtro['filtroNumQuantidadeRegistrosPorPagina']!='') {
				PaginaSEI::getInstance()->prepararPaginacao($objPacoteDTO, $filtro['filtroNumQuantidadeRegistrosPorPagina']);
			} else {
				PaginaSEI::getInstance()->prepararPaginacao($objPacoteDTO, 50);
			}
		}
		
		$arrObjPacotesDTO = $objPacoteRN->listar($objPacoteDTO);
		$numPacotes = count($arrObjPacotesDTO);
		if (isset($filtro['paginacao']) && $filtro['paginacao'] == true) {
			if(isset($filtro['filtroNumQuantidadeRegistrosPorPagina']) && $filtro['filtroNumQuantidadeRegistrosPorPagina']!=''&&$numPacotes>$filtro['filtroNumQuantidadeRegistrosPorPagina']) {
				$objPacoteDTO->setNumRegistrosPaginaAtual($filtro['filtroNumQuantidadeRegistrosPorPagina']);
			}
			PaginaSEI::getInstance()->processarPaginacao($objPacoteDTO);
		}
		$arrObjProcedimentoDTO = $this->montarPacotesMonitorados($arrObjPacotesDTO, $filtro);
		
		return $arrObjProcedimentoDTO;
	}

	public function montarPacotesMonitorados($arrObjPacotesDTO, $filtro = null) {

		$objProtocoloRN = new ProtocoloRN();
		$numPacotes = count($arrObjPacotesDTO);
		$arrObjProcedimentoDTO = array();
		$objProtocoloIntegradoMonitoramentoProcessosDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
		$objProtocoloIntegradoMonitoramentoProcessosDTO->retNumIdProtocoloIntegradoMonitoramentoProcessos();
		$objProtocoloIntegradoMonitoramentoProcessosDTO->retNumIdAtividade();
		$objProtocoloIntegradoMonitoramentoProcessosDTO->retNumIdProtocolo();

		for ($p = 0; $p < $numPacotes; $p++) {

			$idProtocolo = $arrObjPacotesDTO[$p]->getNumIdProtocolo();
			$situacaoPacote = $arrObjPacotesDTO[$p]->getStrStaIntegracao();
			$dataSituacao = $arrObjPacotesDTO[$p]->getDthDataSituacao();

			$pacote = $arrObjPacotesDTO[$p]->getNumIdProtocoloIntegradoPacoteEnvio();
			$arrObjProcedimentoDTO[$pacote]['atividades'] = array();

			$objProtocolo = new ProtocoloDTO();
			$objProtocolo->retStrNomeTipoProcedimentoDocumento();
			$objProtocolo->retStrProtocoloFormatado();
			$objProtocolo->retStrDescricao();
			$objProtocolo->retStrProtocoloFormatadoPesquisa();
			$objProtocolo->retDblIdProtocolo();
			$objProtocolo->retDtaGeracao();
			$objProtocolo->retStrNomeTipoProcedimentoProcedimento();

			$objProtocolo->setDblIdProtocolo($idProtocolo);
			$objProtocolo->setDblIdProtocolo($arrObjPacotesDTO[$p]->getNumIdProtocolo());

			$arrObjProcedimentoDTO[$pacote]['protocolo'] = $objProtocoloRN->consultarRN0186($objProtocolo);
			$arrObjProcedimentoDTO[$pacote]['sta_integracao'] = $situacaoPacote;
			$arrObjProcedimentoDTO[$pacote]['dth_metadados'] = $arrObjPacotesDTO[$p]->getDthDataMetadados();
			$arrObjProcedimentoDTO[$pacote]['id_pacote'] = $arrObjPacotesDTO[$p]->getNumIdProtocoloIntegradoPacoteEnvio();
			$arrObjProcedimentoDTO[$pacote]['num_tentativas'] = $arrObjPacotesDTO[$p]->getNumTentativasEnvio();
			$arrObjProcedimentoDTO[$pacote]['dth_situacao'] = $dataSituacao;
		}
		
		return $arrObjProcedimentoDTO;
	}

	public function listarProcessosPublicacao($filtro) {

		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
		$objPacoteDTO->retNumIdProtocolo();
		$objPacoteDTO->retStrStaIntegracao();
		$objPacoteDTO->retDthDataSituacao();
		$objPacoteDTO->retDthDataMetadados();
		$objPacoteDTO->retNumTentativasEnvio();
		$objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
 		$strSqlNativo = '';
 		
		if (isset($filtro['strDthAgendamentoExecutado'])) {
			$strDataAgendamentoExecutado = str_replace('/', '-', $filtro['strDthAgendamentoExecutado']);
			$strDataAgendamentoExecutado = date('d/m/Y G:i:s', strtotime($strDataAgendamentoExecutado));
			$objPIMonitoraProcessosBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
			
			$strDataAgendamentoExecutado = $objPIMonitoraProcessosBD->retornarFormatoData($strDataAgendamentoExecutado);
			$strSqlNativo .=  "(dth_agendamento_executado is null OR dth_agendamento_executado<>".$strDataAgendamentoExecutado.") AND ";
		}
		if (isset($filtro['numMaxResultados'])) {
			$objPacoteDTO->setNumMaxRegistrosRetorno($filtro['numMaxResultados']);
		}
		if (isset($filtro['numPagina'])) {
			$objPacoteDTO->setNumPaginaAtual($filtro['numPagina']);
		}
		if (isset($filtro['pacotes'])) {
			$objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($filtro['pacotes'], InfraDTO::$OPER_IN);
		} else {
			if (isset($filtro['numMaxTentativas'])) {
				$strSqlNativo .=  "sta_integracao='NI' OR (sta_integracao<>'I' AND num_tentativas_envio<".$filtro['numMaxTentativas'].") AND ";
			} else {
				$strSqlNativo .=  "sta_integracao<>'I' AND ";
			}
		}
		if($strSqlNativo!='') {
			$strSqlNativo = trim($strSqlNativo);	
			$strSqlNativo = substr($strSqlNativo, 0,strlen($strSqlNativo)-3);
			$objPacoteDTO->setStrCriterioSqlNativo($strSqlNativo);
		}
		
		$arrObjPacotesDTO = $objPacoteRN->listar($objPacoteDTO);
		$arrObjProcedimentoDTO = $this->montarPacotesMonitorados($arrObjPacotesDTO, $filtro);
		return $arrObjProcedimentoDTO;
	}

	private function publicarProcessosMonitorados($arrObjRetornoProtocoloIntegradoParametrosDTOFiltro) {
	    
		$filtro = $arrObjRetornoProtocoloIntegradoParametrosDTOFiltro[1];
		
		$objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();

		$objParticipanteDTO = new ParticipanteDTO();
		$objParticipanteDTO->retNumIdContato();
		$objParticipanteDTO->retStrNomeContato();
		$objParticipanteDTO->retStrSiglaContato();
		
		$body = new ListaDocumentoPiDTO();

		$arrObjPacotesEnviados = array();
		$arrObjProtocoloEnviados = array();

		$quantidadeDocumentos = 1;

		$opcoes = array("soap_version" => SOAP_1_1, "trace" => 1, 'exceptions' => 0, 'encoding' => ' UTF-8');

		$objConfiguracaoModProtocoloIntegrado = ConfiguracaoModProtocoloIntegrado::getInstance();
		$urlWebService = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "WebService");
		$loginWebService = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "UsuarioWebService");
		$senhaWebService = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "SenhaWebService");

		$urlApiRest = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "ApiRest");
		$loginApiRest = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "UsuarioApiRest");
		$senhaApiRest = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "SenhaApiRest");

		if (strlen(trim($senhaApiRest)) > 0 && strlen(trim($loginApiRest)) > 0) {
			$conexaoCliente = new ProtocoloIntegradoClienteRestWS($urlApiRest, $loginApiRest, $senhaApiRest, $opcoes);
		}else if (strlen(trim($senhaWebService)) > 0 && strlen(trim($loginWebService)) > 0) {
			$conexaoCliente = new ProtocoloIntegradoClienteWS($urlWebService, $loginWebService, $senhaWebService, $opcoes);
		} else {
			throw new InfraException('Campos Login e Senha para Acesso ao WebService ou Api Rest devem ser informados na tela de Configuração de Parâmetros do Protocolo Integrado.', $e);
		}

		$retornoWS = $conexaoCliente->getQuantidadeMaximaDocumentosPorRequisicaoServidor();

		if (!is_int($retornoWS->NumeroMaximoDocumentos)) {
			throw new InfraException('Não foi Possível Obter a  Quantidade Máxima de Documentos no WebService do Protocolo Integrado.', $retornoWS);
		}

		$quantidadeMaximaDocumentos = $retornoWS->NumeroMaximoDocumentos;
		$numMaximoTentativas = $objConfiguracaoModProtocoloIntegrado->getValor("ProtocoloIntegrado", "TentativasReenvio");
		$unidadesOperacao = array();
		$unidadesOperacaoId = array();

		$strHierarquiaUnidade = '';
		$objInfraSip = new InfraSip(SessaoSEI::getInstance());
		$ret = $objInfraSip->carregarUnidades(SessaoSEI::getInstance()->getNumIdSistema());

		$arrUnidadesSip = array();

		foreach ($ret as $uni) {
			$numIdUnidade = $uni[InfraSip::$WS_UNIDADE_ID];
			$arrUnidadesSip[$numIdUnidade] = array();
			$arrUnidadesSip[$numIdUnidade][UnidadeRN::$POS_UNIDADE_SIGLA] = $uni[InfraSip::$WS_UNIDADE_SIGLA];
			$arrUnidadesSip[$numIdUnidade][UnidadeRN::$POS_UNIDADE_DESCRICAO] = $uni[InfraSip::$WS_UNIDADE_DESCRICAO];
			$arrUnidadesSip[$numIdUnidade][UnidadeRN::$POS_UNIDADE_UNIDADES_SUPERIORES] = $uni[InfraSip::$WS_UNIDADE_UNIDADES_SUPERIORES];
		}
		
		$numTotal = 0;
		$numRodada = 0;
		$filtro["numMaxTentativas"] = $numMaximoTentativas;
		$filtro["numMaxResultados"] = $quantidadeMaximaDocumentos;
		$filtro["numPagina"] = 0;
		
		$strInicioPublicacao = date('d/m/Y H:i:s');
		$filtro['strDthAgendamentoExecutado'] = $strInicioPublicacao;
		
		while (true) {
			$numRodada = $numRodada+1;
			$arrObjProcessosMonitorados = $this->listarProcessosPublicacao($filtro);
			
			if (count($arrObjProcessosMonitorados)==0) {
				InfraDebug::getInstance()->gravar('Sem processos para publicar no PI');
				break;
			}
			$numTotal = $numTotal+count($arrObjProcessosMonitorados);
			
			InfraDebug::getInstance()->gravar($numTotal . ' processos a publicar no PI');

			$contador = 0;

			foreach ($arrObjProcessosMonitorados as $pacote => $protocoloMonitorado) {
				$contador = $contador+1;
				$documento = new DocumentoPiDTO();
	
				$objProtocoloDTO = $protocoloMonitorado['protocolo'];
	
				array_push($arrObjProtocoloEnviados, $objProtocoloDTO);
				
				$objParticipanteDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
	
				$objParticipanteDTO->setStrStaParticipacao(array(ParticipanteRN::$TP_INTERESSADO), InfraDTO::$OPER_IN);
	
				$objParticipanteDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);
	
				$objParticipanteRN = new ParticipanteRN();
				$arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);
				$arrIdParticipanteProcesso = array();
				foreach ($arrObjParticipanteDTO as $ch => $val) {
	
					array_push($arrIdParticipanteProcesso, $val->getNumIdContato());
				}
				$arrDocumentosAssinadosDTO = $this->consultarParticipantesDocumentosAssinadosProcesso($objProtocoloDTO);
	
				if (is_array($arrDocumentosAssinadosDTO) && count($arrDocumentosAssinadosDTO) > 0) {
	
					foreach ($arrDocumentosAssinadosDTO as $key => $participante) {
	
						if (!in_array($participante->getNumIdContato(), $arrIdParticipanteProcesso)) {
							array_push($arrObjParticipanteDTO, $participante);
						}
					}
				}
				
				unset($arrDocumentosAssinadosDTO);
	
				$arrObjParticipanteDTO = array_unique($arrObjParticipanteDTO);
	
				$objRelProtocoloAssuntoDTO = new RelProtocoloAssuntoDTO();
				$objRelProtocoloAssuntoDTO->setDistinct(true);
				$objRelProtocoloAssuntoDTO->retNumSequencia();
				$objRelProtocoloAssuntoDTO->retNumIdAssunto();
				$objRelProtocoloAssuntoDTO->retStrCodigoEstruturadoAssunto();
				$objRelProtocoloAssuntoDTO->retStrDescricaoAssunto();
				$objRelProtocoloAssuntoDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
				$objRelProtocoloAssuntoDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);
	
				$objRelProtocoloAssuntoRN = new RelProtocoloAssuntoRN();
				$arrObjRelProtocoloAssuntoDTO = $objRelProtocoloAssuntoRN->listarRN0188($objRelProtocoloAssuntoDTO);
	
				foreach ($arrObjRelProtocoloAssuntoDTO as $key => $value) {	
					$objRelProtocoloAssuntoDTO = $value;
				}
				unset($arrObjRelProtocoloAssuntoDTO);
	
				$codigoProtocolo = $objProtocoloDTO->getStrProtocoloFormatadoPesquisa();
	
				list($day, $month, $year) = explode('/', $objProtocoloDTO->getDtaGeracao());
				$dataGeracaoConvertida = sprintf('%s-%s-%s', $year, $month, $day);
				$dataGeracao = date('c', strtotime($dataGeracaoConvertida));
	
				$tipoProcedimento = "Processo";
				if (strlen(trim($objProtocoloDTO->getStrDescricao())) > 0) {
					$assunto = $objProtocoloDTO->getStrNomeTipoProcedimentoProcedimento() . ' - ' . $objProtocoloDTO->getStrDescricao();
				} else {	
					$assunto = $objProtocoloDTO->getStrNomeTipoProcedimentoProcedimento();
				}
	
				$documento->setProtocolo($codigoProtocolo);
				$documento->setDataHoraProducao($dataGeracao);
				$documento->setEspecie(Encoding::utf8ToIso($tipoProcedimento));
				$documento->setAssunto(Encoding::utf8ToIso($assunto));
	
				$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
				$objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
				$objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo2();
				$objRelProtocoloProtocoloDTO->setDblIdProtocolo1($objProtocoloDTO->getDblIdProtocolo());
				$arrEstadosRelacaoProtocolo = array();
	
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_SOBRESTADO);
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_RELACIONADO);
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
				$objRelProtocoloProtocoloDTO->setStrStaAssociacao($arrEstadosRelacaoProtocolo, InfraDTO::$OPER_IN);
				$arrRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->listarRN0187($objRelProtocoloProtocoloDTO);
	
				$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
				$objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
				$objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
				$objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProtocoloDTO->getDblIdProtocolo());
				$arrEstadosRelacaoProtocolo = array();
	
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_SOBRESTADO);
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_RELACIONADO);
				array_push($arrEstadosRelacaoProtocolo, RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
				$objRelProtocoloProtocoloDTO->setStrStaAssociacao($arrEstadosRelacaoProtocolo, InfraDTO::$OPER_IN);
				$arrRelProtocoloProtocoloDTO2 = $objRelProtocoloProtocoloRN->listarRN0187($objRelProtocoloProtocoloDTO);
				
				for ($numProtocolo = 0; $numProtocolo < count($arrRelProtocoloProtocoloDTO2); $numProtocolo++) {
					$arrRelProtocoloProtocoloDTO2[$numProtocolo]->setStrProtocoloFormatadoProtocolo2($arrRelProtocoloProtocoloDTO2[$numProtocolo]->getStrProtocoloFormatadoProtocolo1());
					array_push($arrRelProtocoloProtocoloDTO, $arrRelProtocoloProtocoloDTO2[$numProtocolo]);
				}
				unset($arrObjRelProtocoloAssuntoDTO);
				
				if (count($arrRelProtocoloProtocoloDTO) > 0) {
					$arrCodigoProtocoloRelacionado = array();
					for ($k = 0; $k < count($arrRelProtocoloProtocoloDTO); $k++) {
	
						$protocoloRelacionadoDTO = $arrRelProtocoloProtocoloDTO[$k];
						$codProtocoloRelacionado = InfraUtil::retirarFormatacao($protocoloRelacionadoDTO->getStrProtocoloFormatadoProtocolo2());
						if (strlen($codProtocoloRelacionado) == 13 || strlen($codProtocoloRelacionado) == 14 || strlen($codProtocoloRelacionado) == 15 || strlen($codProtocoloRelacionado) == 17 || strlen($codProtocoloRelacionado) == 21) {
							$protocoloRelacionado = InfraUtil::retirarFormatacao($protocoloRelacionadoDTO->getStrProtocoloFormatadoProtocolo2());
							$documento->addProtocoloRelacionado($protocoloRelacionado);
							array_push($arrCodigoProtocoloRelacionado, $codProtocoloRelacionado);
						}
	
					}
				}
				unset($arrObjRelProtocoloAssuntoDTO);
				if (count($arrObjParticipanteDTO) > 0) {
	
					foreach ($arrObjParticipanteDTO as $key => $objInteressadoDTO) {
						$strNomeInteressado = $this->gerarNomeInteressadoComCpfEscondido($objInteressadoDTO->getStrNomeContato());
						$interessado = new InteressadoPiDTO();
						$nomeInteressado = substr($strNomeInteressado, 0, 150);
						$interessado->setNome(Encoding::utf8ToIso($nomeInteressado));
						$documento->addInteressado($interessado);
					}
				}
	
				unset($arrObjParticipanteDTO);
		
				$objProtocoloIntegradoMonitoramentoProcessosDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setNumIdPacote($protocoloMonitorado['id_pacote']);
				
				$arrAtividades = $this->listarAtividadesPublicacao($objProtocoloIntegradoMonitoramentoProcessosDTO);
				
				for ($j = 0; $j < count($arrAtividades); $j++) {
	
					$numAtividade = $arrAtividades[$j]->getNumIdAtividade();
	
					$strMensagem = $arrAtividades[$j]->getStrMensagemPublicacao();
	
					$objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
					$strNomeOperacao = $objProtocoloIntegradoRN->transformarMensagemOperacao($numAtividade, $strMensagem);
	
					$itemHistorico = new HistoricoDocumentoPiDTO();
				
					$dataHoraOperacaoConvertida =  str_replace('/', '-', $arrAtividades[$j]->getDthDataAbertura());
					$dataHoraOperacao = date('c', strtotime($dataHoraOperacaoConvertida));
					$unidadeOperacao = '';
	
					if ($arrAtividades[$j]->getNumIdUnidade() != null) {
	
						$idUnidadeOperacao = $arrAtividades[$j]->getNumIdUnidade();
	
						if (in_array($idUnidadeOperacao, $unidadesOperacaoId)) {
	
							$unidadeOperacao = $unidadesOperacao[$idUnidadeOperacao];
	
						} else {
							$objUnidadeDTO = new UnidadeDTO();
	
							$objUnidadeDTO->retNumIdUnidade();
							$objUnidadeDTO->retStrSigla();
							$objUnidadeDTO->retStrSiglaOrgao();
							$objUnidadeDTO->retStrDescricaoOrgao();
							$objUnidadeDTO->retStrDescricao();
	
							$objUnidadeDTO->setNumIdUnidade($idUnidadeOperacao);
							$objUnidadeDTO->setBolExclusaoLogica(false);
	
							$objUnidadeRN = new UnidadeRN();
	
							$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
							if ($objUnidadeDTO != null) {
	
								$strHierarquiaUnidade = $this->obterHierarquiaUnidade($objUnidadeDTO, $arrUnidadesSip);
								if (strlen(trim($strHierarquiaUnidade)) == 0) {
	
									$strHierarquiaUnidade = $objUnidadeDTO->getStrDescricao();
								}
								$unidadeOperacao = substr($strHierarquiaUnidade . '/' . $objUnidadeDTO->getStrDescricaoOrgao(), 0, 297);
								if (strlen($strHierarquiaUnidade . '/' . $objUnidadeDTO->getStrDescricaoOrgao()) > 297) {
										
									$unidadeOperacao .= '...';
								}	
								$unidadesOperacao[$idUnidadeOperacao] = $unidadeOperacao;
								array_push($unidadesOperacaoId, $idUnidadeOperacao);
							}
						}
					}
					$unidadeOperacao = $unidadeOperacao;
					$operacao = $strNomeOperacao;
	
					$itemHistorico->setCriadoEm($dataHoraOperacao);
					$itemHistorico->setUnidade(Encoding::utf8ToIso($unidadeOperacao));
					$itemHistorico->setOperacao(Encoding::utf8ToIso($operacao));
					$documento->addHistoricoDocumento($itemHistorico);
	
				}
	
				unset($arrAtividades);
	
				$body->addDocumento($documento);
				
				$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
				$objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($protocoloMonitorado['id_pacote']);
				$objPacoteDTO->setStrStaIntegracao($protocoloMonitorado['sta_integracao']);
	
				$objPacoteDTO->setStrArquivoMetadados(json_encode($documento));
				$objPacoteDTO->setNumTentativasEnvio($protocoloMonitorado['num_tentativas']);
				if ($protocoloMonitorado['dth_metadados'] == NULL) {
	
					$objPacoteDTO->setDthDataMetadados(date('d/m/Y H:i:s'));
				}
				$retornoAtualizacao = $objPacoteRN->alterar($objPacoteDTO);
				if($retornoAtualizacao==-1) {
					continue;
					
				}
				if($arrObjPacotesEnviados==null) {
						$arrObjPacotesEnviados = array();
				}
				array_push($arrObjPacotesEnviados, $objPacoteDTO);
				
				if ($quantidadeDocumentos == $quantidadeMaximaDocumentos) {
							
					try{
						$arrObjEnviarListaDocumentosPI = array();
						$arrObjEnviarListaDocumentosPI[0] = $body;
						$arrObjEnviarListaDocumentosPI[1] = $arrObjPacotesEnviados;
						$arrObjEnviarListaDocumentosPI[2] = $arrObjProtocoloEnviados;
						$arrObjEnviarListaDocumentosPI[3] = $strInicioPublicacao;		
						$arrObjEnviarListaDocumentosPI[4] = $conexaoCliente;				
						$this->enviarListaDocumentosPI($arrObjEnviarListaDocumentosPI);
						
					
					}
					catch(Exception $e) {
						error_log($e);
					}
					$body = new ListaDocumentoPiDTO();
	
					unset($arrObjPacotesEnviados);
					$arrObjPacotesEnviados = array();
					$arrObjProtocoloEnviados = array();
					$quantidadeDocumentos = 0;
	
				}
				$quantidadeDocumentos++;
				
			}
			if ($quantidadeDocumentos < $quantidadeMaximaDocumentos) {
				$documentos = $body->getDocumentos();
				
				if (sizeof($documentos) > 0) {
					try{
						$arrObjEnviarListaDocumentosPI = array();
						$arrObjEnviarListaDocumentosPI[0] = $body;
						$arrObjEnviarListaDocumentosPI[1] = $arrObjPacotesEnviados;
						$arrObjEnviarListaDocumentosPI[2] = $arrObjProtocoloEnviados;
						$arrObjEnviarListaDocumentosPI[3] = $strInicioPublicacao;
						$arrObjEnviarListaDocumentosPI[4] = $conexaoCliente;
						$this->enviarListaDocumentosPI($arrObjEnviarListaDocumentosPI);
					}
					catch(Exception $e) {
						error_log($e);
					}
					unset($arrObjPacotesEnviados);
				}
			}
			if (count($arrObjProcessosMonitorados)<$quantidadeMaximaDocumentos) {
				break;
			}
			if ($numTotal>ProtocoloIntegradoParametrosRN::$NUM_MAX_ANDAMENTOS_POR_VEZ) {
				break;
			}
		}

	}
	
	private function gerarNomeInteressadoComCpfEscondido($strNomeInteressado) {
		
		if (preg_match("/[0-9]{3}[.]?[0-9]{3}[.]?[0-9]{3}[-]?[0-9]{2}/", $strNomeInteressado, $matches)) {
			$strCpf = $matches[0];
			$bolCpfValido = $this->validarCPF($strCpf);
			if ($bolCpfValido==true) {
				$strCpf = "***".substr($strCpf, 3, strlen($strCpf)-5);
				if (substr($strCpf, -1, 1)=="-") {
					$strCpf = substr($strCpf, 0, strlen($strCpf)-2)."*-**";
				} else {
					$strCpf = substr($strCpf, 0, strlen($strCpf)-1)."***";
				}
			}
							
			$strNomeInteressado = str_replace($matches[0],$strCpf,$strNomeInteressado);
		}
		
		return $strNomeInteressado;				
	}

	private function validarCPF($cpf = null) {
 
    	// Verifica se um número foi informado
    	if (empty($cpf)) {
        	return false;
    	}
 
    	// Elimina possivel mascara
    	$cpf = ereg_replace('[^0-9]', '', $cpf);
    	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    	// Verifica se o numero de digitos informados é igual a 11
    	if (strlen($cpf) != 11) {
        	return false;
    	} else if ($cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || 
        			$cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
        	// Retorna false caso o CPF seja igual à uma das sequencia invalidas
        	return false;
     	} else {
     	    // Verifica se o CPF é válido
        	for ($t = 9; $t < 11; $t++) {
            	for ($d = 0, $c = 0; $c < $t; $c++) {
                	$d += $cpf{$c} * (($t + 1) - $c);
            	}
            	$d = ((10 * $d) % 11) % 10;
            	if ($cpf{$c} != $d) {
                	return false;
            	}
        	}
        	return true;
    	}
	}

	public function atualizaPacote($objPacoteDTO, $objProtocoloDTO, $resultado, $strInicioPublicacao) {
	    
		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		$erroXML = new DOMDocument("1.0", "UTF-8");
		$erroXML->preserveWhiteSpace = false;
		$erroXML->formatOutput = true;
		
		if($objPacoteDTO!=null) {
			$strStatusStaIntegracao = $objPacoteDTO->getStrStaIntegracao();
			$resultadoDocumento = $erroXML->createElement("ResultadoDocumento");
			if ($resultado == 'Ok!') {
				$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_INTEGRADO);
				$objPacoteDTO->setNumTentativasEnvio($objPacoteDTO->getNumTentativasEnvio() + 1);
			} else if (stripos($resultado, 'NF00') !== false) {
				$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL);
				$objPacoteDTO->setNumTentativasEnvio($objPacoteDTO->getNumTentativasEnvio() + 1);
				$protocoloDocumento = $erroXML->createElement("Protocolo", $objProtocoloDTO->getStrProtocoloFormatadoPesquisa());
				$respostaDocumento = $erroXML->createElement("Resultado", utf8_decode($resultado));
				$resultadoDocumento->appendChild($protocoloDocumento);
				$resultadoDocumento->appendChild($respostaDocumento);
				$erroXML->appendChild($resultadoDocumento);
				$objPacoteDTO->setStrArquivoErro($erroXML->saveXML());
	
			} else {
				$objPacoteDTO->setNumTentativasEnvio($objPacoteDTO->getNumTentativasEnvio() + 1);
				$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA);
				$protocoloDocumento = $erroXML->createElement("Protocolo", $objProtocoloDTO->getStrProtocoloFormatadoPesquisa());
				$respostaDocumento = $erroXML->createElement("Resultado", utf8_decode($resultado));
				$resultadoDocumento->appendChild($protocoloDocumento);
				$resultadoDocumento->appendChild($respostaDocumento);
				$erroXML->appendChild($resultadoDocumento);
				$objPacoteDTO->setStrArquivoErro($erroXML->saveXML());
	
			}
			
			$objPacoteDTO->setDthDataSituacao(date('d/m/Y H:i:s'));
			$objPacoteDTO->setDthDataAgendamentoExecutado($strInicioPublicacao);
			
			if ($strStatusStaIntegracao=='I') {
				$objPacoteDTO->setDthDataMetadados(date('d/m/Y H:i:s'));
			}
			$objPacoteRN->alterar($objPacoteDTO);
					
		}
		
	}

	private function obterHierarquiaUnidade(UnidadeDTO $objUnidadeDTO, $arrUnidadesSip) {

		if (isset($arrUnidadesSip[$objUnidadeDTO->getNumIdUnidade()])) {
			$arrUnidadesSuperiores = $arrUnidadesSip[$objUnidadeDTO->getNumIdUnidade()][UnidadeRN::$POS_UNIDADE_UNIDADES_SUPERIORES];
			$arrUnidadesSuperiores[] = $objUnidadeDTO->getNumIdUnidade();
			$it = 0;
			$arrUnidadesSuperiores = array_reverse($arrUnidadesSuperiores);
			
			foreach ($arrUnidadesSuperiores as $numIdUnidadeSuperior) {
				if ($strHierarquiaUnidade != '') {
					$strHierarquiaUnidade .= '/';
				}
				if ($it > 0) {
					$strHierarquiaUnidade .= $arrUnidadesSip[$numIdUnidadeSuperior][UnidadeRN::$POS_UNIDADE_SIGLA];
				} else {

					$strHierarquiaUnidade .= $arrUnidadesSip[$numIdUnidadeSuperior][UnidadeRN::$POS_UNIDADE_DESCRICAO];
				}
				$it++;
			}
		}
		
		return $strHierarquiaUnidade;
	}

	public function enviarListaDocumentosPIControlado($arrObjEnviarListaDocumentosPI) {
			
		$body = $arrObjEnviarListaDocumentosPI[0];
		$arrObjPacotesEnviados = $arrObjEnviarListaDocumentosPI[1];
		$arrObjProtocolosEnviados = $arrObjEnviarListaDocumentosPI[2];
		$strInicioPublicacao = $arrObjEnviarListaDocumentosPI[3];
		$conexaoCliente = $arrObjEnviarListaDocumentosPI[4];
        
		$ret = $conexaoCliente->enviarListaDocumentosServidor($body);
		$iterador = 0;
		if ($ret instanceof SoapFault) {
			$retorno = new stdClass();
			$retorno->resultadoDocumento = array();

			for ($i = 0; $i < count($arrObjProtocolosEnviados); $i++) {
				$resultado = new stdClass();
				$resultado->resultado = 'SF001 - ' . $ret->getMessage();
				array_push($retorno->resultadoDocumento, $resultado);
			}

			$ret = $retorno;
		}

		if (is_array($ret->resultadoDocumento)) {
			foreach ($ret->resultadoDocumento as $key => $value) {
				$objPacoteDTO = $arrObjPacotesEnviados[$iterador];
				$objProtocoloDTO = $arrObjProtocolosEnviados[$iterador];
				$this->atualizaPacote($objPacoteDTO, $objProtocoloDTO, $value->resultado, $strInicioPublicacao);
				$iterador++;
			}
		} else {
			$objPacoteDTO = $arrObjPacotesEnviados[0];
			$objProtocoloDTO = $arrObjProtocolosEnviados[0];
			$this->atualizaPacote($objPacoteDTO, $objProtocoloDTO, $ret->resultadoDocumento->resultado, $strInicioPublicacao);
		}

	}

	public function notificarPacotesSemEnvio() {
				
		$objConfiguracaoModProtocoloIntegrado = ConfiguracaoModProtocoloIntegrado::getInstance();
		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		$protocoloIntegradoPacoteEnvioDTO = new ProtocoloIntegradoPacoteEnvioDTO();
		$protocoloIntegradoPacoteEnvioDTO->retTodos();
		
		$protocoloIntegradoPacoteEnvioDTO->setNumMaxRegistrosRetorno(1);
		$protocoloIntegradoPacoteEnvioDTO->setOrd('DataSituacao', InfraDTO::$TIPO_ORDENACAO_DESC);

		$arrPacote = $objPacoteRN->listar($protocoloIntegradoPacoteEnvioDTO);
		if (count($arrPacote)>0) {
			$objPacoteIntegradoPacoteEnvio = $arrPacote[0];
			$infraAgendamentoTarefaDTO = new InfraAgendamentoTarefaDTO();
			$infraAgendamentoTarefaDTO->retTodos();
      		$infraAgendamentoTarefaDTO->setBolExclusaoLogica(false);
			$infraAgendamentoTarefaDTO->setStrComando('ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados');
			$arrAgendamentoTarefas = $this->listarAgendamentoTarefa($infraAgendamentoTarefaDTO);
			
			$objTarefaNotificarNovosPacotesNaoSendoGerados = $arrAgendamentoTarefas[0];
			$numDias = $objTarefaNotificarNovosPacotesNaoSendoGerados->getStrParametro();

      		$d = $objPacoteIntegradoPacoteEnvio->getDthDataSituacao();
      		if(is_null($d)) {
				$d = strtotime("2000-01-01 00:00:00");
	 		}
			$numMaxDthSituacao = DateTime::createFromFormat("d/m/Y G:i:s", $d);

			$numAgora = time();
			$diffSegundos = $numAgora - $numMaxDthSituacao->getTimestamp();
			$diffDias = intval($diffSegundos/(60*60*24));
			
			if ($diffDias>=$numDias) {
				$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
				$strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
				$strEmailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
		
				$strMensagem = 'Prezado Administrador de Integração, <br />';
				$strMensagem .= 'Há '.$diffDias.' dias não há envio de informações ao Protocolo Integrado. Favor verificar ';
				$strMensagem .= 'se os parâmetros informados na integração estão corretos, tais como: login e senha para conexão ao webservice, endereço ';
				$strMensagem .= 'de conexão ao webservice e agendamento no SEI do envio das informações.<br /><br /><br />';
				$strMensagem .= 'Obrigado.<br />';
				$strMensagem .= 'Sistema Eletrônico de Informações.';
				
				$strAssunto = '[Plugin SEI-PI] Há '.$diffDias.' dias não há envio de informações ao Protocolo Integrado.';
				InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strEmailSistema, $strEmailAdministrador, null, null, $strAssunto, $strMensagem, 'text/html');
			}
		}
	}


	public function notificarProcessosComFalha() {

		$objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
		$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
		$objPacoteDTO->setNumMaxRegistrosRetorno(1);
		$objPacoteDTO->retTodos();
		$objPacoteDTO->setOrd('DataAgendamentoExecutado', InfraDTO::$TIPO_ORDENACAO_DESC);
		$arrPacotes = $objPacoteRN->listar($objPacoteDTO);
		$dataUltimoEnvio = null;
		if (count($arrPacotes)>0) {
			$pacote = $arrPacotes[0];
			$dataUltimoEnvio = $pacote->getDthDataAgendamentoExecutado();
		}
		
		if ($dataUltimoEnvio!=null) {
			$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
			$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA);
			$objPacoteDTO->setDthDataAgendamentoExecutado($dataUltimoEnvio);
			$objPacoteDTO->retTodos();
			$arrPacotesFalhaInfra = $objPacoteRN->listar($objPacoteDTO);
			
			$objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
			$objPacoteDTO->setStrStaIntegracao(ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL);
			$objPacoteDTO->setDthDataAgendamentoExecutado($dataUltimoEnvio);
			$objPacoteDTO->retTodos();
			$arrPacotesErroNegocial = $objPacoteRN->listar($objPacoteDTO);
			
			$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
	
			if (count($arrPacotesErroNegocial) > 0 || count($arrPacotesFalhaInfra) > 0) {		
				$strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
				$strEmailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
		
				InfraDebug::getInstance()->gravar('Buscando Configuração de Publicação no Protocolo Integrado');
				$objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
				$objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
				$objProtocoloIntegradoParametrosDTO->retDthDataUltimoProcessamento();
				$objRetornoParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
	
				$strMensagem = 'Prezado Administrador de Integração,<br>Alguns processos não puderam ser enviados ao protocolo integrado no último ciclo de integração realizado pelo SEI em ' . $objRetornoParametrosDTO->getDthDataUltimoProcessamento() . ' <br /> <br />';
				$strMensagem .= '<' . count($arrPacotesFalhaInfra) . '>' . 'Processos não enviados por erro de infraestrutura <br />';
				$strMensagem .= '<' . count($arrPacotesErroNegocial) . '>' . 'Processos não enviados por erro negocial <br /> <br />';
				$strMensagem .= 'Favor observar a tabela de monitoramento do SEI para maiores detalhes.  <br />';
				$strMensagem .= 'Obrigado. <br /> <br />';
				$strMensagem .= 'Sistema Eletrônico de Informações';
				$strAssunto = '[Plugin SEI-PI] Processos não integrados no último ciclo ';
				InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strEmailSistema, $strEmailAdministrador, null, null, $strAssunto, $strMensagem, 'text/html');
			} 
		}
	}

	protected function listarAgendamentoTarefaConectado(InfraAgendamentoTarefaDTO $infraAgendamentoTarefaDTO) {
	    
	    try {
	      $objAgendamentoBD = new InfraAgendamentoTarefaBD($this->getObjInfraIBanco());
	      $ret = $objAgendamentoBD->listar($infraAgendamentoTarefaDTO);
	  	
		  return $ret;
	    } catch (Exception $e) {
	      throw new InfraException('Erro listando Tarefas.',$e);
	    }
	    
    }

	protected function listarUnidadesConectado(UnidadeDTO $unidadeDTO) {
	    
	    try {
	      $objUnidadeBD = new UnidadeBD($this->getObjInfraIBanco());
	      $ret = $objUnidadeBD->listar($unidadeDTO);
	  	
		  return $ret;
	    } catch(Exception $e) {
	      throw new InfraException('Erro listando Unidades.',$e);
	    }
	    
    }

}
