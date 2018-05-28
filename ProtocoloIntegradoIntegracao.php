<?php

class ProtocoloIntegradoIntegracao extends SeiIntegracao {
    
    public function __construct() {
        //infraAdicionarPath(dirname(__FILE__) . 'rn');
        //infraAdicionarPath(dirname(__FILE__) . 'dto');
        //infraAdicionarPath(dirname(__FILE__) . 'bd');
        //infraAdicionarPath(dirname(__FILE__) . 'ws');
    }
    
    public function getNome() {
        return 'Módulo do Protocolo Integrado';
    }
    
    public function getVersao() {
        return '2.0.0';
    }
    
    public function getInstituicao() {
        return 'MP - Ministério do Planejamento';
    }
    
    public function processarControlador($strAcao) {
        switch($strAcao) {
            case 'protocolo_integrado_configurar_publicacao':
            case 'protocolo_integrado_mensagens_listar':
                require_once 'protocolo_integrado_mensagens_listar.php';
                return true;
            
            case  'protocolo_integrado_mensagens_alterar':
                require_once 'protocolo_integrado_mensagens_cadastro.php';
                return true;
            
            case  'protocolo_integrado_configurar_parametros':
                require_once 'protocolo_integrado_configurar_parametros.php';
                return true;
            
            case 'protocolo_integrado_monitoramento':
            case'protocolo_integrado_forcar_reenvio':
                require_once 'protocolo_integrado_monitoramento.php';
                return true;
                
            case 'protocolo_integrado_visualizar_metadados':
            case 'protocolo_integrado_visualizar_erro_envio_metadados':
                require_once 'protocolo_integrado_acesso_arquivo_metadados.php';
                return true;
        }
        return false;
    }
    
    public function processarControladorAjax($strAcaoAjax) {
        $xml = null;
        /*
        switch ($strAcaoAjax) {
            case 'protooclo_integrado_xxxxxxx':
                $xml = ProtocoloIntegradoXxxxxxxINT....;
                break;
        }
        */
        return $xml;
    }
    
    public function processarControladorWebServices($strServico) {
        $strArq = null;
        /*
        switch ($strServico) {
            case 'cvm_xxxxxx':
                $strArq = 'cvm_xxxxxx.wsdl';
                break;
        }
        
        if ($strArq!=null){
            $strArq = dirname(__FILE__).'/ws/'.$strArq;
        }
        */
        return $strArq;
    }
    
}

?>
