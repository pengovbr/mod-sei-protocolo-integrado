<?php

/**
 * GDParametroFixture / GDParametroDTO / GDParametroBD
 */
class GDParametroFixture extends \FixtureBase
{
    protected $objMdGdParametroDTO;

    public function __construct()
    {
        $this->objMdGdParametroDTO = new \MdGdParametroDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdParametroDTO = $this->consultar($dados);
        if ($objMdGdParametroDTO) {
            return $objMdGdParametroDTO;
        }
       
        $this->objMdGdParametroDTO->setStrNome($dados['Nome']);
        $this->objMdGdParametroDTO->setStrValor($dados['Valor']);

        $objMdGdParametroBD = new \MdGdParametroBD($this->inicializarObjInfraIBanco());
        return $objMdGdParametroBD->cadastrar($this->objMdGdParametroDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdParametroDTO = new \MdGdParametroDTO();

        $objMdGdParametroDTO->setStrNome($dados['Nome']);
        $objMdGdParametroDTO->retTodos();

        $objMdGdParametroBD = new \MdGdParametroBD($this->inicializarObjInfraIBanco());
        return $objMdGdParametroBD->consultar($objMdGdParametroDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdParametroDTO = $this->consultar($dados);
        if (!$objMdGdParametroDTO) {
            return false;
        }

        $objMdGdParametroBD = new \MdGdParametroBD($this->inicializarObjInfraIBanco());
        return $objMdGdParametroBD->excluir($objMdGdParametroDTO);
    }
}