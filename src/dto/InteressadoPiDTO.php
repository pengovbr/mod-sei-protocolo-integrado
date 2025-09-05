<?php

require_once DIR_SEI_WEB.'/SEI.php';

class InteressadoPiDTO implements JsonSerializable {
    private $id;
    private $nome;
    private $natureza;
    private $cpf;
    private $cnpj;
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

    // Getter for 'natureza'
    public function getNatureza() {
      return $this->natureza;
    }

    // Setter for 'natureza'
    public function setNatureza($natureza) {
        $this->natureza = $natureza;
        return $this;
    }

    // Getter for 'cpf'
    public function getCpf() {
      return $this->cpf;
    }

    // Setter for 'cpf'
    public function setCpf($cpf) {
        $this->cpf = $cpf;
        return $this;
    }

    // Getter for 'cnpj'
    public function getCnpj() {
      return $this->cnpj;
    }

    // Setter for 'cnpj'
    public function setCnpj($cnpj) {
        $this->cnpj = $cnpj;
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
            'natureza' => $this->natureza,
            'cpf' => $this->cpf,
            'cnpj' => $this->cnpj,
            'documento' => $this->documento
        ];
    }
    
}