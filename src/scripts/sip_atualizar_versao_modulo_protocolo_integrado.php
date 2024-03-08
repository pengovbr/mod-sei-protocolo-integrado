<?
require_once dirname(__FILE__) . '/../../web/Sip.php';

// ATENÇÃO: Identificação da versão do módulo mod-SIP-protocolo-integrado. 
// Este deve estar sempre sincronizado com a constante VERSAO_MODULO_PI no arquivo ProtocoloIntegradoIntegracao.php
define("VERSAO_MODULO_PI", "3.0.1");

try {

  class VersaoProtocoloIntegradoRN extends InfraScriptVersao
    {

    public function __construct()
      {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
      {
        return BancoSip::getInstance();
    }

    protected function verificarVersaoInstaladaControlado()
      {
        $objInfraParametroDTOFiltro = new InfraParametroDTO();
        $objInfraParametroDTOFiltro->setStrNome('PI_VERSAO');
        $objInfraParametroDTOFiltro->retStrNome();

        $objInfraParametroBD = new InfraParametroBD(BancoSip::getInstance());
        $objInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTOFiltro);
      if (is_null($objInfraParametroDTO)) {
          $objInfraParametroDTO = new InfraParametroDTO();
          $objInfraParametroDTO->setStrNome('PI_VERSAO');
          $objInfraParametroDTO->setStrValor('0.0.0');
          $objInfraParametroBD->cadastrar($objInfraParametroDTO);
      }

        return $objInfraParametroDTO->getStrNome();
    }

    public function versao_0_0_0($strVersaoAtual){

    }

    public function versao_1_1_2() {
        // Definição de parâmetro de versão para o módulo de Protocolo Integrado
        $objInfraParametro = new InfraParametro(BancoSip::getInstance());
        $objInfraParametro->setValor('PI_VERSAO', '');
    
        $numIdSistemaSei = ScriptSip::obterIdSistema('SEI');
        $numIdPerfilSeiAdministrador = ScriptSip::obterIdPerfil($numIdSistemaSei, 'Administrador');
    
        // recursos
        ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_acesso_arquivo_metadados', 'controlador.php?acao=protocolo_integrado_acesso_arquivo_metadados');
        $objRecursoParametrosDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_parametros', 'controlador.php?acao=protocolo_integrado_configurar_parametros');
        ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_publicacao', 'controlador.php?acao=protocolo_integrado_configurar_publicacao');
        ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_forcar_reenvio', 'controlador.php?acao=protocolo_integrado_forcar_reenvio');
        ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_alterar', 'controlador.php?acao=protocolo_integrado_mensagens_alterar');
        $objRecursoMensagensDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_listar', 'controlador.php?acao=protocolo_integrado_mensagens_listar');
        $objRecursoMonitoramentoDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_monitoramento', 'controlador.php?acao=protocolo_integrado_monitoramento');
    
        // item_menu
        $numIdMenuSei = ScriptSip::obterIdMenu($numIdSistemaSei, 'Principal');
        $numIdItemMenuSeiAdministracao = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Administração');
        ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoParametrosDTO->getNumIdRecurso(), 'Parâmetros', 10);
        ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoMensagensDTO->getNumIdRecurso(), 'Configuração das Mensagens', 20);
        ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, $objRecursoMonitoramentoDTO->getNumIdRecurso(), 'Monitoramento', 30);
    }
    
    
    public function versao_1_1_5() {
        $numIdSistemaSei = ScriptSip::obterIdSistema('SEI');
        $numIdPerfilSeiAdministrador = ScriptSip::obterIdPerfil($numIdSistemaSei, 'Administrador');
        $numIdMenuSei = ScriptSip::obterIdMenu($numIdSistemaSei, 'Principal');
        $numIdItemMenuSeiAdministracao = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Administração');
    
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MÓDULO PROTOCOLO INTEGRADO NA BASE DO SIP...');
    
        //criando os recursos e vinculando-os aos perfil Administrador
        $objRecursoConfigurarParametrosDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_configurar_parametros');
        $objRecursoMensagensListarDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_mensagens_listar');
        $objRecursoMonitoramentoDTO = ScriptSip::adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'protocolo_integrado_monitoramento');
    
        //criando Administração -> Administração do Módulo
        $objItemMenuDTOControleProcesso = ScriptSip::adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Protocolo Integrado', 0);
    
        //criando Protocolo Integrado -> Configuração de Parâmetros
        ScriptSip::adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOControleProcesso->getNumIdItemMenu(),
            $objRecursoConfigurarParametrosDTO->getNumIdRecurso(),
            'Parâmetros',
            10
        );
    
        //criando Protocolo Integrado -> Configuração de Mensagens
        ScriptSip::adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOControleProcesso->getNumIdItemMenu(),
            $objRecursoMensagensListarDTO->getNumIdRecurso(),
            'Configuração das mensagens',
            20
        );
    
        ScriptSip::adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOControleProcesso->getNumIdItemMenu(),
            $objRecursoMonitoramentoDTO->getNumIdRecurso(),
            'Monitoramento',
            30
        );
    
        ScriptSip::adicionarAuditoria($numIdSistemaSei, "Geral", array(
            'protocolo_integrado_acesso_arquivo_metadados',
            'protocolo_integrado_configurar_parametros',
            'protocolo_integrado_configurar_publicacao',
            'protocolo_integrado_mensagens_alterar',
            'protocolo_integrado_mensagens_listar',
            'protocolo_integrado_monitoramento'
        ));
    
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Geral');
        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);
    
      if($objRegraAuditoriaDTO){
          $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
          $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
          $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
        
          $objSistemaRN = new SistemaRN();
          $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
      }
    
    }
    
    public function versao_2_0_0() {
    }
    
    public function versao_3_0_0() {
        // Remoção de menu de consiguração do módulo devido a transição das configurações técnicas para o arquivo de configuração ConfiguracaoModProtocoloIntegrado.php
        $numIdSistemaSei = ScriptSip::obterIdSistema('SEI');
        $numIdMenuSei = ScriptSip::obterIdMenu($numIdSistemaSei, 'Principal');
        $numIdItemMenuPai = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Protocolo Integrado');
        $numIdItemMenu = ScriptSip::obterIdItemMenu($numIdSistemaSei, $numIdMenuSei, 'Parâmetros', $numIdItemMenuPai);
        ScriptSip::removerItemMenu($numIdSistemaSei, $numIdMenuSei, $numIdItemMenu);
        ScriptSip::removerRecurso($numIdSistemaSei, 'protocolo_integrado_configurar_parametros');

        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_acesso_arquivo_metadados', 'md_pi_acesso_arquivo_metadados');
        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_configurar_publicacao', 'md_pi_configurar_publicacao');
        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_forcar_reenvio', 'md_pi_forcar_reenvio');
        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_mensagens_alterar', 'md_pi_mensagens_alterar');
        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_mensagens_listar', 'md_pi_mensagens_listar');
        ScriptSip::renomearRecurso($numIdSistemaSei, 'protocolo_integrado_monitoramento', 'md_pi_monitoramento');

    }

    public function versao_sem_alteracao_banco() {
    }
  }

    session_start();

    SessaoSip::getInstance(false);

    BancoSip::getInstance()->setBolScript(true);

    $VersaoProtocoloIntegradoRN = new VersaoProtocoloIntegradoRN();
    $VersaoProtocoloIntegradoRN->verificarVersaoInstalada();
    $VersaoProtocoloIntegradoRN->setStrNome('SIP');
    $VersaoProtocoloIntegradoRN->setStrVersaoAtual(VERSAO_MODULO_PI);
    $VersaoProtocoloIntegradoRN->setStrParametroVersao('PI_VERSAO');
    $VersaoProtocoloIntegradoRN->setArrVersoes(array(
        '0.0.0' => 'versao_0_0_0',
        '1.1.2' => 'versao_1_1_2',
        '1.1.5' => 'versao_1_1_5',
        '2.0.*' => 'versao_2_0_0',
        '2.1.*' => 'versao_sem_alteracao_banco',
        '3.0.*' => 'versao_3_0_0',
        '3.0.1' => 'versao_sem_alteracao_banco'
    ));

    $VersaoProtocoloIntegradoRN->setStrVersaoInfra('1.595.1');
    $VersaoProtocoloIntegradoRN->setBolMySql(true);
    $VersaoProtocoloIntegradoRN->setBolOracle(true);
    $VersaoProtocoloIntegradoRN->setBolSqlServer(true);
    $VersaoProtocoloIntegradoRN->setBolPostgreSql(true);
    $VersaoProtocoloIntegradoRN->setBolErroVersaoInexistente(true);

    $VersaoProtocoloIntegradoRN->atualizarVersao();
} catch (Exception $e) {
    echo (InfraException::inspecionar($e));
  try {
      LogSIP::getInstance()->gravar(InfraException::inspecionar($e));
  } catch (Exception $e) {
  }
    exit(1);
}
