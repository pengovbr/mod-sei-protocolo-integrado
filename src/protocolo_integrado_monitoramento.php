<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
ini_set('output_buffering', 'On');

try {

  // require_once dirname(__FILE__).'/../../../SEI.php';
  require_once DIR_SEI_WEB . '/SEI.php';

  session_start();
  SessaoSEI::getInstance()->validarLink();
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  $filtro = $_REQUEST;
  $dtaPeriodoDe = $_REQUEST['filtroTxtPeriodoDe'];
  $dtaPeriodoA = $_REQUEST['filtroTxtPeriodoA'];
  if (!isset($_REQUEST['filtroTxtPeriodoGeracaoDe'])) {
    $dtaPeriodoGeracaoDe = date("d/m/Y", time() - 60 * 60 * 24 * 3);
    $filtro['filtroTxtPeriodoGeracaoDe'] = $dtaPeriodoGeracaoDe;
  } else {
    $dtaPeriodoGeracaoDe = $_REQUEST['filtroTxtPeriodoGeracaoDe'];
  }

  $dtaPeriodoGeracaoA = $_REQUEST['filtroTxtPeriodoGeracaoA'];
  $filtroProtocolo = $_REQUEST['filtroCodProtocolo'];
  $filtroStaIntegracao = $_REQUEST['filtroSelSitucaoIntegracao'];
  $filtroUnidadeGeradora = $_REQUEST['filtroSelUnidade'];
  $filtroIncluirUnidadesFilhas = $_REQUEST['filtroIncluirUnidadesFilhas'];
  if ($filtroIncluirUnidadesFilhas == 'on') {
    $filtroIncluirUnidadesFilhas = "checked='checked'";
  } else {
    $filtroIncluirUnidadesFilhas = "";
  }

  if (isset($_POST['sbmPesquisar']) || isset($_POST['hdnInfraPaginaAtual']) == false || $_POST['hdnInfraPaginaAtual'] == '') {
    $_POST['hdnInfraPaginaAtual'] = '0';
  }

  switch ($_GET['acao']) {

    case 'md_pi_forcar_reenvio':
      $arrStrItensSelecionados = explode(',', $_REQUEST['hdnForcarReenvioItensSelecionados']);
      $arrStrItensSelecionados = array_unique($arrStrItensSelecionados);
      $objProtocoloIntegradoMonitoramentoProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
      $objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
      $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
      $objProtocoloIntegradoParametrosDTO->retTodos();
      $objRetornoProtocoloIntegradoParametros = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
      $filtro = array();
      $filtro['pacotes'] = array();

      for ($i = 0; $i < count($arrStrItensSelecionados); $i++) {
        array_push($filtro['pacotes'], $arrStrItensSelecionados[$i]);
        PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
      }
      $arrParam = array();
      $arrParam[0] = $objRetornoProtocoloIntegradoParametros;
      $arrParam[1] = $filtro;

      $objProtocoloIntegradoMonitoramentoProcessosRN->publicarProcessosMonitorados($arrParam);
      $parametros = '';

      if (isset($_REQUEST['filtroCodProtocolo']) && $_REQUEST['filtroCodProtocolo'] != '') {
        $parametros .= '&filtroCodProtocolo=' . $_REQUEST['filtroCodProtocolo'];
      }
      if (isset($_REQUEST['filtroSelSitucaoIntegracao']) && $_REQUEST['filtroSelSitucaoIntegracao'] != '') {
        $parametros .= '&filtroSelSitucaoIntegracao=' . $_REQUEST['filtroSelSitucaoIntegracao'];
      }
      if (isset($_REQUEST['filtroSelUnidade']) && $_REQUEST['filtroSelUnidade'] != '') {
        $parametros .= '&filtroSelUnidade=' . $_REQUEST['filtroSelUnidade'];
      }
      if (isset($_REQUEST['filtroIncluirUnidadesFilhas']) && $_REQUEST['filtroIncluirUnidadesFilhas'] != '') {
        $parametros .= '&filtroIncluirUnidadesFilhas=' . $_REQUEST['filtroIncluirUnidadesFilhas'];
      }
      if (isset($_REQUEST['filtroTxtPeriodoGeracaoDe']) && $_REQUEST['filtroTxtPeriodoGeracaoDe'] != '') {
        $parametros .= '&filtroTxtPeriodoGeracaoDe=' . $_REQUEST['filtroTxtPeriodoGeracaoDe'];
      }
      if (isset($_REQUEST['filtroTxtPeriodoGeracaoA']) && $_REQUEST['filtroTxtPeriodoGeracaoA'] != '') {
        $parametros .= '&filtroTxtPeriodoGeracaoA=' . $_REQUEST['filtroTxtPeriodoGeracaoA'];
      }
      if (isset($_REQUEST['numRegistosPaginaSuperior'])  && $_REQUEST['numRegistosPaginaSuperior'] != '') {
        $parametros .= '&numRegistosPaginaSuperior=' . $_REQUEST['numRegistosPaginaSuperior'];
      }

      header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . $parametros));
        die;

    case 'md_pi_monitoramento':
      $strTitulo = 'Monitoramento de Integração';
        break;

    default:
        throw new InfraException("Módulo Protocolo Integrado: Ação '" . $_GET['acao'] . "' não reconhecida.");
  }
  $objProtocoloIntegradoMonitoramentoProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();

  if (isset($_REQUEST['numRegistosPaginaSuperior']) && $_REQUEST['numRegistosPaginaSuperior'] != '') {
    $filtro['filtroNumQuantidadeRegistrosPorPagina'] = $_REQUEST['numRegistosPaginaSuperior'];
  }

  $filtro['paginacao'] = true;
  $arrObjPacotesMonitoradosDTO = $objProtocoloIntegradoMonitoramentoProcessosRN->listarProcessosMonitorados($filtro);
  $strItensSelSituacoes = SeiINT::montarSelectArray(null, '', $filtroStaIntegracao, $objProtocoloIntegradoMonitoramentoProcessosRN->getSituacoesIntegracao());
  $strItensSelUnidades = SeiINT::montarSelectArray(null, '', $filtroUnidadeGeradora, $objProtocoloIntegradoMonitoramentoProcessosRN->getUnidadesGeradoras());

  $objConfiguracaoModProtocoloIntegrado = ConfiguracaoModProtocoloIntegrado::getInstance();
  $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
  $objProtocoloIntegradoParametrosDTO->retNumIdProtocoloIntegradoParametros();
  $objProtocoloIntegradoParametrosDTO->retDthDataUltimoProcessamento();
  $objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
  $objParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);

  $arrComandos = array();

  $bolAcaoForcarReenvio = SessaoSEI::getInstance()->verificarPermissao('md_pi_forcar_reenvio');

  $numRegistros = count($arrObjPacotesMonitoradosDTO);
  $objPacoteEnvioDTO = new ProtocoloIntegradoPacoteEnvioDTO();
  $objPacoteEnvioDTO->retNumIdProtocolo();
  $objPacoteEnvioDTO->retStrStaIntegracao();
  $objPacoteEnvioDTO->retDthDataSituacao();
  $objPacoteEnvioDTO->retDthDataMetadados();
  $objPacoteEnvioDTO->retNumTentativasEnvio();
  $objPacoteEnvioDTO->retStrProtocoloFormatado();
  $objPacoteEnvioDTO->retNumIdProtocoloIntegradoPacoteEnvio();

  if ($numRegistros > 0) {
    $bolCheck = false;
    if ($_GET['acao'] == 'md_pi_monitoramento') {
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pi_monitoramento');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pi_monitoramento');
      $bolAcaoImprimir = false;
      //$bolAcaoGerarPlanilha = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
      $bolColunaArquivo = SessaoInfra::getInstance()->verificarPermissao('md_pi_acesso_arquivo_metadados');
    } else {
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pi_monitoramento');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pi_monitoramento');
      $bolAcaoImprimir = true;
    }

    if ($bolAcaoExcluir) {
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tarefa_excluir&acao_origem=' . $_GET['acao']);
    }

    $strResultado = '';
    $strSumarioTabela = 'Tabela de Processos.';
    $strCaptionTabela = 'Pacotes';



    $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
    $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      // $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="10%" align="center">' . PaginaSEI::getInstance()->getThCheck('Selecionar', 'ForcarReenvio') . '</th>';
    $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO, 'Data do Metadado', 'DataMetadados', $arrObjPacotesMonitoradosDTO) . '</th>' . "\n";


    $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO, 'Processo', 'ProtocoloFormatado', $arrObjPacotesMonitoradosDTO) . '</th>' . "\n";
    $strResultado .= '<th class="infraTh">Situação</th>' . "\n";
    $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO, 'Data da Situação', 'DataSituacao', $arrObjPacotesMonitoradosDTO) . '</th>' . "\n";
    if ($bolColunaArquivo) {
      $strResultado .= '<th class="infraTh">Ações</th>' . "\n";
    }
    $strResultado .= '</tr>' . "\n";
    $numRegistrosRecebidos = 0;
    $numRegistrosGerados = 0;

    $numCheckRecebidos = 0;
    $numCheckGerados = 0;

    $strRecebidos = '';
    $strGerados = '';
    $strResultadoRecebidos = '';
    $strResultadoGerados = '';
    $strResultadoDetalhado = '';
    $arrRetIconeIntegracao = array();


    $i = 0;
    $maxPacotesReenvio = 0;
    $indicePacoteComFalha = 0;
    foreach ($arrObjPacotesMonitoradosDTO as $key => $pacote) {


      $strImagemStatus = '';
      $strCssProcesso = '';
      $strLinkUsuarioAtribuicao = '&nbsp;';
      $bolFlagGerado = false;
      $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';

      $strResultado .= $strCssTr;

      $strResultado .= '<td align="center" >';

      $strResultado .= PaginaSEI::getInstance()->getTrCheck($indicePacoteComFalha, $pacote['id_pacote'], $pacote['protocolo']->getStrProtocoloFormatado(), 'N', 'ForcarReenvio');
      $maxPacotesReenvio++;
      $indicePacoteComFalha++;

      if ($pacote['dth_metadados'] == '') { $pacote['dth_metadados'] = '-';
      }
      $strResultado .= '<td width="7%" align="center" style="font-size:1em"> ' . $pacote['dth_metadados'] . ' </td>';

      $strResultado .= '<td width="10%" align="center" style="font-size:.9em"><a onclick="abrirProcesso(\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_procedimento=' . $pacote['protocolo']->getDblIdProtocolo())) . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '">' . $pacote['protocolo']->getStrProtocoloFormatado() . '</a></td>';

      switch (trim($pacote['sta_integracao'])) {

        case ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO:
          $strResultado .= '<td width="10%" style="font-size:1em"> Não Integrado </td>';
            break;
        case ProtocoloIntegradoPacoteEnvioRN::$STA_INTEGRADO:
          $strResultado .= '<td width="10%" style="font-size:1em"> Integrado </td>';
            break;
        case ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA:
          $strResultado .= '<td width="10%" style="font-size:1em"> Falha Infra </td>';
            break;
        case ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL:
          $strResultado .= '<td width="10%" style="font-size:1em"> Erro Negocial </td>';
            break;

        default:
          $strResultado .= '<td width="10%" style="font-size:1em"> - </td>';
            break;
      }

      if ($pacote['dth_situacao'] == '') { $pacote['dth_situacao'] = '-';
      }
      $strResultado .= '<td width="7%" align="center" style="font-size:1em">' . $pacote['dth_situacao'] . '</td>';

      if ($bolColunaArquivo) {

        if ($pacote['sta_integracao'] != ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO) {

          $strResultado .=   '<td width="3%" align="center" ><a  target="_blank"  id="linkArquivoMetadados" href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_visualizar_metadados&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_pacote=' . $pacote['id_pacote'])) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" title="Visualizar os Metadados Gerados" alt="Visualizar os Metadados Gerados" class="infraImg" /></a>&nbsp;';
          if ($pacote['sta_integracao'] == ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA || $pacote['sta_integracao'] == ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL) {

            $strResultado .= ' <a TARGET="_blank" href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_visualizar_erro_envio_metadados&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_pacote=' . $pacote['id_pacote'])) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '">' . '<img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/menos.gif" title="Visualizar XML de Erro" alt="Visualizar XML de Erro" class="infraImg" /></a>';
          }
          $strResultado .= '</td>';
        } else if ($bolColunaArquivo) {

          $strResultado .= '<td align="center"></td>';
        }
      }
      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $pacote['protocolo']->getDblIdProtocolo());

      $i++;
    }
    $strResultado .= '</table>';
  }

  if ($bolAcaoForcarReenvio && $maxPacotesReenvio > 0) {
    $arrComandos[] = '<input type="button" onclick="forcarReenvio()" name="btnForcar" id="btnForcar" value="Forçar Reenvio" class="infraButton" />';
    $strLinkForcarReenvio = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_forcar_reenvio&acao_origem=' . $_GET['acao']);
  }

  $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" name="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
} catch (Exception $e) {
  PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

?>
a.disabled {
pointer-events: none;
cursor: default;
}
a.enabled {Fechar
cursor: default;
}
a:hover {
text-decoration: underline;
}
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
  function inicializar() {

    infraEfeitoTabelas();
    infraExibirMenuSistemaEsquema();
    infraSelecaoLimpar('ForcarReenvio');

    if (document.getElementById("divInfraAreaPaginacaoSuperior") != null && document.getElementById("selInfraPaginacaoSuperior") != null) {

      var label = document.createElement("Label");
      label.innerHTML = "Página";
      label.id = "lblInfraPaginacaoSuperior";
      label.style = 'padding:5px';
      document.getElementById("divInfraAreaPaginacaoSuperior").insertBefore(label, document.getElementById("selInfraPaginacaoSuperior"));
    }
    if (document.getElementById("divInfraAreaPaginacaoInferior") != null && document.getElementById("selInfraPaginacaoInferior") != null) {

      var label = document.createElement("Label");
      label.innerHTML = "Página";
      label.style = 'padding:5px';
      label.id = "lblInfraPaginacaoInferior";
      document.getElementById("divInfraAreaPaginacaoInferior").insertBefore(label, document.getElementById("selInfraPaginacaoInferior"));
    }
  }

  function abrirProcesso(link) {

    window.open(link);
    //document.getElementById('frmMonitoramentoIntegracaoProcessosLista').action = link;
    //document.getElementById('frmMonitoramentoIntegracaoProcessosLista').submit();
    //infraOcultarMenuSistemaEsquema();

  }

  function replicaValorNumeroRegistrosPorPagina(objValor) {

    if (objValor.name == 'numRegistosPaginaSuperior') {
      document.getElementById('numRegistosPaginaInferior').value = objValor.value;
    } else {

      document.getElementById('numRegistosPaginaSuperior').value = objValor.value;

    }

  }
  <? if ($bolAcaoForcarReenvio) { ?>

    function forcarReenvio() {

      document.getElementById('frmMonitoramentoIntegracaoProcessosLista').action = '<?= $strLinkForcarReenvio ?>';
      document.getElementById('frmMonitoramentoIntegracaoProcessosLista').submit();
    }
  <? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMonitoramentoIntegracaoProcessosLista" method="post" action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">

  <input type="hidden" id="chkPacoteAcao" name="chkPacoteAcao" />

  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->abrirAreaDados('15em');
  ?>


  <div id="divfiltroCodProtocolo" class="infraAreaDados d-flex flex-column flex-md-row mb-2" style="">
    <div class="col-12 col-md-2 mx-0 px-0 pt-1">
      <label id="filtroCodProtocoloLabel" for="filtroCodProtocolo" accesskey="" class="infraLabelOpcional">Nº Processo:</label>
    </div>
    <div class="col-7 col-md-4 pl-0 pl-md-1 pt-1 media">
      <input type="text" id="filtroCodProtocolo" name="filtroCodProtocolo" maxlength="50" class="infraText w-100 w-md-75" onkeypress="return infraLimitarTexto(this,event,50);" value="<?= PaginaSEI::tratarHTML($filtro['filtroCodProtocolo']) ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
    </div>
  </div>


  <div id="divSituacaoPesquisa" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
    <div class="col-12 col-md-2 mx-0 px-0 pt-1">
      <label id="lblSituacaoPesquisa" for="selSituacaoPesquisa" accesskey="" class="infraLabelOpcional">Situação:</label>
    </div>
    <div class="col-7 col-md-4 pl-0 pl-md-1 pt-1 media">
      <select id="selSituacaoPesquisa" name="selSituacaoPesquisa" class="infraSelect w-100 w-md-75" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
        <?= $strItensSelSituacoes ?>
      </select>
    </div>
  </div>


  <div id="divPeriodoDe" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
    <div class="col-12 col-md-2 mx-0 px-0 pt-1">
      <label id="lblPeriodoDe" for="filtroTxtPeriodoDe" class="infraLabelOpcional">Data de envio: </label>
    </div>

    <div class="d-flex flex-column flex-md-row col-12 col-md-7 pl-0 pl-md-1 media">
      <div class="col-12 col-md-8 media pl-0 pt-1">
        <div class="col-6 pl-0 media">
          <input type="text" id="filtroTxtPeriodoDe" name="filtroTxtPeriodoDe" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoDe); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <img id="imgDataInicio" src="<?= PaginaSEI::getInstance()->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoDe',this);" alt="Selecionar Data Inicial" title="Selecionar Data Inicial" class="infraImg mx-1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <label id="lblDataE" accesskey="" class="infraLabelOpcional mx-0 pt-1 pl-md-2">a</label>
        </div>
        <div class="col-6 pl-0 pl-md-2 media">
          <input type="text" id="filtroTxtPeriodoA" name="filtroTxtPeriodoA" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoA); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <img id="imgDataFim" src="<?= PaginaSEI::getInstance()->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoA',this);" alt="Selecionar Data Final" title="Selecionar Data Final" class="infraImg mx-1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
        </div>
      </div>
    </div>
  </div>


  <div id="divPeriodoGeracaoDe" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
    <div class="col-12 col-md-2 mx-0 px-0 pt-1">
      <label id="lblPeriodoGeracaoDe" for="txtPeriodoGeracaoDe" class="infraLabelOpcional">Data de geração do processo:</label>
    </div>

    <div class="d-flex flex-column flex-md-row col-12 col-md-7 pl-0 pl-md-1 media">
      <div class="col-12 col-md-8 media pl-0 pt-1">
        <div class="col-6 pl-0 media">
          <input type="text" id="filtroTxtPeriodoGeracaoDe" name="filtroTxtPeriodoGeracaoDe" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoGeracaoDe); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <img id="imgDataInicio" src="<?= PaginaSEI::getInstance()->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoGeracaoDe',this);" alt="Selecionar Data Inicial" title="Selecionar Data Inicial" class="infraImg mx-1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <label id="lblDataE" accesskey="" class="infraLabelOpcional mx-0 pt-1 pl-md-2">a</label>
        </div>
        <div class="col-6 pl-0 pl-md-2 media">
          <input type="text" id="filtroTxtPeriodoGeracaoA" name="filtroTxtPeriodoGeracaoA" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoGeracaoA); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
          <img id="imgDataFim" src="<?= PaginaSEI::getInstance()->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoGeracaoA',this);" alt="Selecionar Data Final" title="Selecionar Data Final" class="infraImg mx-1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
        </div>
      </div>
    </div>
  </div>


  <div id="divfiltroSelUnidade" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
    <div class="col-12 col-md-2 mx-0 px-0 pt-1">
      <label id="lblfiltroSelUnidade" for="filtroSelUnidade" accesskey="" class="infraLabelOpcional">Unidade Geradora:</label>
    </div>
    <select id="filtroSelUnidade" name="filtroSelUnidade" class="infraSelect w-100 w-md-100" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
      <?= $strItensSelUnidades ?>
    </select>
  </div>

  <div id="divfiltroIncluirUnidadesFilhas" class="infraDivCheckbox col-10 col-md-3 pl-0 pl-md-1 pt-2 media my-auto">
    <input type="checkbox" id="filtroIncluirUnidadesFilhas" name="filtroIncluirUnidadesFilhas" class="infraCheckbox" <?= $filtroIncluirUnidadesFilhas ?> tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
    <label id="lblfiltroIncluirUnidadesFilhas" for="filtroIncluirUnidadesFilhas" accesskey="">Incluir Unidades Filhas</label>
  </div>
  
  <?
  if ($numRegistros > 0) {
    ?>
    <div style="float:right;padding:0 1.5em">
      <label style="">Quantidade de registros por página</label>
      <input style="background-color: #FFF;border: 1px solid #333;" size="5" type='number' value="<?= $_REQUEST['numRegistosPaginaSuperior'] ?>" onkeyup='replicaValorNumeroRegistrosPorPagina(this)' id='numRegistosPaginaSuperior' name='numRegistosPaginaSuperior'>
      <input size="10" value="OK" class="infraButton" style="vertical-align:top" type="submit">
    </div>
    <?
  }
  ?>

  <?
  // PaginaSEI::getInstance()->fecharAreaDados();
  PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
  ?>
  <?
  if ($numRegistros > 0) {
    ?>
    <div style="float:right;padding:0 1.5em">
      <label style="">Quantidade de registros por página</label>
      <input style="background-color: #FFF;border: 1px solid #333;" size="5" type='number' id='numRegistosPaginaInferior' value="<?= $_REQUEST['numRegistosPaginaSuperior'] ?>" name='numRegistosPaginaInferior' onkeyup='replicaValorNumeroRegistrosPorPagina(this)'>
      <input size="10" value="OK" class="infraButton" style="vertical-align:top" type="submit">
    </div>
    <?
  }
  ?>
  <?
  PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);

  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>