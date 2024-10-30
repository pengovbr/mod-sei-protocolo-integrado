<?php

/**
 * GDDesarquivamentoFixture / GDDesarquivamentoDTO / GDDesarquivamentoBD
 */
class GDDesarquivamentoFixture extends \FixtureBase
{
    protected $objMdGdDesarquivamentoDTO;

    public function __construct()
    {
        $this->objMdGdDesarquivamentoDTO = new \MdGdDesarquivamentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdDesarquivamentoDTO = $this->consultar($dados);
        if ($objMdGdDesarquivamentoDTO) {
            return $objMdGdDesarquivamentoDTO;
        }

        $this->objMdGdDesarquivamentoDTO->setNumIdArquivamento($dados['IdArquivamento']);
        $this->objMdGdDesarquivamentoDTO->setDblIdProcedimento($dados['IdProcedimento']);
        $this->objMdGdDesarquivamentoDTO->setDblIdDespachoDesarquivamento($dados['IdDespachoDesarquivamento']);
        $this->objMdGdDesarquivamentoDTO->setNumIdJustificativa($dados['IdJustificativa']);
        $this->objMdGdDesarquivamentoDTO->setDthDataDesarquivamento($dados['DataDesarquivamento']);

        $objMdGdDesarquivamentoBD = new \MdGdDesarquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdDesarquivamentoBD->cadastrar($this->objMdGdDesarquivamentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdDesarquivamentoDTO = new \MdGdDesarquivamentoDTO();

        $objMdGdDesarquivamentoDTO->setNumIdDesarquivamento($dados['IdDesarquivamento']);
        $objMdGdDesarquivamentoDTO->retTodos();

        $objMdGdDesarquivamentoBD = new \MdGdDesarquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdDesarquivamentoBD->consultar($objMdGdDesarquivamentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdDesarquivamentoDTO = $this->consultar($dados);
        if (!$objMdGdDesarquivamentoDTO) {
            return false;
        }

        $objMdGdDesarquivamentoBD = new \MdGdDesarquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdDesarquivamentoBD->excluir($objMdGdDesarquivamentoDTO);
    }
}