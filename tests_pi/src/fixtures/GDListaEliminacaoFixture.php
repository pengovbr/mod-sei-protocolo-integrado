<?php

/**
 * GDListaEliminacaoFixture / GDListaEliminacaoDTO / GDListaEliminacaoBD
 */
class GDListaEliminacaoFixture extends \FixtureBase
{
    protected $objMdGdListaEliminacaoDTO;

    public function __construct()
    {
        $this->objMdGdListaEliminacaoDTO = new \MdGdListaEliminacaoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdListaEliminacaoDTO = $this->consultar($dados);
        if ($objMdGdListaEliminacaoDTO) {
            return $objMdGdListaEliminacaoDTO;
        }

        $this->objMdGdListaEliminacaoDTO->setDblIdProcedimentoEliminacao($dados['IdProcedimentoEliminacao']);
        $this->objMdGdListaEliminacaoDTO->setDblIdDocumentoEliminacao($dados['IdDocumentoEliminacao']);
        $this->objMdGdListaEliminacaoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdListaEliminacaoDTO->setStrNumero($dados['Numero']);
        $this->objMdGdListaEliminacaoDTO->setDthEmissaoListagem($dados['EmissaoListagem']);
        $this->objMdGdListaEliminacaoDTO->setNumAnoLimiteInicio($dados['AnoLimiteInicio']);
        $this->objMdGdListaEliminacaoDTO->setNumAnoLimiteFim($dados['AnoLimiteFim']);
        $this->objMdGdListaEliminacaoDTO->setStrSituacao($dados['Situacao']);
        $this->objMdGdListaEliminacaoDTO->setNumQtdProcessos($dados['QtdProcessos']);
        $this->objMdGdListaEliminacaoDTO->setStrSinDocumentosFisicos($dados['SinDocumentosFisicos']);
        $this->objMdGdListaEliminacaoDTO->setStrAnotacao($dados['Anotacao']);

        $objMdGdListaEliminacaoBD = new \MdGdListaEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaEliminacaoBD->cadastrar($this->objMdGdListaEliminacaoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdListaEliminacaoDTO = new \MdGdListaEliminacaoDTO();

        $objMdGdListaEliminacaoDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $objMdGdListaEliminacaoDTO->retTodos();

        $objMdGdListaEliminacaoBD = new \MdGdListaEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaEliminacaoBD->consultar($objMdGdListaEliminacaoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdListaEliminacaoDTO = $this->consultar($dados);
        if (!$objMdGdListaEliminacaoDTO) {
            return false;
        }

        $objMdGdListaEliminacaoBD = new \MdGdListaEliminacaoBD($this->inicializarObjInfraIBanco());
        return $objMdGdListaEliminacaoBD->excluir($objMdGdListaEliminacaoDTO);
    }
}