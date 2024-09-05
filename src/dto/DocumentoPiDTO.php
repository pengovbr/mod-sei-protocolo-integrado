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
    private $historicoDocumentos = [];

    // Getter for 'assunto'
    public function getAssunto() {
        return $this->assunto;
    }

    // Setter for 'assunto'
    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    // Getter for 'protocolo'
    public function getProtocolo() {
        return $this->protocolo;
    }

    // Setter for 'protocolo'
    public function setProtocolo($protocolo) {
        $this->protocolo = $protocolo;
    }

    // Getter for 'especie'
    public function getEspecie() {
        return $this->especie;
    }

    // Setter for 'especie'
    public function setEspecie($especie) {
        $this->especie = $especie;
    }

    // Getter for 'dataHoraProducao'
    public function getDataHoraProducao() {
        return $this->dataHoraProducao;
    }

    // Setter for 'dataHoraProducao'
    public function setDataHoraProducao($dataHoraProducao) {
        $this->dataHoraProducao = $dataHoraProducao;
    }

    // Getter for 'dataHoraProducaoTz'
    public function getDataHoraProducaoTz() {
        return $this->dataHoraProducaoTz;
    }

    // Setter for 'dataHoraProducaoTz'
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

    public function getHistoricoDocumentos() {
        return $this->historicoDocumentos;
    }

    public function addHistoricoDocumento($historicoDocumento) {
        $this->historicoDocumentos[] = $historicoDocumento;
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
            'historicoDocumentos' => $this->historicoDocumentos
        ];
    }
}