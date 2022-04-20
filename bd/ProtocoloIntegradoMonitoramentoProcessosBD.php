<?php
/**
* TRIBUNAL REGIONAL FEDERAL DA 4º REGIÃO
*
* 02/05/2011 - criado por mga
*
* Versão do Gerador de Código: 1.31.0
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../../SEI.php';

class ProtocoloIntegradoMonitoramentoProcessosBD extends InfraBD {

    const HINTSQLSERVER_NOLOCK = "WITH (NOLOCK)";

    private static $SQL_HINT_TABLE = "";

    private $maxIdAtividadeMonitorada;


    public function __construct(InfraIBanco $objInfraIBanco){
        parent::__construct($objInfraIBanco);

        if ($objInfraIBanco instanceof InfraSQLServer) {
            self::$SQL_HINT_TABLE = self::HINTSQLSERVER_NOLOCK;
        }
    }
   
    public function consultarNovasOperacoesProcessosNaoEnviados($maxIdAtividade, $limit, $numUnidadeTeste=null){
  		
        try {

            $topSQLServer = "";
            $restricaoMaxAtividade = "";
            if ($this->getObjInfraIBanco() instanceof InfraSQLServer) {
                $topSQLServer = "top ".$limit;
            }
            
            if ($maxIdAtividade!=null && $maxIdAtividade>0) {
                $restricaoMaxAtividade = "AND a.id_atividade<".$maxIdAtividade . " ";
            }

            $sql = "select " . $topSQLServer. " a.* FROM  atividade a " . self::$SQL_HINT_TABLE . " ".
 			     "INNER JOIN protocolo p " . self::$SQL_HINT_TABLE . " on a.id_protocolo=p.id_protocolo ".
 			     "INNER JOIN md_pi_mensagem pi " . self::$SQL_HINT_TABLE . " on a.id_tarefa = pi.id_tarefa ".
			     "WHERE NOT EXISTS (select id_protocolo from md_pi_pacote_envio mppe " . self::$SQL_HINT_TABLE . " where mppe.id_protocolo=p.id_protocolo) ".$restricaoMaxAtividade.
			     "AND sin_publicar = 'S' ".
			     "AND (sta_protocolo = 'P' AND  (sta_nivel_acesso_global = 0 or (sta_nivel_acesso_global=1 and exists (select * from md_pi_parametros " . self::$SQL_HINT_TABLE . " where sin_publicacao_restritos='S'))) ) ".
			     "AND exists (select * from documento d " . self::$SQL_HINT_TABLE . " inner join protocolo p2 " . self::$SQL_HINT_TABLE . " on p2.id_protocolo_agrupador=d.id_documento inner join rel_protocolo_protocolo rpp " . self::$SQL_HINT_TABLE . " on rpp.id_protocolo_2 = p2.id_protocolo where rpp.id_protocolo_1 = p.id_protocolo and d.sin_bloqueado='S' )";
			    
            if ($numUnidadeTeste!=null) {
                $sql = $sql." AND p.id_unidade_geradora NOT IN (".$numUnidadeTeste.") ";
            }

            $sql = $sql." order by a.dth_abertura "; 

            //MYSQL, monta clausula LIMIT no final
            if ($this->getObjInfraIBanco() instanceof InfraMySQL) {
                $sql = $sql." limit ".$limit;
            } else if ($this->getObjInfraIBanco() instanceof InfraOracle) {
                //Oracle, monta clasusula especifica
                $sql = "select * from (". $sql. ") where rownum <= ".$limit;
            }
    
            $rs = $this->getObjInfraIBanco()->consultarSql($sql);
  
            $arrObjAtividadeMonitoradas = array();
            $arrPacotes = array();
  
            return $this->formataAtividadesMonitoradasParaDTO($rs);
  
        } catch(Exception $e) {
            throw new InfraException('Erro ao carregar atividades.',$e);
        }

    }

    public function consultaMaxAtividadeMonitorada() {
        
        $maxIdAtividade = 0;
        $sqlAtividade = "select max(id_atividade) id_atividade from md_pi_monitora_processos";
        $rs = $this->getObjInfraIBanco()->consultarSql($sqlAtividade);
        
        foreach ($rs as $item) {
            $maxIdAtividade = $this->getObjInfraIBanco()->formatarLeituraNum($item ['id_atividade']);
        }
        
        if (is_null($maxIdAtividade)) {
            $maxIdAtividade = 0;
        }
        
        return $maxIdAtividade;
    }

    public function consultarNovasOperacoesProcesso($limit, $numUnidadeTeste=null) {
    
        try {
        
            //SQL Server usa top para limitar numero de registros retornados
            $topSQLServer = "";
            
            $this->maxIdAtividadeMonitorada = $this->consultaMaxAtividadeMonitorada();
            $atividadesProcessosIneditos = $this->consultarNovasOperacoesProcessosNaoEnviados($this->maxIdAtividadeMonitorada,$limit,$numUnidadeTeste);
            
            if (count($atividadesProcessosIneditos) >= $limit) {
                return $atividadesProcessosIneditos;
            }
            
            if ($this->getObjInfraIBanco() instanceof InfraSQLServer) {
                $topSQLServer = "top ".($limit - count($atividadesProcessosIneditos));
            }
            
            $restricaoAtividade = "a.id_atividade > " . $this->maxIdAtividadeMonitorada;
            $sql = "select " . $topSQLServer. " a.* FROM  atividade a " . self::$SQL_HINT_TABLE . " ".
            	     "INNER JOIN protocolo p " . self::$SQL_HINT_TABLE . " on a.id_protocolo=p.id_protocolo ".
            	     "INNER JOIN md_pi_mensagem pi " . self::$SQL_HINT_TABLE . " on a.id_tarefa = pi.id_tarefa ".
            	     "WHERE ".$restricaoAtividade." AND (sta_protocolo = 'P' AND  (sta_nivel_acesso_global = 0 or (sta_nivel_acesso_global=1 and exists (select * from md_pi_parametros " . self::$SQL_HINT_TABLE . " where sin_publicacao_restritos='S'))) ) ".
            	     "AND sin_publicar = 'S' ".
            	     "AND exists (select * from documento d " . self::$SQL_HINT_TABLE . " inner join protocolo p2 " . self::$SQL_HINT_TABLE . " on p2.id_protocolo_agrupador=d.id_documento inner join rel_protocolo_protocolo rpp " . self::$SQL_HINT_TABLE . " on rpp.id_protocolo_2 = p2.id_protocolo where rpp.id_protocolo_1 = p.id_protocolo and d.sin_bloqueado='S' )";
            	     // "AND not exists(select * from md_pi_monitora_processos pimp where pimp.id_atividade=a.id_atividade)";
            	 
            if ($numUnidadeTeste!=null) {
                $sql = $sql." AND p.id_unidade_geradora NOT IN (".$numUnidadeTeste.") ";
            }
            
            $sql = $sql." order by a.dth_abertura "; 
            
            // Monta clausula LIMIT de acordo com banco do sistema
            if ($this->getObjInfraIBanco() instanceof InfraMySQL) {
                $sql = $sql." limit ".($limit - count($atividadesProcessosIneditos));
            } else if ($this->getObjInfraIBanco() instanceof InfraOracle) {
                $sql = "select * from (". $sql. ") where rownum <= ".($limit - count($atividadesProcessosIneditos));
            }
            
            $rs = $this->getObjInfraIBanco()->consultarSql($sql);
            
            $arrObjAtividadeMonitoradas = $this->formataAtividadesMonitoradasParaDTO($rs,$atividadesProcessosIneditos);
            $arrPacotes = array();
            
            return $arrObjAtividadeMonitoradas;
          
        } catch (Exception $e) {
            throw new InfraException('Erro ao carregar atividades.',$e);
        }
    
    }
  
    public function formataAtividadesMonitoradasParaDTO($resultadoAtividades,$arrAtividadesMonitoradasPrevias=array()){
    
        $i=count($arrAtividadesMonitoradasPrevias);
        
        foreach ($resultadoAtividades as $item) {
            $objProtocoloIntegradoMonitoramentoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
            $objProtocoloIntegradoMonitoramentoDTO->setNumIdAtividade($this->getObjInfraIBanco()->formatarLeituraNum($item['id_atividade']));
            $objProtocoloIntegradoMonitoramentoDTO->setDthDataCadastro(date('d/m/Y H:i:s'));
            $objProtocoloIntegradoMonitoramentoDTO->setNumIdProtocolo($item['id_protocolo']);
            
            $arrAtividadesMonitoradasPrevias[$i] = $objProtocoloIntegradoMonitoramentoDTO;
            
            if($this->getObjInfraIBanco()->formatarLeituraNum($item['id_atividade'])>$this->maxIdAtividadeMonitorada){
                $this->maxIdAtividadeMonitorada = $this->getObjInfraIBanco()->formatarLeituraNum($item['id_atividade']);
            }
            
            $i++;
        }
        
        return $arrAtividadesMonitoradasPrevias;
    }
  
    public function recuperarChavePrimaria(){
    
        $objMonitoramentoDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
        $chavePrimaria = "";
        
        if (BancoSEI::getInstance() instanceof InfraSqlServer){
            $sql = "SELECT constraint_name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME ='".$objMonitoramentoDTO->getStrNomeTabela()."' and constraint_type ='PRIMARY KEY'";
            $rs = $this->getObjInfraIBanco()->consultarSql($sql);
            
            //var_dump($rs);
            foreach($rs as $item){
                if($item[0] != null){
                    $chavePrimaria = $item[0];
                }
            }
             
            return $chavePrimaria;
        }
    }
    /**
      ** Função Criada para recuperar o nome chaves estrangeiras em base Mysql da tabela de pacote
      ** Dependendo da versão a rodar o script de atualização para 1.1.3,a foreign key terá nomes diferentes.
      ** 
    **/
    public function recuperarChavesEstrangeirasv112(){

           $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
           $chaveEstrangeira = ""; 
           if (BancoSEI::getInstance() instanceof InfraMySql || BancoSEI::getInstance() instanceof InfraSqlServer){

               $sql = "SELECT constraint_name FROM information_schema.TABLE_CONSTRAINTS  WHERE information_schema.TABLE_CONSTRAINTS.CONSTRAINT_TYPE = 'FOREIGN KEY' AND information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = 'sei' AND information_schema.TABLE_CONSTRAINTS.TABLE_NAME = 'protocolo_integrado_monitoramento_processos';";
               $rs = $this->getObjInfraIBanco()->consultarSql($sql);
               //var_dump($rs);
               return $rs;


           }

    }
    public function consultarParticipantesDocumentosAssinadosProcesso($idProtocolo) {
        
    	$sql = "select distinct con.id_contato,con.nome,con.sigla".
				" from rel_protocolo_protocolo rpp " . self::$SQL_HINT_TABLE . " ".
				" inner join participante par " . self::$SQL_HINT_TABLE . " on par.id_protocolo=rpp.id_protocolo_2".
				" inner join contato con " . self::$SQL_HINT_TABLE . " on con.id_contato=par.id_contato". 
				" inner join documento d " . self::$SQL_HINT_TABLE . " on d.id_documento=par.id_protocolo".
				" where rpp.id_protocolo_1=".$idProtocolo.
				" and par.sta_participacao = '".ParticipanteRN::$TP_INTERESSADO."' ". 
				" and (d.sin_bloqueado='S' or id_tipo_conferencia is not null)"; 
		  
        $resultadoDocumentos = $this->getObjInfraIBanco()->consultarSql($sql);
        $objParticipanteDTO = new ProtocoloDTO();
        $arrParticipanteDTO = array();
		 
        foreach($resultadoDocumentos as $item) {
            $objParticipanteDTO = new ParticipanteDTO();
            $objParticipanteDTO->setStrNomeContato($item['nome']);
            $objParticipanteDTO->setNumIdContato($item['id_contato']);
            $objParticipanteDTO->setStrSiglaContato($item['sigla']);
            array_push($arrParticipanteDTO,$objParticipanteDTO);
        }
  		
        return $arrParticipanteDTO; 
    }
  
    public function consultarAtividadesPublicacao($idPacote) {
    		
        //Adriano MPOG - ajustando para identificadores de ate 30 posicoes
        $sql = "select a.id_atividade,a.id_tarefa,a.id_protocolo,a.dth_abertura,a.id_unidade,pi.mensagem_publicacao  from md_pi_pacote_envio pepi " . self::$SQL_HINT_TABLE . " ".
          	  " inner join md_pi_monitora_processos pimp " . self::$SQL_HINT_TABLE . " on pimp.id_md_pi_pacote_envio = pepi.id_md_pi_pacote_envio ".
          	  " inner join atividade a " . self::$SQL_HINT_TABLE . " on pimp.id_atividade = a.id_atividade ".
          	  " inner join md_pi_mensagem pi " . self::$SQL_HINT_TABLE . " on pi.id_tarefa=a.id_tarefa ".
        	  " where pepi.id_md_pi_pacote_envio = ".$idPacote.
        	  " and  pi.sin_publicar='S'".
        	  " order by a.dth_abertura";
        $arrProtocoloIntegradoMonitoramentoProcessosDTO = array();
        $resultadoDocumentos = $this->getObjInfraIBanco()->consultarSql($sql);
    
        foreach ($resultadoDocumentos as $item) {
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

    // Função que recebe a data no formato dd/mm/yyyy hh:mm:ss e retorna uma funcao correspondente ao banco do sistema
    public function retornarFormatoData ($strData) {
        
        if (($this->getObjInfraIBanco() instanceof InfraMySQL)) {
            return "STR_TO_DATE('". $strData ."', '%d/%m/%Y %H:%i:%s')";
        } else if ($this->getObjInfraIBanco() instanceof InfraOracle) {
            return "TO_DATE('". $strData ."', 'dd/mm/yyyy hh24:mi:ss')";
        }
        
        //SQL Server, monta clasusula dele
        else if ( $this->getObjInfraIBanco() instanceof InfraSQLServer) {
            // Remove espaços e substitui barras por hifem
            $strNovaData = trim($strData);
            $strNovaData = str_replace("/","-",$strNovaData);
            
            return " convert(datetime, '". substr($strNovaData, 6, 4) . "-". substr($strNovaData, 3, 2) . "-". substr($strNovaData, 0, 2) . " " . substr($strNovaData, 11, 8) . "', 120)";
        }
    }

    public function recuperarIdsTabelaMonitoramentov112() {
    
        $sql = 'select id_protocolo_integrado_monitoramento_processos from protocolo_integrado_monitoramento_processos';
        $rs = $this->getObjInfraIBanco()->consultarSql($sql);
        
        $arrMonitoramentoProcessosDTO = array();
        foreach ($rs as $item) {
            $objMonitoramentoProcessosDTO = new ProtocoloIntegradoMonitoramentoProcessosDTO();
            $objMonitoramentoProcessosDTO->setNumIdProtocoloIntegradoMonitoramentoProcessos($item[0]);
            array_push($arrMonitoramentoProcessosDTO,$objMonitoramentoProcessosDTO);
        }
        
        return $arrMonitoramentoProcessosDTO;
    }
  
}

?>
