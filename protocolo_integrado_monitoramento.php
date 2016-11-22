<?
ini_set('max_execution_time','0');
ini_set('memory_limit','-1');

try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();

  
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
 	 
  $filtro = $_REQUEST;
 
  $dtaPeriodoDe = $_REQUEST['filtroTxtPeriodoDe'];
  $dtaPeriodoA = $_REQUEST['filtroTxtPeriodoA'];
  $dtaPeriodoGeracaoDe = $_REQUEST['filtroTxtPeriodoGeracaoDe'];
  $dtaPeriodoGeracaoA = $_REQUEST['filtroTxtPeriodoGeracaoA'];
  $filtroProtocolo = $_REQUEST['filtroCodProtocolo'];
  $filtroStaIntegracao = $_REQUEST['filtroSelSitucaoIntegracao'];
  $filtroUnidadeGeradora = $_REQUEST['filtroSelUnidade'];
  $filtroIncluirUnidadesFilhas = $_REQUEST['filtroIncluirUnidadesFilhas'];
  if ($filtroIncluirUnidadesFilhas=='on'){
  	$filtroIncluirUnidadesFilhas="checked='checked'";
  }
  else{
  	$filtroIncluirUnidadesFilhas="";
  }
  
  if (isset($_POST['sbmPesquisar']) || isset($_POST['hdnInfraPaginaAtual'])==false || $_POST['hdnInfraPaginaAtual']==''){
	  $_POST['hdnInfraPaginaAtual'] = '0';
  }
 
  switch($_GET['acao']){
  	
  	case 'protocolo_integrado_forcar_reenvio':
		
		$arrStrItensSelecionados = explode(',',$_REQUEST['hdnForcarReenvioItensSelecionados']);
		$arrStrItensSelecionados = array_unique($arrStrItensSelecionados);
		$objProtocoloIntegradoMonitoramentoProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
		$objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
		$objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
		$objProtocoloIntegradoParametrosDTO->retTodos();
		$objRetornoProtocoloIntegradoParametros = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
		$filtro = array();
		$filtro['pacotes'] = array();
		
		for($i = 0;$i < count($arrStrItensSelecionados); $i++){
			
			
			array_push($filtro['pacotes'],$arrStrItensSelecionados[$i]);
			PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
		}	
		$arrParam = array();
		$arrParam[0] = $objRetornoProtocoloIntegradoParametros;
		$arrParam[1] = $filtro;

		$objProtocoloIntegradoMonitoramentoProcessosRN->publicarProcessosMonitorados($arrParam);
		
		header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem']));
      	die;
		  
    case 'protocolo_integrado_monitoramento':
      $strTitulo = 'Monitoramento de Integração';
      break;
              
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }
   $objProtocoloIntegradoMonitoramentoProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
  
  $filtro['paginacao'] = true;
  $arrObjPacotesMonitoradosDTO = $objProtocoloIntegradoMonitoramentoProcessosRN->listarProcessosMonitorados($filtro);
 
  $strItensSelSituacoesIntegracoes = $objProtocoloIntegradoMonitoramentoProcessosRN->getSituacoesIntegracao();
  
  $strItensSelUnidades = $objProtocoloIntegradoMonitoramentoProcessosRN->getUnidadesGeradoras();
  	
   $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
   $objProtocoloIntegradoParametrosDTO->retNumIdProtocoloIntegradoParametros();
   $objProtocoloIntegradoParametrosDTO->retNumQuantidadeTentativas();
   $objProtocoloIntegradoParametrosDTO->retDthDataUltimoProcessamento();
   $objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
  
  $objParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);	
  $arrComandos = array();
 
  $bolAcaoForcarReenvio = SessaoSEI::getInstance()->verificarPermissao('protocolo_integrado_forcar_reenvio');
  
  $numRegistros = count($arrObjPacotesMonitoradosDTO);
  $objPacoteEnvioDTO = new ProtocoloIntegradoPacoteEnvioDTO();
  $objPacoteEnvioDTO -> retNumIdProtocolo();
  $objPacoteEnvioDTO -> retStrStaIntegracao();
  $objPacoteEnvioDTO -> retDthDataSituacao();
  $objPacoteEnvioDTO -> retDthDataMetadados();
  $objPacoteEnvioDTO -> retNumTentativasEnvio();
  $objPacoteEnvioDTO -> retStrProtocoloFormatado();
  $objPacoteEnvioDTO -> retNumIdProtocoloIntegradoPacoteEnvio();
  
  if ($numRegistros > 0){
		
	$bolCheck = false;
	
    if ($_GET['acao']=='protocolo_integrado_monitoramento'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('protocolo_integrado_monitoramento');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('protocolo_integrado_monitoramento');
      $bolAcaoImprimir = false;
      //$bolAcaoGerarPlanilha = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
	  $bolColunaArquivo = SessaoInfra::getInstance()->verificarPermissao('protocolo_integrado_acesso_arquivo_metadados');
    }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('protocolo_integrado_monitoramento');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('protocolo_integrado_monitoramento');
      $bolAcaoImprimir = true;
     
    }

    if ($bolAcaoExcluir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tarefa_excluir&acao_origem='.$_GET['acao']);
    }
    $strResultado = '';
	$strSumarioTabela = 'Tabela de Processos.';
    $strCaptionTabela = 'Pacotes';
	

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
     // $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="10%" align="center">'.PaginaSEI::getInstance()->getThCheck('Selecionar','ForcarReenvio').'</th>';
	$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO,'Data do Metadado','DataMetadados',$arrObjPacotesMonitoradosDTO).'</th>'."\n";
		
	
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO,'Processo','ProtocoloFormatado',$arrObjPacotesMonitoradosDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">Situação</th>'."\n";
	$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objPacoteEnvioDTO,'Data da Situação','DataSituacao',$arrObjPacotesMonitoradosDTO).'</th>'."\n";
	if($bolColunaArquivo){
		$strResultado .= '<th class="infraTh">Ações</th>'."\n";
	}
    $strResultado .= '</tr>'."\n";
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
    
	
	$i=0;
	$maxPacotesReenvio = 0;
	$indicePacoteComFalha = 0;
    foreach($arrObjPacotesMonitoradosDTO as $key=>$pacote){
			       
			  
	  $strImagemStatus = '';
      $strCssProcesso = '';
      $strLinkUsuarioAtribuicao = '&nbsp;';
      $bolFlagGerado = false;
      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
	  
	  $strResultado .= $strCssTr ;
    
      $strResultado .= '<td align="center">';
	  
 	  $strResultado .= PaginaSEI::getInstance()->getTrCheck($indicePacoteComFalha,$pacote['id_pacote'],$pacote['protocolo']->getStrProtocoloFormatado(),'N','ForcarReenvio');
	  $maxPacotesReenvio ++;
	  $indicePacoteComFalha++;
	  		  
	  if($pacote['dth_metadados']=='') $pacote['dth_metadados'] = '-';
	  $strResultado .= '<td width="7%" align="center"> '.$pacote['dth_metadados']. ' </td>';
	  
	  $strResultado .= '<td width="10%" align="center">'.$pacote['protocolo']->getStrProtocoloFormatado().'</td>';
	   
	  switch(trim($pacote['sta_integracao'])){
	  		
	  	case ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO:
			 $strResultado .= '<td width="10%"> Não Integrado </td>';
	  		 break;
		case ProtocoloIntegradoPacoteEnvioRN::$STA_INTEGRADO:
			$strResultado .= '<td width="10%"> Integrado </td>';
	  		break;	
		case ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA:
			$strResultado .= '<td width="10%"> Falha Infra </td>';
	  		break;	
		case ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL:
			$strResultado .= '<td width="10%"> Erro Negocial </td>';
	  		break;
			
		default:
			$strResultado .= '<td width="10%"> - </td>';	
			break;			 
	  }
	 
	  if($pacote['dth_situacao']=='') $pacote['dth_situacao'] = '-';
	  $strResultado .= '<td width="7%" align="center">'. $pacote['dth_situacao'] . '</td>';
	  
	  if($bolColunaArquivo){
	  	  
		  if($pacote['sta_integracao']!=ProtocoloIntegradoPacoteEnvioRN::$STA_NAO_INTEGRADO){
		 	 	 	
		 	 $strResultado .= 	'<td width="3%" align="center"><a  target="_blank"  id="linkArquivoMetadados" href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=protocolo_integrado_visualizar_metadados&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_pacote='.$pacote['id_pacote'])).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Visualizar os Metadados Gerados" alt="Visualizar os Metadados Gerados" class="infraImg" /></a>&nbsp;';
		  	  if($pacote['sta_integracao']==ProtocoloIntegradoPacoteEnvioRN::$STA_FALHA_INFRA||$pacote['sta_integracao']==ProtocoloIntegradoPacoteEnvioRN::$STA_ERRO_NEGOCIAL ){
		  						 
		  			$strResultado .= ' <a TARGET="_blank" href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=protocolo_integrado_visualizar_erro_envio_metadados&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_pacote='.$pacote['id_pacote'])).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'">'. '<img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/menos.gif" title="Visualizar XML de Erro" alt="Visualizar XML de Erro" class="infraImg" /></a>';
			  }
			  $strResultado .= '</td>';
		  }else if($bolColunaArquivo){
	  	
			$strResultado .= '<td align="center"></td>';
	  	
		  }
			
	  }	
      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$pacote['protocolo']->getDblIdProtocolo());
	   
	  $i++;
	
    }
    $strResultado .= '</table>';
  }
  if ($bolAcaoForcarReenvio && $maxPacotesReenvio>0){
    $arrComandos[] = '<input type="button" onclick="forcarReenvio()" name="btnForcar" id="btnForcar" value="Forçar Reenvio" class="infraButton" />';
    $strLinkForcarReenvio = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=protocolo_integrado_forcar_reenvio&acao_origem='.$_GET['acao']);
   
  }
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  

}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
} 

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

?>
a.disabled {
   pointer-events: none;
   cursor: default;
}
a.enabled {
	cursor: default;
}
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
	
  infraEfeitoTabelas();
}
<? if ($bolAcaoForcarReenvio){ ?>
function forcarReenvio(){

  document.getElementById('frmMonitoramentoIntegracaoProcessosLista').action='<?=$strLinkForcarReenvio?>';
  document.getElementById('frmMonitoramentoIntegracaoProcessosLista').submit();
}
<? } ?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmMonitoramentoIntegracaoProcessosLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  <input type="hidden" id="chkPacoteAcao" name="chkPacoteAcao"/>
  
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  ?>
   <label style='font-weight:bold;'>Último Envio de Metadados:</label> <?echo $objParametrosDTO->getDthDataUltimoProcessamento();?><br/>
   <h3 style='font-weight:bold;text-decoration: underline;'>Pesquisar por processo</h3><br/>
   <table>
   	 <tr style="height:28px;">
   	 	<td style="text-align: right;">
   	 		<label id="filtroCodProtocoloLabel" for="filtroCodProtocolo" accesskey="P" style="font-size: 12px;">Nº Processo:</label>
   	 	</td>
   	 	<td>
   	 		<input type="text" id="filtroCodProtocolo" size="35" name="filtroCodProtocolo" class="infraText" value="<?=$filtro['filtroCodProtocolo']?>"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" value="<?$filtroProtocolo?>" style="font-size: 12px;" />
   	 	</td>
   	 </tr>
   	 <tr style="height:28px;">
   	 	<td style="text-align: right;">
   	 		<label id="lblPeriodoDe" for="filtroTxtPeriodoDe" accesskey="S" style="font-size: 12px;">Situação:</label>
   	 	</td>
   	 	<td>
   	 		<select id="filtroSelTipoProcedimentoPesquisa" name="filtroSelSitucaoIntegracao"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;">
            <?
    	      foreach($strItensSelSituacoesIntegracoes as $strAtributoChave=>$strAtributoDescricao){
			     echo '<option value="'.$strAtributoChave.'"';
			     if($strAtributoChave==$filtroStaIntegracao){
				    echo ' selected>';
			     } 
			     else{
				    echo '>'; 
			     } 
			     echo $strAtributoDescricao;
			     echo '</option>';
    	      }
	        ?>	
    		</select>
   	 	</td>
   	 </tr>
   	 <tr style="height:28px;">
   	 	<td style="text-align: right;">
   	 		<label id="lblPeriodoDe" for="txtPeriodoDe" accesskey="" style="font-size: 12px;">Envio para o PI:</label>
   	 	</td>
   	 	<td>
   	 		<input type="text" id="filtroTxtPeriodoDe" name="filtroTxtPeriodoDe" class="infraText" value="<?=$dtaPeriodoDe?>" onkeypress="return infraMascaraData(this, event)" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;" />	  
    		<img id="imgCalPeriodoD" title="Selecionar Data Inicial" alt="Selecionar Data Inicial" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" class="infraImg" onclick="infraCalendario('filtroTxtPeriodoDe',this);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />    
			<label id="lblPeriodoA" for="txtPeriodoA" accesskey="" style="font-size: 12px;" >a</label>
			<input type="text" id="filtroTxtPeriodoA" name="filtroTxtPeriodoA" class="infraText" value="<?=$dtaPeriodoA?>" onkeypress="return infraMascaraData(this, event)" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;" />
			<img id="imgCalPeriodoA" title="Selecionar Data Final" alt="Selecionar Data Final" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" class="infraImg" onclick="infraCalendario('filtroTxtPeriodoA',this);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
   	 	</td>
   	 </tr>
   	 <tr style="height:28px;">
   	 	<td style="text-align: right;">
   	 		<label id="lblPeriodoGeracaoDe" for="txtPeriodoGeracaoDe" accesskey="" style="font-size: 12px;">Geração do Processo:</label>
   	 	</td>
   	 	<td>
   	 		<input type="text" id="filtroTxtPeriodoGeracaoDe" name="filtroTxtPeriodoGeracaoDe" class="infraText" value="<?=$dtaPeriodoGeracaoDe?>" onkeypress="return infraMascaraData(this, event)" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;" />	  
    		<img id="imgCalPeriodoD" title="Selecionar Data Inicial" alt="Selecionar Data Inicial" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" class="infraImg" onclick="infraCalendario('filtroTxtPeriodoGeracaoDe',this);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />    
			<label id="lblPeriodoGeracaoA" for="txtPeriodoGeracaoA" accesskey="" style="font-size: 12px;">a</label>
			<input type="text" id="filtroTxtPeriodoGeracaoA" name="filtroTxtPeriodoGeracaoA" class="infraText" value="<?=$dtaPeriodoGeracaoA?>" onkeypress="return infraMascaraData(this, event)" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;" />
			<img id="imgCalPeriodoA" title="Selecionar Data Final" alt="Selecionar Data Final" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" class="infraImg" onclick="infraCalendario('filtroTxtPeriodoGeracaoA',this);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
   	 	</td>
   	 </tr>
   	 <tr style="height:28px;background: transparent;">
   	 	<td style="text-align: right;">
   	 		<label id="lblPeriodoDe" for="filtroTxtPeriodoDe" accesskey="S" style="font-size: 12px;">Unidade Geradora:</label>
   	 	</td>
   	 	<td>
   	 		<select id="filtroSelUnidade" name="filtroSelUnidade"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" style="font-size: 12px;">
    		<?
    		   foreach($strItensSelUnidades as $strAtributoChave=>$strAtributoDescricao){
				 echo '<option value="'.$strAtributoChave.'"';
				 if($strAtributoChave==$filtroUnidadeGeradora){
					echo ' selected>';
				 } 
				 else{
					echo '>'; 
				 } 
				 echo $strAtributoDescricao;
				 echo '</option>';
    		  }
			?>	
    		</select>
    		<input type="checkbox" id="filtroIncluirUnidadesFilhas" name="filtroIncluirUnidadesFilhas" <?=$filtroIncluirUnidadesFilhas?> /> <label  accesskey="" style="font-size: 12px;">Incluir Unidades Filhas</label>
   	 	</td>
   	 </tr>
   	 <tr style="height:28px;">
   	 	<td>
   	 	</td>
   	 	<td>
   	 		<input type="submit" id="sbmPesquisar" name="sbmPesquisar" value="Pesquisar" class="infraButton" /> <br/><br/>
   	 	</td>
   	 </tr>
   </table>

   
	
	
	
	
    
    
	
	
    
   
  
  <?
  //PaginaSEI::getInstance()->abrirAreaDados('5em');
  //PaginaSEI::getInstance()->fecharAreaDados();
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  //PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
