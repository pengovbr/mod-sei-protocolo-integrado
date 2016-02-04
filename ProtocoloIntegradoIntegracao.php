<?

class ProtocoloIntegradoIntegracao implements ISeiIntegracao{
  
  public function __construct(){
  	
	
		infraAdicionarPath(dirname(__FILE__).'rn');
		infraAdicionarPath(dirname(__FILE__).'dto');
		infraAdicionarPath(dirname(__FILE__).'bd');
		infraAdicionarPath(dirname(__FILE__).'ws');
  }

  public function montarBotaoProcedimento(SeiIntegracaoDTO $objSeiIntegracaoDTO){
    return array();
  }
  
  public function montarIconeProcedimento(SeiIntegracaoDTO $objSeiIntegracaoDTO){
    return array();
  }
  
  public function montarBotaoDocumento(SeiIntegracaoDTO $objSeiIntegracaoDTO){
    return array();
  }
  
  public function montarIconeDocumento(SeiIntegracaoDTO $objSeiIntegracaoDTO){
    return array();
  }
  public function montarBotaoControleProcessos(){
    return array();
  }
  public function montarIconeControleProcessos($arrObjProcedimentoDTOIntegracao){
    return array();
  }
  
  
  public function excluirProcedimento(ProcedimentoDTO $objProcedimentoDTO){
  }
  
  public function atualizarConteudoDocumento(DocumentoDTO $objDocumentoDTO){
  }
    
  public function excluirDocumento(DocumentoDTO $objDocumentoDTO){
  }
   public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO){
  	
  }
}
?>