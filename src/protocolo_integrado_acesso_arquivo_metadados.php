<?php

try {
    
    // require_once dirname(__FILE__).'/../../../SEI.php';
    require_once DIR_SEI_WEB.'/SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    SessaoSEI::getInstance()->validarPermissao('protocolo_integrado_acesso_arquivo_metadados');

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    switch($_GET['acao']){
  	
        case 'protocolo_integrado_visualizar_metadados':
            $idPacote = $_REQUEST['id_pacote'];
            $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
            $objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
            $objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($idPacote);
            	 
            $objPacoteDTO->retStrArquivoMetadados();
            			
            $objRetornoPacote =  $objPacoteRN->consultar($objPacoteDTO);	
            header("Content-Type: text/xml");
            print($objRetornoPacote->getStrArquivoMetadados());
            die;
            break;
            
        case 'protocolo_integrado_visualizar_erro_envio_metadados':
            $idPacote = $_REQUEST['id_pacote'];
            $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
            $objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
            $objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($idPacote);
            	 
            $objPacoteDTO->retStrArquivoErro();
            			
            $objRetornoPacote =  $objPacoteRN->consultar($objPacoteDTO);	
            header("Content-Type: text/xml");
            print ($objRetornoPacote->getStrArquivoErro());
            die;
            break;
            
        default:
            throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
            
    }
    
} catch(Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}  

?>