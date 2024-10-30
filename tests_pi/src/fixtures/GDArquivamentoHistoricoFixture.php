<?php

/**
 * GDArquivamentoHistoricoFixture / GDArquivamentoHistoricoDTO / GDArquivamentoHistoricoBD
 */
class GDArquivamentoHistoricoFixture extends \FixtureBase
{
    protected $objMdGdArquivamentoHistoricoDTO;

    public function __construct()
    {
        $this->objMdGdArquivamentoHistoricoDTO = new \MdGdArquivamentoHistoricoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdArquivamentoHistoricoDTO = $this->consultar($dados);
        if ($objMdGdArquivamentoHistoricoDTO) {
            return $objMdGdArquivamentoHistoricoDTO;
        }

        $this->objMdGdArquivamentoHistoricoDTO->setNumIdArquivamento($dados['IdArquivamento']);
        $this->objMdGdArquivamentoHistoricoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdArquivamentoHistoricoDTO->setNumIdUnidade($dados['IdUnidade']);
        $this->objMdGdArquivamentoHistoricoDTO->setStrSituacaoAntiga($dados['SituacaoAntiga']);
        $this->objMdGdArquivamentoHistoricoDTO->setStrSituacaoAtual($dados['SituacaoAtual']);
        $this->objMdGdArquivamentoHistoricoDTO->setStrDescricao($dados['Descricao']);
        $this->objMdGdArquivamentoHistoricoDTO->setDthHistorico($dados['Historico']);

        $objMdGdArquivamentoHistoricoBD = new \MdGdArquivamentoHistoricoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoHistoricoBD->cadastrar($this->objMdGdArquivamentoHistoricoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdArquivamentoHistoricoDTO = new \MdGdArquivamentoHistoricoDTO();

        $objMdGdArquivamentoHistoricoDTO->setNumIdArquivamentoHistorico($dados['IdArquivamentoHistorico']);
        $objMdGdArquivamentoHistoricoDTO->retTodos();

        $objMdGdArquivamentoHistoricoBD = new \MdGdArquivamentoHistoricoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoHistoricoBD->consultar($objMdGdArquivamentoHistoricoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdArquivamentoHistoricoDTO = $this->consultar($dados);
        if (!$objMdGdArquivamentoHistoricoDTO) {
            return false;
        }

        $objMdGdArquivamentoHistoricoBD = new \MdGdArquivamentoHistoricoBD($this->inicializarObjInfraIBanco());
        return $objMdGdArquivamentoHistoricoBD->excluir($objMdGdArquivamentoHistoricoDTO);
    }
}