<?php

require_once DIR_SEI_WEB.'/SEI.php';

class DocumentoPiDTO implements JsonSerializable {
    private $assunto;
    private $protocolo;
    private $especie;
    private $dataHoraProducao;
    private $dataHoraProducaoTz;
    private $interessados = [];
    private $protocoloRelacionados = [];
    private $protocoloAnteriores = [];
    private $historico = [];

    public function getAssunto() {
        return $this->assunto;
    }

    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    public function getProtocolo() {
        return $this->protocolo;
    }

    public function setProtocolo($protocolo) {
        $this->protocolo = $protocolo;
    }

    public function getEspecie() {
        return $this->especie;
    }

    public function setEspecie($especie) {
        $this->especie = $especie;
    }

    public function getDataHoraProducao() {
        return $this->dataHoraProducao;
    }

    public function setDataHoraProducao($dataHoraProducao) {
        $this->dataHoraProducao = $dataHoraProducao;
    }

    public function getDataHoraProducaoTz() {
        return $this->dataHoraProducaoTz;
    }

    public function setDataHoraProducaoTz($dataHoraProducaoTz) {
        $this->dataHoraProducaoTz = $dataHoraProducaoTz;
    }

    public function getInteressados() {
        return $this->interessados;
    }

    public function addInteressado($interessado) {
        $this->interessados[] = $interessado;
    }

    public function getProtocoloRelacionados() {
        return $this->protocoloRelacionados;
    }

    public function addProtocoloRelacionado($protocoloRelacionado) {
        $this->protocoloRelacionados[] = $protocoloRelacionado;
    }

    public function getProtocoloAnteriores() {
        return $this->protocoloAnteriores;
    }

    public function addProtocoloAnteriore($protocoloAnterior) {
        $this->protocoloAnteriores[] = $protocoloAnterior;
    }

    public function getHistorico() {
        return $this->historico;
    }

    public function addHistorico($historico) {
        $this->historico[] = $historico;
    }

    public function jsonSerialize() {
        return [
            'assunto' => $this->assunto,
            'protocolo' => $this->protocolo,
            'especie' => $this->especie,
            'dataHoraProducao' => $this->dataHoraProducao,
            'dataHoraProducaoTz' => $this->dataHoraProducaoTz,
            'interessados' => $this->interessados,
            'protocoloRelacionados' => $this->protocoloRelacionados,
            'protocoloAnteriores' => $this->protocoloAnteriores,
            'historico' => $this->historico
        ];
    }
}