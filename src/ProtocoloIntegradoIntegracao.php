<?php

// ATENÇÃO: Identificação da versão do módulo mod-sei-protocolo-integrado. 
// Este deve estar sempre sincronizado com a constante VERSAO_MODULO_PI no arquivo sip_atualizar_versao_modulo_protocolo_integrado.php
define("VERSAO_MODULO_PI", "3.0.0");

class ProtocoloIntegradoIntegracao extends SeiIntegracao {
      
    public function getNome() {
        return 'Protocolo Integrado';
    }
    
    public function getVersao() {
        return VERSAO_MODULO_PI;
    }
    
    public function getInstituicao() {
        return 'Ministério da Economia - ME';
    }

    public function inicializar($strVersaoSEI)
    {
        define('DIR_SEI_WEB', realpath(DIR_SEI_CONFIG.'/../web'));
        $this->carregarArquivoConfiguracaoModulo(DIR_SEI_CONFIG);
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
            
            case 'protocolo_integrado_configurar_parametros':
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

    private function carregarArquivoConfiguracaoModulo($strDiretorioSeiWeb){
        try{
            $strArquivoConfiguracao = $strDiretorioSeiWeb . '/mod-protocolo-integrado/ConfiguracaoModProtocoloIntegrado.php';
            include_once $strArquivoConfiguracao;       
        } catch(Exception $e){
            LogSEI::getInstance()->gravar("Arquivo de configuração do módulo Protocolo Integrado não pode ser localizado em " . $strArquivoConfiguracao);
        }
    }
}


?>
