<?php

/**
 * GDRecolhimentoFixture / GDRecolhimentoDTO / GDRecolhimentoBD
 */
class GDRecolhimentoFixture extends \FixtureBase
{
    protected $objMdGdRecolhimentoDTO;

    public function __construct()
    {
        $this->objMdGdRecolhimentoDTO = new \MdGdRecolhimentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdRecolhimentoDTO = $this->consultar($dados);
        if ($objMdGdRecolhimentoDTO) {
            return $objMdGdRecolhimentoDTO;
        }

        $this->objMdGdRecolhimentoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdRecolhimentoDTO->setNumIdUnidade($dados['IdUnidade']);
        $this->objMdGdRecolhimentoDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $this->objMdGdRecolhimentoDTO->setDthDataRecolhimento($dados['DataRecolhimento']);

        $objMdGdRecolhimentoBD = new \MdGdRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolhimentoBD->cadastrar($this->objMdGdRecolhimentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdRecolhimentoDTO = new \MdGdRecolhimentoDTO();

        $objMdGdRecolhimentoDTO->setNumIdRecolhimento($dados['IdRecolhimento']);
        $objMdGdRecolhimentoDTO->retTodos();

        $objMdGdRecolhimentoBD = new \MdGdRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolhimentoBD->consultar($objMdGdRecolhimentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdRecolhimentoDTO = $this->consultar($dados);
        if (!$objMdGdRecolhimentoDTO) {
            return false;
        }

        $objMdGdRecolhimentoBD = new \MdGdRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdRecolhimentoBD->excluir($objMdGdRecolhimentoDTO);
    }
}