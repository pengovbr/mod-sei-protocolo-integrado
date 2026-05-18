<?php

use \utilphp\util;
use PHPUnit\Extensions\Selenium2TestCase;
use Tests\Funcional\Sei\Fixtures\{ProtocoloFixture,ProcedimentoFixture,AtividadeFixture,ContatoFixture};
use Tests\Funcional\Sei\Fixtures\{ParticipanteFixture,RelProtocoloAssuntoFixture,AtributoAndamentoFixture};
use Tests\Funcional\Sei\Fixtures\{DocumentoFixture,AssinaturaFixture,AnexoFixture,AnexoProcessoFixture};
use Tests\Funcional\Sei\Fixtures\{HipoteseLegalFixture,TipoProcedimentoFixture,RelProtocoloProtocoloFixture};
use Tests\Funcional\Sei\Fixtures\{PublicacaoFixture};


use function PHPSTORM_META\map;
/**
 * Classe base contendo rotinas comuns utilizadas nos casos de teste do módulo que utiliza fixture
 */
class FixtureCenarioBaseTestCase extends CenarioBaseTestCase
{
    protected function cadastrarProcessoFixture(&$dadosProcesso, $cadastrarParticipante = true)
    {
        if (!is_null($dadosProcesso['HIPOTESE_LEGAL'])){
            $objHipLegalDTO = $this->buscarHipoteseLegal($dadosProcesso);
        }

        $parametros = [
            'Descricao' => $dadosProcesso['DESCRICAO'] ?: util::random_string(20),
            'Interessados' => $dadosProcesso['INTERESSADOS'] ?: util::random_string(40),
            'IdHipoteseLegal' => $dadosProcesso['HIPOTESE_LEGAL'] ? $objHipLegalDTO->getNumIdHipoteseLegal() : null,
            'StaNivelAcessoLocal' => $dadosProcesso["RESTRICAO"] ?: PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO,
            'StaNivelAcessoGlobal' => $dadosProcesso["RESTRICAO"] ?: PaginaIniciarProcesso::STA_NIVEL_ACESSO_PUBLICO
        ];
        $objProtocoloFixture = new ProtocoloFixture();
        $objProtocoloDTO = $objProtocoloFixture->carregar($parametros);
        $objProcedimentoFixture = new ProcedimentoFixture();

        $parametrosProcedimento = [
          'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo()
        ];
        if (!is_null($dadosProcesso['ID_TIPO_PROCESSO'])) {
          $parametrosProcedimento['IdTipoProcedimento'] = $dadosProcesso['ID_TIPO_PROCESSO'];
        }
        $objProcedimentoDTO = $objProcedimentoFixture->carregar($parametrosProcedimento);

        $objAtividadeFixture = new AtividadeFixture();
        $objAtividadeDTO = $objAtividadeFixture->carregar([
            'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
            'IdTarefa' => \TarefaRN::$TI_GERACAO_PROCEDIMENTO,
            'IdUsuarioConclusao' => 100000001
        ]);

        $objContatoFixture = new ContatoFixture();
        $objContatoDTO = $objContatoFixture->carregar([
            'Nome' => $parametros['Interessados']
        ]);

        if ($cadastrarParticipante) {
            $objParticipanteFixture = new ParticipanteFixture();
            $objParticipanteDTO = $objParticipanteFixture->carregar([
                'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
                'IdContato' => $objContatoDTO->getNumIdContato()
            ]);
        }

        $objProtocoloAssuntoFixture = new RelProtocoloAssuntoFixture();
        $objProtocoloAssuntoFixture->carregar([
            'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
            'IdAssunto' => 12
        ]);

        $objAtributoAndamentoFixture = new AtributoAndamentoFixture();
        $objAtributoAndamentoFixture->carregar([
            'IdAtividade' => $objAtividadeDTO->getNumIdAtividade()
        ]);

        $dadosProcesso['PROTOCOLO'] = $objProtocoloDTO->getStrProtocoloFormatado();
        
        return $objProtocoloDTO;
    }

    protected function concluirProcessoFixture($dados)
    {
        $objData = DateTime::createFromFormat('Y-m-d H:i:s', $dados['data'] ?: '2020'.date('-m-d H:i:s'));
        
        $objAtividadeFixture = new AtividadeFixture();

        $arrObjAtividadeDTO = $objAtividadeFixture->buscar([
            'IdProtocolo' => $dados['IdProtocolo']
        ]);
        
        foreach ($arrObjAtividadeDTO as $atividadeDTO){
            $dadosAtividades = [
                'IdAtividade' => $atividadeDTO->getNumIdAtividade(),
                'IdProtocolo' => $dados['IdProtocolo'],
                'Abertura' => $objData->format('d/m/Y H:i:58'),
                'Conclusao' => $objData->format('d/m/Y H:i:59')
            ];
            $objAtividadeFixture->atualizar($dadosAtividades);
        }

        $objAtividadeDTO = $objAtividadeFixture->carregar([
            'IdProtocolo' => $dados['IdProtocolo'],
            'IdTarefa' => \TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE,
            'IdUsuarioConclusao' => 100000001,
            'Abertura' => $objData->format('d/m/Y H:i:58'),
            'Conclusao' => $objData->format('d/m/Y H:i:59')
        ]);

    }

    protected function alteraDataHistoricoProcesso($dados)
    {
        $objData = DateTime::createFromFormat('Y-m-d H:i:s', $dados['data'] ?: '2020'.date('-m-d H:i:s'));

        // Necessário para não dar erro no histórico
        $objRelProtocoloProtocoloFixture = new RelProtocoloProtocoloFixture();
        $arrProtocoloProtocoloDTO = $objRelProtocoloProtocoloFixture->buscar([
            'IdProtocolo1' => $dados['IdProtocolo']
        ]);

        $objProtocoloFixture = new ProtocoloFixture();
        foreach ($arrProtocoloProtocoloDTO as $objProtocoloProtocoloDTO){
            $objProtocoloFixture->atualizar([
                'IdProtocolo' => $objProtocoloProtocoloDTO->getDblIdProtocolo2(),
                'Geracao' => $objData->format('d/m/Y H:i:58'),
                'Inclusao' =>$objData->format('d/m/Y H:i:59')
            ]);
        }
        
        $objProtocoloDTO = $objProtocoloFixture->atualizar([
            'IdProtocolo' => $dados['IdProtocolo'],
            'Geracao' => $objData->format('d/m/Y H:i:58'),
            'Inclusao' =>$objData->format('d/m/Y H:i:59')
        ]);
    }

    protected function buscarHipoteseLegal($dados)
    {
        $param = [
            'Nome' => trim(explode('(',$dados['HIPOTESE_LEGAL'])[0]),
            'BaseLegal' => explode(')',trim(explode('(',$dados['HIPOTESE_LEGAL'])[1]))[0]
        ];
        $objHipLegalFixture = new HipoteseLegalFixture();     
        return $objHipLegalFixture->buscar($param)[0];
    }

    protected function cadastrarDocumentoInternoFixture($dadosDocumentoInterno, $idProtocolo, $assinarDocumento = true)
    {

        if (!is_null($dadosDocumentoInterno['HIPOTESE_LEGAL'])){
            $objHipLegalDTO = $this->buscarHipoteseLegal($dadosDocumentoInterno);
        }

        $dadosDocumentoDTO = [
            'IdProtocolo' => $idProtocolo,
            'IdProcedimento' => $idProtocolo,
            'Descricao' => $dadosDocumentoInterno['DESCRICAO'],
            'IdHipoteseLegal' => $dadosDocumentoInterno["HIPOTESE_LEGAL"] ? $objHipLegalDTO->getNumIdHipoteseLegal() : null,
            'StaNivelAcessoGlobal' => $dadosDocumentoInterno["RESTRICAO"] ?: \ProtocoloRN::$NA_PUBLICO,
            'StaNivelAcessoLocal' => $dadosDocumentoInterno["RESTRICAO"] ?: \ProtocoloRN::$NA_PUBLICO,
            'IdUnidadeResponsavel' => $dadosDocumentoInterno["UNIDADE_RESPONSAVEL"] ?: null
        ];

        if ($serieDTO = $this->buscarIdSerieDoDocumento($dadosDocumentoInterno['TIPO_DOCUMENTO'])) {
            $dadosDocumentoDTO['IdSerie'] = $serieDTO->getNumIdSerie();
        }

        $objDocumentoFixture = new DocumentoFixture();
        $objDocumentoDTO = $objDocumentoFixture->carregar($dadosDocumentoDTO);

        if ($assinarDocumento) {
            $this->assinaDocumentoFixture($idProtocolo,$objDocumentoDTO->getDblIdDocumento());
        }

        return $objDocumentoDTO;

    }

    protected function assinaDocumentoFixture($idProtocolo, $idDocumento) {
        //Adicionar assinatura ao documento
        $objAssinaturaFixture = new AssinaturaFixture();
        $objAssinaturaFixture->carregar([
            'IdProtocolo' => $idProtocolo,
            'IdDocumento' => $idDocumento,
        ]);

    }

    protected function cadastrarDocumentoExternoFixture($dadosDocumentoExterno, $idProtocolo)
    {
        $dadosDocumentoDTO = [
            'IdProtocolo' => $idProtocolo,
            'IdProcedimento' => $idProtocolo,
            'Descricao' => $dadosDocumentoExterno['DESCRICAO'],
            'StaProtocolo' => \ProtocoloRN::$TP_DOCUMENTO_RECEBIDO,
            'StaDocumento' => \DocumentoRN::$TD_EXTERNO,
            'IdConjuntoEstilos' => NULL,
        ];

        if ($serieDTO = $this->buscarIdSerieDoDocumento($dadosDocumentoExterno['TIPO_DOCUMENTO'])) {
            $dadosDocumentoDTO['IdSerie'] = $serieDTO->getNumIdSerie();
        }

        $objDocumentoFixture = new DocumentoFixture();
        $objDocumentoDTO = $objDocumentoFixture->carregar($dadosDocumentoDTO);

        //Adicionar anexo ao documento
        $objAnexoFixture = new AnexoFixture();
        $objAnexoFixture->carregar([
            'IdProtocolo' => $objDocumentoDTO->getDblIdDocumento(),
            'Nome' => basename($dadosDocumentoExterno['ARQUIVO']),
        ]);

        $objAtividadeFixture = new AtividadeFixture();
        $objAtividadeDTO = $objAtividadeFixture->carregar([
            'IdProtocolo' => $idProtocolo,
            'Conclusao' => \InfraData::getStrDataHoraAtual(),
            'IdTarefa' => \TarefaRN::$TI_ARQUIVO_ANEXADO,
            'IdUsuarioConclusao' => 100000001
        ]);

        $objAtributoAndamentoFixture = new AtributoAndamentoFixture();
        $objAtributoAndamentoFixture->carregar([
            'IdAtividade' => $objAtividadeDTO->getNumIdAtividade(),
            'Nome' => 'ANEXO'
        ]);
      
        return $objDocumentoDTO;
    }

    protected function anexarProcessoFixture($protocoloPrincipalId, $protocoloProcessoAnexadoId)
    {
        // Realizar a anexação de processos
        $objAnexoProcessoFixture = new AnexoProcessoFixture();
        $objAnexoProcessoFixture->carregar([
            'IdProtocolo' => $protocoloPrincipalId,
            'IdDocumento' => $protocoloProcessoAnexadoId,
        ]);
    }

    protected function relacionarProcessoFixture($protocoloId, $protocolo2Id)
    {
        $parametros = [
            'IdProtocolo' => $protocoloId,
            'IdDocumento' => $protocolo2Id,
            'Associacao' => 3,
        ];
        $objRelProtocoloProtocoloFixture = new RelProtocoloProtocoloFixture();
        $objRelProtocoloProtocoloFixtureDTO = $objRelProtocoloProtocoloFixture->carregar($parametros);
          
        return $objRelProtocoloProtocoloFixtureDTO;
    }

    protected function consultarProcessoFixture($protocoloFormatado, $staProtocolo = null)
    {
        $objProtocoloFixture = new ProtocoloFixture();
        $objProtocoloDTO = $objProtocoloFixture->buscar([
            'ProtocoloFormatado' => $protocoloFormatado,
            'StaProtocolo' => $staProtocolo ?: \ProtocoloRN::$TP_DOCUMENTO_GERADO,
        ]);
        return $objProtocoloDTO[0];
    }
    
    protected function consultarDocumentosFixture($dados)
    {
        $objDocumentoFixture = new DocumentoFixture();
        $objDocumentoDTO = $objDocumentoFixture->buscar($dados);
        return $objDocumentoDTO;
    }

    protected function cadastrarPublicacaoFixture($idProtocolo, $idDocumento)
    {
        $dataAtual = new DateTime();
        $objPublicacaoFixture = new PublicacaoFixture();
        $objPublicacaoDTO = $objPublicacaoFixture->carregar([
            'IdDocumento' => $idDocumento,
            'IdProtocolo' => $idProtocolo,
            'PublicacaoIO' => $dataAtual->modify('-45 days')->format('d/m/Y H:i:s')
        ]);
        return $objPublicacaoDTO;
    }

    protected function buscarIdSerieDoDocumento($tipoDocumento)
    {
        $serieDTO = new \SerieDTO();
        $serieDTO->setStrNome($tipoDocumento);
        $serieDTO->retNumIdSerie();
        $serieDTO->setNumMaxRegistrosRetorno(1);

        $objBD = new \SerieBD(\BancoSEI::getInstance());
        return $objBD->consultar($serieDTO);
    }

    protected function atualizarProcessoFixture($objProtocoloDTO, $dadosProcesso = [])
    {
        if (!is_null($dadosProcesso['DESCRICAO'])) {
            $parametros['Descricao'] = $dadosProcesso['DESCRICAO'];
        }

        $parametros['IdProtocolo'] = $objProtocoloDTO->getDblIdProtocolo();
        $objProtocoloFixture = new ProtocoloFixture();

        return $objProtocoloFixture->atualizar($parametros);
    }
  /**
   * Método cadastrarHipoteseLegal
   * 
   * Este método realiza o cadastro de uma hipótese legal para testes de trâmite de processos e documentos.
   * Ele recebe um array com os dados da hipótese legal, cria uma nova instância de `HipoteseLegalFixture`, 
   * e utiliza esses dados para carregar a hipótese legal no sistema.
   * 
   * @param array $hipotesLegal Um array contendo os dados da hipótese legal a ser cadastrada, com as seguintes chaves:
   * - `HIPOTESE_LEGAL` (string): O nome da hipótese legal.
   * - `HIPOTESE_LEGAL_BASE_LEGAL` (string): A base legal associada à hipótese.
   * - `HIPOTESE_LEGAL_DESCRICAO` (string) [opcional]: Uma descrição para a hipótese legal (padrão: 'Nova hipotese legal para testes').
   * - `HIPOTESE_LEGAL_STA_NIVEL_ACESSO` (int) [opcional]: O nível de acesso para a hipótese legal (padrão: nível restrito).
   * - `HIPOTESE_LEGAL_SIN_ATIVO` (string) [opcional]: Indicador de atividade da hipótese legal ('S' para ativo por padrão).
   * 
   * @return object $objHipoteseLegalDTO Retorna um objeto `HipoteseLegalDTO` contendo os dados da hipótese legal cadastrada.
   */
  protected function cadastrarHipoteseLegal($hipotesLegal)
  {
    // Criação de uma nova instância de HipoteseLegalFixture
    $objHipLegalFixture = new HipoteseLegalFixture();

    // Definição dos parâmetros para cadastro da hipótese legal
    $param = [
      'Nome' => $hipotesLegal['HIPOTESE_LEGAL'],
      'BaseLegal' => $hipotesLegal['HIPOTESE_LEGAL_BASE_LEGAL'],
      'Descricao' => $hipotesLegal['HIPOTESE_LEGAL_DESCRICAO'] ?? 'Nova hipotese legal para testes',
      'StaNivelAcesso' => $hipotesLegal['HIPOTESE_LEGAL_STA_NIVEL_ACESSO'] ?? \ProtocoloRN::$NA_RESTRITO,
      'SinAtivo' => $hipotesLegal['HIPOTESE_LEGAL_SIN_ATIVO'] ?? "S"
    ];

    // Carregar a hipótese legal com os parâmetros fornecidos
    $objHipoteseLegalDTO = $objHipLegalFixture->carregar($param);

    // Retorna o objeto DTO da hipótese legal cadastrada
    return $objHipoteseLegalDTO;
  }

    protected function cadastrarTipoProcedimentoFixture($dados = [])
    {
      $objTipoProcedimentoFixture = new TipoProcedimentoFixture();
      $objTipoProcedimentoDTO = $objTipoProcedimentoFixture->carregar([
        'Nome' => $dados['NOME']
      ]);

      return $objTipoProcedimentoDTO;
    }

}
