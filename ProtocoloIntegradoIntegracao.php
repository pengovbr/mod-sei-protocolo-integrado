<?

class ProtocoloIntegradoIntegracao extends SeiIntegracao {
  
  public function __construct(){
  	
	
		infraAdicionarPath(dirname(__FILE__).'rn');
		infraAdicionarPath(dirname(__FILE__).'dto');
		infraAdicionarPath(dirname(__FILE__).'bd');
		infraAdicionarPath(dirname(__FILE__).'ws');
  }

  
}
?>