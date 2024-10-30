<?php

/**
 * GDAnotacaoPendenciaFixture / GDAnotacaoPendenciaDTO / GDAnotacaoPendenciaBD
 */
class GDAnotacaoPendenciaFixture extends \FixtureBase
{
    protected $objMdGdAnotacaoPendenciaDTO;

    public function __construct()
    {
        $this->objMdGdAnotacaoPendenciaDTO = new \MdGdAnotacaoPendenciaDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdAnotacaoPendenciaDTO = $this->consultar($dados);
        if ($objMdGdAnotacaoPendenciaDTO) {
            return $objMdGdAnotacaoPendenciaDTO;
        }

        $this->objMdGdAnotacaoPendenciaDTO->setNumIdUnidade($dados['IdUnidade']);
        $this->objMdGdAnotacaoPendenciaDTO->setStrAnotacao($dados['Anotacao']);

        $objMdGdAnotacaoPendenciaBD = new \MdGdAnotacaoPendenciaBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnotacaoPendenciaBD->cadastrar($this->objMdGdAnotacaoPendenciaDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdAnotacaoPendenciaDTO = new \MdGdAnotacaoPendenciaDTO();

        $objMdGdAnotacaoPendenciaDTO->setNumIdAnotacaoPendencia($dados['IdAnotacaoPendencia']);
        $objMdGdAnotacaoPendenciaDTO->retTodos();

        $objMdGdAnotacaoPendenciaBD = new \MdGdAnotacaoPendenciaBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnotacaoPendenciaBD->consultar($objMdGdAnotacaoPendenciaDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdAnotacaoPendenciaDTO = $this->consultar($dados);
        if (!$objMdGdAnotacaoPendenciaDTO) {
            return false;
        }

        $objMdGdAnotacaoPendenciaBD = new \MdGdAnotacaoPendenciaBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnotacaoPendenciaBD->excluir($objMdGdAnotacaoPendenciaDTO);
    }
}