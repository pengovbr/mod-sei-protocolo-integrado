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

  PaginaSEI::getInstance()->verificarSelecao('tarefa_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $arrComandos = array();
  
  $objProtocoloIntegradoDTO = new ProtocoloIntegradoDTO();
  $objProtocoloIntegradoDTO->retNumIdProtocoloIntegrado();
  $objProtocoloIntegradoDTO->retStrNomeTarefa();
  $objProtocoloIntegradoDTO->retStrSinPublicar();
  $objProtocoloIntegradoDTO->retStrMensagemPublicacao();
  
  $objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
  
  if (isset($_REQUEST['id_mensagem_protocolo_integrado'])){
    $idProtocolo = $_REQUEST['id_mensagem_protocolo_integrado'];
  }
  $objRetornoProtocoloIntegradoDTO = new ProtocoloIntegradoDTO();

  switch($_GET['acao']){
   
    case 'md_pi_mensagens_alterar':
      $strTitulo = 'Editar Publicação no Protocolo Integrado';
      $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarOrgao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
      $strDesabilitar = 'disabled="disabled"';
      
      if (isset($_REQUEST['id_mensagem_protocolo_integrado'])){
            
            $objProtocoloIntegradoDTO->setNumIdProtocoloIntegrado($idProtocolo);
            $objRetornoProtocoloIntegradoDTO = $objProtocoloIntegradoRN->consultar($objProtocoloIntegradoDTO);
        if(isset($_POST['hdnFomularioSubmetido']) ){
                
          if($objRetornoProtocoloIntegradoDTO->getStrSinPublicar()=='S'){
                    
                $objProtocoloIntegradoDTO->setStrMensagemPublicacao($_POST['txtMensagemPublicacao']);
                $objProtocoloIntegradoRN->alterar($objProtocoloIntegradoDTO);
                header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_mensagens_listar'));
                die;
        
          }
                
        }
            
      }
        break;
    default:
        throw new InfraException("Módulo Protocolo Integrado: Ação '".$_GET['acao']."' não reconhecida.");
      
  }   
 
} catch(Exception $e){
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

<?

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
function inicializar(){
}
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmProtocoloIntegrado" method="post" onsubmit="return OnSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<input type="hidden" id="hdnFomularioSubmetido" name="hdnFomularioSubmetido" value="true" />
<input type="hidden" id="id_mensagem_protocolo_integrado" name="id_mensagem_protocolo_integrado" value="<?=$idProtocolo?>" />
<?
//PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
//PaginaSEI::getInstance()->montarAreaValidacao();
PaginaSEI::getInstance()->abrirAreaDados('55em');
?>
  <br/><br/><br/><br/>  
  <label id="lblTarefa" for="txtTarefa" accesskey="a" class="infraLabelOpcional">Texto Original da Publicação:</label><br/>
  <textarea id="txtTarefa" rows="4" cols="50" name="txtTarefa" class="infraText"   tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" readonly="true" /><?=$objRetornoProtocoloIntegradoDTO->getStrNomeTarefa()?></textarea><br/><br/><br/>    

  <label id="lblMensagemPublicacao" for="txtMensagemPublicacao" accesskey="" class="infraLabelOpcional">Texto para Publicação:</label><br/>
  <textarea id="txtMensagemPublicacao" rows="4" cols="50" name="txtMensagemPublicacao" class="infraText"  onkeypress="infraMascaraTexto(this,event,500);"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"  /><?=$objRetornoProtocoloIntegradoDTO->getStrMensagemPublicacao()?></textarea><br/>
  <button type="submit" accesskey="S" name="sbmAlterarOrgao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>   
  <button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="location.href='<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pi_mensagens_listar')?>';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>
  <?
  PaginaSEI::getInstance()->fecharAreaDados();

  //PaginaSEI::getInstance()->montarAreaDebug();
  //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?  
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
