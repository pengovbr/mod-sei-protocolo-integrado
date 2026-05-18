<?php

use \utilphp\util;
use PHPUnit\Extensions\Selenium2TestCase;
use Tests\Funcional\Sei\Fixtures\ContatoFixture;
use function PHPSTORM_META\map;

/**
 * Classe base contendo rotinas comuns utilizadas nos casos de teste do módulo
 */
class CenarioBaseTestCase extends Selenium2TestCase
{
    const PASTA_ARQUIVOS_TESTE = "/tmp";

    private static $setupMultiOrgao = false;

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
    protected $paginaTramitar = null;
    protected $paginaConsultarAndamentos = null;
    protected $paginaControleProcesso = null;
    protected $paginaIncluirDocumento = null;
    protected $paginaAnexarProcesso = null;
    protected $paginaCancelarDocumento = null;
    protected $paginaMoverDocumento = null;
    protected $paginaArquivarProcesso = null;
    protected $paginaAvaliacaoDeProcessos = null;
    protected $paginaPreparacaoListagemEliminacao = null;
    protected $paginaGestaoListagemEliminacao = null;
    protected $paginaUnidades = null;
    protected $paginaAcervoGlobalDePendencias = null;
    protected $paginaArquivoDaUnidade = null;
    protected $paginaAgendamentos = null;

    public function setUpPage(): void
    {
        $this->paginaBase = new PaginaTeste($this);
        $this->paginaDocumento = new PaginaDocumento($this);
        $this->paginaAssinaturaDocumento = new PaginaAssinaturaDocumento($this);
        $this->paginaProcesso = new PaginaProcesso($this);
        $this->paginaEditarProcesso = new PaginaEditarProcesso($this);
        $this->paginaTramitar = new PaginaTramitarProcesso($this);
        $this->paginaConsultarAndamentos = new PaginaConsultarAndamentos($this);
        $this->paginaControleProcesso = new PaginaControleProcesso($this);
        $this->paginaIncluirDocumento = new PaginaIncluirDocumento($this);
        $this->paginaAnexarProcesso = new PaginaAnexarProcesso($this);
        $this->paginaCancelarDocumento = new PaginaCancelarDocumento($this);
        $this->paginaMoverDocumento = new PaginaMoverDocumento($this);
        $this->paginaUnidades = new PaginaUnidades($this);
        $this->paginaAgendamentos = new PaginaAgendamentos($this);
        $this->currentWindow()->maximize();
    }

    private static function runDatabaseSetup(): void
    {
    }

    public static function runDatabaseSetupMultiOrgao(): void
    {
    }

    public static function setUpBeforeClass(): void
    {
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
            'LOGIN' => constant($nomeContexto . '_USUARIO_LOGIN'),
            'SENHA' => constant($nomeContexto . '_USUARIO_SENHA'),
            'TIPO_PROCESSO' => constant($nomeContexto . '_TIPO_PROCESSO'),
            'TIPO_DOCUMENTO' => constant($nomeContexto . '_TIPO_DOCUMENTO'),
            'TIPO_DOCUMENTO_NAO_MAPEADO' => constant($nomeContexto . '_TIPO_DOCUMENTO_NAO_MAPEADO'),
            'CARGO_ASSINATURA' => constant($nomeContexto . '_CARGO_ASSINATURA'),
            'SIGLA_UNIDADE_SECUNDARIA' => constant($nomeContexto . '_SIGLA_UNIDADE_SECUNDARIA'),
            'HIPOTESE_RESTRICAO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO'),
            'HIPOTESE_RESTRICAO_NAO_MAPEADO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_NAO_MAPEADO'),
            'HIPOTESE_RESTRICAO_PADRAO' => constant($nomeContexto . '_HIPOTESE_RESTRICAO_PADRAO'),
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

    protected function tramitarProcessoInternamente($unidadeDestino, $manterAbertoNaUnidadeAtual = false)
    {
        // Acessar funcionalidade de trâmite interno
        $this->paginaProcesso->navegarParaTramitarProcessoInterno();

        // Preencher parâmetros do trâmite
        $this->paginaTramitar->unidadeInterna($unidadeDestino);
        if ($manterAbertoNaUnidadeAtual) {
            $this->paginaTramitar->manterAbertoNaUnidadeAtual();
        }
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

    protected function navegarMapeamentoUnidade () {
        $this->frame(null);
        $this->byXPath("//img[contains(@title, 'Controle de Processos')]")->click();
        $this->paginaMapeamentoUnidade->navegarMapeamentoUnidade();
    }

}