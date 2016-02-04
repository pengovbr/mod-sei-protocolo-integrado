<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 02/05/2011 - criado por mga
*
* Vers�o do Gerador de C�digo: 1.31.0
*
* Vers�o no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloIntegradoMonitoramentoProcessosBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }
  public function consultarAtividadesIntegracaoCargaInicial(){

    try{
      
	  $sql = "select a.*  FROM  atividade a ".
 			 "inner join protocolo p on a.id_protocolo=p.id_protocolo ".
 			 "inner join protocolo_integrado pi on a.id_tarefa = pi.id_tarefa ".
			 "WHERE (sta_arquivamento<>'A' AND sta_protocolo = 'P' AND (sta_nivel_acesso_global = 0 or (sta_nivel_acesso_global=1 and exists (select * from protocolo_integrado_parametros where sin_publicacao_restritos='S'))  ))  AND sin_publicar = 'S' ".
			 "AND exists (select * from documento d  inner join protocolo p2 on p2.id_protocolo_agrupador=d.id_documento inner join rel_protocolo_protocolo rpp on rpp.id_protocolo_2 = p2.id_protocolo where rpp.id_protocolo_1 = p.id_protocolo and (d.crc_assinatura IS NOT NULL or d.id_tipo_conferencia is not null) )".
			 "AND not exists(select * from protocolo_integrado_monitoramento_processos pimp where pimp.id_atividade=a.id_atividade) AND ".
			 "dth_abertura>=(select dta_corte from protocolo_integrado_parametros) AND dth_abertura <= (select dta_corte_final from protocolo_integrado_parametros) order by a.id_protocolo";
      
	  $rs = $this->getObjInfraIBanco()->consultarSql($sql);
	  
	
	  $arrObjAtividadeMonitoradas = array();
	  $arrPacotes = array();
	  return $this->formataAtividadesMonitoradasParaDTO($rs);
	
	  
      
    }catch(Exception $e){
      throw new InfraException('Erro ao carregar atividades.',$e);
    }
  }
  public function consultarNovasOperacoesProcesso($limit, $numUnidadeTeste=null){

    try{
      
	  /*$sql = "select a.*  FROM  atividade a ".
 			 "inner join protocolo p on a.id_protocolo=p.id_protocolo ".
 			 "inner join protocolo_integrado pi on a.id_tarefa = pi.id_tarefa ".
			 "WHERE (sta_arquivamento<>'A' AND sta_protocolo = 'P' and sta_nivel_acesso_global = 0)  AND sin_publicar = 'S' ".
			 "AND exists (select * from documento d  inner join protocolo p2 on p2.id_protocolo_agrupador=d.id_documento inner join rel_protocolo_protocolo rpp on rpp.id_protocolo_2 = p2.id_protocolo where rpp.id_protocolo_1 = p.id_protocolo and ( d.crc_assinatura IS NOT NULL or d.id_tipo_conferencia is not null) )".
			 "AND not exists(select * from protocolo_integrado_monitoramento_processos pimp where pimp.id_atividade=a.id_atividade) AND ".
			 "dth_abertura>(select max(dth_abertura) from atividade a2 inner join protocolo_integrado_monitoramento_processos pimp on a2.id_atividade = pimp.id_atividade)  order by a.id_protocolo";*/
	  $sql = "select a.*  FROM  atividade a ".
 			 "INNER JOIN protocolo p on a.id_protocolo=p.id_protocolo ".
 			 "INNER JOIN protocolo_integrado pi on a.id_tarefa = pi.id_tarefa ".
			 "WHERE (sta_arquivamento<>'A' AND sta_protocolo = 'P' and  (sta_nivel_acesso_global = 0 or (sta_nivel_acesso_global=1 and exists (select * from protocolo_integrado_parametros where sin_publicacao_restritos='S'))) ) ".
			 "AND sin_publicar = 'S' ".
			 "AND exists (select * from documento d  inner join protocolo p2 on p2.id_protocolo_agrupador=d.id_documento inner join rel_protocolo_protocolo rpp on rpp.id_protocolo_2 = p2.id_protocolo where rpp.id_protocolo_1 = p.id_protocolo and d.sin_bloqueado='S' )".
			 "AND not exists(select * from protocolo_integrado_monitoramento_processos pimp where pimp.id_atividade=a.id_atividade)";
			 
	  if ($numUnidadeTeste!=null){
	  	  $sql = $sql." AND p.id_unidade_geradora NOT IN (".$numUnidadeTeste.") ";
	  }
	  
	  $sql = $sql." order by a.dth_abertura limit ".$limit;
    
    
	  $rs = $this->getObjInfraIBanco()->consultarSql($sql);
	  
	  $arrObjAtividadeMonitoradas = array();
	  $arrPacotes = array();
	  
	return $this->formataAtividadesMonitoradasParaDTO($rs);
      
    }catch(Exception $e){
      throw new InfraException('Erro ao carregar atividades.',$e);
    }
  }
  public function formataAtividadesMonitoradasParaDTO($resultadoAtividades){
  	
	  $i=0;
      foreach($resultadoAtividades as $item){
      	  
        $objProtocoloIntegradoMonitoramentoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
		
        $objProtocoloIntegradoMonitoramentoDTO->setNumIdAtividade($this->getObjInfraIBanco()->formatarLeituraNum($item['id_atividade']));
        $objProtocoloIntegradoMonitoramentoDTO->setDthDataCadastro(date('d/m/Y H:i:s'));
        $objProtocoloIntegradoMonitoramentoDTO->setNumIdProtocolo($item['id_protocolo']);
       
        $arrObjAtividadeMonitoradas[$i] =  $objProtocoloIntegradoMonitoramentoDTO;
        $i++;
       
      }
      
      return $arrObjAtividadeMonitoradas;
	
	 
	
  }
  public function consultarParticipantesDocumentosAssinadosProcesso($idProtocolo){
  	
		 /* $sql = "select distinct con.id_contato,con.nome,con.sigla from atributo_andamento aa ".
				 "inner join atividade a  on a.id_atividade = aa.id_atividade ".
				 "inner join documento d on d.id_documento = aa.id_origem ".
				 "inner join procedimento p on p.id_procedimento = d.id_procedimento ".
				 "inner join protocolo pro on pro.id_protocolo = a.id_protocolo ".
				 "inner join tipo_procedimento tp on tp.id_tipo_procedimento = p.id_tipo_procedimento ".
				 "inner join rel_protocolo_protocolo rpp on rpp.id_protocolo_1=pro.id_protocolo ".
				 "inner join participante par on par.id_protocolo = rpp.id_protocolo_2 ".
				 "inner join contato con on con.id_contato=par.id_contato ".
				 "where aa.nome = 'DOCUMENTO'  and  (a.id_tarefa = 5 or a.id_tarefa=13) and (d.crc_assinatura IS NOT NULL or id_tipo_conferencia is not null)  and par.sta_participacao = '".ParticipanteRN::$TP_INTERESSADO."' and pro.id_protocolo=".$idProtocolo.
    			 " order by a.dth_abertura ";*/
    	$sql = "select distinct con.id_contato,con.nome,con.sigla".
				" from rel_protocolo_protocolo rpp".
				" inner join participante par on par.id_protocolo=rpp.id_protocolo_2".
				" inner join contato con on con.id_contato=par.id_contato". 
				" inner join documento d on d.id_documento=par.id_protocolo".
				" where rpp.id_protocolo_1=".$idProtocolo.
				" and par.sta_participacao = '".ParticipanteRN::$TP_INTERESSADO."' ". 
				" and (d.crc_assinatura IS NOT NULL or id_tipo_conferencia is not null)"; 
		  
		 $resultadoDocumentos = $this->getObjInfraIBanco()->consultarSql($sql);
		 $objParticipanteDTO = new ProtocoloDTO();
		 $arrParticipanteDTO = array();
		 foreach($resultadoDocumentos as $item){
		 		
				$objParticipanteDTO = new ParticipanteDTO();
				$objParticipanteDTO->setStrNomeContato($item['nome']);
			 	$objParticipanteDTO->setNumIdContato($item['id_contato']);
			 	$objParticipanteDTO->setStrSiglaContato($item['sigla']);
				array_push($arrParticipanteDTO,$objParticipanteDTO);
	
  		}
		return $arrParticipanteDTO; 
  }	
  public function consultarAtividadesPublicacao($idPacote){
  			
			/*  $sql = "select a.id_atividade,a.id_tarefa,a.id_protocolo,a.dth_abertura,a.id_unidade,pi.mensagem_publicacao  from protocolo_integrado_pacote_envio pepi ".
			  	  " inner join protocolo_integrado_monitoramento_processos pimp on pimp.id_protocolo_integrado_pacote_envio = pepi.id_protocolo_integrado_pacote_envio ".
			  	  " inner join atividade a on pimp.id_atividade = a.id_atividade ".
			  	  " inner join protocolo_integrado pi on pi.id_tarefa=a.id_tarefa ".
				 "where pepi.id_protocolo_integrado_pacote_envio = ".$idPacote." and  exists ( select * from protocolo_integrado pi where pi.id_tarefa = a.id_tarefa and sin_publicar='S'  )".
				 " and pepi.sta_integracao<>'I'".
    			 " order by a.dth_abertura ";*/
    		$sql = "select a.id_atividade,a.id_tarefa,a.id_protocolo,a.dth_abertura,a.id_unidade,pi.mensagem_publicacao  from protocolo_integrado_pacote_envio pepi ".
			  	  " inner join protocolo_integrado_monitoramento_processos pimp on pimp.id_protocolo_integrado_pacote_envio = pepi.id_protocolo_integrado_pacote_envio ".
			  	  " inner join atividade a on pimp.id_atividade = a.id_atividade ".
			  	  " inner join protocolo_integrado pi on pi.id_tarefa=a.id_tarefa ".
				 "where pepi.id_protocolo_integrado_pacote_envio = ".$idPacote.
				 " and  pi.sin_publicar='S'".
				 /*" and pepi.sta_integracao<>'I'".*/
    			 " order by a.dth_abertura";
  			$arrProtocoloIntegradoMonitoramentoProcessosDTO = array();
			$resultadoDocumentos = $this->getObjInfraIBanco()->consultarSql($sql);
			foreach($resultadoDocumentos as $item){
		 		
				$objProtocoloIntegradoMonitoramentoProcessosDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setNumIdAtividade($item['id_atividade']);
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setNumIdTarefa($item['id_tarefa']);
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setNumIdProtocolo($item['id_protocolo']);
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setStrMensagemPublicacao($item['mensagem_publicacao']);
				$objProtocoloIntegradoMonitoramentoProcessosDTO->setDthDataAbertura($item['dth_abertura']);
			 	$objProtocoloIntegradoMonitoramentoProcessosDTO->setNumIdUnidade($item['id_unidade']);
				array_push($arrProtocoloIntegradoMonitoramentoProcessosDTO,$objProtocoloIntegradoMonitoramentoProcessosDTO);
	
  			}
  			return $arrProtocoloIntegradoMonitoramentoProcessosDTO;
  }
}
?>