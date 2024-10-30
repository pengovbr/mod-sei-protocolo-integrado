<?php

/**
 * GDJustificativaFixture / GDJustificativaDTO / GDJustificativaBD
 */
class GDJustificativaFixture extends \FixtureBase
{
    protected $objMdGdJustificativaDTO;

    public function __construct()
    {
        $this->objMdGdJustificativaDTO = new \MdGdJustificativaDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdJustificativaDTO = $this->consultar($dados);
        if ($objMdGdJustificativaDTO) {
            return $objMdGdJustificativaDTO;
        }

        $this->objMdGdJustificativaDTO->setStrStaTipo($dados['StaTipo']);
        $this->objMdGdJustificativaDTO->setStrNome($dados['Nome']);
        $this->objMdGdJustificativaDTO->setStrDescricao($dados['Descricao']);

        $objMdGdJustificativaBD = new \MdGdJustificativaBD($this->inicializarObjInfraIBanco());
        return $objMdGdJustificativaBD->cadastrar($this->objMdGdJustificativaDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdJustificativaDTO = new \MdGdJustificativaDTO();

        $objMdGdJustificativaDTO->setNumIdJustificativa($dados['IdJustificativa']);
        $objMdGdJustificativaDTO->retTodos();

        $objMdGdJustificativaBD = new \MdGdJustificativaBD($this->inicializarObjInfraIBanco());
        return $objMdGdJustificativaBD->consultar($objMdGdJustificativaDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdJustificativaDTO = $this->consultar($dados);
        if (!$objMdGdJustificativaDTO) {
            return false;
        }

        $objMdGdJustificativaBD = new \MdGdJustificativaBD($this->inicializarObjInfraIBanco());
        return $objMdGdJustificativaBD->excluir($objMdGdJustificativaDTO);
    }
}