<?php
try {
  require_once dirname(__FILE__).'/../../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();

 
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $objOrgaoDTO = new OrgaoDTO();

  $strDesabilitar = '';

  $arrComandos = array();
  
  $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
  $objProtocoloIntegradoParametrosDTO->retTodos();
  
  $objProtocoloIntegradoParametrosRN = new ProtocoloIntegradoParametrosRN();
  $objRetornoProtocoloIntegradoParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
  $senhaWebService = '';

  if(isset($_POST['txtSenhaServico'])){

        $senhaWebService = $_POST['txtSenhaServico'];
  }
  else if(strlen(trim($objRetornoProtocoloIntegradoParametrosDTO->getStrSenhaWebservice()))>0){

      $senhaWebService = $objProtocoloIntegradoParametrosRN->encriptaSenha(rawurldecode(trim($objRetornoProtocoloIntegradoParametrosDTO->getStrSenhaWebservice())));
  
  }
  
  switch($_GET['acao']){
   
    case 'protocolo_integrado_configurar_parametros':
		
      $strTitulo = 'Parâmetros de Integração';
      $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarOrgao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
      $strDesabilitar = 'disabled="disabled"';
	  
	  if(isset($_POST['hdnFomularioSubmetido'])){

			$strValorSinPublicacaoRestritos='N';
			if ($_POST['chkEnviarInformacoesProcessosRestritos']=='on'){
				$strValorSinPublicacaoRestritos='S';
			}
			$objProtocoloIntegradoParametrosDTO->setNumIdProtocoloIntegradoParametros($_POST['hdnIdProtocoloIntegradoParametros']);
			$objProtocoloIntegradoParametrosDTO->setStrUrlWebservice($_POST['txtUrlServico']);
			
			$objProtocoloIntegradoParametrosDTO->setStrLoginWebservice($_POST['txtLoginServico']);

      $senha = rawurlencode($objProtocoloIntegradoParametrosRN->encriptaSenha($_POST['txtSenhaServico']));
			$objProtocoloIntegradoParametrosDTO->setStrSenhaWebservice($senha);
			$objProtocoloIntegradoParametrosDTO->setNumQuantidadeTentativas($_POST['txtQuantidadeTentativas']);
			$objProtocoloIntegradoParametrosDTO->setNumAtividadesCarregar($_POST['txtQuantidadeAtividades']);
			$objProtocoloIntegradoParametrosDTO->setStrEmailAdministrador($_POST['txtEmailAdministrador']);
			$objProtocoloIntegradoParametrosDTO->setStrSinPublicacaoRestritos($strValorSinPublicacaoRestritos);
			
			$objProtocoloIntegradoParametrosRN->alterar($objProtocoloIntegradoParametrosDTO);
			$objRetornoProtocoloIntegradoParametrosDTO = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
			//var_dump($objRetornoProtocoloIntegradoParametrosDTO);
	  	
	  }
	  
	  break;
	 default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
	  
  }	  
  $chkEnviarInformacoesProcessosRestritos = "";
  if ($objRetornoProtocoloIntegradoParametrosDTO->getStrSinPublicacaoRestritos()!=null && $objRetornoProtocoloIntegradoParametrosDTO->getStrSinPublicacaoRestritos()=='S'){
  	$chkEnviarInformacoesProcessosRestritos = "checked='checked'";
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
function OnSubmitForm() {
  return validarCadastroProtocololIntegradoParametros();
  
}
function validarCadastroProtocololIntegradoParametros(){
	
	if (infraTrim(document.getElementById('txtLoginServico').value)=='') {
	    alert('Informe o Usuário de Acesso ao Webservice');
	    document.getElementById('txtLoginServico').focus();
	    return false;
  	}
  
  	if (infraTrim(document.getElementById('txtLoginServico').value).length >10) {
	    alert('O campo Usuário deve ter no máximo 10 caracteres');
	    document.getElementById('txtLoginServico').focus();
	    return false;
  	}
  	if (infraTrim(document.getElementById('txtSenhaServico').value).length!=16) {
	    alert('O campo Senha deve possuir 16 caracteres');
	    document.getElementById('txtSenhaServico').focus();
	    return false;
  	}
  	if (infraTrim(document.getElementById('txtSenhaServico').value)=='') {
	    alert('Informe o Senha de Acesso ao Webservice');
	    document.getElementById('txtSenhaServico').focus();
	    return false;
  	}
	if (infraTrim(document.getElementById('txtUrlServico').value)=='') {
	    alert('Informe a URL do WebService');
	    document.getElementById('txtUrlServico').focus();
	    return false;
  	}
  	/*if (infraTrim(document.getElementById('txtDataCorte').value)=='') {
	    alert('Informe a Data de Corte');
	    document.getElementById('txtDataCorte').focus();
	    return false;
  	}
  	if (infraTrim(document.getElementById('txtDataCorteFinal').value)=='') {
	    alert('Informe a Data de Corte Final');
	    document.getElementById('txtDataCorteFinal').focus();
	    return false;
  	}*/
  	if(!infraValidarData(infraTrim(document.getElementById('txtDataCorte')))){
  		alert('A Data de Corte deve ser uma data válida');
	    document.getElementById('txtDataCorte').focus();
	    return false;	
  	}
  	if (infraTrim(document.getElementById('txtQuantidadeTentativas').value)=='') {
	    alert('Informe a Quantidade de tentativas');
	    document.getElementById('txtQuantidadeTentativas').focus();
	    return false;
  	}
  	
  	if (isNaN(document.getElementById('txtQuantidadeTentativas').value)) {
	    alert('A Quantidade de tentativas deve ser um número inteiro');
	    document.getElementById('txtQuantidadeTentativas').focus();
	    return false;
  	}
  	if (infraTrim(document.getElementById('txtQuantidadeAtividades').value)=='') {
	    alert('Informe a quantidade máxima de andamentos a enviar por vez');
	    document.getElementById('txtQuantidadeTentativas').focus();
	    return false;
  	}
  	
  	if (isNaN(document.getElementById('txtQuantidadeAtividades').value)) {
	    alert('A quantidade máxima de andamentos deve ser um número inteiro');
	    document.getElementById('txtQuantidadeTentativas').focus();
	    return false;
  	}else if(document.getElementById('txtQuantidadeAtividades').value>500000){
  		
  		alert('A quantidade de máxima de andamentos não deve ultrapassar o valor 500000');
	    document.getElementById('txtQuantidadeAtividades').focus();
	    return false;
  	}
  	if (infraTrim(document.getElementById('txtEmailAdministrador').value)=='') {
	    alert('Informe o Email do Administrator');
	    document.getElementById('txtEmailAdministrador').focus();
	    return false;
  	}
  	return true;
}
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmProtocoloIntegrado" method="post" onsubmit="return OnSubmitForm();"  action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<input type="hidden" id="hdnFomularioSubmetido" name="hdnFomularioSubmetido" value="true" />
<input type='hidden' id'hdnIdProtocoloIntegradoParametros' name='hdnIdProtocoloIntegradoParametros' value='<?=$objRetornoProtocoloIntegradoParametrosDTO->getNumIdProtocoloIntegradoParametros()?>'/>
<?
//PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
//PaginaSEI::getInstance()->montarAreaValidacao();
PaginaSEI::getInstance()->abrirAreaDados(null);
?>
  
  <h2 style='font-weight:bold;text-decoration: underline;'>Endereço do Serviço</h2>
  <label id="lblUrlServico" for="txtUrlServico" accesskey=""   class="infraLabelObrigatorio">URL referente ao webservice do Protocolo Integrado que será utilizado:</label><br/><br/>
  <input type="text" id="txtUrlServico" name="txtUrlServico" class="infraText" size="80" value="<?=$objRetornoProtocoloIntegradoParametrosDTO->getStrUrlWebservice()?>"   tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /><br/><br/><br/>
  <label id="lblLoginServico" for="txtLoginServico" accesskey="" class="infraLabelObrigatorio">Usuário</label><br/>
  <input type="text" id="txtLoginServico" name="txtLoginServico" class="infraText" size="10" value="<?=$objRetornoProtocoloIntegradoParametrosDTO->getStrLoginWebservice()?>"   tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /><br/><br/><br/>
  <label id="lblSenhaServico" for="txtSenhaServico" accesskey="" class="infraLabelObrigatorio">Senha</label><br/>
  <input type="password" id="txtSenhaServico" name="txtSenhaServico" class="infraText" size="20" value="<?=$senhaWebService?>"   tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /><br/><br/><br/>
  
  <hr>
  <h2 style='font-weight:bold;text-decoration: underline;'>Tentativas de Reenvio</h2>
  <h3 style='font-weight:bold; font-style: italic;'>Quantidade de Tentativas para Reenvio dos Metadados:</h3><br/>
  <label id="lblQuantidadeTentativas" for="txtQuantidadeTentativas" accesskey="">
  		Quando o envio de processos para o Protocolo Integrado for malsucedido, o SEI tentará reenviá-los respeitando a quantidade de vezes especificada abaixo.
  </label><br/><br/>
  <input type="text" id="txtQuantidadeTentativas" name="txtQuantidadeTentativas" class="infraText" size="3" value="<?=$objRetornoProtocoloIntegradoParametrosDTO->getNumQuantidadeTentativas()?>"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /> vezes<br/><br/><br/>

  <hr>
  <h2 style='font-weight:bold;text-decoration: underline;'>Andamentos a Enviar</h2>
  <h3 style='font-weight:bold; font-style: italic;'>Enviar Informações de Processos Restritos:</h3>
  <input id="chkEnviarInformacoesProcessosRestritos" name="chkEnviarInformacoesProcessosRestritos" type="checkbox" <?=$chkEnviarInformacoesProcessosRestritos?> tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />Sim
  
  <h3 style='font-weight:bold; font-style: italic;'>Quantidade máxima de andamentos a enviar por vez:</h3><br/>
  <label id="lblQuantidadeTentativas" for="txtQuantidadeTentativas" accesskey="">
  		Quando o agendamento for executado, este parâmetro será utilizado como número máximo de andamentos de processos a ser enviado.
  </label><br/><br/>
  <input type="text" id="txtQuantidadeAtividades" name="txtQuantidadeAtividades" class="infraText" size="8" value="<?=$objRetornoProtocoloIntegradoParametrosDTO->getNumAtividadesCarregar()?>"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /> atividades<br/><br/><br/>
  
  <hr>	
  <h2 style='font-weight:bold;text-decoration: underline;'>Administrador da Integração</h2>
  <h3 style='font-weight:bold; font-style: italic;'>Endereço de e-mail:</h3><br/>
  <label id="lblEmailAdministrador" for="txtEmailAdministrador" accesskey="">
  		Contato para questões relacionadas à integração do SEI com o Protocolo Integrado.<br/>
  </label><br/>
  <input type="text" id="txtEmailAdministrador" name="txtEmailAdministrador" class="infraText" size="80" value="<?=$objRetornoProtocoloIntegradoParametrosDTO->getStrEmailAdministrador()?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" /> <br/><br/><br/>
		 
  <button type="submit" accesskey="S" name="sbmAlterarOrgao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>	
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