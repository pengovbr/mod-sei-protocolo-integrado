<?php

require_once DIR_SEI_WEB.'/SEI.php';

class HistoricoDocumentoPiDTO implements JsonSerializable {

    private $id;
    private $unidade;
    private $operacao;
    private $documento;
    private $dataHoraOperacao;
    private $atualizadoEm;

    public function getId() {
        return $this->id;
    }

    public function setUnidade($unidade) {
        $this->unidade = $unidade;
    }

    public function getUnidade() {
        return $this->unidade;
    }

    public function setOperacao($operacao) {
        $this->operacao = $operacao;
    }

    public function getOperacao() {
        return $this->operacao;
    }

    public function setDocumento(DocumentoDto $documento) {
        $this->documento = $documento;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function setDataHoraOperacao($dataHoraOperacao) {
        $this->dataHoraOperacao = $dataHoraOperacao;
    }

    public function getDataHoraOperacao() {
        return $this->dataHoraOperacao;
    }

    public function setAtualizadoEm($atualizadoEm) {
        $this->atualizadoEm = $atualizadoEm;
    }

    public function getAtualizadoEm() {
        return $this->atualizadoEm;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'unidade' => $this->unidade,
            'operacao' => $this->operacao,
            'documento' => $this->documento,
            'dataHoraOperacao' => $this->dataHoraOperacao,
            'atualizadoEm' => $this->atualizadoEm
        ];
    }
}