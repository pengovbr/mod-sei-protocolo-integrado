<?php

/**
 * GDUnidadeArquivamentoFixture / GDUnidadeArquivamentoDTO / GDUnidadeArquivamentoBD
 */
class GDUnidadeArquivamentoFixture extends \FixtureBase
{
    protected $objMdGdUnidadeArquivamentoDTO;

    public function __construct()
    {
        $this->objMdGdUnidadeArquivamentoDTO = new \MdGdUnidadeArquivamentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdUnidadeArquivamentoDTO = $this->consultar($dados);
        if ($objMdGdUnidadeArquivamentoDTO) {
            return $objMdGdUnidadeArquivamentoDTO;
        }

        $this->objMdGdUnidadeArquivamentoDTO->setNumIdUnidadeOrigem($dados['IdUnidadeOrigem']);
        $this->objMdGdUnidadeArquivamentoDTO->setNumIdUnidadeDestino($dados['IdUnidadeDestino']);

        $objMdGdUnidadeArquivamentoBD = new \MdGdUnidadeArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdUnidadeArquivamentoBD->cadastrar($this->objMdGdUnidadeArquivamentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdUnidadeArquivamentoDTO = new \MdGdUnidadeArquivamentoDTO();

        $objMdGdUnidadeArquivamentoDTO->setNumIdUnidadeArquivamento($dados['IdUnidadeArquivamento']);
        $objMdGdUnidadeArquivamentoDTO->retTodos();

        $objMdGdUnidadeArquivamentoBD = new \MdGdUnidadeArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdUnidadeArquivamentoBD->consultar($objMdGdUnidadeArquivamentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdUnidadeArquivamentoDTO = $this->consultar($dados);
        if (!$objMdGdUnidadeArquivamentoDTO) {
            return false;
        }

        $objMdGdUnidadeArquivamentoBD = new \MdGdUnidadeArquivamentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdUnidadeArquivamentoBD->excluir($objMdGdUnidadeArquivamentoDTO);
    }
}