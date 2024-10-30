<?php

/**
 * GDListaRecolhimentoFixture / GDListaRecolhimentoDTO / GDListaRecolhimentoBD
 */
class GDListaRecolhimentoFixture extends \FixtureBase
{
    protected $objMdGdListaRecolhimentoDTO;

    public function __construct()
    {
        $this->objMdGdListaRecolhimentoDTO = new \MdGdListaRecolhimentoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdListaRecolhimentoDTO = $this->consultar($dados);
        if ($objMdGdListaRecolhimentoDTO) {
            return $objMdGdListaRecolhimentoDTO;
        }

        $this->objMdGdListaRecolhimentoDTO->setDblIdProcedimentoRecolhimento($dados['IdProcedimentoRecolhimento']);
        $this->objMdGdListaRecolhimentoDTO->setDblIdDocumentoRecolhimento($dados['IdDocumentoRecolhimento']);
        $this->objMdGdListaRecolhimentoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdListaRecolhimentoDTO->setStrNumero($dados['Numero']);
        $this->objMdGdListaRecolhimentoDTO->setDthEmissaoListagem($dados['EmissaoListagem']);
        $this->objMdGdListaRecolhimentoDTO->setNumAnoLimiteInicio($dados['AnoLimiteInicio']);
        $this->objMdGdListaRecolhimentoDTO->setNumAnoLimiteFim($dados['AnoLimiteFim']);
        $this->objMdGdListaRecolhimentoDTO->setNumQtdProcessos($dados['QtdProcessos']);
        $this->objMdGdListaRecolhimentoDTO->setStrSinDocumentosFisicos($dados['SinDocumentosFisicos']);
        $this->objMdGdListaRecolhimentoDTO->setStrSituacao($dados['Situacao']);
        $this->objMdGdListaRecolhimentoDTO->setStrAnotacao($dados['Anotacao']);


        $objMdGdListaRecolhimentoBD = new \MdGdListaRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaRecolhimentoBD->cadastrar($this->objMdGdListaRecolhimentoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdListaRecolhimentoDTO = new \MdGdListaRecolhimentoDTO();

        $objMdGdListaRecolhimentoDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $objMdGdListaRecolhimentoDTO->retTodos();

        $objMdGdListaRecolhimentoBD = new \MdGdListaRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaRecolhimentoBD->consultar($objMdGdListaRecolhimentoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdListaRecolhimentoDTO = $this->consultar($dados);
        if (!$objMdGdListaRecolhimentoDTO) {
            return false;
        }

        $objMdGdListaRecolhimentoBD = new \MdGdListaRecolhimentoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaRecolhimentoBD->excluir($objMdGdListaRecolhimentoDTO);
    }
}