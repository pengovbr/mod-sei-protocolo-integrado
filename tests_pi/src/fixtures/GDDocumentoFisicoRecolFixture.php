<?php

/**
 * GDDocumentoFisicoRecolFixture / GDDocumentoFisicoRecolDTO / GDDocumentoFisicoRecolBD
 */
class GDDocumentoFisicoRecolFixture extends \FixtureBase
{
    protected $objMdGdDocumentoFisicoRecolDTO;

    public function __construct()
    {
        $this->objMdGdDocumentoFisicoRecolDTO = new \MdGdDocumentoFisicoRecolDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdDocumentoFisicoRecolDTO = $this->consultar($dados);
        if ($objMdGdDocumentoFisicoRecolDTO) {
            return $objMdGdDocumentoFisicoRecolDTO;
        }

        $this->objMdGdDocumentoFisicoRecolDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $this->objMdGdDocumentoFisicoRecolDTO->setDblIdDocumento($dados['IdDocumento']);
        
        $objMdGdDocumentoFisicoRecolBD = new \MdGdDocumentoFisicoRecolBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoRecolBD->cadastrar($this->objMdGdDocumentoFisicoRecolDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdDocumentoFisicoRecolDTO = new \MdGdDocumentoFisicoRecolDTO();

        $objMdGdDocumentoFisicoRecolDTO->setNumIdListaRecolhimento($dados['IdListaRecolhimento']);
        $objMdGdDocumentoFisicoRecolDTO->retTodos();

        $objMdGdDocumentoFisicoRecolBD = new \MdGdDocumentoFisicoRecolBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoRecolBD->consultar($objMdGdDocumentoFisicoRecolDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdDocumentoFisicoRecolDTO = $this->consultar($dados);
        if (!$objMdGdDocumentoFisicoRecolDTO) {
            return false;
        }

        $objMdGdDocumentoFisicoRecolBD = new \MdGdDocumentoFisicoRecolBD($this->inicializarObjInfraIBanco());
        return $objMdGdDocumentoFisicoRecolBD->excluir($objMdGdDocumentoFisicoRecolDTO);
    }
}