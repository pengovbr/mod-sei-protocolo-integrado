<?php

require_once DIR_SEI_WEB . '/SEI.php';

session_start();

InfraDebug::getInstance()->setBolLigado(false);
InfraDebug::getInstance()->setBolDebugInfra(true);
InfraDebug::getInstance()->limpar();

$objPaginaSEI = PaginaSEI::getInstance();
$objSessaoSEI = SessaoSEI::getInstance();

try {

  $objSessaoSEI->validarLink();
  $objSessaoSEI->validarPermissao($_GET['acao']);

  $filtro = $_REQUEST;
  $dtaPeriodoDe = $_REQUEST['filtroTxtPeriodoDe'];
  $dtaPeriodoA = $_REQUEST['filtroTxtPeriodoA'];
  if (isset($_REQUEST['filtroTxtPeriodoGeracaoDe'])) {
    $dtaPeriodoGeracaoDe = $_REQUEST['filtroTxtPeriodoGeracaoDe'];
  }

  $dtaPeriodoGeracaoA = $_REQUEST['filtroTxtPeriodoGeracaoA'];
  $filtroProtocolo = $_REQUEST['filtroCodProtocolo'];
  $filtroStaIntegracao = $_REQUEST['filtroSelSituacaoIntegracao'];
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

  $objProtocoloIntegradoMonitoramentoProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
  $objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();

  switch ($_GET['acao']) {
    case 'md_pi_forcar_reenvio':
      $arrStrItensSelecionados = explode(',', $_REQUEST['hdnInfraItensSelecionados']);
      $arrStrItensSelecionados = array_unique($arrStrItensSelecionados);
      $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
      $objProtocoloIntegradoParametrosDTO->retTodos();
      $objRetornoProtocoloIntegradoParametros = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
      $filtro = array();
      $filtro['pacotes'] = array();

      for ($i = 0; $i < count($arrStrItensSelecionados); $i++) {
        if (!empty(trim($arrStrItensSelecionados[$i]))) {
          array_push($filtro['pacotes'], $arrStrItensSelecionados[$i]);
        }
      }

      if (isset($_REQUEST['filtroCodProtocolo']) && $_REQUEST['filtroCodProtocolo'] != '') {
        $parametros .= '&filtroCodProtocolo=' . $_REQUEST['filtroCodProtocolo'];
      }
      if (isset($_REQUEST['filtroSelSituacaoIntegracao']) && $_REQUEST['filtroSelSituacaoIntegracao'] != '') {
        $parametros .= '&filtroSelSituacaoIntegracao=' . $_REQUEST['filtroSelSituacaoIntegracao'];
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

      if (empty($filtro['pacotes'])) {
        $objPaginaSEI->adicionarMensagem('Selecione ao menos um processo para reenvio.', InfraPagina::$TIPO_MSG_AVISO);
        header('Location: ' . $objSessaoSEI->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . $parametros));
        die;
      }

      $arrParam = array();
      $arrParam[0] = $objRetornoProtocoloIntegradoParametros;
      $arrParam[1] = $filtro;

      $objProtocoloIntegradoMonitoramentoProcessosRN->publicarProcessosMonitorados($arrParam);
      $parametros = '';

      $objPaginaSEI->adicionarMensagem('Processo(s) reenviado(s) para protocolo integrado com sucesso.', 5);
      header('Location: ' . $objSessaoSEI->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . $parametros));
      die;
    case 'md_pi_monitoramento':
      $strTitulo = 'Monitoramento de Integração';
      break;
    default:
      throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
  }

  if (isset($_REQUEST['numRegistosPaginaSuperior']) && $_REQUEST['numRegistosPaginaSuperior'] != '') {
    $filtro['filtroNumQuantidadeRegistrosPorPagina'] = $_REQUEST['numRegistosPaginaSuperior'];
  }

  $filtro['paginacao'] = true;

  $objPacoteEnvioDTO = new ProtocoloIntegradoPacoteEnvioDTO();
  $objPacoteEnvioDTO->retNumIdProtocolo();
  $objPacoteEnvioDTO->retStrStaIntegracao();
  $objPacoteEnvioDTO->retDthDataSituacao();
  $objPacoteEnvioDTO->retDthDataMetadados();
  $objPacoteEnvioDTO->retNumTentativasEnvio();
  $objPacoteEnvioDTO->retStrProtocoloFormatado();
  $objPacoteEnvioDTO->retNumIdProtocoloIntegradoPacoteEnvio();

  $objPaginaSEI->prepararOrdenacao($objPacoteEnvioDTO, 'DataMetadados', InfraDTO::$TIPO_ORDENACAO_DESC);
  $objPaginaSEI->prepararPaginacao($objPacoteEnvioDTO);
  $arrObjPacotesMonitoradosDTO = $objProtocoloIntegradoMonitoramentoProcessosRN->listarProcessosMonitorados($objPacoteEnvioDTO, $filtro);
  $strItensSelSituacoes = InfraINT::montarSelectArray(null, '', $filtroStaIntegracao, $objProtocoloIntegradoMonitoramentoProcessosRN->getSituacoesIntegracao());
  $strItensSelUnidades = InfraINT::montarSelectArray(null, '', $filtroUnidadeGeradora, $objProtocoloIntegradoMonitoramentoProcessosRN->getUnidadesGeradoras());

  $objConfiguracaoModProtocoloIntegrado = ConfiguracaoModProtocoloIntegrado::getInstance();
  $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
  $objProtocoloIntegradoParametrosDTO->retNumIdProtocoloIntegradoParametros();
  $objProtocoloIntegradoParametrosDTO->retDthDataUltimoProcessamento();
  $objParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);

  $arrComandos = array();

  $bolAcaoForcarReenvio = $objSessaoSEI->verificarPermissao('md_pi_forcar_reenvio');

  $objPaginaSEI->processarPaginacao($objPacoteEnvioDTO);
  $numRegistros = count($arrObjPacotesMonitoradosDTO);

  if ($numRegistros > 0) {
    $bolCheck = false;
    if ($_GET['acao'] == 'md_pi_monitoramento') {
      $bolAcaoReativar = false;
      $bolAcaoConsultar = $objSessaoSEI->verificarPermissao('md_pi_monitoramento');
      $bolAcaoAlterar = $objSessaoSEI->verificarPermissao('md_pi_monitoramento');
      $bolAcaoImprimir = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
      $bolColunaArquivo = SessaoInfra::getInstance()->verificarPermissao('md_pi_acesso_arquivo_metadados');
    } else {
      $bolAcaoReativar = false;
      $bolAcaoConsultar = $objSessaoSEI->verificarPermissao('md_pi_monitoramento');
      $bolAcaoAlterar = $objSessaoSEI->verificarPermissao('md_pi_monitoramento');
      $bolAcaoImprimir = true;
    }

    if ($bolAcaoExcluir) {
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = $objSessaoSEI->assinarLink('controlador.php?acao=tarefa_excluir&acao_origem=' . $_GET['acao']);
    }

    $strSumarioTabela = 'Tabela de Processos.';
    $strCaptionTabela = 'Pacotes';

    $strResultado = '';
    $strResultado .= '<table width="99%" id="tblMonitoramentoIntegracaoProcessos" class="infraTable" summary="' . $strSumarioTabela . '">';
    $strResultado .= '<caption class="infraCaption">' . $objPaginaSEI->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
    $strResultado .= "<thead>";
    $strResultado .= '<tr>';
    $strResultado .= '<th class="infraTh" width="1%">' . $objPaginaSEI->getThCheck() . '</th>';
    $strResultado .= '<th class="infraTh">' . $objPaginaSEI->getThOrdenacao($objPacoteEnvioDTO, 'Data do Metadado', 'DataMetadados', $arrObjPacotesMonitoradosDTO) . '</th>';
    $strResultado .= '<th class="infraTh">' . $objPaginaSEI->getThOrdenacao($objPacoteEnvioDTO, 'Processo', 'ProtocoloFormatado', $arrObjPacotesMonitoradosDTO) . '</th>';
    $strResultado .= '<th class="infraTh">Situação</th>';
    $strResultado .= '<th class="infraTh">' . $objPaginaSEI->getThOrdenacao($objPacoteEnvioDTO, 'Data da Situação', 'DataSituacao', $arrObjPacotesMonitoradosDTO) . '</th>';
    $strResultado .= $bolColunaArquivo ? '<th class="infraTh" width="8%">Ações</th>' : '';
    $strResultado .= '</tr>';

    $strResultado .= "</thead><tbody>";

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

    foreach ($arrObjPacotesMonitoradosDTO as $key => $pacote) {

      $strImagemStatus = '';
      $strCssProcesso = '';
      $strLinkUsuarioAtribuicao = '&nbsp;';
      $bolFlagGerado = false;
      $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';

      $strResultado .= $strCssTr;

      $strResultado .= '<td align="center" >';

      $strResultado .= $objPaginaSEI->getTrCheck($key, $pacote->getNumIdProtocoloIntegradoPacoteEnvio(), '');

      $strResultado .= '<td align="center"> ' . $pacote->getDthDataMetadados() ?: '-' . ' </td>';
      $strResultado .= '<td align="center"><a onclick="abrirProcesso(\'' . $objPaginaSEI->formatarXHTML($objSessaoSEI->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_procedimento=' . $pacote->getNumIdProtocolo())) . '\');" tabindex="' . $objPaginaSEI->getProxTabTabela() . '">' . $pacote->getStrProtocoloFormatado() . '</a></td>';
      $strResultado .= '<td align="center">' . $objProtocoloIntegradoMonitoramentoProcessosRN->tratarSituacao(trim($pacote->getStrStaIntegracao())) . '</td>';
      $strResultado .= '<td align="center">' . $pacote->getDthDataSituacao() ?: '-' . '</td>';

      if ($bolColunaArquivo) {
        if ($pacote->getStrStaIntegracao() != ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO) {
          $strResultado .=   '<td width="6%" align="center" >';

          $strResultado .=   '<a  target="_blank"  id="linkArquivoMetadados" href="' . $objPaginaSEI->formatarXHTML($objSessaoSEI->assinarLink('controlador.php?acao=md_pi_visualizar_metadados&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_pacote=' . $pacote->getNumIdProtocoloIntegradoPacoteEnvio())) . '" tabindex="' . $objPaginaSEI->getProxTabTabela() . '"><img src="' . ProtocoloIntegradoIntegracao::getDiretorio() . '/imagens/page_green.png" title="Visualizar os Metadados Gerados" alt="Visualizar os Metadados Gerados" class="infraImg" /></a>&nbsp;';

          if ($pacote->getStrStaIntegracao() == ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA || $pacote->getStrStaIntegracao() == ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL) {
            $strResultado .= ' <a TARGET="_blank" href="' . $objPaginaSEI->formatarXHTML($objSessaoSEI->assinarLink('controlador.php?acao=md_pi_visualizar_erro_envio_metadados&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_pacote=' . $pacote->getNumIdProtocoloIntegradoPacoteEnvio())) . '" tabindex="' . $objPaginaSEI->getProxTabTabela() . '">' . '<img src="' . ProtocoloIntegradoIntegracao::getDiretorio() . '/imagens/page_red.png" title="Visualizar XML de Erro" alt="Visualizar XML de Erro" class="infraImg" /></a>';
          }

          $strResultado .= '</td>';
        } else if ($bolColunaArquivo) {
          $strResultado .= '<td align="center"></td>';
        }
      }
      $strResultado .= $objPaginaSEI->getAcaoTransportarItem($key, $pacote->getNumIdProtocolo());
    }
    $strResultado .= '</tbody></table>';
  }

  if ($bolAcaoForcarReenvio && $numRegistros > 0) {
    $arrComandos[] = '<input type="button" onclick="forcarReenvio()" name="btnForcar" id="btnForcar" value="Forçar Reenvio" class="infraButton" />';
    $strLinkForcarReenvio = $objSessaoSEI->assinarLink('controlador.php?acao=md_pi_forcar_reenvio&acao_origem=' . $_GET['acao']);
  }

  $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" name="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . $objPaginaSEI->formatarXHTML($objSessaoSEI->assinarLink('controlador.php?acao=' . $objPaginaSEI->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
} catch (Exception $e) {
  $objPaginaSEI->processarExcecao($e);
}

$objPaginaSEI->montarDocType();
$objPaginaSEI->abrirHtml();
$objPaginaSEI->abrirHead();
$objPaginaSEI->montarMeta();
$objPaginaSEI->montarTitle($objPaginaSEI->getStrNomeSistema() . ' - ' . $strTitulo);
$objPaginaSEI->montarStyle();

?>
<style type="text/css">
  a.disabled {
    pointer-events: none;
    cursor: default;
  }

  a.enabled {
    cursor: default;
  }

  a:hover {
    text-decoration: underline;
  }

  /* Personalize o estilo da paginação */
  .dataTables_paginate {
    margin: 10px;
    text-align: end;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin-right: 5px;
    border: 1px solid #ccc;
    background-color: #f2f2f2;
    color: #333;
    cursor: pointer;
  }

  .dataTables_paginate .paginate_button.current {
    background-color: var(--color-primary-default);
    color: #fff;
  }
</style>
<?
$objPaginaSEI->montarJavaScript();
?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
<script type="text/javascript">
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

  $(document).ready(function() {
    $('#tblMonitoramentoIntegracaoProcessos').dataTable({
      "searching": false,
      "columnDefs": [{
        targets: [0, 4],
        orderable: true
      }],
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'Todos']
      ],
      "language": {
        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "infoEmpty": "Mostrando 0 a 0 de 0 registros",
        "zeroRecords": "Nenhum registro encontrado",
        "paginate": {
          "previous": "Anterior",
          "next": "Próximo"
        },
      }
    });
  });
</script>
<?
$objPaginaSEI->fecharHead();
$objPaginaSEI->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMonitoramentoIntegracaoProcessosLista" method="post" action="<?= $objPaginaSEI->formatarXHTML($objSessaoSEI->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
  <input type="hidden" id="chkPacoteAcao" name="chkPacoteAcao" />
  <?
  $objPaginaSEI->montarBarraComandosSuperior($arrComandos);
  $objPaginaSEI->abrirAreaDados('');
  ?>

  <div class="row">
    <div class="col-md-6 col-12 ">
      <div id="divfiltroCodProtocolo" class="infraAreaDados d-flex flex-column flex-md-row mb-2" style="">
        <div class="col-12 col-md-2 mx-0 px-0 pt-1">
          <label id="filtroCodProtocoloLabel" for="filtroCodProtocolo" accesskey="" class="infraLabelOpcional">Nº Processo:</label>
        </div>
        <div class="col-7 col-md-4 pl-0 pl-md-1 pt-1 media">
          <input type="text" id="filtroCodProtocolo" name="filtroCodProtocolo" maxlength="50" class="infraText w-100 w-md-75" onkeypress="return infraLimitarTexto(this,event,50);" value="<?= PaginaSEI::tratarHTML($filtro['filtroCodProtocolo']) ?>" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
        </div>
      </div>

      <div id="divSituacaoPesquisa" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
        <div class="col-12 col-md-2 mx-0 px-0 pt-1">
          <label id="filtroSelSituacaoIntegracaoLabel" for="filtroSelSituacaoIntegracao" accesskey="" class="infraLabelOpcional">Situação:</label>
        </div>
        <div class="col-7 col-md-4 pl-0 pl-md-1 pt-1 media">
          <select id="filtroSelSituacaoIntegracao" name="filtroSelSituacaoIntegracao" class="infraSelect w-100 w-md-75" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>">
            <?= $strItensSelSituacoes ?>
          </select>
        </div>
      </div>

      <div id="divPeriodoDe" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
        <div class="col-12 col-md-2 mx-0 px-0 pt-1">
          <label id="lblPeriodoDe" for="filtroTxtPeriodoDe" class="infraLabelOpcional">Data de envio: </label>
        </div>

        <div class="d-flex flex-column flex-md-row col-12 col-md-7 pl-0 pl-md-1 media">
          <div class="col-12 col-md-12 media pl-0 pt-1">
            <div class="col-6 pl-0 media">
              <input type="text" id="filtroTxtPeriodoDe" name="filtroTxtPeriodoDe" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoDe); ?>" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <img id="imgDataInicio" src="<?= $objPaginaSEI->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoDe',this);" alt="Selecionar Data Inicial" title="Selecionar Data Inicial" class="infraImg mx-1" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <label id="lblDataE" accesskey="" class="infraLabelOpcional mx-0 pt-1 pl-md-2">a</label>
            </div>
            <div class="col-6 pl-0 pl-md-2 media">
              <input type="text" id="filtroTxtPeriodoA" name="filtroTxtPeriodoA" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoA); ?>" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <img id="imgDataFim" src="<?= $objPaginaSEI->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoA',this);" alt="Selecionar Data Final" title="Selecionar Data Final" class="infraImg mx-1" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
            </div>
          </div>
        </div>
      </div>

      <div id="divPeriodoGeracaoDe" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
        <div class="col-12 col-md-2 mx-0 px-0 pt-1">
          <label id="lblPeriodoGeracaoDe" for="txtPeriodoGeracaoDe" class="infraLabelOpcional">Data de geração do processo:</label>
        </div>

        <div class="d-flex flex-column flex-md-row col-12 col-md-7 pl-0 pl-md-1 media">
          <div class="col-12 col-md-12 media pl-0 pt-1">
            <div class="col-6 pl-0 media">
              <input type="text" id="filtroTxtPeriodoGeracaoDe" name="filtroTxtPeriodoGeracaoDe" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoGeracaoDe); ?>" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <img id="imgDataInicio" src="<?= $objPaginaSEI->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoGeracaoDe',this);" alt="Selecionar Data Inicial" title="Selecionar Data Inicial" class="infraImg mx-1" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <label id="lblDataE" accesskey="" class="infraLabelOpcional mx-0 pt-1 pl-md-2">a</label>
            </div>
            <div class="col-6 pl-0 pl-md-2 media">
              <input type="text" id="filtroTxtPeriodoGeracaoA" name="filtroTxtPeriodoGeracaoA" onkeypress="return infraMascaraData(this, event)" class="infraText w-75" value="<?= PaginaSEI::tratarHTML($dtaPeriodoGeracaoA); ?>" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
              <img id="imgDataFim" src="<?= $objPaginaSEI->getIconeCalendario() ?>" onclick="infraCalendario('filtroTxtPeriodoGeracaoA',this);" alt="Selecionar Data Final" title="Selecionar Data Final" class="infraImg mx-1" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
            </div>
          </div>
        </div>
      </div>

      <div id="divfiltroSelUnidade" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
        <div class="col-12 col-md-2 mx-0 px-0 pt-1">
          <label id="lblfiltroSelUnidade" for="filtroSelUnidade" accesskey="" class="infraLabelOpcional">Unidade Geradora:</label>
        </div>
        <div class="col-12 col-md-12 pl-0 pl-md-1 pt-1 media">
          <select id="filtroSelUnidade" name="filtroSelUnidade" class="infraSelect w-100 w-md-100" tabindex="<?= $objPaginaSEI->getProxTabDados() ?>">
            <?= $strItensSelUnidades ?>
          </select>
        </div>
      </div>

      <br />

      <div id="divfiltroIncluirUnidadesFilhas" class="infraAreaDados d-flex flex-column flex-md-row mb-1">
        <input type="checkbox" id="filtroIncluirUnidadesFilhas" name="filtroIncluirUnidadesFilhas" class="infraCheckbox" <?= $filtroIncluirUnidadesFilhas ?> tabindex="<?= $objPaginaSEI->getProxTabDados() ?>" />
        <label id="lblfiltroIncluirUnidadesFilhas" for="filtroIncluirUnidadesFilhas" accesskey="">Incluir Unidades Filhas</label>
      </div>
    </div>
  </div>

  <? $objPaginaSEI->fecharAreaDados(); ?>
  <? $objPaginaSEI->montarAreaTabela($strResultado, $numRegistros); ?>
  <? $objPaginaSEI->montarBarraComandosInferior($arrComandos); ?>
</form>
<?
$objPaginaSEI->fecharBody();
$objPaginaSEI->fecharHtml();
?>