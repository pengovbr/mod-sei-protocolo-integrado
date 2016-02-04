<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 13/10/2009 - criado por mga
*
* Versão do Gerador de Código: 1.29.1
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoRN extends InfraRN {
    
  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }
  protected function listarConectado(ProtocoloIntegradoDTO $objProtocoloDTO) {
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_mensagens_listar',__METHOD__,$objProtocoloDTO);
  
      //Regras de Negocio
      //$objInfraException = new InfraException();
  
      //$objInfraException->lancarValidacoes();
  
  
      $objProtocoloBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->listar($objProtocoloDTO);
  
      //Auditoria
  
      return $ret;
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Tarefas.',$e);
    }
  }
  protected function consultarConectado(ProtocoloIntegradoDTO $objProtocoloDTO) {
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_mensagens_listar',__METHOD__,$objProtocoloDTO);
  
      //Regras de Negocio
      //$objInfraException = new InfraException();
  
      //$objInfraException->lancarValidacoes();
  
  
      $objProtocoloBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
      $ret = $objProtocoloBD->consultar($objProtocoloDTO);
  
      //Auditoria
  
      return $ret;
  
    }catch(Exception $e){
      throw new InfraException('Erro listando Tarefas.',$e);
    }
  }
  protected function alterarControlado(ProtocoloIntegradoDTO $protocoloIntegradoDTO){
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_mensagens_alterar',__METHOD__,$protocoloIntegradoDTO);
  
      //Regras de Negocio
      $objInfraException = new InfraException();
  
      /*if ($objTarefaDTO->isSetStrNome()){
        $this->validarStrNome($objTarefaDTO, $objInfraException);
      }
      
      if ($objTarefaDTO->isSetStrSinHistoricoResumido()){
        $this->validarStrSinHistoricoResumido($objTarefaDTO, $objInfraException);
      }
      */
      $objInfraException->lancarValidacoes();
  
      $objProtocoloBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
      $objProtocoloBD->alterar($protocoloIntegradoDTO);
  
  
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicação no Protocolo Integrado.',$e);
    }
  }
  protected function alterarOperacoesPublicacaoControlado(ProtocoloIntegradoDTO $protocoloIntegradoDTO){
    try {
  
      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_integrado_configurar_publicacao',__METHOD__,$protocoloIntegradoDTO);
  
      //Regras de Negocio
      $objInfraException = new InfraException();
  
      /*if ($objTarefaDTO->isSetStrNome()){
        $this->validarStrNome($objTarefaDTO, $objInfraException);
      }
      
      if ($objTarefaDTO->isSetStrSinHistoricoResumido()){
        $this->validarStrSinHistoricoResumido($objTarefaDTO, $objInfraException);
      }
      */
      $objInfraException->lancarValidacoes();
  
      $objProtocoloBD = new ProtocoloIntegradoBD($this->getObjInfraIBanco());
      $objProtocoloBD->alterar($protocoloIntegradoDTO);
  
  
    }catch(Exception $e){
      throw new InfraException('Erro alterando Mensagens de Publicação no Protocolo Integrado.',$e);
    }
  }
  public function transformarMensagemOperacao($numAtividade,$strNomeTarefa){
  		
		$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
    	$objAtributoAndamentoDTO->retStrNome();
		$objAtributoAndamentoDTO->retStrValor();
		$objAtributoAndamentoDTO->retStrIdOrigem();
		$objAtributoAndamentoDTO->retNumIdAtividade();
    	$objAtributoAndamentoDTO->setNumIdAtividade($numAtividade);
		
    	$objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_ASC);
    	
    	$objAtributoAndamentoRN = new AtributoAndamentoRN();
    	$arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);
	
		$arrObjAtributoAndamentoDTOPorNome = InfraArray::indexarArrInfraDTO($arrObjAtributoAndamentoDTO,'Nome',true);
		
	      	 
		for($k=0;$k<count($arrObjAtributoAndamentoDTO);$k++){
			
			
			$objAtributoAndamentoDTO = $arrObjAtributoAndamentoDTO[$k];
			switch($objAtributoAndamentoDTO->getStrNome()){
					
				case 'DOCUMENTO':
							if (isset($arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'])){
								    $dto = new DocumentoDTO();
								    $dto->retDblIdDocumento();
								    $dto->retStrProtocoloDocumentoFormatado();
								    $dto->retStrNomeSerie();
								    $dto->retStrStaProtocoloProtocolo();
								    $dto->setDblIdDocumento(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'],'IdOrigem'),InfraDTO::$OPER_IN);
								      
								    $objDocumentoRN = new DocumentoRN();
								    $arrObjDocumentoDTO = InfraArray::indexarArrInfraDTO($objDocumentoRN->listarRN0008($dto),'IdDocumento');
									$strNomeTarefa = $this->substituirAtributoDocumentoHistorico($objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, $strNomeTarefa);
		      	  		
					      	}
		      	  			break;
		      	  		
	      	  	case 'DOCUMENTOS':
						$bolAcaoDocumentoVisualizar = SessaoSEI::getInstance()->verificarPermissao('documento_visualizar');
	      	  			$strNomeTarefa = $this->substitutirAtributoMultiploDocumentos($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'], $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar,$strNomeTarefa);
	      	  			break;
		      	  		
	      	  	case 'NIVEL_ACESSO':
						$objProtocoloRN = new ProtocoloRN();
	      	  			$arrObjNivelAcessoDTO = InfraArray::indexarArrInfraDTO($objProtocoloRN->listarNiveisAcessoRN0878(),'StaNivel');
						$strNomeTarefa = str_replace('@NIVEL_ACESSO@', $arrObjNivelAcessoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(),$strNomeTarefa);
						
						break;
	
 	  			case 'GRAU_SIGILO':
						
						$arrObjGrauSigiloDTO = InfraArray::indexarArrInfraDTO(ProtocoloRN::listarGrausSigiloso(),'StaGrau');
					    foreach($arrObjGrauSigiloDTO as $objGrauSigiloDTO){
					      	  	
					      	  $objGrauSigiloDTO->setStrDescricao(InfraString::transformarCaixaBaixa($objGrauSigiloDTO->getStrDescricao()));
					    }
     	  				if ($objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_GERACAO_PROCEDIMENTO ||
	      	          		$objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_GERACAO_DOCUMENTO ||
	      	          		$objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_RECEBIMENTO_DOCUMENTO ||
	      	         		$objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_GLOBAL){
	      	          	
     	  				 		 $strNomeTarefa = str_replace('@GRAU_SIGILO@', ' ('.$arrObjGrauSigiloDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao().')', $strNomeTarefa);
	      	      
				  		}else{
				  		
      	      				$strNomeTarefa = str_replace('@GRAU_SIGILO@', ' '.$arrObjGrauSigiloDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
      	    		 	}
 	  				break;
				
  				case 'HIPOTESE_LEGAL':
					
					
					$objHipoteseLegalDTO = new HipoteseLegalDTO();
			      	$objHipoteseLegalDTO->setBolExclusaoLogica(false);
			      	$objHipoteseLegalDTO->retNumIdHipoteseLegal();
			      	$objHipoteseLegalDTO->retStrNome();
			      	$objHipoteseLegalDTO->retStrBaseLegal();
			      	 
			      	$objHipoteseLegalRN = new HipoteseLegalRN();
			      	$arrObjHipoteseLegalDTO = InfraArray::indexarArrInfraDTO($objHipoteseLegalRN->listar($objHipoteseLegalDTO),'IdHipoteseLegal');	
					
  					if($objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_PROCESSO ||
  					    $objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_GRAU_SIGILO_PROCESSO ||
  					    $objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_HIPOTESE_LEGAL_PROCESSO ||
  					    $objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_DOCUMENTO ||
  					    $objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_GRAU_SIGILO_DOCUMENTO ||
  					    $objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_HIPOTESE_LEGAL_DOCUMENTO){
				    	if ($objAtributoAndamentoDTO->getStrIdOrigem()==null){
				    		$strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', '"não informada"', $strNomeTarefa);
				    	}else if(is_array($arrObjHipoteseLegalDTO)){
				    		$strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', HipoteseLegalINT::formatarHipoteseLegal($arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrNome(), $arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrBaseLegal()), $strNomeTarefa);
				    	}
  					}else if(is_array($arrObjHipoteseLegalDTO)){
  						$strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', ', '.HipoteseLegalINT::formatarHipoteseLegal($arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrNome(), $arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrBaseLegal()), $strNomeTarefa);
  					}
					
  					break;
 	  			
  				 case 'DATA_AUTUACAO':
  					if ($objAtributoAndamentoDTO->getStrValor()!=null){
  						$strNomeTarefa = str_replace('@DATA_AUTUACAO@', ' (autuado em '.$objAtributoAndamentoDTO->getStrValor().')', $strNomeTarefa);
  					}
  					break;

  					case 'TIPO_CONFERENCIA':
							$objTipoConferenciaDTO = new TipoConferenciaDTO();
					      	$objTipoConferenciaDTO->setBolExclusaoLogica(false);
					      	$objTipoConferenciaDTO->retNumIdTipoConferencia();
					      	$objTipoConferenciaDTO->retStrDescricao();
							$objTipoConferenciaRN = new TipoConferenciaRN();
      						$arrObjTipoConferenciaDTO = InfraArray::indexarArrInfraDTO($objTipoConferenciaRN->listar($objTipoConferenciaDTO),'IdTipoConferencia');
      
  						if ($objAtributoAndamentoDTO->getNumIdAtividade()==TarefaRN::$TI_ALTERACAO_TIPO_CONFERENCIA_DOCUMENTO){
  							if ($objAtributoAndamentoDTO->getStrIdOrigem()==null){
  								$strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', '"não informado"', $strNomeTarefa);
  							}else{
  								$strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', $arrObjTipoConferenciaDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
  							}
  						}else{
  						  $strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', ', conferido com '.$arrObjTipoConferenciaDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
  						}
  						break;

					case 'PROCESSO':
						if (isset($arrObjAtributoAndamentoDTOPorNome['PROCESSO'])){
					      	  $dto = new ProcedimentoDTO();
					      	  $dto->retDblIdProcedimento();
							  $bolAcaoProcedimentoTrabalhar = SessaoSEI::getInstance()->verificarPermissao('procedimento_trabalhar');
							  $objObjProcedimentoRN =  new ProcedimentoRN();
					          $dto->setDblIdProcedimento(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['PROCESSO'],'IdOrigem'),InfraDTO::$OPER_IN);
					          $arrObjProcedimentoDTO = InfraArray::indexarArrInfraDTO($objObjProcedimentoRN->listarRN0278($dto),'IdProcedimento');
							  $strNomeTarefa = $this->substituirAtributoProcessoHistorico($objAtributoAndamentoDTO, $arrObjProcedimentoDTO, $bolAcaoProcedimentoTrabalhar, $strNomeTarefa);
						
					     }
						break;
  						
					case 'USUARIO':
						
						if ($objAtributoAndamentoDTO->getStrValor()!=null){
							$arrValor = explode('¥',$objAtributoAndamentoDTO->getStrValor());
							$strSubstituicao = $arrValor[0];
						}else{
							$strSubstituicao = '';
						}
						$strNomeTarefa = str_replace('@USUARIO@', $strSubstituicao, $strNomeTarefa);
						break;
						
					case 'USUARIOS':
						
						$strNomeTarefa = $this->substitutirAtributoMultiploUsuarios($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTOPorNome['USUARIO'], $strNomeTarefa);
						break;
									
					case 'UNIDADE':
							
							$arrValor = explode('¥',$objAtributoAndamentoDTO->getStrValor());
							$strSubstituicao = $arrValor[0];
							$strNomeTarefa = str_replace('@UNIDADE@', $strSubstituicao, $strNomeTarefa);
							break;

					case 'BLOCO':
							$bolAcaoRelBlocoProtocoloListar = SessaoSEI::getInstance()->verificarPermissao('rel_bloco_protocolo_listar');
							if (isset($arrObjAtributoAndamentoDTOPorNome['BLOCO'])){
							      $objBlocoDTO = new BlocoDTO();
							      $objBlocoDTO->retNumIdBloco();
							      $objBlocoDTO->setNumIdBloco(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['BLOCO'],'IdOrigem'),InfraDTO::$OPER_IN);
							      $objBlocoDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
							      
							      $objBlocoRN = new BlocoRN();
							      $arrIdBloco = InfraArray::converterArrInfraDTO($objBlocoRN->listarRN1277($objBlocoDTO),'IdBloco');
							      
							      $objRelBlocoUnidadeDTO = new RelBlocoUnidadeDTO();
							      $objRelBlocoUnidadeDTO->retNumIdBloco();
							      $objRelBlocoUnidadeDTO->setNumIdBloco(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['BLOCO'],'IdOrigem'),InfraDTO::$OPER_IN);
							      $objRelBlocoUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
							      $objRelBlocoUnidadeRN = new RelBlocoUnidadeRN();
							      $arrIdBloco = array_unique(array_merge($arrIdBloco, InfraArray::converterArrInfraDTO($objRelBlocoUnidadeRN->listarRN1304($objRelBlocoUnidadeDTO),'IdBloco')));
						     	  $strNomeTarefa = $this->substituirAtributoBlocoHistorico($objAtributoAndamentoDTO, $arrIdBloco, $bolAcaoRelBlocoProtocoloListar, $strNomeTarefa);
							
							 }
							break;
					
					case 'DATA_HORA':
							$strNomeTarefa = str_replace('@DATA_HORA@', substr($objAtributoAndamentoDTO->getStrValor(),0,16), $strNomeTarefa);
							break;
							
					case 'USUARIO_ANULACAO':
							$arrValor = explode('¥',$objAtributoAndamentoDTO->getStrValor());
							$strSubstituicao = $arrValor[0];
							$strNomeTarefa = str_replace('@USUARIO_ANULACAO@', $strSubstituicao, $strNomeTarefa);
							break;
							
					case 'INTERESSADO':
							$arrValor = explode('¥',$objAtributoAndamentoDTO->getStrValor());
							$strSubstituicao = $arrValor[0];
							$strNomeTarefa = str_replace('@INTERESSADO@', $strSubstituicao, $strNomeTarefa);
							break;

					case 'LOCALIZADOR':
							$bolAcaoLocalizadorProtocolosListar = SessaoSEI::getInstance()->verificarPermissao('localizador_protocolos_listar');
							$strNomeTarefa = $this->substituirAtributoLocalizadorHistorico($objAtributoAndamentoDTO, $bolAcaoLocalizadorProtocolosListar, $strNomeTarefa);
							break;
							
					case 'ANEXO':
							 
							$strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
							$bolAcaoDownload = SessaoSEI::getInstance()->verificarPermissao('documento_download_anexo');
							if ($bolAcaoDownload){
								$objAnexoDTO = new AnexoDTO();
								$objAnexoDTO->setNumIdAnexo($objAtributoAndamentoDTO->getStrIdOrigem());
								 
								$objAnexoRN = new AnexoRN();
								$strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
								
							}
							$strNomeTarefa = str_replace('@ANEXO@', $strSubstituicao, $strNomeTarefa);
							break;
							    
        	  	default:
        	  		$strNomeTarefa = str_replace('@'.$objAtributoAndamentoDTO->getStrNome().'@', $objAtributoAndamentoDTO->getStrValor(), $strNomeTarefa);
      
			}	
			
		}
		$strNomeTarefa = str_replace(array('@NIVEL_ACESSO@','@GRAU_SIGILO@','@TIPO_CONFERENCIA@', '@DATA_AUTUACAO@','@HIPOTESE_LEGAL@'),'',$strNomeTarefa);
		 
		return $strNomeTarefa;

  }
  public function substituirAtributoDocumentoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, &$strNomeTarefa){
    $strSubstituicao = $this->montarAtributoDocumentoHistorico($objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar);
    $strNomeTarefa = str_replace('@DOCUMENTO@', $strSubstituicao, $strNomeTarefa);
    return $strNomeTarefa;
  }	
  public function montarAtributoDocumentoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar){
    
    $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();

    if (!isset($arrObjDocumentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()])){
    	$strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
    }else{
     
        $strSubstituicao = $arrObjDocumentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrProtocoloDocumentoFormatado().' ('.$arrObjDocumentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrNomeSerie().')';
      
  	}
    
    return $strSubstituicao;
  }
  public function substitutirAtributoMultiploDocumentos($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, &$strNomeTarefa){
	  if (is_array($arrObjAtributoAndamentoDTO)){
	    
		  $arr = array();
		  
	  	  $numAtributosTotal = count($arrObjAtributoAndamentoDTO);
	  	  for($i=0;$i<$numAtributosTotal;$i++){
	  	    if ($arrObjAtributoAndamentoDTO[$i]->getNumIdAtividade()==$objAtributoAndamentoDTO->getNumIdAtividade()){
	  	      $arr[] = $arrObjAtributoAndamentoDTO[$i];
	  	    }  
	  	  } 
	      
	      $n = count($arr);
	      $strValorMultiplo = '';
	      for($i=0;$i<$n;$i++){
	        if ($strValorMultiplo!=''){
	          if ($i == ($n-1)){
	            $strValorMultiplo .= ' e ';
	          }else{
	            $strValorMultiplo .= ', ';
	          }
	        }
	        $strValorMultiplo .= $this->montarAtributoDocumentoHistorico($arr[$i], $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar);
	      } 
	      
	      $strNomeTarefa = str_replace('#DOCUMENTOS#', $strValorMultiplo, $strNomeTarefa);
	  }
	  return $strNomeTarefa;
  }
  public function substituirAtributoProcessoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjProcedimentoDTO, $bolAcaoProcedimentoTrabalhar, &$strNomeTarefa){
    
    $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();

    if ($bolAcaoProcedimentoTrabalhar){
     
        $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
      
    }
    
    $strNomeTarefa = str_replace('@PROCESSO@', $strSubstituicao, $strNomeTarefa);
	return $strNomeTarefa;
  }
  public function substituirAtributoLocalizadorHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $bolAcaoLocalizadorProtocoloListar, &$strNomeTarefa){

  	$arrIdOrigem = explode('¥',$objAtributoAndamentoDTO->getStrIdOrigem());

  	//só mostra link se o localizador é da unidade atual
    $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();	
    
    
    $strNomeTarefa = str_replace('@LOCALIZADOR@', $strSubstituicao, $strNomeTarefa);
	return $strNomeTarefa;
  }
  public function substitutirAtributoMultiploUsuarios($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTO, &$strNomeTarefa){
	  if (is_array($arrObjAtributoAndamentoDTO)){
	    
		  $arr = array();
		  
	  	  $numAtributosTotal = count($arrObjAtributoAndamentoDTO);
	  	  for($i=0;$i<$numAtributosTotal;$i++){
	  	    if ($arrObjAtributoAndamentoDTO[$i]->getNumIdAtividade()==$objAtributoAndamentoDTO->getNumIdAtividade()){
	  	      $arr[] = $arrObjAtributoAndamentoDTO[$i];
	  	    }  
	  	  } 
	      
	      $n = count($arr);
	      $strValorMultiplo = '';
	      for($i=0;$i<$n;$i++){
	        if ($strValorMultiplo!=''){
	          if ($i == ($n-1)){
	            $strValorMultiplo .= ' e ';
	          }else{
	            $strValorMultiplo .= ', ';
	          }
	        }
	        $arrValor = explode('¥',$arr[$i]->getStrValor());
	        $strValorMultiplo .= $arrValor[0];
	      } 
	      
	      $strNomeTarefa = str_replace('#USUARIOS#', $strValorMultiplo, $strNomeTarefa);
	  }
	  return $strNomeTarefa;
  }
 public function substituirAtributoBlocoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrIdBloco, $bolAcaoRelBlocoProtocoloListar, &$strNomeTarefa){
    
    $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
    
    $strNomeTarefa = str_replace('@BLOCO@', $strSubstituicao, $strNomeTarefa);
	return $strNomeTarefa;
  }

 
}
?>