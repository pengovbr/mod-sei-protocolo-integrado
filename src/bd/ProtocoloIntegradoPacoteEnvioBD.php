<?php

require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoPacoteEnvioBD extends InfraBD {
    
    public function __construct(InfraIBanco $objInfraIBanco) {
        parent::__construct($objInfraIBanco);
    }
    
    /*
     * * Funчуo retorna dados de uma coluna da tabela de pacotes que щ passada por parтmetro
     *
     */
    public function recuperarColunaTabelaPacote($coluna) {
        
        $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
        $sql = 'select ' . $coluna . ' from ' . $objPacoteDTO->getStrNomeTabela();
        $rs = $this->getObjInfraIBanco()->consultarSql($sql);
        
        $arrPacotesDTO = array();
        foreach ($rs as $item) {
            $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
            $objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($item[0]);
            array_push($arrPacotesDTO, $objPacoteDTO);
        }
        
        return $arrPacotesDTO;
        
    }
    
    /*
     * Funчуo utilizada para recuperar nome da chave primУЁria no Sql Server na tabela de pacotes
     *
     */
    public function recuperarChavePrimaria() {
        
        $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
        $chavePrimaria = "";
        if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            $sql = "SELECT constraint_name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME ='" . $objPacoteDTO->getStrNomeTabela() . "' and constraint_type ='PRIMARY KEY'";
            $rs = $this->getObjInfraIBanco()->consultarSql( $sql );
            
            // var_dump($rs);
            foreach ($rs as $item) {
                if ($item [0] != null) {
                    $chavePrimaria = $item[0];
                }
            }
            
            return $chavePrimaria;
        }
        
    }
    
    /**
      ** Funчуo Criada para recuperar o nome chaves estrangeiras em base Mysql da tabela de pacote
      ** Dependendo da versуo a rodar o script de atualizaчуo para 1.1.3,a foreign key terс nomes diferentes.
      ** 
      **/
      public function recuperarChavesEstrangeirasv112(){

       $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
       $chaveEstrangeira = ""; 
       if (BancoSEI::getInstance() instanceof InfraMySql || BancoSEI::getInstance() instanceof InfraSqlServer){

           $sql = "SELECT constraint_name FROM information_schema.TABLE_CONSTRAINTS  WHERE information_schema.TABLE_CONSTRAINTS.CONSTRAINT_TYPE = 'FOREIGN KEY' AND information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = 'sei' AND information_schema.TABLE_CONSTRAINTS.TABLE_NAME = 'protocolo_integrado_pacote_envio';";
           $rs = $this->getObjInfraIBanco()->consultarSql($sql);
           //var_dump($rs);
           return $rs;


       }

    }
    
}

?>