<?php

/**
 * GDArquivamentoFixture / GDArquivamentoDTO / GDArquivamentoBD
 */
class GDArquivamentoFixture extends \FixtureBase
{
    protected $objMdGdArquivamentoDTO;

    public function __construct()
    {
        $this->objMdGdArquivamentoDTO = new \MdGdArquivamentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdArquivamentoDTO = $this->consultar($dados);
        if ($objMdGdArquivamentoDTO) {
            return $objMdGdArquivamentoDTO;
        }

        // $this->objMdGdArquivamentoDTO->setNumIdArquivamento($dados['IdArquivamento'] ?? 110000001);
        $this->objMdGdArquivamentoDTO->setDblIdProcedimento($dados['IdProcedimento']);
        $this->objMdGdArquivamentoDTO->setDblIdDespachoArquivamento($dados['IdDespachoArquivamento']);
        $this->objMdGdArquivamentoDTO->setNumIdJustificativa($dados['IdJustificativa']);
        $this->objMdGdArquivamentoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdArquivamentoDTO->setNumIdUnidadeCorrente($dados['IdUnidadeCorrente']);
        $this->objMdGdArquivamentoDTO->setNumIdUnidadeIntermediaria($dados['IdUnidadeIntermediaria']);
        $this->objMdGdArquivamentoDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $this->objMdGdArquivamentoDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $this->objMdGdArquivamentoDTO->setNumIdAssunto($dados['IdAssunto']);
        $this->objMdGdArquivamentoDTO->setDthDataArquivamento($dados['DataArquivamento']);
        $this->objMdGdArquivamentoDTO->setDthDataAvaliacao($dados['DataAvaliacao']);
        $this->objMdGdArquivamentoDTO->setDthDataGuardaCorrente($dados['DataGuardaCorrente']);
        $this->objMdGdArquivamentoDTO->setDthDataGuardaIntermediaria($dados['DataGuardaIntermediaria']);
        $this->objMdGdArquivamentoDTO->setNumGuardaCorrente($dados['GuardaCorrente']);
        $this->objMdGdArquivamentoDTO->setNumGuardaIntermediaria($dados['GuardaIntermediaria']);
        $this->objMdGdArquivamentoDTO->setStrStaGuarda($dados['StaGuarda']);
        $this->objMdGdArquivamentoDTO->setStrSituacao($dados['Situacao']);
        $this->objMdGdArquivamentoDTO->setStrStaDestinacaoFinal($dados['StaDestinacaoFinal']);
        $this->objMdGdArquivamentoDTO->setStrSinCondicionante($dados['SinCondicionante']);
        $this->objMdGdArquivamentoDTO->setStrSinAtivo($dados['SinAtivo']);
        $this->objMdGdArquivamentoDTO->setStrObservacaoDevolucao($dados['ObservacaoDevolucao']);
        $this->objMdGdArquivamentoDTO->setStrObservacaoEliminacao($dados['ObservacaoEliminacao']);
        $this->objMdGdArquivamentoDTO->setStrObservacaoRecolhimento($dados['ObservacaoObservacaoRecolhimentoDevolucao']);
        $this->objMdGdArquivamentoDTO->setNumIdOrgao($dados['IdOrgao']);

        $objMdGdArquivamentoBD = new \MdGdArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoBD->cadastrar($this->objMdGdArquivamentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdArquivamentoDTO = new \MdGdArquivamentoDTO();

        $objMdGdArquivamentoDTO->setNumIdArquivamento($dados['IdArquivamento']);
        $objMdGdArquivamentoDTO->retTodos();

        $objMdGdArquivamentoBD = new \MdGdArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoBD->consultar($objMdGdArquivamentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdArquivamentoDTO = $this->consultar($dados);
        if (!$objMdGdArquivamentoDTO) {
            return false;
        }

        $objMdGdArquivamentoBD = new \MdGdArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoBD->excluir($objMdGdArquivamentoDTO);
    }
}