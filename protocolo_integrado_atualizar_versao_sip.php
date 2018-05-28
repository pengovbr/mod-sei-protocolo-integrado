<?
	try{
	
    require_once dirname(__FILE__).'/../../../sip/Sip.php';
    require_once dirname(__FILE__).'/../../SEI.php';

    session_start();
		
		SessaoSip::getInstance(false);
		
		$objProtocoloIntegradoVersaoSipRN = new ProtocoloIntegradoVersaoSipRN();
		$objProtocoloIntegradoVersaoSipRN->atualizarVersao();

	}catch(Exception $e){
		echo(InfraException::inspecionar($e));
		try{LogSIP::getInstance()->gravar(InfraException::inspecionar($e));	}catch (Exception $e){}
	}
?>