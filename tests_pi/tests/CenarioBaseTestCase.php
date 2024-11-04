<?php

use \utilphp\util;
use PHPUnit\Extensions\Selenium2TestCase;

use function PHPSTORM_META\map;

/**
 * Classe base contendo rotinas comuns utilizadas nos casos de teste do módulo
 */
class CenarioBaseTestCase extends Selenium2TestCase
{
    const PASTA_ARQUIVOS_TESTE = "/tmp";

    private static $setupDone = false;

    //Referência para unidades que serão consideradas no fluxo de trâmite (Remetente -> Destinatário)
    protected static $urlSistemaRemetente = null;
    protected static $siglaOrgaoRemetente = null;
    protected static $siglaUnidadeRemetente = null;
    protected static $nomeUnidadeRemetente = null;

    protected static $urlSistemaDestinatario = null;
    protected static $siglaOrgaoDestinatario = null;
    protected static $siglaUnidadeDestinatario = null;
    protected static $nomeUnidadeDestinatario = null;

    //Referências para as páginas do SEI utilizadas nos cenarios de teste
    protected $paginaBase = null;
    protected $paginaDocumento = null;
    protected $paginaAssinaturaDocumento = null;
    protected $paginaProcesso = null;
    protected $paginaEditarProcesso = null;
    protected $paginaConsultarAndamentos = null;
    protected $paginaControleProcesso = null;
    protected $paginaIncluirDocumento = null;
    protected $paginaAnexarProcesso = null;
    protected $paginaCancelarDocumento = null;
    protected $paginaMoverDocumento = null;

    public function setUpPage(): void
    {
        $this->paginaBase = new PaginaTeste($this);
        $this->paginaDocumento = new PaginaDocumento($this);
        $this->paginaAssinaturaDocumento = new PaginaAssinaturaDocumento($this);
        $this->paginaProcesso = new PaginaProcesso($this);
        $this->paginaEditarProcesso = new PaginaEditarProcesso($this);
        $this->paginaConsultarAndamentos = new PaginaConsultarAndamentos($this);
        $this->paginaControleProcesso = new PaginaControleProcesso($this);
        $this->paginaIncluirDocumento = new PaginaIncluirDocumento($this);
        $this->paginaAnexarProcesso = new PaginaAnexarProcesso($this);
        $this->paginaCancelarDocumento = new PaginaCancelarDocumento($this);
        $this->paginaMoverDocumento = new PaginaMoverDocumento($this);

        $this->currentWindow()->maximize();
    }

    private static function runDatabaseSetup(): void
    {
        $bancoSEI = new DatabaseUtils('SEI');
        $bancoSIP = new DatabaseUtils('SIP');

        /*INICIO test_00ConfigSIP*/   
        $bancoSIP->execute("INSERT INTO `permissao` VALUES (100000938,100000100,100000002,110000004,1,'2024-09-17 00:00:00',NULL,'N'),(100000938,100000100,100000003,110000005,1,'2024-09-17 00:00:00',NULL,'N'),(100000941,100000100,100000002,110000004,1,'2024-09-17 00:00:00',NULL,'N'),(100000941,100000100,100000003,110000005,1,'2024-09-17 00:00:00',NULL,'N'),(100000950,100000100,100000002,110000004,1,'2024-09-17 00:00:00',NULL,'N'),(100000950,100000100,100000003,110000005,1,'2024-09-17 00:00:00',NULL,'N'),(100000951,100000100,100000003,110000005,1,'2024-09-17 00:00:00',NULL,'N');");

        $bancoSIP->execute("INSERT INTO `unidade` VALUES (110000004,0,'Arquivo Setorial','Arquivo Setorial','S','N',NULL),(110000005,0,'Arquivo Central','Arquivo Central','S','N',NULL);");
        
        // adicionar ao perfil Administrador recursos que comecem com gd_
        $result_recurso = $bancoSIP->query("SELECT id_recurso from recurso r where r.nome LIKE ?", array("gd_%"));

        foreach ($result_recurso as $recurso) {
            $bancoSIP->execute("INSERT INTO `rel_perfil_recurso` VALUES (100000939,100000100,?);", array($recurso['id_recurso']));
            
            // adicionar relacionamento entre o perfil Administrador e items de menu dos recursos gd
            $result_recurso_item_menu = $bancoSIP->query("SELECT id_item_menu, id_menu from item_menu im where im.id_recurso = ?", array($recurso['id_recurso']));
            foreach ($result_recurso_item_menu as $recurso_item_menu) {
                $bancoSIP->execute("INSERT INTO `rel_perfil_item_menu` VALUES (100000939,100000100,?,?,?);", array($recurso_item_menu['id_menu'],$recurso_item_menu['id_item_menu'],$recurso['id_recurso']));
            }
        }
        // nome_tabela, num_atual,
        $bancoSIP->execute("UPDATE `infra_sequencia` SET num_atual = ? WHERE nome_tabela = ?", array("100000003","usuario"));

        $bancoSIP->execute("UPDATE `infra_sequencia` SET num_atual = ? WHERE nome_tabela = ?", array("110000005","unidade"));

        $bancoSIP->execute("INSERT INTO `rel_hierarquia_unidade` VALUES (110000004,100000018,NULL,NULL,'2019-01-01 00:00:00',NULL,'S'),(110000005,100000018,NULL,NULL,'2019-01-01 00:00:00',NULL,'S');");
        /*FIM */ 
        
        /*INICIO test_02ConfigSEILocalizadores*/
        $bancoSEI->execute("INSERT INTO `lugar_localizador` VALUES (1,110000001,'Lugar 01','S'),(2,110000001,'Lugar 02','S');");
        
        $bancoSEI->execute("INSERT INTO `tipo_localizador` VALUES (1,110000001,'TLOC01','Tipo de Localizador 01','S','desc1'),(2,110000001,'TLOC02','Tipo de Localizador 02','S','desc2');");
        
        $bancoSEI->execute("INSERT INTO `localizador` VALUES (1,110000001,1,'complemento localizador 01','A',1,1,1),(2,110000001,1,NULL,'A',1,2,2);");

        $bancoSEI->execute("UPDATE `unidade` SET sin_arquivamento = ?, sin_protocolo = ? WHERE sigla = ?", array("S", "S", "Teste"));

        $bancoSEI->execute("INSERT INTO `rel_assinante_unidade` VALUES (110000004,10);");

        $bancoSEI->execute("INSERT INTO `rel_assinante_unidade` VALUES (110000005,10);");

        $bancoSEI->execute("INSERT INTO `rel_assinante_unidade` VALUES (110000004,2);");

        $bancoSEI->execute("INSERT INTO `rel_assinante_unidade` VALUES (110000005,2);");
        /* FIM */
    }

    public static function setUpBeforeClass(): void
    {
        $bancoSEI = new DatabaseUtils('SEI');

        // Verifica no banco se o setup já foi feito
        $result = $bancoSEI->query("SELECT * FROM unidade WHERE sigla = 'Arquivo Central'");

        // Verifica se o setup já foi feito
        if (!isset($result[0])) {
           self::runDatabaseSetup();
        }

    }

    public static function tearDownAfterClass(): void
    {
    }

    public function setUp(): void
    {
        $this->setHost(PHPUNIT_HOST);
        $this->setPort(intval(PHPUNIT_PORT));
        $this->setBrowser(PHPUNIT_BROWSER);
        $this->setBrowserUrl(PHPUNIT_TESTS_URL);
        $this->setDesiredCapabilities(
            array(
                'platform' => 'LINUX',
                'chromeOptions' => array(
                    'w3c' => false,
                    'args' => [
                        '--profile-directory=' . uniqid(),
                        '--disable-features=TranslateUI',
                        '--disable-translate',
                    ],
                )
            )
        );

    }

    protected function definirContextoTeste($nomeContexto)
    {
        $objContexto = array(
            'URL' => constant($nomeContexto . '_URL'),
            'ORGAO' => constant($nomeContexto . '_SIGLA_ORGAO'),
            'SIGLA_UNIDADE' => constant($nomeContexto . '_SIGLA_UNIDADE'),
            'SIGLA_UNIDADE_HIERARQUIA' => constant($nomeContexto . '_SIGLA_UNIDADE_HIERARQUIA'),
            'NOME_UNIDADE' => constant($nomeContexto . '_NOME_UNIDADE'),
            'LOGIN' => constant($nomeContexto . '_USUARIO_LOGIN'),
            'SENHA' => constant($nomeContexto . '_USUARIO_SENHA'),
            'TIPO_PROCESSO' => constant($nomeContexto . '_TIPO_PROCESSO'),
            'TIPO_DOCUMENTO' => constant($nomeContexto . '_TIPO_DOCUMENTO'),
            'TIPO_DOCUMENTO_NAO_MAPEADO' => constant($nomeContexto . '_TIPO_DOCUMENTO_NAO_MAPEADO'),
            'CARGO_ASSINATURA' => constant($nomeContexto . '_CARGO_ASSINATURA'),
            'SIGLA_UNIDADE_HIERARQUIA' => constant($nomeContexto . '_SIGLA_UNIDADE_HIERARQUIA'),
            'SIGLA_UNIDADE_SECUNDARIA' => constant($nomeContexto . '_SIGLA_UNIDADE_SECUNDARIA'),
            'SIGLA_UNIDADE_SECUNDARIA_HIERARQUIA' => constant($nomeContexto . '_SIGLA_UNIDADE_SECUNDARIA_HIERARQUIA'),
            'NOME_UNIDADE_SECUNDARIA' => constant($nomeContexto . '_NOME_UNIDADE_SECUNDARIA'),
            'HIPOTESE_RESTRICAO_ID' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_ID'),
            'HIPOTESE_RESTRICAO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO'),
            'HIPOTESE_RESTRICAO_NAO_MAPEADO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_NAO_MAPEADO'),
            'REP_ESTRUTURAS' => constant($nomeContexto . '_REP_ESTRUTURAS'),
            'HIPOTESE_RESTRICAO_PADRAO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_PADRAO'),
            'ID_REP_ESTRUTURAS' => constant($nomeContexto . '_ID_REP_ESTRUTURAS'),
            'ID_ESTRUTURA' => constant($nomeContexto . '_ID_ESTRUTURA'),
            'SIGLA_ESTRUTURA' => constant($nomeContexto . '_SIGLA_ESTRUTURA'),
            'HIPOTESE_RESTRICAO_INATIVA' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_INATIVA'),
            'TIPO_PROCESSO_SIGILOSO' => constant($nomeContexto . '_TIPO_PROCESSO_SIGILOSO'),
            'HIPOTESE_SIGILOSO' => constant($nomeContexto . '_HIPOTESE_SIGILOSO'),
        );

        return $objContexto;
    }

    protected function acessarSistema($url, $siglaUnidade, $login, $senha)
    {
        $this->url($url);
        PaginaLogin::executarAutenticacao($this);
        PaginaTeste::selecionarUnidadeContexto($this, $siglaUnidade);
        $this->url($url);
    }

    protected function selecionarUnidadeInterna($unidadeDestino)
    {
        PaginaTeste::selecionarUnidadeContexto($this, $unidadeDestino);
    }

    protected function sairSistema()
    {
        $this->paginaBase->sairSistema();
    }

    protected function abrirProcesso($protocolo)
    {
        $this->paginaBase->navegarParaControleProcesso();
        $this->paginaControleProcesso->abrirProcesso($protocolo);
    }

    protected function abrirProcessoPelaDescricao($descricao)
    {
        $this->paginaBase->navegarParaControleProcesso();
        $protocolo = $this->paginaControleProcesso->localizarProcessoPelaDescricao($descricao);
        if ($protocolo) {
            $this->paginaControleProcesso->abrirProcesso($protocolo);
        }
        return $protocolo;
    }

    protected function cadastrarDocumentoInterno($dadosDocumentoInterno)
    {
        $this->paginaProcesso->selecionarProcesso();
        $this->paginaIncluirDocumento->gerarDocumentoTeste($dadosDocumentoInterno);
        sleep(2);
    }

    protected function cadastrarDocumentoExterno($dadosDocumentoExterno, $comAnexo = true)
    {
        $this->paginaProcesso->selecionarProcesso();
        $this->paginaIncluirDocumento->gerarDocumentoExternoTeste($dadosDocumentoExterno, $comAnexo);
        sleep(2);
    }

    protected function assinarDocumento($siglaOrgao, $cargoAssinante, $loginSenha)
    {
        // Navegar para página de assinatura
        $this->paginaDocumento->navegarParaAssinarDocumento();
        sleep(2);

        // Assinar documento
        $this->paginaAssinaturaDocumento->selecionarOrgaoAssinante($siglaOrgao);
        $this->paginaAssinaturaDocumento->selecionarCargoAssinante($cargoAssinante);
        $this->paginaAssinaturaDocumento->assinarComLoginSenha($loginSenha);
        $this->window('');
        sleep(2);
    }

    protected function anexarProcesso($protocoloProcessoAnexado)
    {
        $this->paginaProcesso->navegarParaAnexarProcesso();
        $this->paginaAnexarProcesso->anexarProcesso($protocoloProcessoAnexado);
    }

    protected function tramitarProcessoInternamente($unidadeDestino)
    {
        // Acessar funcionalidade de trâmite interno
        $this->paginaProcesso->navegarParaTramitarProcessoInterno();

        // Preencher parâmetros do trâmite
        $this->paginaTramitar->unidadeInterna($unidadeDestino);
        $this->paginaTramitar->tramitarInterno();

        sleep(1);
    }

    protected function navegarParaCancelarDocumento($ordemDocumento)
    {
        $listaDocumentos = $this->paginaProcesso->listarDocumentos();
        $this->paginaProcesso->selecionarDocumento($listaDocumentos[$ordemDocumento]);
        $this->paginaDocumento->navegarParaCancelarDocumento();
    }

    protected function tramitarProcessoInternamenteParaCancelamento($unidadeOrigem, $unidadeDestino, $protocolo)
    {
        //Tramitar internamento para liberação da funcionalidade de cancelar
        $this->tramitarProcessoInternamente($unidadeDestino);

        //Selecionar unidade interna
        $this->selecionarUnidadeInterna($unidadeDestino);
        if ($protocolo) {
            $this->paginaControleProcesso->abrirProcesso($protocolo['PROTOCOLO']);
        }

        //Tramitar internamento para liberação da funcionalidade de cancelar
        $this->tramitarProcessoInternamente($unidadeOrigem);

        //Selecionar unidade interna
        $this->selecionarUnidadeInterna($unidadeOrigem);
        if ($protocolo) {
            $this->paginaControleProcesso->abrirProcesso($protocolo['PROTOCOLO']);
        }

        sleep(1);
    }

    protected function validarDadosProcesso($descricao, $restricao, $observacoes, $listaInteressados, $hipoteseLegal = null)
    {
        sleep(2);
        $this->paginaProcesso->navegarParaEditarProcesso();
        $this->paginaEditarProcesso = new PaginaEditarProcesso($this);
        $this->assertEquals(utf8_encode($descricao), $this->paginaEditarProcesso->descricao());
        $this->assertEquals($restricao, $this->paginaEditarProcesso->restricao());

        $listaInteressados = is_array($listaInteressados) ? $listaInteressados : array($listaInteressados);
        for ($i = 0; $i < count($listaInteressados); $i++) {
            $this->assertStringStartsWith(substr($listaInteressados[$i], 0, 100), $this->paginaEditarProcesso->listarInteressados()[$i]);
        }

        if ($observacoes) {
            $this->assertStringContainsString($observacoes, $this->byCssSelector('body')->text());
        }

        if ($hipoteseLegal != null) {
            $hipoteseLegalDocumento = $this->paginaEditarProcesso->recuperarHipoteseLegal();
            $this->assertEquals($hipoteseLegal, $hipoteseLegalDocumento);
        }
    }

    protected function validarDocumentoCancelado($nomeDocArvore)
    {
        sleep(2);
        $this->assertTrue($this->paginaProcesso->ehDocumentoCancelado($nomeDocArvore));
    }

    protected function validarDocumentoMovido($nomeDocArvore)
    {
        sleep(2);
        $this->assertTrue($this->paginaProcesso->ehDocumentoMovido($nomeDocArvore));
    }

    protected function validarDadosDocumento($nomeDocArvore, $dadosDocumento, $destinatario, $unidadeSecundaria = false, $hipoteseLegal = null)
    {
        sleep(2);

        // Verifica se documento possui marcação de documento anexado
        $bolPossuiDocumentoReferenciado = !is_null($dadosDocumento['ORDEM_DOCUMENTO_REFERENCIADO']);
        $this->assertTrue($this->paginaProcesso->deveSerDocumentoAnexo($bolPossuiDocumentoReferenciado, $nomeDocArvore));

        if (($this->paginaProcesso->ehDocumentoCancelado($nomeDocArvore) == false) and ($this->paginaProcesso->ehDocumentoMovido($nomeDocArvore) == false)) {

            $this->paginaProcesso->selecionarDocumento($nomeDocArvore);
            $this->paginaDocumento->navegarParaConsultarDocumento();
                        
            $mesmoOrgao = $dadosDocumento['ORIGEM'] == $destinatario['URL'];

            if ($mesmoOrgao && $dadosDocumento['TIPO'] == 'G') {
                $this->assertEquals($dadosDocumento["DESCRICAO"], $this->paginaDocumento->descricao());
                if (!$mesmoOrgao) {
                    $observacoes = ($unidadeSecundaria) ? $this->paginaDocumento->observacoesNaTabela() : $this->paginaDocumento->observacoes();
                    $this->assertEquals($dadosDocumento['OBSERVACOES'], $observacoes);
                }
            } else {
                $this->assertNotNull($this->paginaDocumento->nomeAnexo());
                $contemVariosComponentes = is_array($dadosDocumento['ARQUIVO']);
                if (!$contemVariosComponentes) {
                    $nomeArquivo = $dadosDocumento['ARQUIVO'];
                    $this->assertStringContainsString(basename($nomeArquivo), $this->paginaDocumento->nomeAnexo());
                    if ($hipoteseLegal != null) {
                        $hipoteseLegalDocumento = $this->paginaDocumento->recuperarHipoteseLegal();
                        $this->assertEquals($hipoteseLegal, $hipoteseLegalDocumento);
                    }
                }
            }
        }
    }

    protected function validarProcessosTramitados($protocolo, $deveExistir)
    {
        $this->frame(null);
        $this->paginaBase->navegarParaControleProcesso();
        $this->byId("txtInfraPesquisarMenu")->value(utf8_encode('Processos em Tramitação Externa'));
        $this->byLinkText(utf8_encode("Processos em Tramitação Externa"))->click();
        $this->assertEquals($deveExistir, $this->paginaProcessosTramitadosExternamente->contemProcesso($protocolo));
    }

    protected function validarProcessoRejeitado()
    {
        $this->paginaBase->navegarParaControleProcesso();
        $this->assertTrue($this->paginaControleProcesso->contemProcesso(self::$protocoloTeste));
        $this->assertTrue($this->paginaControleProcesso->contemAlertaProcessoRecusado(self::$protocoloTeste));
    }

    public function gerarDadosProcessoTeste($contextoProducao)
    {
        return array(
            "TIPO_PROCESSO" => $contextoProducao['TIPO_PROCESSO'],
            "DESCRICAO" => util::random_string(100),
            "OBSERVACOES" => null,
            "INTERESSADOS" => str_repeat(util::random_string(9) . ' ', 25),
            "RESTRICAO" => PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO,
            "ORIGEM" => $contextoProducao['URL'],
        );
    }

    public function gerarDadosDocumentoInternoTeste($contextoProducao)
    {
        return array(
            'TIPO' => 'G', // Documento do tipo Gerado pelo sistema
            "NUMERO" => null, //Gerado automaticamente no cadastramento do documento
            "TIPO_DOCUMENTO" => $contextoProducao['TIPO_DOCUMENTO'],
            "DESCRICAO" => trim(str_repeat(util::random_string(9) . ' ', 10)),
            "OBSERVACOES" => null,
            "INTERESSADOS" => str_repeat(util::random_string(9) . ' ', 25),
            "RESTRICAO" => PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO,
            "ORDEM_DOCUMENTO_REFERENCIADO" => null,
            "ARQUIVO" => ".html",
            "ORIGEM" => $contextoProducao['URL'],
        );
    }

    public function gerarDadosDocumentoExternoTeste($contextoProducao, $nomesArquivos = 'arquivo_pequeno.txt', $ordemDocumentoReferenciado = null)
    {
        // Tratamento para lista de arquivos em casos de documentos com mais de um componente digital
        $pasta = self::PASTA_ARQUIVOS_TESTE;
        $arquivos = is_array($nomesArquivos) ? array_map(function ($item) use ($pasta) {
            return "$pasta/$item";
        }, $nomesArquivos) : "$pasta/$nomesArquivos";

        return array(
            'TIPO' => 'R', // Documento do tipo Recebido pelo sistema
            "NUMERO" => null, //Gerado automaticamente no cadastramento do documento
            "TIPO_DOCUMENTO" => $contextoProducao['TIPO_DOCUMENTO'],
            "DATA_ELABORACAO" => '01/01/2017',
            "DESCRICAO" => str_repeat(util::random_string(9) . ' ', 10),
            "OBSERVACOES" => util::random_string(500),
            "INTERESSADOS" => str_repeat(util::random_string(9) . ' ', 25),
            "ORDEM_DOCUMENTO_REFERENCIADO" => $ordemDocumentoReferenciado,
            "RESTRICAO" => PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO,
            "ARQUIVO" => $arquivos,
            "ORIGEM" => $contextoProducao['URL'],
        );
    }

    public function gerarDadosDocumentoExternoGrandeTeste($contextoProducao, $nomesArquivo = 'arquivo_grande_gerado.txt', $tamanhoMB = 100,  $ordemDocumentoReferenciado = null)
    {
        // Tratamento para lista de arquivos em casos de documentos com mais de um componente digital
        $pasta = self::PASTA_ARQUIVOS_TESTE;
        shell_exec('dd if=/dev/zero of=' . self::PASTA_ARQUIVOS_TESTE . '/' . $nomesArquivo . ' bs=1M count=' . $tamanhoMB);
        $arquivos = "$pasta/$nomesArquivo";

        return array(
            'TIPO' => 'R', // Documento do tipo Recebido pelo sistema
            "NUMERO" => null, //Gerado automaticamente no cadastramento do documento
            "TIPO_DOCUMENTO" => $contextoProducao['TIPO_DOCUMENTO'],
            "DATA_ELABORACAO" => '01/01/2017',
            "DESCRICAO" => str_repeat(util::random_string(9) . ' ', 10),
            "OBSERVACOES" => util::random_string(500),
            "INTERESSADOS" => str_repeat(util::random_string(9) . ' ', 25),
            "ORDEM_DOCUMENTO_REFERENCIADO" => $ordemDocumentoReferenciado,
            "RESTRICAO" => PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO,
            "ARQUIVO" => $arquivos,
            "ORIGEM" => $contextoProducao['URL'],
        );
    }

    protected function selecionarProcessos($numProtocolo=null)
    {
        $this->paginaBase->navegarParaControleProcesso();
        $this->paginaTramitarProcessoEmLote->selecionarProcessos($numProtocolo);
        sleep(2);
    }

    protected function visualizarProcessoTramitadosEmLote($test)
    {
        $this->paginaBase->navegarParaControleProcesso();
        $this->byId("txtInfraPesquisarMenu")->value(utf8_encode('Processos Tramitados em Bloco'));
        $this->byLinkText("Processos Tramitados em Bloco")->click();
    }

    protected function navegarProcessoEmLote($selAndamento, $numProtocolo=null)
    {
        if($selAndamento == 0){
            $selAndamento = PaginaTramitarProcessoEmLote::STA_ANDAMENTO_PROCESSAMENTO;
        }if($selAndamento == 2){
            $selAndamento = PaginaTramitarProcessoEmLote::STA_ANDAMENTO_CONCLUIDO;
        }if($selAndamento == 7){
            $selAndamento = PaginaTramitarProcessoEmLote::STA_ANDAMENTO_CANCELADO;
        }
        $this->paginaTramitarProcessoEmLote->navegarProcessoEmLote($selAndamento, $numProtocolo);
    }

    protected function navegarMapeamentoUnidade () {
        $this->frame(null);
        $this->byXPath("//img[contains(@title, 'Controle de Processos')]")->click();
        $this->paginaMapeamentoUnidade->navegarMapeamentoUnidade();
    }

}