<?php

require_once DIR_SEI_WEB.'/SEI.php';

class ProtocoloIntegradoBD extends InfraBD {
    
    public function __construct(InfraIBanco $objInfraIBanco) {
        parent::__construct( $objInfraIBanco );
    }
     /**
	  ** Função Criada para recuperar o nome chaves estrangeiras em base Mysql da tabela de pacote
	  ** Dependendo da versão a rodar o script de atualização para 1.1.3,a foreign key terá nomes diferentes.
	  ** 
	**/
	public function recuperarChavesEstrangeirasv112(){

	       $objPacoteDTO = new ProtocoloIntegradoDTO();
	       $chaveEstrangeira = ""; 
	       if (BancoSEI::getInstance() instanceof InfraMySql || BancoSEI::getInstance() instanceof InfraSqlServer){

	           $sql = "SELECT constraint_name FROM information_schema.TABLE_CONSTRAINTS  WHERE information_schema.TABLE_CONSTRAINTS.CONSTRAINT_TYPE = 'FOREIGN KEY' AND information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = 'sei' AND information_schema.TABLE_CONSTRAINTS.TABLE_NAME = 'protocolo_integrado';";
	           $rs = $this->getObjInfraIBanco()->consultarSql($sql);
	           //var_dump($rs);
	           return $rs;


	       }

	}
    
}

?>