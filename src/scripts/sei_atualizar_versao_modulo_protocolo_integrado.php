<?

try {

    require_once dirname(__FILE__) . '/../../web/SEI.php';

    class VersaoProtocoloIntegradoRN extends InfraScriptVersao
    {

        public function __construct()
        {
            parent::__construct();
        }

        protected function inicializarObjInfraIBanco()
        {
            return BancoSEI::getInstance();
        }

        protected function verificarVersaoInstaladaControlado()
        {
            $objInfraParametroDTOFiltro = new InfraParametroDTO();
            $objInfraParametroDTOFiltro->setStrNome('PI_VERSAO');
            $objInfraParametroDTOFiltro->retStrNome();

            $objInfraParametroBD = new InfraParametroBD(BancoSEI::getInstance());
            $objInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTOFiltro);
            if (is_null($objInfraParametroDTO)) {
                $objInfraParametroDTO = new InfraParametroDTO();
                $objInfraParametroDTO->setStrNome('PI_VERSAO');
                $objInfraParametroDTO->setStrValor('0.0.0');
                $objInfraParametroBD->cadastrar($objInfraParametroDTO);
            }

            return $objInfraParametroDTO->getStrNome();
        }

        private function getMaxIdAgendamento()
        {
            $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
            $numMaxIdAgendamento = 0;
            $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
            $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
            $arrAgendamentos =  $objAgendamentoBD->listar($objAgendamentoDTO);
            foreach ($arrAgendamentos as $key => $value) {
                $idAgendamento = $value->getNumIdInfraAgendamentoTarefa();
                if ($idAgendamento > $numMaxIdAgendamento) {
                    $numMaxIdAgendamento = $idAgendamento;
                }
            }
            return $numMaxIdAgendamento;
        }

        private function getMaxIdPacote()
        {
            $numMaxIdPacote = 0;
            $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
            $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
            $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD(BancoSEI::getInstance());
            $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();
            $arrPacotes =  $objPacoteBD->listar($objPacoteDTO);
            foreach ($arrPacotes as $key => $value) {
                $idPacote = $value->getNumIdProtocoloIntegradoPacoteEnvio();
                if ($idPacote > $numMaxIdPacote) {
                    $numMaxIdPacote = $idPacote;
                }
            }
            return $numMaxIdPacote;
        }

        private function getMaxIdMonitoramentoProcesso()
        {
            $numMaxIdMonitoramentoProcesso = 0;
            $objMonitoramentoProcessoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
            $objMonitoramentoProcessoDTO->retNumIdProtocoloIntegradoMonitoramentoProcessos();
            $objMonitoramentoProcessoBD = new ProtocoloIntegradoMonitoramentoProcessosBD(BancoSEI::getInstance());
            $objMonitoramentoProcessoDTO->retNumIdProtocoloIntegradoMonitoramentoProcessos();
            $arrMonitoramentoProcessos =  $objMonitoramentoProcessoBD->listar($objMonitoramentoProcessoDTO);
            foreach ($arrMonitoramentoProcessos as $key => $value) {
                $idMonitoramentoProcesso = $value->getNumIdProtocoloIntegradoMonitoramentoProcessos();
                if ($idMonitoramentoProcesso > $numMaxIdMonitoramentoProcesso) {
                    $numMaxIdMonitoramentoProcesso = $idMonitoramentoProcesso;
                }
            }
            return $numMaxIdMonitoramentoProcesso;
        }

        private function recuperaAgendamento($strComando)
        {
            $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $objAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
            $objAgendamentoDTO->setStrComando($strComando);
            $objAgendamentoDTO->setBolExclusaoLogica(false);

            $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
            $objAgendamentoDTO =  $objAgendamentoBD->consultar($objAgendamentoDTO);

            return $objAgendamentoDTO;
        }

        private function cadastrarAgendamento($objAgendamentoDTO)
        {
            $objAgendamentoBD = new InfraAgendamentoTarefaBD(BancoSEI::getInstance());
            $objAgendamentoDTO =  $objAgendamentoBD->cadastrar($objAgendamentoDTO);
            return $objAgendamentoDTO;
        }

        private function adicionarAgendamento($strComando, $strDescricao, $strPeriodicidadeExecucao, $strComplementoPeriodicidade, $strParametro = null)
        {
            $objAgendamentoDTO = $this->recuperaAgendamento($strComando);
            if ($objAgendamentoDTO == null) {
                $objAgendamentoDTO = new InfraAgendamentoTarefaDTO();
                $objAgendamentoDTO->setNumIdInfraAgendamentoTarefa(null);
                $objAgendamentoDTO->setStrComando($strComando);
                $objAgendamentoDTO->setStrDescricao($strDescricao);
                $objAgendamentoDTO->setStrStaPeriodicidadeExecucao($strPeriodicidadeExecucao);
                $objAgendamentoDTO->setStrPeriodicidadeComplemento($strComplementoPeriodicidade);
                if ($strParametro == null) {
                    $objAgendamentoDTO->setStrParametro('');
                } else {
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


        public function versao_0_0_0($strVersaoAtual)
        {
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            //Criando a tabela de pacotes nos três bancos
            BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_pacote_envio (
            id_md_pi_pacote_envio " . $objInfraMetaBD->tipoNumeroGrande() . " NOT NULL,
            id_protocolo " . $objInfraMetaBD->tipoNumeroGrande() . " NOT NULL,
            dth_metadados " . $objInfraMetaBD->tipoDataHora() . "  NULL,
            dth_situacao " . $objInfraMetaBD->tipoDataHora() . "  NULL,
            sta_integracao " . $objInfraMetaBD->tipoTextoFixo(2) . " NOT NULL,
            arquivo_metadados " . $objInfraMetaBD->tipoTextoGrande() . " NULL,
            arquivo_erro " . $objInfraMetaBD->tipoTextoGrande() . " NULL,
            num_tentativas_envio " . $objInfraMetaBD->tipoNumero() . " DEFAULT '0',
            dth_agendamento_executado " . $objInfraMetaBD->tipoDataHora() . "  NULL)");

            $objInfraMetaBD->adicionarChavePrimaria('md_pi_pacote_envio', 'pk_id_md_pi_pacote_envio', array('id_md_pi_pacote_envio'));

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pacote_pi_protocolo', 'md_pi_pacote_envio', array('id_protocolo'), 'protocolo', array('id_protocolo'));

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint identity(1,1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_pacote_envio', 1);
            }

            //Criando a tabela de monitoramento de processos nos três bancos
            BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_monitora_processos (
                        id_md_pi_monitora_processos " . $objInfraMetaBD->tipoNumeroGrande() . "  NOT NULL,
                        id_atividade " . $objInfraMetaBD->tipoNumero() . "  NOT NULL,
                        dth_cadastro " . $objInfraMetaBD->tipoDataHora() . " NULL,
                        id_md_pi_pacote_envio " . $objInfraMetaBD->tipoNumeroGrande() . " NOT NULL)");

            /*$objInfraMetaBD->adicionarChavePrimaria('md_pi_monitora_processos','pk_id_md_pi_monitora_processos',array('id_md_pi_monitora_processos'));*/

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_ativ', 'md_pi_monitora_processos', array('id_atividade'), 'atividade', array('id_atividade'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_pacote', 'md_pi_monitora_processos', array('id_md_pi_pacote_envio'), 'md_pi_pacote_envio', array('id_md_pi_pacote_envio'));

            $objInfraMetaBD->criarIndice('md_pi_monitora_processos', 'i01_md_pi_monitora_processos', array('id_atividade'));
            $objInfraMetaBD->criarIndice('md_pi_monitora_processos', 'i02_md_pi_monitora_processos', array('id_md_pi_pacote_envio'));

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint identity(1,1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_monitora_processos', 1);
            }

            //Criando a tabela de configuração de mensagens de publicação no Protocolo Integrado nos três bancos    
            BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_mensagem (
                        id_md_pi_mensagem " . $objInfraMetaBD->tipoNumeroGrande() . " NOT NULL,
                        id_tarefa " . $objInfraMetaBD->tipoNumero() . "  NULL, 
                        sin_publicar " . $objInfraMetaBD->tipoTextoFixo(1) . " NOT NULL,
                        mensagem_publicacao " . $objInfraMetaBD->tipoTextoVariavel(255) . " NOT NULL)");

            $objInfraMetaBD->adicionarChavePrimaria('md_pi_mensagem', 'pk_id_md_pi_mensagem', array('id_md_pi_mensagem'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_mensagem_tarefa', 'md_pi_mensagem', array('id_tarefa'), 'tarefa', array('id_tarefa'));

            $objInfraMetaBD->criarIndice('md_pi_mensagem', 'i01_md_pi_mensagem', array('id_tarefa'));

            //Criando a tabela de configuração de parâmetros do m�dulo  nos três bancos
            BancoSEI::getInstance()->executarSql("CREATE TABLE md_pi_parametros (
                        id_md_pi_parametros " . $objInfraMetaBD->tipoNumeroGrande() . " NOT NULL,
                        url_webservice " . $objInfraMetaBD->tipoTextoVariavel(255) . " NOT NULL,
                        quantidade_tentativas " . $objInfraMetaBD->tipoNumero() . " NOT NULL,
                        email_administrador " . $objInfraMetaBD->tipoTextoVariavel(255) . " NULL,
                        dth_ultimo_processamento " . $objInfraMetaBD->tipoDataHora() . "   NULL,
                        login_webservice " . $objInfraMetaBD->tipoTextoVariavel(10) . "  NULL,
                        senha_webservice " . $objInfraMetaBD->tipoTextoVariavel(20) . "  NULL,
                        sin_executando_publicacao " . $objInfraMetaBD->tipoTextoFixo(1) . "  DEFAULT 'N',
                        sin_publicacao_restritos " . $objInfraMetaBD->tipoTextoFixo(1) . "  DEFAULT 'S',
                        num_atividades_carregar " . $objInfraMetaBD->tipoNumero() . "  NULL)");

            $objInfraMetaBD->adicionarChavePrimaria('md_pi_parametros', 'pk_id_md_pi_parametros', array('id_md_pi_parametros'));

            //Inserindo as atividades que devem ser enviadas,por padrão,ao Protocolo Integrado  
            BancoSEI::getInstance()->executarSql("insert into md_pi_mensagem (id_md_pi_mensagem, id_tarefa,sin_publicar,mensagem_publicacao) select id_tarefa, id_tarefa,'N',nome from tarefa");

            $objProtocoloIntegradoRN = new ProtocoloIntegradoRN();
            $tarefasPublicacao =  $objProtocoloIntegradoRN->montaTarefasPadraoPublicacao();
            foreach ($tarefasPublicacao as $key => $value) {

                BancoSEI::getInstance()->executarSql("UPDATE md_pi_mensagem set sin_publicar = 'S' where id_tarefa = " . $value . " ");
            }
            BancoSEI::getInstance()->executarSql("INSERT INTO md_pi_parametros (id_md_pi_parametros,url_webservice,quantidade_tentativas,email_administrador,
                login_webservice,senha_webservice,sin_executando_publicacao,sin_publicacao_restritos,num_atividades_carregar) VALUES (1,'https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl',15,'','','','N','S',100000)");
        }


        public function versao_1_1_2($strVersaoAtual)
        {
            $comandosExecutados = '';
            $resultado = array();
            $resultado["operacoes"] = null;

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
            foreach ($tarefasPublicacao as $key => $value) {
                BancoSEI::getInstance()->executarSql("UPDATE protocolo_integrado SET sin_publicar='S' WHERE id_tarefa= " . $value . " ");
            }

            BancoSEI::getInstance()->executarSql("CREATE TABLE protocolo_integrado_parametros (id_protocolo_integrado_parametros bigint(20) NOT NULL AUTO_INCREMENT,url_webservice varchar(255) NOT NULL,
                quantidade_tentativas int(11) NOT NULL,email_administrador varchar(255) NOT NULL,dth_ultimo_processamento datetime DEFAULT NULL,login_webservice varchar(10) DEFAULT NULL,
                senha_webservice varchar(20) DEFAULT NULL,sin_executando_publicacao char(1) NOT NULL DEFAULT 'N',sin_publicacao_restritos char(1) NOT NULL DEFAULT 'S',num_atividades_carregar int(11) 
                DEFAULT NULL, PRIMARY KEY (id_protocolo_integrado_parametros)) ENGINE=InnoDB;");

            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from protocolo_integrado_parametros");
            if ($numCountElementos == 0) {
                BancoSEI::getInstance()->executarSql("INSERT INTO protocolo_integrado_parametros (id_protocolo_integrado_parametros,url_webservice,quantidade_tentativas,email_administrador,
                login_webservice,senha_webservice,sin_executando_publicacao,sin_publicacao_restritos,num_atividades_carregar) VALUES (1,'https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl'
                ,15,'','','','N','S',100000);");
            }

            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado'");
            if ($numCountElementos == 0) {

                $comando = "INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, sin_ativo,sin_sucesso) 
                VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Processo de Publicação do PI', 'ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado', 
                'D', '2', 'S','N')";
                $comandosExecutados .= '<label>' . $comando . '</label>' . '<br/>' . '<br/>';

                BancoSEI::getInstance()->executarSql($comando);
            }

            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados'");
            if ($numCountElementos == 0) {
                $comando = "INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa, descricao, comando, sta_periodicidade_execucao, periodicidade_complemento, 
                parametro, sin_ativo,sin_sucesso) VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat), 'Agendamento do alarme de e-mail disparado quando novos pacotes não 
                estão sendo gerados', 'ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados', 'D', '12', '2', 'S','N')";
                $comandosExecutados .= '<label>' . $comando . '</label>' . '<br/>' . '<br/>';

                BancoSEI::getInstance()->executarSql($comando);
            }

            $numCountElementos = BancoSEI::getInstance()->executarSql("select * from infra_agendamento_tarefa where comando='ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado'");
            if ($numCountElementos == 0) {
                $comando = "INSERT INTO infra_agendamento_tarefa (id_infra_agendamento_tarefa,descricao,comando,sta_periodicidade_execucao,periodicidade_complemento,sin_ativo,sin_sucesso)
                VALUES ((select max(iat.id_infra_agendamento_tarefa)+1 from infra_agendamento_tarefa iat),'Agendamento do alarme de e-mail disparado quando há falha na publocação de pacotes',
                'ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado','D','17','S','N')";
                $comandosExecutados .= '<label>' . $comando . '</label>' . '<br/>' . '<br/>';

                BancoSEI::getInstance()->executarSql($comando);
            }

            return $resultado;
        }


        public function versao_1_1_3($strVersaoAtual)
        {
            $this->logar(' INICIANDO OPERACOES DA INSTALACAO DA VERSAO 1.1.3 DO MODULO PROTOCOLO INTEGRADO NA BASE DO SEI');

            $resultado = array();
            $resultado["operacoes"] = null;
            $comandosExecutados = '';
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            $objProtocoloIntegradoBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
            $chavesEstrangeirasProtocoloIntegrado = $objProtocoloIntegradoBD->recuperarChavesEstrangeirasv112();

            foreach ($chavesEstrangeirasProtocoloIntegrado as $key => $arrChaveEstrangeiraProtocoloIntegrado) {
                foreach ($arrChaveEstrangeiraProtocoloIntegrado as $k => $objChave) {
                    $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado', $objChave);
                }
            }

            $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
            $chavesEstrangeirasPacote = $objPacoteBD->recuperarChavesEstrangeirasv112();

            foreach ($chavesEstrangeirasPacote as $key => $arrChaveEstrangeiraPacote) {
                //$this->logar($chaveEstrangeiraPacote);
                foreach ($arrChaveEstrangeiraPacote as $k => $objChave) {
                    $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado_pacote_envio', $objChave);
                }
            }

            $objProtocoloIntegradoMonitoramentoProcessosBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
            $chavesEstrangeirasMonitoramentoProcessos = $objProtocoloIntegradoMonitoramentoProcessosBD->recuperarChavesEstrangeirasv112();

            foreach ($chavesEstrangeirasMonitoramentoProcessos as $key => $arrChaveEstrangeiraMonitoramentoProcessos) {
                //$this->logar($chaveEstrangeiraPacote);
                foreach ($arrChaveEstrangeiraMonitoramentoProcessos as $k => $objChave) {
                    $objInfraMetaBD->excluirChaveEstrangeira('protocolo_integrado_monitoramento_processos', $objChave);
                }
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('RENAME TABLE protocolo_integrado to md_pi_mensagem');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado', 'md_pi_mensagem';");
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_mensagem CHANGE id_protocolo_integrado id_md_pi_mensagem BIGINT(20)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_mensagem.id_protocolo_integrado' , 'id_md_pi_mensagem', 'COLUMN' ");
            }

            /*$objInfraMetaBD->adicionarChavePrimaria('md_pi_mensagem','pk_id_md_pi_mensagem',array('id_md_pi_mensagem'));*/
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_mensagem_tarefa', 'md_pi_mensagem', array('id_tarefa'), 'tarefa', array('id_tarefa'));

            $arrPacotesPrevios = array();
            $objPacoteBD = new ProtocoloIntegradoPacoteEnvioBD($this->getObjInfraIBanco());
            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('RENAME TABLE protocolo_integrado_pacote_envio to md_pi_pacote_envio');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_pacote_envio', 'md_pi_pacote_envio';");
                $arrPacotesPrevios = $objPacoteBD->recuperarColunaTabelaPacote('id_protocolo_integrado_pacote_envio');
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio CHANGE id_protocolo_integrado_pacote_envio id_md_pi_pacote_envio BIGINT(20)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio add  id_md_pi_pacote_envio bigint;");
                BancoSEI::getInstance()->executarSql("update md_pi_pacote_envio set id_md_pi_pacote_envio=id_protocolo_integrado_pacote_envio");
                $nomeRestricaoChavePrimaria = $objPacoteBD->recuperarChavePrimaria();
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio drop constraint " . $nomeRestricaoChavePrimaria . "; ");
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio drop column id_protocolo_integrado_pacote_envio; ");
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio alter column id_md_pi_pacote_envio bigint not null; ");
                $objInfraMetaBD->adicionarChavePrimaria('md_pi_pacote_envio', 'pk_id_md_pi_pacote_envio', array('id_md_pi_pacote_envio'));
            }

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pacote_pi_protocolo', 'md_pi_pacote_envio', array('id_protocolo'), 'protocolo', array('id_protocolo'));

            if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio ALTER COLUMN arquivo_metadados " . $objInfraMetaBD->tipoTextoGrande() . " NULL");
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_pacote_envio ALTER COLUMN arquivo_erro " . $objInfraMetaBD->tipoTextoGrande() . " NULL");
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("RENAME TABLE protocolo_integrado_parametros to md_pi_parametros");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_parametros', 'md_pi_parametros';");
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_parametros CHANGE id_protocolo_integrado_parametros id_md_pi_parametros BIGINT(20)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_parametros.id_protocolo_integrado_parametros' , 'id_md_pi_parametros', 'COLUMN' ");
            }

            $arrProcessosMonitoradosPrevios = array();
            $objMonitoramentoProcessosBD = new ProtocoloIntegradoMonitoramentoProcessosBD($this->getObjInfraIBanco());
            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("RENAME TABLE protocolo_integrado_monitoramento_processos to md_pi_monitora_processos");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                $arrProcessosMonitoradosPrevios = $objMonitoramentoProcessosBD->recuperarIdsTabelaMonitoramentov112();
                BancoSEI::getInstance()->executarSql("EXEC sp_rename 'protocolo_integrado_monitoramento_processos', 'md_pi_monitora_processos';");
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_monitora_processos CHANGE id_protocolo_integrado_monitoramento_processos id_md_pi_monitora_processos BIGINT(20)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {

                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos add  id_md_pi_monitora_processos bigint;");
                $objMonitoraProcessosRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
                foreach ($arrProcessosMonitoradosPrevios as $key => $value) {
                    BancoSEI::getInstance()->executarSql('update md_pi_monitora_processos set id_md_pi_monitora_processos=\'' . $value->getNumIdProtocoloIntegradoMonitoramentoProcessos() . '\' where id_protocolo_integrado_monitoramento_processos=\'' . $value->getNumIdProtocoloIntegradoMonitoramentoProcessos() . '\';');
                }
                $nomeRestricaoChavePrimaria = $objMonitoramentoProcessosBD->recuperarChavePrimaria();
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos drop constraint " . $nomeRestricaoChavePrimaria . "; ");
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos drop column id_protocolo_integrado_monitoramento_processos; ");
                BancoSEI::getInstance()->executarSql("ALTER TABLE md_pi_monitora_processos alter column id_md_pi_monitora_processos bigint not null; ");
                $objInfraMetaBD->adicionarChavePrimaria('md_pi_monitora_processos', 'pk_id_md_pi_monitora_processos', array('id_md_pi_monitora_processos'));
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("Alter TABLE md_pi_monitora_processos CHANGE id_protocolo_integrado_pacote_envio id_md_pi_pacote_envio BIGINT(20)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("EXEC sp_RENAME 'md_pi_monitora_processos.id_protocolo_integrado_pacote_envio' , 'id_md_pi_pacote_envio', 'COLUMN' ");
            }

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_ativ', 'md_pi_monitora_processos', array('id_atividade'), 'atividade', array('id_atividade'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pi_monit_processo_pacote', 'md_pi_monitora_processos', array('id_md_pi_pacote_envio'), 'md_pi_pacote_envio', array('id_md_pi_pacote_envio'));

            $objInfraMetaBD->criarIndice('md_pi_monitora_processos', 'i01_md_pi_monitora_processos', array('id_atividade'));
            $objInfraMetaBD->criarIndice('md_pi_monitora_processos', 'i02_md_pi_monitora_processos', array('id_md_pi_pacote_envio'));

            $maxIdPacote = $this->getMaxIdPacote();

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = ' . ($maxIdPacote + 1));
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_pacote_envio (id bigint identity(' . ($maxIdPacote + 1) . ',1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_pacote_envio', ($maxIdPacote + 1));
            }

            $maxIdMonitoramentoProcesso = $this->getMaxIdMonitoramentoProcesso();

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = ' . ($maxIdMonitoramentoProcesso + 1));
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pi_monitora_processos (id bigint identity(' . ($maxIdMonitoramentoProcesso + 1) . ',1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pi_monitora_processos', ($maxIdMonitoramentoProcesso + 1));
            }

            return $resultado;
        }


        public function versao_1_1_4($strVersaoAtual)
        {
            //Criando a tabela de pacotes nos tr�s bancos
            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros modify column senha_webservice varchar(100)");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros alter column senha_webservice varchar(100)");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql("alter table md_pi_parametros modify( senha_webservice varchar(100))");
            }

            try {
                $objProtocoloIntegradoParametrosDTO = new ProtocoloIntegradoParametrosDTO();
                $objProtocoloIntegradoParametrosRN  = new ProtocoloIntegradoParametrosRN();
                $objProtocoloIntegradoParametrosDTO->retTodos();
                $objParametrosRetornados = $objProtocoloIntegradoParametrosRN->consultar($objProtocoloIntegradoParametrosDTO);

                if (strlen(trim($objParametrosRetornados->getStrSenhaWebservice())) > 0) {
                    $senhaEncriptada = rawurlencode($objProtocoloIntegradoParametrosRN->encriptaSenha(trim($objParametrosRetornados->getStrSenhaWebservice())));
                    $objParametrosRetornados->setStrSenhaWebservice($senhaEncriptada);
                    $objProtocoloIntegradoParametrosRN->alterar($objParametrosRetornados);
                }
            } catch (\Exception $e) {
                // Nada dever� ser feito pois os campos de login e senha ser�o removidos posteriormente neste script 
            }
        }

        public function versao_1_1_5($strVersaoAtual)
        {
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            if (strlen(ProtocoloIntegradoParametrosRN::$CHAVE_MODULO_PI) != ProtocoloIntegradoParametrosRN::$NUM_CARACTERES_CHAVE_PI) {
                throw new InfraException("Erro instalando/atualizando m�dulo Protocolo Integrado no SEI. Necess�rio definir uma chave de 16 caracteres para vari�vel CHAVE_MODULO_PI no arquivo ProtocoloIntegradoParametrosRN.php");
            }

            $objInfraSequencia = new InfraSequencia(BancoSEI::getInstance());
            $objInfraSequenciaBD = new InfraSequenciaBD(BancoSEI::getInstance());
            $objInfraSequenciaDTO = new InfraSequenciaDTO();
            $objInfraSequenciaDTO->setStrNome('infra_agendamento_tarefa');
            $objInfraSequenciaDTO->retDblNumAtual();
            $objInfraSequencia = $objInfraSequenciaBD->consultar($objInfraSequenciaDTO);
            $numProximoValorSequencia = $objInfraSequencia->getDblNumAtual();
            $numMaxIdAgendamento = $this->getMaxIdAgendamento();

            if ($numProximoValorSequencia < $numMaxIdAgendamento) {
                $objInfraSequenciaDTO = new InfraSequenciaDTO();
                $objInfraSequenciaDTO->setDblNumAtual($numMaxIdAgendamento);
                $objInfraSequenciaDTO->setStrNome('infra_agendamento_tarefa');
                $objInfraSequenciaBD->alterar($objInfraSequenciaDTO);
            }

            $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::publicarProtocoloIntegrado', 'Processo de Publicação do PI', 'D', '2');
            $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::notificarProcessosComFalhaPublicacaoProtocoloIntegrado', 'Agendamento do alarme de e-mail disparado quando há falha na publicação de pacotes', 'D', '17');
            $this->adicionarAgendamento('ProtocoloIntegradoAgendamentoRN::notificarNovosPacotesNaoSendoGerados', 'Agendamento do alarme de e-mail disparado quando novos pacotes não estão sendo gerados', 'D', '12');
            $objInfraMetaBD->excluirChaveEstrangeira('md_pi_mensagem', 'fk_md_pi_mensagem_tarefa');

            BancoSEI::getInstance()->executarSql('ALTER TABLE md_pi_mensagem ADD CONSTRAINT fk_md_pi_mensagem_tarefa FOREIGN KEY (id_tarefa) REFERENCES tarefa (id_tarefa) ON DELETE CASCADE');
        }

        public function versao_2_0_0($strVersaoPreviaModuloProtocoloIntegrado)
        {
        }


        public function versao_3_0_0($strVersaoAtual)
        {
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            // Remove colunas de par�metros desnecess�rios ap�s mudan�a para arquivo de configura��o
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "url_webservice");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "quantidade_tentativas");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "email_administrador");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "login_webservice");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "senha_webservice");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "sin_publicacao_restritos");
            $objInfraMetaBD->excluirColuna("md_pi_parametros", "num_atividades_carregar");
        }
    }

    session_start();

    SessaoSEI::getInstance(false);

    BancoSEI::getInstance()->setBolScript(true);

    $VersaoProtocoloIntegradoRN = new VersaoProtocoloIntegradoRN();
    $VersaoProtocoloIntegradoRN->verificarVersaoInstalada();
    $VersaoProtocoloIntegradoRN->setStrNome('SEI');
    $VersaoProtocoloIntegradoRN->setStrVersaoAtual(VERSAO_MODULO_PI);
    $VersaoProtocoloIntegradoRN->setStrParametroVersao('PI_VERSAO');
    $VersaoProtocoloIntegradoRN->setArrVersoes(array(
        '0.0.0' => 'versao_0_0_0',
        '1.1.2' => 'versao_1_1_2',
        '1.1.3' => 'versao_1_1_3',
        '1.1.4' => 'versao_1_1_4',
        '1.1.5' => 'versao_1_1_5',
        '2.0.*' => 'versao_2_0_0',
        '3.0.*' => 'versao_3_0_0'
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
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}
