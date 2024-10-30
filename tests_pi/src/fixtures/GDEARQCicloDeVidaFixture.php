<?php

/**
 * GDEARQCicloDeVidaFixture / GDEARQCicloDeVidaDTO / GDEARQCicloDeVidaBD
 */
class GDEARQCicloDeVidaFixture extends \FixtureBase
{
    protected $objMdGdEARQCicloDeVidaDTO;

    public function __construct()
    {
        $this->objMdGdEARQCicloDeVidaDTO = new \MdGdEARQCicloDeVidaDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdEARQCicloDeVidaDTO = $this->consultar($dados);
        if ($objMdGdEARQCicloDeVidaDTO) {
            return $objMdGdEARQCicloDeVidaDTO;
        }

        $this->objMdGdEARQCicloDeVidaDTO->setStrProcesso($dados['Processo']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrTipo($dados['Tipo']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrDocumento($dados['Documento']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrLote($dados['Lote']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrAgente($dados['Agente']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrAgente($dados['Agente']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrDetalhe($dados['Detalhe']);
        $this->objMdGdEARQCicloDeVidaDTO->setStrDataCriado($dados['DataCriado']);

        $objMdGdEARQCicloDeVidaBD = new \MdGdEARQCicloDeVidaBD($this->inicializarObjInfraIBanco());
        return $objMdGdEARQCicloDeVidaBD->cadastrar($this->objMdGdEARQCicloDeVidaDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdEARQCicloDeVidaDTO = new \MdGdEARQCicloDeVidaDTO();

        $objMdGdEARQCicloDeVidaDTO->setNumId($dados['Id']);
        $objMdGdEARQCicloDeVidaDTO->retTodos();

        $objMdGdEARQCicloDeVidaBD = new \MdGdEARQCicloDeVidaBD($this->inicializarObjInfraIBanco());
        return $objMdGdEARQCicloDeVidaBD->consultar($objMdGdEARQCicloDeVidaDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdEARQCicloDeVidaDTO = $this->consultar($dados);
        if (!$objMdGdEARQCicloDeVidaDTO) {
            return false;
        }

        $objMdGdEARQCicloDeVidaBD = new \MdGdEARQCicloDeVidaBD($this->inicializarObjInfraIBanco());
        return $objMdGdEARQCicloDeVidaBD->excluir($objMdGdEARQCicloDeVidaDTO);
    }
}