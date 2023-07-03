<?php
    
require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoAgendamentoRN extends InfraRN {

  public function __construct() {
      parent::__construct();
  }
        
  protected function inicializarObjInfraIBanco() {
      return BancoSEI::getInstance();
  }
        
  public function publicarProtocoloIntegrado() {
        
    try {
        LimiteSEI::getInstance()->configurarNivel3();

        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        InfraDebug::getInstance()->limpar();
            
        SessaoSEI::getInstance(false)->simularLogin(SessaoSEI::$USUARIO_SEI, SessaoSEI::$UNIDADE_TESTE);
        $numSeg = InfraUtil::verificarTempoProcessamento();
            
        InfraDebug::getInstance()->gravar('Inicializando Publicaчѕes no Protocolo Integrado');
        $objProtocoloIntegradoMonitoramento = new ProtocoloIntegradoMonitoramentoProcessosDTO();    
        $objProtocoloIntegrado = new ProtocoloIntegradoMonitoramentoProcessosRN();
      try {           
        $objProtocoloIntegrado->publicarProcessos($objProtocoloIntegradoMonitoramento);
      } catch (Exception $e) {
          throw new InfraException('Erro ao executar publicaчуo de protocolos.', $e);
      }
            
        $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
        InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
        InfraDebug::getInstance()->gravar('FIM');
            
        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
        InfraDebug::getInstance()->limpar();
          
    } catch(Exception $e) {
        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
            
        InfraDebug::getInstance()->limpar();
        throw new InfraException('Erro ao publicar Metadados e Operaчѕes dos Processos no Protocolo Integrado.', $e);
    }
    
  }

  public function notificarNovosPacotesNaoSendoGerados() {
        
    try {
        
        LimiteSEI::getInstance()->configurarNivel3();
            
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        InfraDebug::getInstance()->limpar();
        $numSeg = InfraUtil::verificarTempoProcessamento();
        InfraDebug::getInstance()->gravar('Inicializando Notificaчѕes de Novos Pacotes Nуo sendo gerados para enviar para oo Protocolo Integrado');
            
        $objProtocoloIntegrado = new ProtocoloIntegradoMonitoramentoProcessosRN();
        $objProtocoloIntegrado->notificarPacotesSemEnvio(); 
            
        $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
        InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
        InfraDebug::getInstance()->gravar('FIM');
        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
        InfraDebug::getInstance()->limpar();
        
    } catch(Exception $e) {
        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
              
        InfraDebug::getInstance()->limpar();
        throw new InfraException('Erro ao publicar Metadados e Operaчѕes dos Processos no Protocolo Integrado.', $e);
    }
        
  }

  public function notificarProcessosComFalhaPublicacaoProtocoloIntegrado() {
    
    try {
        LimiteSEI::getInstance()->configurarNivel3();
            
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        InfraDebug::getInstance()->limpar();
            
        //SessaoSEI::getInstance(false)->simularLogin(SessaoSEI::$USUARIO_SEI, SessaoSEI::$UNIDADE_TESTE);
        $numSeg = InfraUtil::verificarTempoProcessamento();
            
        InfraDebug::getInstance()->gravar('Inicializando Notificaчѕes de Processos Nуo Publicados no Protocolo Integrado');
        $objProtocoloIntegradoMonitoramento = new ProtocoloIntegradoMonitoramentoProcessosDTO();    
        $objProtocoloIntegrado = new ProtocoloIntegradoMonitoramentoProcessosRN();
        $objProtocoloIntegrado->notificarProcessosComFalha($objProtocoloIntegradoMonitoramento);
        $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
        InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
        InfraDebug::getInstance()->gravar('FIM');
            
        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
        InfraDebug::getInstance()->limpar();
        
    } catch(Exception $e) {
        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
            
        InfraDebug::getInstance()->limpar();
        throw new InfraException('Erro ao publicar Metadados e Operaчѕes dos Processos no Protocolo Integrado.', $e);
    }   
  } 
}
 
?>