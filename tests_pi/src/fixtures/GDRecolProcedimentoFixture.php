<?php

/**
 * GDListaRecolProcedimentoFixture / GDListaRecolProcedimentoDTO / GDListaRecolProcedimentoBD
 */
class GDListaRecolProcedimentoFixture extends \FixtureBase
{
    protected $objMdGdListaRecolProcedimentoDTO;

    public function __construct()
    {
        $this->objMdGdListaRecolProcedimentoDTO = new \MdGdListaRecolProcedimentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdListaRecolProcedimentoDTO = $this->consultar($dados);
        if ($objMdGdListaRecolProcedimentoDTO) {
            return $objMdGdListaRecolProcedimentoDTO;
        }

        $this->objMdGdListaRecolProcedimentoDTO->setDblIdProcedimento($dados['IdProcedimento']);

        $objMdGdRecolProcedimentoBD = new \MdGdRecolProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolProcedimentoBD->cadastrar($this->objMdGdListaRecolProcedimentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdListaRecolProcedimentoDTO = new \MdGdListaRecolProcedimentoDTO();

        $objMdGdListaRecolProcedimentoDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $objMdGdListaRecolProcedimentoDTO->retTodos();

        $objMdGdRecolProcedimentoBD = new \MdGdRecolProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolProcedimentoBD->consultar($objMdGdListaRecolProcedimentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdListaRecolProcedimentoDTO = $this->consultar($dados);
        if (!$objMdGdListaRecolProcedimentoDTO) {
            return false;
        }

        $objMdGdRecolProcedimentoBD = new \MdGdRecolProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolProcedimentoBD->excluir($objMdGdListaRecolProcedimentoDTO);
    }
}