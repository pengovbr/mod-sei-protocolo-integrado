<?php

/**
 * GDListaElimProcedimentoFixture / GDListaElimProcedimentoDTO / GDListaElimProcedimentoBD
 */
class GDListaElimProcedimentoFixture extends \FixtureBase
{
    protected $objMdGdListaElimProcedimentoDTO;

    public function __construct()
    {
        $this->objMdGdListaElimProcedimentoDTO = new \MdGdListaElimProcedimentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdListaElimProcedimentoDTO = $this->consultar($dados);
        if ($objMdGdListaElimProcedimentoDTO) {
            return $objMdGdListaElimProcedimentoDTO;
        }

        // $this->objMdGdListaElimProcedimentoDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $this->objMdGdListaElimProcedimentoDTO->setStrNumero($dados['Numero']);

        $objMdGdListaElimProcedimentoBD = new \MdGdListaElimProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaElimProcedimentoBD->cadastrar($this->objMdGdListaElimProcedimentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdListaElimProcedimentoDTO = new \MdGdListaElimProcedimentoDTO();

        $objMdGdListaElimProcedimentoDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $objMdGdListaElimProcedimentoDTO->retTodos();

        $objMdGdListaElimProcedimentoBD = new \MdGdListaElimProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaElimProcedimentoBD->consultar($objMdGdListaElimProcedimentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdListaElimProcedimentoDTO = $this->consultar($dados);
        if (!$objMdGdListaElimProcedimentoDTO) {
            return false;
        }

        $objMdGdListaElimProcedimentoBD = new \MdGdListaElimProcedimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaElimProcedimentoBD->excluir($objMdGdListaElimProcedimentoDTO);
    }
}