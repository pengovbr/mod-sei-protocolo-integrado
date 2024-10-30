<?php

/**
 * GDEliminacaoFixture / GDEliminacaoDTO / GDEliminacaoBD
 */
class GDEliminacaoFixture extends \FixtureBase
{
    protected $objMdGdEliminacaoDTO;

    public function __construct()
    {
        $this->objMdGdEliminacaoDTO = new \MdGdEliminacaoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdEliminacaoDTO = $this->consultar($dados);
        if ($objMdGdEliminacaoDTO) {
            return $objMdGdEliminacaoDTO;
        }

        $this->objMdGdEliminacaoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdEliminacaoDTO->setStrAssinante($dados['Assinante']);
        $this->objMdGdEliminacaoDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $this->objMdGdEliminacaoDTO->setNumIdUnidade($dados['IdUnidade']);
        $this->objMdGdEliminacaoDTO->setNumIdVeiculoPublicacao($dados['IdVeiculoPublicacao']);
        $this->objMdGdEliminacaoDTO->setNumPagina($dados['Pagina']);
        $this->objMdGdEliminacaoDTO->setNumIdSecaoImprensaNacional($dados['IdSecaoImprensaNacional']);
        $this->objMdGdEliminacaoDTO->setDthDataImprensa($dados['DataImprensa']);
        $this->objMdGdEliminacaoDTO->setDthDataEliminacao($dados['DataEliminacao']);


        $objMdGdEliminacaoBD = new \MdGdEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdEliminacaoBD->cadastrar($this->objMdGdEliminacaoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdEliminacaoDTO = new \MdGdEliminacaoDTO();

        $objMdGdEliminacaoDTO->setNumIdEliminacao($dados['IdEliminacao']);
        $objMdGdEliminacaoDTO->retTodos();

        $objMdGdEliminacaoBD = new \MdGdEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdEliminacaoBD->consultar($objMdGdEliminacaoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdEliminacaoDTO = $this->consultar($dados);
        if (!$objMdGdEliminacaoDTO) {
            return false;
        }

        $objMdGdEliminacaoBD = new \MdGdEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdEliminacaoBD->excluir($objMdGdEliminacaoDTO);
    }
}