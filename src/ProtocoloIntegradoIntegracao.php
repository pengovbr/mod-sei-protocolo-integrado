<?php

// ATEN��O: Identifica��o da vers�o do m�dulo mod-sei-protocolo-integrado. 
// Este deve estar sempre sincronizado com a constante VERSAO_MODULO_PI no arquivo sip_atualizar_versao_modulo_protocolo_integrado.php
define("VERSAO_MODULO_PI", "3.0.2");

class ProtocoloIntegradoIntegracao extends SeiIntegracao {

  const COMPATIBILIDADE_MODULO_SEI = [
    // Vers�es SEI
    '4.0.12', '4.1.1', '4.1.2', '4.1.5',
    '4.0.12.15', '5.0.0'
  ];
      
  public function getNome() {
      return 'Protocolo Integrado';
  }
    
  public function getVersao() {
      return VERSAO_MODULO_PI;
  }
    
  public function getInstituicao() {
      return 'Minist�rio da Gest�o e da Inova��o em Servi�os P�blicos - MGI';
  }

  public function inicializar($strVersaoSEI)
    {      
      if (!defined('DIR_SEI_WEB')) {
        define('DIR_SEI_WEB', realpath(DIR_SEI_CONFIG.'/../web'));
      }

      $this->carregarArquivoConfiguracaoModulo(DIR_SEI_CONFIG);
  }    

  public function processarControlador($strAcao) {        
    switch($strAcao) {
      case 'md_pi_configurar_publicacao':
      case 'md_pi_mensagens_listar':
        require_once 'protocolo_integrado_mensagens_listar.php';
          return true;
            
      case 'md_pi_mensagens_alterar':
          require_once 'protocolo_integrado_mensagens_cadastro.php';
          return true;
            
      case 'md_pi_monitoramento':
      case 'md_pi_forcar_reenvio':
          require_once 'protocolo_integrado_monitoramento.php';
          return true;
                
      case 'md_pi_visualizar_metadados':
      case 'md_pi_visualizar_erro_envio_metadados':
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
        LogSEI::getInstance()->gravar("Arquivo de configura��o do m�dulo Protocolo Integrado n�o pode ser localizado em " . $strArquivoConfiguracao);
    }
  }
}


?>
