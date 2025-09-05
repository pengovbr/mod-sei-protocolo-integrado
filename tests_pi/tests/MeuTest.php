<?php

use Tests\Funcional\Sei\Fixtures\{ProtocoloFixture,ProcedimentoFixture,AtividadeFixture,ContatoFixture,ParticipanteFixture,RelProtocoloAssuntoFixture,AtributoAndamentoFixture,DocumentoFixture,AssinaturaFixture,AnexoFixture,AnexoProcessoFixture};


/**
 * Testes de trâmite de processos anexados considerando cenário específico de trâmites e devoluções sucessivas
 *
 * O cenário descreve uma falha relatada pelos usuários em que um erro de inconsistência era causado após a realização dos seguintes passos:
 *
 *  - Trâmite de processo simples X do órgão A para o órgão B
 *  - Adição de novos documentos e devolução do processo para órgão A
 *  - Adição de novos documentos no processo X e anexação ao processo Y
 *  - Trâmite do processo Y para órgão B
 *  - Adição de novos documentos ao processo Y e devolução para o órgão A
 *  - Adição de novos documentos e devolução para órgão B
 *
 * Execution Groups
 * @group execute_alone_group2
 */
class MeuTest extends FixtureCenarioBaseTestCase
{
    public static $remetente;
    public static $destinatario;
    public static $processoTestePrincipal;
    public static $processoTesteAnexado;
    public static $documentoTeste1;
    public static $documentoTeste2;
    public static $documentoTeste3;
    public static $documentoTeste4;
    public static $documentoTeste5;
    public static $documentoTeste6;
    public static $protocoloTestePrincipal;
    public static $protocoloTesteAnexado;
    public static $protocoloTesteAnexadoId;
    public static $protocoloTestePrincipalId;

    /**
     * Teste inicial de trâmite de processo apartado para o órgão B
     *
     * @return void
     */
    public function test_simples()
    {
        self::$remetente = $this->definirContextoTeste(CONTEXTO_SEI);

        // Definição de dados de teste do processo principal
        self::$processoTestePrincipal = $this->gerarDadosProcessoTeste(self::$remetente);
        self::$documentoTeste1 = $this->gerarDadosDocumentoInternoTeste(self::$remetente);
        self::$documentoTeste2 = $this->gerarDadosDocumentoInternoTeste(self::$remetente);

        // Definição de dados de teste do processo a ser anexado
        self::$processoTesteAnexado = $this->gerarDadosProcessoTeste(self::$remetente);
        self::$documentoTeste3 = $this->gerarDadosDocumentoInternoTeste(self::$remetente);
        self::$documentoTeste4 = $this->gerarDadosDocumentoInternoTeste(self::$remetente);

        $parametros = [
            [
            'Descricao' => self::$processoTestePrincipal['DESCRICAO'],
            'Interessados' => self::$processoTestePrincipal['INTERESSADOS'],
            'Documentos' => [self::$documentoTeste1, self::$documentoTeste2],
            ],
            [
            'Descricao' => self::$processoTesteAnexado['DESCRICAO'],
            'Interessados' => self::$processoTesteAnexado['INTERESSADOS'],
            'Documentos' => [self::$documentoTeste3, self::$documentoTeste4],
            ]
        ];

        $objProtocoloFixture = new ProtocoloFixture();
        $objProtocolosDTO = $objProtocoloFixture->carregarVariados($parametros);

        // Cadastrar novo processo de teste principal e incluir documentos relacionados
        $i = 0;
        foreach($objProtocolosDTO as $objProtocoloDTO) {
            $objProcedimentoFixture = new ProcedimentoFixture();

            $objProcedimentoDTO = $objProcedimentoFixture->carregar([
                'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo()
            ]);

            $objAtividadeFixture = new AtividadeFixture();
            $objAtividadeDTO = $objAtividadeFixture->carregar([
                'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
                'Conclusao' => \InfraData::getStrDataHoraAtual(),
                'IdTarefa' => \TarefaRN::$TI_GERACAO_PROCEDIMENTO,
                'IdUsuarioConclusao' => 100000001
            ]);

            $objContatoFixture = new ContatoFixture();
            $objContatoDTO = $objContatoFixture->carregar([
                'Nome' => self::$processoTestePrincipal['INTERESSADOS']
            ]);

            $objParticipanteFixture = new ParticipanteFixture();
            $objParticipanteDTO = $objParticipanteFixture->carregar([
                'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
                'IdContato' => $objContatoDTO->getNumIdContato()
            ]);

            $objProtocoloAssuntoFixture = new RelProtocoloAssuntoFixture();
            $objProtocoloAssuntoFixture->carregar([
                'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo()
            ]);

            $objAtributoAndamentoFixture = new AtributoAndamentoFixture();
            $objAtributoAndamentoFixture->carregar([
                'IdAtividade' => $objAtividadeDTO->getNumIdAtividade()
            ]);
            
            // Incluir e assinar documentos relacionados
            foreach($parametros[$i]['Documentos'] as $documento) {
                $objDocumentoFixture = new DocumentoFixture();
                $objDocumentoDTO = $objDocumentoFixture->carregar([
                    'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
                    'IdProcedimento' => $objProcedimentoDTO->getDblIdProcedimento(),
                    'Descricao' => $documento['DESCRICAO'],
                ]);
                // Armazenar nome que o arquivo receberá no org destinatário
                $docs[$i][] = str_pad($objDocumentoDTO->getDblIdDocumento(), 6, 0, STR_PAD_LEFT).'.html';

                $objAssinaturaFixture = new AssinaturaFixture();
                $objAssinaturaFixture->carregar([
                    'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
                    'IdDocumento' => $objDocumentoDTO->getDblIdDocumento(),
                ]);
            }
            $protocolo[$i]['formatado'] = $objProtocoloDTO->getStrProtocoloFormatado();
            $protocolo[$i]['id'] = $objProtocoloDTO->getDblIdProtocolo();
            $i++;
        }

        // Preencher variaveis que serão usadas posteriormente nos testes
        self::$documentoTeste1['ARQUIVO'] = $docs[0][0];
        self::$documentoTeste2['ARQUIVO'] = $docs[0][1];
        self::$documentoTeste3['ARQUIVO'] = $docs[1][0];
        self::$documentoTeste4['ARQUIVO'] = $docs[1][1];
        self::$protocoloTestePrincipal = $protocolo[0]['formatado'];
        self::$protocoloTestePrincipalId = $protocolo[0]['id'];
        self::$protocoloTesteAnexado = $protocolo[1]['formatado'];
        self::$protocoloTesteAnexadoId = $protocolo[1]['id'];

        // Realizar a anexação de processos
        $objAnexoProcessoFixture = new AnexoProcessoFixture();
        $objAnexoProcessoFixture->carregar([
            'IdProtocolo' => self::$protocoloTestePrincipalId,
            'IdDocumento' => self::$protocoloTesteAnexadoId,
        ]);
        
        // Acessar sistema do this->REMETENTE do processo
        $this->acessarSistema(self::$remetente['URL'], self::$remetente['SIGLA_UNIDADE'], self::$remetente['LOGIN'], self::$remetente['SENHA']);
        $this->abrirProcesso(self::$protocoloTestePrincipal);
        sleep(2);

        $this->assertTrue(true);
    }


}
