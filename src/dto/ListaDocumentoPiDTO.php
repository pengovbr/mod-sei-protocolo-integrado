<?php

require_once DIR_SEI_WEB.'/SEI.php';

class ListaDocumentoPiDTO implements JsonSerializable {
    private $documentos = [];

    public function getDocumentos() {
        return $this->documentos;
    }

    public function addDocumento($documento) {
        $this->documentos[] = $documento;
    }

    public function jsonSerialize() {
        return [
            'documentos' => $this->documentos
        ];
    }
}