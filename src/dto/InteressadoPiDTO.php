<?php

require_once DIR_SEI_WEB.'/SEI.php';

class InteressadoPiDTO implements JsonSerializable {
    private $id;
    private $nome;
    private $documento;

    // Getter for 'id'
    public function getId() {
        return $this->id;
    }

    // Setter for 'id'
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    // Getter for 'nome'
    public function getNome() {
        return $this->nome;
    }

    // Setter for 'nome'
    public function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }

    // Getter for 'documento'
    public function getDocumento() {
        return $this->documento;
    }

    // Setter for 'documento'
    public function setDocumento($documento) {
        $this->documento = $documento;
        return $this;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'documento' => $this->documento
        ];
    }
    
}