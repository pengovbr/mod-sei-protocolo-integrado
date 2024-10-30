<?php

/**
 * GDDocumentoFisicoElimFixture / GDDocumentoFisicoElimDTO / GDDocumentoFisicoElimBD
 */
class GDDocumentoFisicoElimFixture extends \FixtureBase
{
    protected $objMdGdDocumentoFisicoElimDTO;

    public function __construct()
    {
        $this->objMdGdDocumentoFisicoElimDTO = new \MdGdDocumentoFisicoElimDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdDocumentoFisicoElimDTO = $this->consultar($dados);
        if ($objMdGdDocumentoFisicoElimDTO) {
            return $objMdGdDocumentoFisicoElimDTO;
        }

        $this->objMdGdDocumentoFisicoElimDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $this->objMdGdDocumentoFisicoElimDTO->setNumIdDocumento($dados['IdDocumento']);

        $objMdGdDocumentoFisicoElimBD = new \MdGdDocumentoFisicoElimBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoElimBD->cadastrar($this->objMdGdDocumentoFisicoElimDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdDocumentoFisicoElimDTO = new \MdGdDocumentoFisicoElimDTO();

        $objMdGdDocumentoFisicoElimDTO->setNumIdListaEliminacao($dados['IdListaEliminacao']);
        $objMdGdDocumentoFisicoElimDTO->retTodos();

        $objMdGdDocumentoFisicoElimBD = new \MdGdDocumentoFisicoElimBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoElimBD->consultar($objMdGdDocumentoFisicoElimDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdDocumentoFisicoElimDTO = $this->consultar($dados);
        if (!$objMdGdDocumentoFisicoElimDTO) {
            return false;
        }

        $objMdGdDocumentoFisicoElimBD = new \MdGdDocumentoFisicoElimBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoElimBD->excluir($objMdGdDocumentoFisicoElimDTO);
    }
}