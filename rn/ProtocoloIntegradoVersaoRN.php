<?php
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 07/05/2013 - criado por mga
*
*/

require_once dirname(__FILE__).'/../../../../SEI.php';
require_once dirname(__FILE__).'/../../../../../../sip/web/Sip.php';
require_once dirname(__FILE__).'/../../../../../../sip/web/BancoSip.php';

class ProtocoloIntegradoVersaoRN extends InfraRN {

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '2.1.2';
    private $nomeParametroModulo = 'PI_VERSAO';
    private $historicoVersoes = array('1.1.1','1.1.2','1.1.3','1.1.4','1.1.5','2.0.0');

    public function __construct(){
        //parent::__construct(); 
        $this->inicializar(' SEI - INICIALIZAR ');
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function inicializar($strTitulo){
    
        ini_set('max_execution_time','0');
        ini_set('memory_limit','-1');
        
        try {
            @ini_set('zlib.output_compression','0');
            @ini_set('implicit_flush', '1');
        } catch(Exception $e) {}
        
        BancoSEI::getInstance()->abrirConexao();
        BancoSEI::getInstance()->abrirTransacao();
        
        ob_implicit_flush();
        
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(true);
        InfraDebug::getInstance()->setBolEcho(true);
        InfraDebug::getInstance()->limpar();
        
        $this->logar($strTitulo);
        
    }

    private function logar($strMsg){
        InfraDebug::getInstance()->gravar($strMsg);
        flush();
        //para encher o buffer e fazer o flush
        //echo str_repeat(' ',64*1024);  flush();
        //LogSEI::getInstance()->gravar($strMsg);
    }

    private function finalizar($strMsg=null, $bolErro){

        if (!$bolErro) {
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $this->logar('TEMPO TOTAL DE EXECUCAO: ' . $this->numSeg . ' s');
        } else {
            $strMsg = 'ERRO: '.$strMsg;
        }
        
        if ($strMsg!=null){
            $this->logar($strMsg);
        }

        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        BancoSEI::getInstance()->cancelarTransacao();
        BancoSEI::getInstance()->fecharConexao();
        InfraDebug::getInstance()->limpar();
        $this->numSeg = 0;
        die;
    
    }


    /**
    * @throws InfraException
    */
    protected function atualizarVersaoControlado(){
        
        try {
            
            if (!(BancoSEI::getInstance() instanceof InfraMySql) && !(BancoSEI::getInstance() instanceof InfraSqlServer) && !(BancoSEI::getInstance() instanceof InfraOracle)){
                $this->finalizar('BANCO DE DADOS NAO SUPORTADO: '.get_parent_class(BancoSEI::getInstance()),true);
            }
            
            //Selecionando versão a ser instalada
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $strVersaoPreviaModuloProtocoloIntegrado = $objInfraParametro->getValor('PI_VERSAO', false);
            
            $instalacao = array();
            switch($this->versaoAtualDesteModulo) {
                case '1.1.2':
                    // Versão do plugin com suporte apenas ao Mysql
                    $instalacao = $this->instalarv112($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                case '1.1.3':
                    //Versão do plugin com suporte multibancos : Mysql,SqlServer e Oracle
                    $instalacao = $this->instalarv113($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                case '1.1.4':
                    //Versão do plugin com suporte multibancos : Mysql,SqlServer e Oracle
                    $instalacao = $this->instalarv114($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                case '1.1.5':
                    //Versão do plugin com suporte multibancos : Mysql,SqlServer e Oracle
                    $instalacao = $this->instalarv115($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                case '2.0.0':
                    //Versão com suporte ao SEI 3.0
                    $instalacao = $this->instalarv200($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                case '2.1.2':
                    $instalacao = $this->instalarv212($strVersaoPreviaModuloProtocoloIntegrado);
                    break;
                default:
                    $instalacao["operacoes"] = null;
                    $instalacao["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão do módulo".$strVersaoPreviaModuloProtocoloIntegrado." inválida";
                    break;      
            }
            if (isset($instalacao["erro"])) {
                 $this->finalizar($instalacao["erro"],true);
            } else {
                 $this->logar("Instalação/Atualização realizada com sucesso");
            }
            
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
    
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
            
            BancoSEI::getInstance()->confirmarTransacao();
            BancoSEI::getInstance()->fecharConexao();
            InfraDebug::getInstance()->limpar();
            
        } catch(Exception $e) {
            
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            
            BancoSEI::getInstance()->cancelarTransacao();
            BancoSEI::getInstance()->fecharConexao();
    
            InfraDebug::getInstance()->limpar();
            throw new InfraException('Erro instalando/atualizando módulo do protocolo integrado no SEI.', $e);
                    
        }
    
    }
  
    private function instalarv212($strVersaoPreviaModuloProtocoloIntegrado) {
        // Versão 2.1.2 não gerou modificações no banco
        $resultado = array();
        if(trim($strVersaoPreviaModuloProtocoloIntegrado)==trim($this->versaoAtualDesteModulo)){
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." já instalada";
            return $resultado;
        }else if($strVersaoPreviaModuloProtocoloIntegrado=='2.0.0'){

            BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\'PI_VERSAO\'');
            
        }else{

            $this->instalarv115($strVersaoPreviaModuloProtocoloIntegrado);
        }

        // Configurar SIP
        //$this->configurarSIP();
    }
  
    private function instalarv200($strVersaoPreviaModuloProtocoloIntegrado) {
        // Versão 2.0.0 não gerou modificações no banco
        $resultado = array();
        if(trim($strVersaoPreviaModuloProtocoloIntegrado)==trim($this->versaoAtualDesteModulo)){
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." já instalada";
            return $resultado;
        }else if($strVersaoPreviaModuloProtocoloIntegrado=='1.1.5'){

            BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\'PI_VERSAO\'');
            
        }else{

            $this->instalarv115($strVersaoPreviaModuloProtocoloIntegrado);
        }

        // Configurar SIP
        //$this->configurarSIP();
    }
  
    private function configurarSIP() {
        BancoSIP::getInstance()->abrirConexao();
        BancoSIP::getInstance()->abrirTransacao();
        
        // recursos
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_acesso_arquivo_metadados'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
               (select max(s.id_recurso)+1 from recurso s), 'protocolo_integrado_acesso_arquivo_metadados', 'Visualização do arquivo XML que foi gerado para um pacote de envio ao PI',
               'controlador.php?acao=protocolo_integrado_acesso_arquivo_metadados', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_configurar_parametros'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s), 'protocolo_integrado_configurar_parametros', 'Configuração dos Parametros Gerais do Modulo',
                'controlador.php?acao=protocolo_integrado_configurar_parametros', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_configurar_publicacao'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_configurar_publicacao', 'Opção de configurar quais históricos sobem ou não pro PI',
                'controlador.php?acao=protocolo_integrado_configurar_publicacao', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_forcar_reenvio'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_forcar_reenvio', 'Forçar reenvio de dados a partir da tela de monitoramento do PI',
                'controlador.php?acao=protocolo_integrado_forcar_reenvio', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_mensagens_alterar'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_mensagens_alterar',
                'Opção de configurar qual a mensagem que será traduzida ao PI, uma vez que ela já foi configurada para subir', 'controlador.php?acao=protocolo_integrado_mensagens_alterar', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_mensagens_listar'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_mensagens_listar', 'Listagem dos Históricos que estão configurados para serem publicados no PI',
                'controlador.php?acao=protocolo_integrado_mensagens_listar', 'S');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from recurso where id_sistema=(select id_sistema from sistema where sigla='SEI') and nome='protocolo_integrado_monitoramento'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO recurso (id_sistema, id_recurso, nome, descricao, caminho, sin_ativo) VALUES ((select id_sistema from sistema where sigla='SEI'),
                (select max(s.id_recurso)+1 from recurso s),  'protocolo_integrado_monitoramento', 'Monitoramento da Integração', 'controlador.php?acao=protocolo_integrado_monitoramento', 'S');");
        }
        
        // perfil_recursos
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_acesso_arquivo_metadados' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'),
                (select id_recurso from recurso where nome='protocolo_integrado_acesso_arquivo_metadados' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_publicacao' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_configurar_publicacao' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_forcar_reenvio' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_forcar_reenvio' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_alterar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_mensagens_alterar' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_recurso where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_recurso (id_perfil, id_sistema, id_recurso) VALUES ((select id_perfil from perfil where nome='Administrador' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_recurso from recurso where
                nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        // item_menu
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_sistema=(select id_sistema from sistema where sigla='SEI')
        and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Administração' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and rotulo='Protocolo Integrado'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, rotulo, sequencia, sin_ativo, sin_nova_janela) VALUES
                ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im), (select id_sistema
                from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select im.id_item_menu from
                item_menu im where im.rotulo='Administração' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), 'Protocolo Integrado', '0', 'S', 'N');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_sistema=(select id_sistema from sistema where sigla='SEI')
        and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and rotulo='Parâmetros'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela)
                VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im),
                (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select
                im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_recurso
                from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI')), 'Parâmetros', '10', 'S', 'N');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_sistema=(select id_sistema from sistema where sigla='SEI')
        and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and rotulo='Configuração das Mensagens'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela)
                VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im),
                (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')),
                (select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')),
                (select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI')),
                'Configuração das Mensagens', '20', 'S', 'N');");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from item_menu where id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_sistema=(select id_sistema from sistema where sigla='SEI')
        and id_menu_pai=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
        and rotulo='Monitoramento'");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO item_menu (id_menu, id_item_menu, id_sistema, id_menu_pai, id_item_menu_pai, id_recurso, rotulo, sequencia, sin_ativo, sin_nova_janela)
                VALUES ((select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select max(im.id_item_menu)+1 from item_menu im),
                (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')),
                (select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')),
                (select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI')),
                'Monitoramento', '30', 'S', 'N');");
        }
        
        // rel_perfil_item_menu
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Parâmetros' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil
                where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu
                from menu where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from
                sistema where sigla='SEI') and rotulo='Parâmetros' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and
                im.id_sistema=(select id_sistema from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_configurar_parametros' and
                id_sistema=(select id_sistema from sistema where sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Configuração das Mensagens' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil where
                nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu
                where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI')
                and rotulo='Configuração das Mensagens' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema
                from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_mensagens_listar' and id_sistema=(select id_sistema from sistema where
                sigla='SEI')));");
        }
        
        $numCountElementos = BancoSIP::getInstance()->executarSql("select * from rel_perfil_item_menu where id_perfil=(select id_perfil from perfil where nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_sistema=(select id_sistema from sistema where sigla='SEI')
            and id_menu=(select id_menu from menu where id_sistema=(select id_sistema from sistema where sigla='SEI'))
            and id_item_menu=(select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where sigla='SEI') and rotulo='Monitoramento' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select id_sistema from sistema where sigla='SEI')))
            and id_recurso=(select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from sistema where sigla='SEI'))");
        if ($numCountElementos==0){
            BancoSIP::getInstance()->executarSql("INSERT INTO rel_perfil_item_menu (id_perfil, id_sistema, id_menu, id_item_menu, id_recurso) VALUES ((select id_perfil from perfil where
                nome='Administrador' and id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_sistema from sistema where sigla='SEI'), (select id_menu from menu
                where id_sistema=(select id_sistema from sistema where sigla='SEI')), (select id_item_menu from item_menu where id_sistema=(select id_sistema from sistema where
                sigla='SEI') and rotulo='Monitoramento' and id_item_menu_pai=(select im.id_item_menu from item_menu im where im.rotulo='Protocolo Integrado' and im.id_sistema=(select
                id_sistema from sistema where sigla='SEI'))), (select id_recurso from recurso where nome='protocolo_integrado_monitoramento' and id_sistema=(select id_sistema from
                sistema where sigla='SEI')));");
        }
        
        BancoSIP::getInstance()->confirmarTransacao();
        BancoSIP::getInstance()->fecharConexao();
    }
  
    private function instalarv115($strVersaoPreviaModuloProtocoloIntegrado){
    
        $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
        $objProtocoloIntegradoParametrosRN  = new ProtocoloIntegradoParametrosRN();
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        
        $resultado = array();
        if (strlen(ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI)!=ProtocoloIntegradoParametrosRN::$NUM_CARACTERES_CHAVE_PI) {
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI.É necessário definir uma chave de 16 caracteres para variável CHAVE_MODULO_PI no arquivo ProtocoloIntegradoParametrosRN.php ";
            return $resultado;
        }
        
        if (trim($strVersaoPreviaModuloProtocoloIntegrado)==trim($this->versaoAtualDesteModulo)) {
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." já instalada";
            return $resultado;
        } else {
            $this->instalarv114($strVersaoPreviaModuloProtocoloIntegrado);
        }
        if(!InfraString::isBolVazia($strVersaoPreviaModuloProtocoloIntegrado)){
      
         
          $objInfraSequencia = new InfraSequencia(BancoSEI::getInstance());
          $objInfraSequenciaBD = new InfraSequenciaBD(BancoSEI::getInstance());
          $objInfraSequenciaDTO = new InfraSequenciaDTO();
          $objInfraSequenciaDTO->setStrNome('infra_agendamento_tarefa');
          $objInfraSequenciaDTO->retDblNumAtual();
          $objInfraSequencia = $objInfraSequenciaBD->consultar($objInfraSequenciaDTO);
          $numProximoValorSequencia = $objInfraSequencia->getDblNumAtual();
          $numMaxIdAgendamento = $this->getMaxIdAgendamento();

          if($numProximoValorSequencia<$numMaxIdAgendamento){

                
                $objInfraSequenciaDTO = new InfraSequenciaDTO();
          
                $objInfraSequenciaDTO->setDblNumAtual($numMaxIdAgendamento);
                $objInfraSequenciaDTO->setStrNome('infra_agendamento_tarefa');
                $objInfraSequenciaBD->alterar($objInfraSequenciaDTO);

          }

      }
      $objAgendamentoDTO  = $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado','Processo de Publicação do PI','D','2');
      $objAgendamentoDTO  = $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado','Agendamento do alarme de e-mail disparado quando há falha na publicação de pacotes','D','17');
      $objAgendamentoDTO  = $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados','Agendamento do alarme de e-mail disparado quando novos pacotes não estão sendo gerados','D','12');
      
      $objInfraMetaBD->excluirChaveEstrangeira('md_pi_mensagem','fk_md_pi_mensagem_tarefa');

      BancoSEI::getInstance()->executarSql('ALTER TABLE md_pi_mensagem ADD CONSTRAINT fk_md_pi_mensagem_tarefa FOREIGN KEY (id_tarefa) REFERENCES tarefa (id_tarefa) ON DELETE CASCADE');
 
      BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\'PI_VERSAO\'');
        
    }

    private function instalarv114($strVersaoPreviaModuloProtocoloIntegrado){

        $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
        $objProtocoloIntegradoParametrosRN  = new ProtocoloIntegradoParametrosRN();

        $resultado = array();
        if( strlen(ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI)!=ProtocoloIntegradoParametrosRN::$NUM_CARACTERES_CHAVE_PI){

            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI.É necessário definir uma chave de 16 caracteres para variável CHAVE_MODULO_PI no arquivo ProtocoloIntegradoParametrosRN.php ";
            return $resultado;
        }
        if(InfraString::isBolVazia($strVersaoPreviaModuloProtocoloIntegrado)|| trim($strVersaoPreviaModuloProtocoloIntegrado)=='1.1.2'){

              $instalacao = $this->instalarv113($strVersaoPreviaModuloProtocoloIntegrado);
              if(!isset($instalacao["erro"])){
                    return $this->instalarv114('1.1.3');  
              }else{

                  return $instalacao;
              }
                
        }else if(trim($strVersaoPreviaModuloProtocoloIntegrado)=='1.1.3'){

            //Criando a tabela de pacotes nos três bancos
            if (BancoSEI::getInstance() instanceof InfraMySql){
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros modify column senha_webservice varchar(100)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros alter column senha_webservice varchar(100)");
            }else if (BancoSEI::getInstance() instanceof InfraOracle){
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros modify( senha_webservice varchar(100))");
            }
            $objProtocoloIntegradoParametrosDTO->retTodos();
        
            $objParametrosRetornados = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);
            
            if(strlen(trim($objParametrosRetornados->getStrSenhaWebservice()))>0){
                $senhaEncriptada = rawurlencode($objProtocoloIntegradoParametrosRN->encriptaSenha(trim($objParametrosRetornados->getStrSenhaWebservice())));
                $objParametrosRetornados->setStrSenhaWebservice($senhaEncriptada);
                $objProtocoloIntegradoParametrosRN->alterar($objParametrosRetornados);
            }
            BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\'PI_VERSAO\'');

        } else if (trim($strVersaoPreviaModuloProtocoloIntegrado)==trim($this->versaoAtualDesteModulo)) {
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." já instalada";
            return $resultado;
        }
  }
  
  private function instalarv113($strVersaoPreviaModuloProtocoloIntegrado){

        
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar(' INICIANDO OPERACOES DA INSTALACAO DA VERSAO 1.1.3 DO MODULO PROTOCOLO INTEGRADO NA BASE DO SEI');
        
        $erros = null;
        $versao = '1.1.3';

        $resultado = array();
        $resultado["operacoes"] = null;
        $comandosExecutados = '';
        if(InfraString::isBolVazia($strVersaoPreviaModuloProtocoloIntegrado)){

                //Criando a tabela de pacotes nos três bancos
                BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_pacote_envio (
                            id_md_pi_pacote_envio ".$objInfraMetaBD->tipoNumeroGrande()." NOT NULL,
                            id_protocolo ".$objInfraMetaBD->tipoNumeroGrande()." NOT NULL,
                            dth_metadados ".$objInfraMetaBD->tipoDataHora()."  NULL,
                            dth_situacao ".$objInfraMetaBD->tipoDataHora()."  NULL,
                            sta_integracao ".$objInfraMetaBD->tipoTextoFixo(2)." NOT NULL,
                            arquivo_metadados ".$objInfraMetaBD->tipoTextoGrande()." NULL,
                            arquivo_erro ".$objInfraMetaBD->tipoTextoGrande()." NULL,
                            num_tentativas_envio ".$objInfraMetaBD->tipoNumero()." DEFAULT '0',
                            dth_agendamento_executado ".$objInfraMetaBD->tipoDataHora()."  NULL)");

                $objInfraMetaBD->adicionarChavePrimaria('md_pi_pacote_envio','pk_id_md_pi_pacote_envio',array('id_md_pi_pacote_envio'));

                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pacote_pi_protocolo','md_pi_pacote_envio',array('id_protocolo'),'protocolo',array('id_protocolo'));

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint identity(1,1), campo char(1) null)');
                } else if (BancoSEI::getInstance() instanceof InfraOracle){
                    BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_pacote_envio', 1);
                }

                //Criando a tabela de monitoramento de processos nos três bancos
                BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_monitora_processos (
                            id_md_pi_monitora_processos ".$objInfraMetaBD->tipoNumeroGrande()."  NOT NULL,
                            id_atividade ".$objInfraMetaBD->tipoNumero()."  NOT NULL,
                            dth_cadastro ".$objInfraMetaBD->tipoDataHora()." NULL,
                            id_md_pi_pacote_envio ".$objInfraMetaBD->tipoNumeroGrande()." NOT NULL)");
                
                /*$objInfraMetaBD->adicionarChavePrimaria('md_pi_monitora_processos','pk_id_md_pi_monitora_processos',array('id_md_pi_monitora_processos'));*/

                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_ativ','md_pi_monitora_processos',array('id_atividade'),'atividade',array('id_atividade'));
                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_pacote','md_pi_monitora_processos',array('id_md_pi_pacote_envio'),'md_pi_pacote_envio',array('id_md_pi_pacote_envio')); 
                
                $objInfraMetaBD->criarIndice('md_pi_monitora_processos','i01_md_pi_monitora_processos',array('id_atividade'));
                $objInfraMetaBD->criarIndice('md_pi_monitora_processos','i02_md_pi_monitora_processos',array('id_md_pi_pacote_envio'));

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint identity(1,1), campo char(1) null)');
                } else if (BancoSEI::getInstance() instanceof InfraOracle){
                    BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_monitora_processos', 1);
                }

                //Criando a tabela de configuração de mensagens de publicação no Protocolo Integrado nos três bancos    
                BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_mensagem (
                            id_md_pi_mensagem ".$objInfraMetaBD->tipoNumeroGrande()." NOT NULL,
                            id_tarefa ".$objInfraMetaBD->tipoNumero()."  NULL, 
                            sin_publicar ".$objInfraMetaBD->tipoTextoFixo(1)." NOT NULL,
                            mensagem_publicacao ".$objInfraMetaBD->tipoTextoVariavel(255)." NOT NULL)");
              
                $objInfraMetaBD->adicionarChavePrimaria('md_pi_mensagem','pk_id_md_pi_mensagem',array('id_md_pi_mensagem'));
                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_mensagem_tarefa','md_pi_mensagem',array('id_tarefa'),'tarefa',array('id_tarefa'));

                $objInfraMetaBD->criarIndice('md_pi_mensagem','i01_md_pi_mensagem',array('id_tarefa'));

                //Criando a tabela de configuração de parâmetros do módulo  nos três bancos
                BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_parametros (
                            id_md_pi_parametros ".$objInfraMetaBD->tipoNumeroGrande()." NOT NULL,
                            url_webservice ".$objInfraMetaBD->tipoTextoVariavel(255)." NOT NULL,
                            quantidade_tentativas ".$objInfraMetaBD->tipoNumero()." NOT NULL,
                            email_administrador ".$objInfraMetaBD->tipoTextoVariavel(255)." NULL,
                            dth_ultimo_processamento ".$objInfraMetaBD->tipoDataHora()."   NULL,
                            login_webservice ".$objInfraMetaBD->tipoTextoVariavel(10)."  NULL,
                            senha_webservice ".$objInfraMetaBD->tipoTextoVariavel(20)."  NULL,
                            sin_executando_publicacao ".$objInfraMetaBD->tipoTextoFixo(1)."  DEFAULT 'N',
                            sin_publicacao_restritos ".$objInfraMetaBD->tipoTextoFixo(1)."  DEFAULT 'S',
                            num_atividades_carregar ".$objInfraMetaBD->tipoNumero()."  NULL)");
                    
                $objInfraMetaBD->adicionarChavePrimaria('md_pi_parametros','pk_id_md_pi_parametros',array('id_md_pi_parametros'));
              
                //Inserindo as atividades que devem ser enviadas,por padrão,ao Protocolo Integrado  
                BancoSEI::getInstance()->executarSql("insert into md_pi_mensagem (id_md_pi_mensagem, id_tarefa,sin_publicar,mensagem_publicacao) select id_tarefa, id_tarefa,'N',nome from tarefa");
                
                $objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
                $tarefasPublicacao =  $objProtocoloIntegradoRN->montaTarefasPadraoPublicacao();
                foreach($tarefasPublicacao as $key=>$value){

                        BancoSEI::getInstance()->executarSql("UPDATE md_pi_mensagem set sin_publicar = 'S' where id_tarefa = ".$value." ");

                }
                BancoSEI::getInstance()->executarSql("INSERT INTO md_pi_parametros (id_md_pi_parametros,url_webservice,quantidade_tentativas,email_administrador,
                    login_webservice,senha_webservice,sin_executando_publicacao,sin_publicacao_restritos,num_atividades_carregar) VALUES (1,'https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl',15,'','','','N','S',100000)");
                
                BancoSEI::getInstance()->executarSql('insert into infra_parametro(nome,valor) values(\'PI_VERSAO\', \''.$versao.'\')'); 
                
                
                
        }else if(trim($strVersaoPreviaModuloProtocoloIntegrado)==$versao){


                $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." já instalada";
                return $resultado;

        }else if(trim($strVersaoPreviaModuloProtocoloIntegrado)=='1.1.2'){

                $objProtocoloIntegradoBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
                $chavesEstrangeirasProtocoloIntegrado = $objProtocoloIntegradoBD->recuperarChavesEstrangeirasv112();

                foreach($chavesEstrangeirasProtocoloIntegrado as $key=>$arrChaveEstrangeiraProtocoloIntegrado){
                    foreach($arrChaveEstrangeiraProtocoloIntegrado as $k=>$objChave){

                        $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado',$objChave);
           
                    }  
                }
                        
                $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
                $chavesEstrangeirasPacote = $objPacoteBD->recuperarChavesEstrangeirasv112();

                foreach($chavesEstrangeirasPacote as $key=>$arrChaveEstrangeiraPacote){
                    //$this->logar($chaveEstrangeiraPacote);
                    foreach($arrChaveEstrangeiraPacote as $k=>$objChave){

                        $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado_pacote_envio',$objChave);          
                    }  
                }

                $objProtocoloIntegradoMonitoramentoProcessosBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
                $chavesEstrangeirasMonitoramentoProcessos = $objProtocoloIntegradoMonitoramentoProcessosBD->recuperarChavesEstrangeirasv112();
                    
                foreach($chavesEstrangeirasMonitoramentoProcessos as $key=>$arrChaveEstrangeiraMonitoramentoProcessos){

                      //$this->logar($chaveEstrangeiraPacote);
                      foreach($arrChaveEstrangeiraMonitoramentoProcessos as $k=>$objChave){
                        
                          $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado_monitoramento_processos',$objChave);          
                      }  
      
                }
                
                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql('RENAME TABLE protocolo_integrado to md_pi_mensagem');
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado', 'md_pi_mensagem';");
                } 

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_mensagem CHANGE id_protocolo_integrado id_md_pi_mensagem BIGINT(20)");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_mensagem.id_protocolo_integrado' , 'id_md_pi_mensagem', 'COLUMN' ");
                } 

                /*$objInfraMetaBD->adicionarChavePrimaria('md_pi_mensagem','pk_id_md_pi_mensagem',array('id_md_pi_mensagem'));*/
                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_mensagem_tarefa','md_pi_mensagem',array('id_tarefa'),'tarefa',array('id_tarefa'));
                
                $arrPacotesPrevios = array();
                $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
                if (BancoSEI::getInstance() instanceof InfraMySql){
                    
                    
                    
                    BancoSEI::getInstance()->executarSql('RENAME TABLE protocolo_integrado_pacote_envio to md_pi_pacote_envio');

                    
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){

                    BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_pacote_envio', 'md_pi_pacote_envio';");

                    $arrPacotesPrevios = $objPacoteBD->recuperarColunaTabelaPacote('id_protocolo_integrado_pacote_envio');


                } 
                
                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio CHANGE id_protocolo_integrado_pacote_envio id_md_pi_pacote_envio BIGINT(20)");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio add  id_md_pi_pacote_envio bigint;");

                    BancoSEI::getInstance()->executarSql("update md_pi_pacote_envio set id_md_pi_pacote_envio=id_protocolo_integrado_pacote_envio"); 
                    $nomeRestricaoChavePrimaria = $objPacoteBD->recuperarChavePrimaria();
                    
                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio drop constraint ".$nomeRestricaoChavePrimaria."; ");

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio drop column id_protocolo_integrado_pacote_envio; ");

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio alter column id_md_pi_pacote_envio bigint not null; ");

                    $objInfraMetaBD->adicionarChavePrimaria('md_pi_pacote_envio','pk_id_md_pi_pacote_envio',array('id_md_pi_pacote_envio'));
                } 

                                

                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pacote_pi_protocolo','md_pi_pacote_envio',array('id_protocolo'),'protocolo',array('id_protocolo'));


                if(BancoSEI::getInstance() instanceof InfraSqlServer){

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio ALTER COLUMN arquivo_metadados ".$objInfraMetaBD->tipoTextoGrande()." NULL");
                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio ALTER COLUMN arquivo_erro ".$objInfraMetaBD->tipoTextoGrande()." NULL");
                }
                

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("RENAME TABLE protocolo_integrado_parametros to md_pi_parametros");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_parametros', 'md_pi_parametros';");
                } 

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_parametros CHANGE id_protocolo_integrado_parametros id_md_pi_parametros BIGINT(20)");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_parametros.id_protocolo_integrado_parametros' , 'id_md_pi_parametros', 'COLUMN' ");
                } 

                
                $arrProcessosMonitoradosPrevios = array();
                $objMonitoramentoProcessosBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
                if (BancoSEI::getInstance() instanceof InfraMySql){
                    
                    BancoSEI::getInstance()->executarSql( "RENAME TABLE protocolo_integrado_monitoramento_processos to md_pi_monitora_processos");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    
                    
                    $arrProcessosMonitoradosPrevios = $objMonitoramentoProcessosBD->recuperarIdsTabelaMonitoramentov112();
                    BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_monitoramento_processos', 'md_pi_monitora_processos';");
                } 
                
                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_monitora_processos CHANGE id_protocolo_integrado_monitoramento_processos id_md_pi_monitora_processos BIGINT(20)");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos add  id_md_pi_monitora_processos bigint;");
                    $objMonitoraProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
                    foreach($arrProcessosMonitoradosPrevios as $key=>$value){

                        BancoSEI::getInstance()->executarSql('update md_pi_monitora_processos set id_md_pi_monitora_processos=\''.$value->getNumIdProtocoloIntegradoMonitoramentoProcessos().'\' where id_protocolo_integrado_monitoramento_processos=\''.$value->getNumIdProtocoloIntegradoMonitoramentoProcessos().'\';');

                    }
                    $nomeRestricaoChavePrimaria = $objMonitoramentoProcessosBD->recuperarChavePrimaria();
                    
                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos drop constraint ".$nomeRestricaoChavePrimaria."; ");

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos drop column id_protocolo_integrado_monitoramento_processos; ");

                    BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos alter column id_md_pi_monitora_processos bigint not null; ");

                    $objInfraMetaBD->adicionarChavePrimaria('md_pi_monitora_processos','pk_id_md_pi_monitora_processos',array('id_md_pi_monitora_processos'));
                    /*
                    BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_monitora_processos.id_protocolo_integrado_monitoramento_processos' , 'id_md_pi_monitora_processos', 'COLUMN' ");*/
                } 

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_monitora_processos CHANGE id_protocolo_integrado_pacote_envio id_md_pi_pacote_envio BIGINT(20)");
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){

                    
                    BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_monitora_processos.id_protocolo_integrado_pacote_envio' , 'id_md_pi_pacote_envio', 'COLUMN' ");
                } 

                /*$objInfraMetaBD->adicionarChavePrimaria('md_pi_monitora_processos','pk_id_md_pi_monitora_processos',array('id_md_pi_monitora_processos'));*/

                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_ativ','md_pi_monitora_processos',array('id_atividade'),'atividade',array('id_atividade'));
                $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_pacote','md_pi_monitora_processos',array('id_md_pi_pacote_envio'),'md_pi_pacote_envio',array('id_md_pi_pacote_envio')); 
                    
                $objInfraMetaBD->criarIndice('md_pi_monitora_processos','i01_md_pi_monitora_processos',array('id_atividade'));
                $objInfraMetaBD->criarIndice('md_pi_monitora_processos','i02_md_pi_monitora_processos',array('id_md_pi_pacote_envio'));


                /*$objProtocoloIntegradoPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
                $objProtocoloIntegradoPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
                $numPacotes =  $objProtocoloIntegradoPacoteRN->contar($objProtocoloIntegradoPacoteDTO);*/

                $maxIdPacote = $this->getMaxIdPacote();

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = '.($maxIdPacote+1));
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint identity('.($maxIdPacote+1).',1), campo char(1) null)');
                } else if (BancoSEI::getInstance() instanceof InfraOracle){
                    BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_pacote_envio', ($maxIdPacote+1));
                }

                /*$objProtocoloIntegradoMonitoraProcessoRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
                $objProtocoloIntegradoMonitoraProcessoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
                $numAtividadesPacotes =  $objProtocoloIntegradoMonitoraProcessoRN->contar($objProtocoloIntegradoMonitoraProcessoDTO);*/

                $maxIdMonitoramentoProcesso = $this->getMaxIdMonitoramentoProcesso();

                if (BancoSEI::getInstance() instanceof InfraMySql){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = '.($maxIdMonitoramentoProcesso+1));
                } else if (BancoSEI::getInstance() instanceof InfraSqlServer){
                    BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint identity('.($maxIdMonitoramentoProcesso+1).',1), campo char(1) null)');
                } else if (BancoSEI::getInstance() instanceof InfraOracle){
                    BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_monitora_processos', ($maxIdMonitoramentoProcesso+1));
                }

                BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$versao.'\' where nome=\'PI_VERSAO\';');
        } else if(trim($strVersaoPreviaModuloProtocoloIntegrado)<'1.1.2') {
            $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoPreviaModuloProtocoloIntegrado." não pode ser atualizada para versão ".$this->versaoAtualDesteModulo;
            return $resultado;
        }
        
        return $resultado;
        
  }
  private function instalarv112($strVersaoInstaladaModuloProtocoloIntegrado){

        $erros = null;
        $comandosExecutados = '';
        $resultado = array();
        $resultado["operacoes"] = null;
        
        //Verifica se não possui módulo prévios cadastrados
        if(InfraString::isBolVazia($strVersaoInstaladaModuloProtocoloIntegrado) || 
            intval($strVersaoInstaladaModuloProtocoloIntegrado)<=intval($this->versaoAtualDesteModulo)) {

            if (intval($strVersaoInstaladaModuloProtocoloIntegrado)==intval($this->versaoAtualDesteModulo)) {
                $resultado["erro"] = "Erro instalando/atualizando Módulo Protocolo Integrado no SEI. Versão ".$strVersaoInstaladaModuloProtocoloIntegrado." já instalada";
                return $resultado;
            }

            BancoSEI::getInstance()->executarSql("CREATE TABLE protocolo_integrado_pacote_envio (id_protocolo_integrado_pacote_envio bigint(20) NOT NULL AUTO_INCREMENT,id_protocolo bigint(20) NOT NULL,
                    dth_metadados datetime DEFAULT NULL,dth_situacao datetime DEFAULT NULL,sta_integracao char(2) NOT NULL,arquivo_metadados MEDIUMBLOB,arquivo_erro blob,num_tentativas_envio int(11) DEFAULT '0',
                    dth_agendamento_executado varchar(45) DEFAULT NULL,PRIMARY KEY (id_protocolo_integrado_pacote_envio),KEY fk_pacote_envio_protocolo_integrado_protocolo (id_protocolo),
                    CONSTRAINT fk_pacote_envio_protocolo_integrado_protocolo FOREIGN KEY (id_protocolo) REFERENCES protocolo (id_protocolo) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;");
            
            
            BancoSEI::getInstance()->executarSql("CREATE TABLE protocolo_integrado_monitoramento_processos (id_protocolo_integrado_monitoramento_processos bigint(20) NOT NULL AUTO_INCREMENT,
                    id_atividade int(11) NOT NULL,dth_cadastro datetime DEFAULT NULL,id_protocolo_integrado_pacote_envio bigint(20) NOT NULL,PRIMARY KEY (id_protocolo_integrado_monitoramento_processos),
                    KEY id_atividade_idx (id_atividade),KEY fk_protocolo_integrado_monitoramento_processos_pacote (id_protocolo_integrado_pacote_envio),
                    CONSTRAINT fk_protocolo_integrado_monitoramento_processos_atividade FOREIGN KEY (id_atividade) REFERENCES atividade (id_atividade) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT fk_protocolo_integrado_monitoramento_processos_pacote_envio FOREIGN KEY (id_protocolo_integrado_pacote_envio) 
                    REFERENCES protocolo_integrado_pacote_envio (id_protocolo_integrado_pacote_envio) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;");  
            
            
            BancoSEI::getInstance()->executarSql("CREATE TABLE protocolo_integrado (id_protocolo_integrado bigint(20) NOT NULL AUTO_INCREMENT,id_tarefa int(11) DEFAULT NULL, 
                    sin_publicar char(1) NOT NULL DEFAULT 'N',mensagem_publicacao varchar(255) NOT NULL,PRIMARY KEY (id_protocolo_integrado),KEY fk_protocolo_integrado_tarefa_idx (id_tarefa), 
                    CONSTRAINT fk_protocolo_integrado_tarefa FOREIGN KEY (id_tarefa) REFERENCES tarefa (id_tarefa) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB;");
            

            BancoSEI::getInstance()->executarSql("insert into protocolo_integrado (id_tarefa,sin_publicar,mensagem_publicacao) select id_tarefa,'N',nome from tarefa");

            $objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
            $tarefasPublicacao =  $objProtocoloIntegradoRN->montaTarefasPadraoPublicacao();
            foreach ($tarefasPublicacao as $key=>$value) {
                BancoSEI::getInstance()->executarSql("UPDATE protocolo_integrado SET sin_publicar='S' WHERE id_tarefa= ".$value." ");
            }
                
            BancoSEI::getInstance()->executarSql("CREATE TABLE protocolo_integrado_parametros (id_protocolo_integrado_parametros bigint(20) NOT NULL AUTO_INCREMENT,url_webservice varchar(255) NOT NULL,
                    quantidade_tentativas int(11) NOT NULL,email_administrador varchar(255) NOT NULL,dth_ultimo_processamento datetime DEFAULT NULL,login_webservice varchar(10) DEFAULT NULL,
                    senha_webservice varchar(20) DEFAULT NULL,sin_executando_publicacao char(1) NOT NULL DEFAULT 'N',sin_publicacao_restritos char(1) NOT NULL DEFAULT 'S',num_atividades_carregar int(11) 
                    DEFAULT NULL, PRIMARY KEY (id_protocolo_integrado_parametros)) ENGINE=InnoDB;");
            
            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from protocolo_integrado_parametros");
            if ($numCountElementos==0) {
                BancoSEI::getInstance()->executarSql("INSERT INTO protocolo_integrado_parametros (id_protocolo_integrado_parametros,url_webservice,quantidade_tentativas,email_administrador,
                    login_webservice,senha_webservice,sin_executando_publicacao,sin_publicacao_restritos,num_atividades_carregar) VALUES (1,'https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl'
                    ,15,'','','','N','S',100000);");
            }
            
            $bolExiste = BancoSEI::getInstance()->executarSql('select * from infra_parametro where nome=\'PI_VERSAO\'');
            if (InfraString::isBolVazia($strVersaoInstaladaModuloProtocoloIntegrado)) {
                BancoSEI::getInstance()->executarSql('insert into infra_parametro(nome,valor) values(\'PI_VERSAO\', \''.$this->versaoAtualDesteModulo.'\')');   
            } else {
                BancoSEI::getInstance()->executarSql('update infra_parametro set valor=\''.$this->versaoAtualDesteModulo.'\' where nome=\'PI_VERSAO\';');
            }

            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado'");
            if ($numCountElementos==0){

                $comando ="INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, sin_ativo,sin_sucesso) 
                    VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Processo de Publicação do PI', 'ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado', 
                    'D', '2', 'S','N')";
                $comandosExecutados .= '<label>'.$comando . '</label>'.'<br/>'.'<br/>';
                
                BancoSEI::getInstance()->executarSql($comando);
            }
            
            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados'");
            if ($numCountElementos==0){
                $comando = "INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, 
                    parametro, sin_ativo,sin_sucesso) VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Agendamento do alarme de e-mail disparado quando novos pacotes não 
                    estão sendo gerados', 'ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados', 'D', '12', '2', 'S','N')";
                $comandosExecutados .= '<label>'.$comando . '</label>'.'<br/>'.'<br/>';
                
                BancoSEI::getInstance()->executarSql($comando);
            }      
            
            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado'");
            if ($numCountElementos==0){

                $comando = "INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa,descricao,comando,sta_periodicidade_execucao,periodicidade_complemento,sin_ativo,sin_sucesso)
                    VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat),'Agendamento do alarme de e-mail disparado quando há falha na publocação de pacotes',
                    'ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado','D','17','S','N')";
                $comandosExecutados .= '<label>'.$comando . '</label>'.'<br/>'.'<br/>';
                
                BancoSEI::getInstance()->executarSql($comando);
            } 
            $this->configurarSIP();     
            
    }   
    return $resultado;
  }
  private function getMaxIdAgendamento(){
      
      $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
      $objAgendamentoRN = new InfraAgendamentoTarefaRN();
      $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
      $numMaxIdAgendamento = 0;
      
      $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
      $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
      $arrAgendamentos =  $objAgendamentoBD->listar($objAgendamentoDTO);
      foreach($arrAgendamentos as $key =>$value){

            $idAgendamento = $value->getNumIdInfraAgendamentoTarefa();
            if($idAgendamento>$numMaxIdAgendamento){

                $numMaxIdAgendamento = $idAgendamento;
            }
      }
      return $numMaxIdAgendamento;
       
      
  }
  private function getMaxIdPacote(){
              
      $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
      $objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
      $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
      $numMaxIdPacote = 0;
              
      $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD(BancoSEI::getInstance());
      $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
      $arrPacotes =  $objPacoteBD->listar($objPacoteDTO);
      foreach($arrPacotes as $key =>$value){

            $idPacote = $value->getNumIdProtocoloIntegradoPacoteEnvio();
            if($idPacote>$numMaxIdPacote){

                $numMaxIdPacote = $idPacote;
            }
      }
      return $numMaxIdPacote;


  }
  private function getMaxIdMonitoramentoProcesso(){
              
      $objMonitoramentoProcessoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
      $objMonitoramentoProcessoRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
      $objMonitoramentoProcessoDTO->retNumIdProtocoloIntegradoMonitoramentoProcessos();
      $numMaxIdMonitoramentoProcesso = 0;
              
      $objMonitoramentoProcessoBD = new ProtocoloIntegradoMonitoramentoProcessosBD(BancoSEI::getInstance());
      $objMonitoramentoProcessoDTO->retNumIdProtocoloIntegradoMonitoramentoProcessos();
      $arrMonitoramentoProcessos =  $objMonitoramentoProcessoBD->listar($objMonitoramentoProcessoDTO);
      foreach($arrMonitoramentoProcessos as $key =>$value){

            $idMonitoramentoProcesso = $value->getNumIdProtocoloIntegradoMonitoramentoProcessos();
            if($idMonitoramentoProcesso>$numMaxIdMonitoramentoProcesso){

                $numMaxIdMonitoramentoProcesso = $idMonitoramentoProcesso;
            }
      }
      return $numMaxIdMonitoramentoProcesso;


  }
  private function recuperaAgendamento($strComando){

       $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
       $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
       $objAgendamentoDTO->setStrComando($strComando);
       $objAgendamentoDTO->setBolExclusaoLogica(false);

       $objAgendamentoRN = new InfraAgendamentoTarefaRN();
       $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
       $objAgendamentoDTO =  $objAgendamentoBD->consultar($objAgendamentoDTO);
       
       return $objAgendamentoDTO;
       
    }
    private function cadastrarAgendamento($objAgendamentoDTO){

       $objAgendamentoRN = new InfraAgendamentoTarefaRN();
       $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
       $objAgendamentoDTO =  $objAgendamentoBD->cadastrar($objAgendamentoDTO);
       
       return $objAgendamentoDTO;
       
    }
    private function adicionarAgendamento($strComando, $strDescricao, $strPeriodicidadeExecucao,$strComplementoPeriodicidade,$strParametro=null){

        $objAgendamentoDTO = $this->recuperaAgendamento($strComando);
        if ($objAgendamentoDTO==null){

            $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $objAgendamentoDTO->setNumIdInfraAgendamentoTarefa(null);
            $objAgendamentoDTO->setStrComando($strComando);
            $objAgendamentoDTO->setStrDescricao($strDescricao);
            $objAgendamentoDTO->setStrStaPeriodicidadeExecucao($strPeriodicidadeExecucao);
            $objAgendamentoDTO->setStrPeriodicidadeComplemento($strComplementoPeriodicidade);
            if($strParametro==null){

                 $objAgendamentoDTO->setStrParametro('');
              
            }else{

                 $objAgendamentoDTO->setStrParametro($strParametro);
            }
            $objAgendamentoDTO->setDthUltimaExecucao(null);
            $objAgendamentoDTO->setDthUltimaConclusao(null);
            $objAgendamentoDTO->setStrSinSucesso('N');
            $objAgendamentoDTO->setStrEmailErro('');
            
            $objAgendamentoDTO->setStrSinAtivo('N');
            $objAgendamentoDTO = $this->cadastrarAgendamento($objAgendamentoDTO);
       }
       return $objAgendamentoDTO;
    }


    
}

?>