<?php

try {
  // require_once dirname(__FILE__).'/../../../SEI.php';
  require_once DIR_SEI_WEB.'/SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();

  
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $objProtocoloIntegradoDTO = new ProtocoloIntegradoDTO();
  $objProtocoloIntegradoDTO->retNumIdProtocoloIntegrado();
  //$objProtocoloIntegradoDTO->retNumIdTarefa();
  $objProtocoloIntegradoDTO->retStrSinPublicar();
  $objProtocoloIntegradoDTO->retStrMensagemPublicacao();

  PaginaSEI::getInstance()->prepararOrdenacao($objProtocoloIntegradoDTO, 'MensagemPublicacao', InfraDTO::$TIPO_ORDENACAO_ASC);
 
  $objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
  $arrObjProtocoloIntegradoDTO = $objProtocoloIntegradoRN->listar($objProtocoloIntegradoDTO);
  
  switch($_GET['acao']){
  	
    case 'md_pi_configurar_publicacao':
		
		$arrStrIds = explode(',',$_REQUEST['hdnPublicarItensSelecionados']);
		
		$arrStrItensModificados = explode(',',$_REQUEST['chkPublicarItemAlterados']);
		$arrStrItensModificados = array_unique($arrStrItensModificados);
		$ConfiguracaoProtocoloIntegradoDTO = new ProtocoloIntegradoDTO();
		for($i = 0;$i < count($arrStrItensModificados)-1; $i++){
			
			if(in_array($arrStrItensModificados[$i],$arrStrIds)){
				
				$ConfiguracaoProtocoloIntegradoDTO->setNumIdProtocoloIntegrado($arrStrItensModificados[$i]);
				$ConfiguracaoProtocoloIntegradoDTO->setStrSinPublicar('S');
				$objProtocoloIntegradoRN->alterarOperacoesPublicacao($ConfiguracaoProtocoloIntegradoDTO);  
				
			}else{
				
				$ConfiguracaoProtocoloIntegradoDTO->setNumIdProtocoloIntegrado($arrStrItensModificados[$i]);
				$ConfiguracaoProtocoloIntegradoDTO->setStrSinPublicar('N');
				$objProtocoloIntegradoRN->alterarOperacoesPublicacao($ConfiguracaoProtocoloIntegradoDTO); 
			}
			PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
		}	
		header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem']));
      	die;
      break;
    case 'md_pi_mensagens_alterar':
      
      break;

    case 'md_pi_mensagens_listar':
      $strTitulo = 'Configuração  de Publicação no Protocolo Integrado';
      break;
              
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
  
  $bolAcaoConfigurarPublicacao = SessaoSEI::getInstance()->verificarPermissao('md_pi_configurar_publicacao');
  
  if ($bolAcaoConfigurarPublicacao){
    $arrComandos[] = '<input type="button" onclick="configurarHistorico()" name="btnSalvar" id="btnSalvar" value="Salvar" class="infraButton" />';
    $strLinkConfigurarHistorico = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_configurar_publicacao&acao_origem='.$_GET['acao']);
  }
  
   $numRegistros = count($arrObjProtocoloIntegradoDTO);
  
  if ($numRegistros > 0){

    $bolCheck = false;
	
    if ($_GET['acao']=='md_pi_mensagens_listar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pi_mensagens_listar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pi_mensagens_alterar');
      $bolAcaoImprimir = false;
      //$bolAcaoGerarPlanilha = false;
      $bolCheck = true;
    }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pi_mensagens_listar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pi_mensagens_alterar');
    
    }

   
    $strResultado = '';
	$strSumarioTabela = 'Tabela de Mensagens.';
    $strCaptionTabela = 'Mensagens';

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
	if($bolAcaoConfigurarPublicacao){
    	$strResultado .= '<th class="infraTh"  width="7%">'.PaginaSEI::getInstance()->getThCheck('Publicar','Publicar','onClick="mudaEstadoTodosLinkEditar(\'Publicar\');"').'</th>'."\n";
	}
    $strResultado .= '<th class="infraTh"  width="90%">'.PaginaSEI::getInstance()->getThOrdenacao($objProtocoloIntegradoDTO,'Mensagem para Publicação','MensagemPublicacao',$arrObjProtocoloIntegradoDTO).'</th>';
	if($bolAcaoAlterar){
		$strResultado .= '<th class="infraTh"  width="7%"> Ação </th>'."\n";
	}
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
	
    for($i = 0;$i < $numRegistros; $i++){
		
      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      //$strResultado .= $strCssTr;
      $strResultado .= $strCssTr;
      
	  if($bolAcaoConfigurarPublicacao){ 
      	$strResultado .= '<td>'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjProtocoloIntegradoDTO[$i]->getNumIdProtocoloIntegrado(),$arrObjProtocoloIntegradoDTO[$i]->getStrMensagemPublicacao(),$arrObjProtocoloIntegradoDTO[$i]->getStrSinPublicar(),'Publicar','onChange="mudaEstadoLinkEditar('.$i.');"').'</td>'; 
	  }
 
	  $strTagId = 'chkPublicarItem'.$i;
	  $strTagName = $strTagId;
      $strResultado .= '<td width="10%">'.$arrObjProtocoloIntegradoDTO[$i]->getStrMensagemPublicacao().'</td>';
	  if($bolAcaoAlterar){
	  $strResultado .= '<td align="center">';

	      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjProtocoloIntegradoDTO[$i]->getNumIdProtocoloIntegrado());
	
	      if ($arrObjProtocoloIntegradoDTO[$i]->getStrSinPublicar()=='S'){
	        $strResultado .= '<a class="enabled" id="linkEditarMensagem_'.$i.'" href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_mensagens_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_mensagem_protocolo_integrado='.$arrObjProtocoloIntegradoDTO[$i]->getNumIdProtocoloIntegrado())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Tipo de Andamento" alt="Alterar Tipo de Andamento" class="infraImg" /></a>&nbsp;';
	      }else {
	      	
			 $strResultado .= '<a class="disabled" id="linkEditarMensagem_'.$i.'" href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_mensagens_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_mensagem_protocolo_integrado='.$arrObjProtocoloIntegradoDTO[$i]->getNumIdProtocoloIntegrado())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Tipo de Andamento" alt="Alterar Tipo de Andamento" class="infraImg" /></a>&nbsp;';
	  
	      }
	      $strResultado .= '</td>';
	  }	
      
      $strResultado .= '</tr>'."\n"; 
      $strResultado .= "</tr>\n";
    }
    $strResultado .= '</table>';
  }
  if ($_GET['acao'] == 'tarefa_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  }

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
  if ('<?=$_GET['acao']?>'=='tarefa_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}
function mudaEstadoTodosLinkEditar(nomeSelecao){
	 
	 var nomeHdnNroItens = 'hdn'+nomeSelecao+'NroItens';
	 infraCheck = document.getElementById('img'+nomeSelecao+'Check');
	 infraNroItens = document.getElementById(nomeHdnNroItens);
	 document.getElementById('chkPublicarItemAlterados').value  = '';
	 if(infraCheck.title == 'Selecionar Tudo'){
	 	
	 	
	 	for(var i=0;i<infraNroItens.value;i++){
	 		
	 		var link = document.getElementById('linkEditarMensagem_'+i);
	 		if(link!=null){
		 		link.className = 'enabled';	
	 		}
	 		document.getElementById('chkPublicarItemAlterados').value += document.getElementById('chkPublicarItem'+i).value + ',';
	 	}	
	 	
	 }else{
	 	
	 	for(var i=0;i<infraNroItens.value;i++){
	 		
	 		var link = document.getElementById('linkEditarMensagem_'+i);
	 		if(link!=null){
	 			link.className = 'disabled';
	 		}
	 		document.getElementById('chkPublicarItemAlterados').value += document.getElementById('chkPublicarItem'+i).value + ',';
	 	}
	 } 
	 infraSelecaoMultipla(nomeSelecao);
	
}
function mudaEstadoLinkEditar(indice){

	var link = document.getElementById('linkEditarMensagem_'+indice);
	
	if(link!=null){
		
		if(document.getElementById('chkPublicarItem'+indice).checked==false){
			link.className = 'disabled';
		}else{
			
			link.className = 'enabled';
		}
	}
	document.getElementById('chkPublicarItemAlterados').value += document.getElementById('chkPublicarItem'+indice).value + ',';
}


<? if ($bolAcaoConfigurarPublicacao){ ?>
function configurarHistorico(){
 
  document.getElementById('frmMensagemLista').action='<?=$strLinkConfigurarHistorico?>';
  document.getElementById('frmMensagemLista').submit();
}
<? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmMensagemLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  <input type="hidden" id="chkPublicarItemAlterados" name="chkPublicarItemAlterados"/>
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
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