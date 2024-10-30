<?php

/**
 * GDAnexoFixture / GDAnexoDTO / GDAnexoBD
 */
class GDAnexoFixture extends \FixtureBase
{
    protected $objMdGdAnexoDTO;

    public function __construct()
    {
        $this->objMdGdAnexoDTO = new \MdGdAnexoDTO();
    }

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {
        $objMdGdAnexoDTO = $this->consultar($dados);
        if ($objMdGdAnexoDTO) {
            return $objMdGdAnexoDTO;
        }

        $this->objMdGdAnexoDTO->setNumIdAnexo($dados['IdAnexo']);
        $this->objMdGdAnexoDTO->setStrNome($dados['Nome']);
        $this->objMdGdAnexoDTO->setNumIdProtocolo($dados['IdProtocolo']);
        $this->objMdGdAnexoDTO->setNumIdUnidade($dados['IdUnidade']);
        $this->objMdGdAnexoDTO->setNumIdUsuario($dados['IdUsuario']);
        $this->objMdGdAnexoDTO->setNumTamanho($dados['Tamanho']);
        $this->objMdGdAnexoDTO->setDthInclusao($dados['Inclusao']);
        $this->objMdGdAnexoDTO->setNumIdProjeto($dados['IdProjeto']);
        $this->objMdGdAnexoDTO->setStrHash($dados['Hash']);

        $objMdGdAnexoBD = new \MdGdAnexoBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnexoBD->cadastrar($this->objMdGdAnexoDTO);
    }
    
    public function consultar($dados = [])
    {
        $objMdGdAnexoDTO = new \MdGdAnexoDTO();

        $objMdGdAnexoDTO->setNumId($dados['Id']);
        $objMdGdAnexoDTO->retTodos();

        $objMdGdAnexoBD = new \MdGdAnexoBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnexoBD->consultar($objMdGdAnexoDTO);
    }

    public function excluir($dados = [])
    {

        $objMdGdAnexoDTO = $this->consultar($dados);
        if (!$objMdGdAnexoDTO) {
            return false;
        }

        $objMdGdAnexoBD = new \MdGdAnexoBD($this->inicializarObjInfraIBanco());
        return $objMdGdAnexoBD->excluir($objMdGdAnexoDTO);
    }
}