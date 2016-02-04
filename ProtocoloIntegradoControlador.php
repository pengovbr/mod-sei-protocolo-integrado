<?

class ProtocoloIntegradoControlador implements ISeiControlador {
  
	public function processar($strAcao){
	  
		switch($strAcao) {
				
			case 'protocolo_integrado_configurar_publicacao':  
			case 'protocolo_integrado_mensagens_listar':

			require_once 'protocolo_integrado_mensagens_listar.php';
			return true;
		  
			case  'protocolo_integrado_mensagens_alterar':
				require_once 'protocolo_integrado_mensagens_cadastro.php';
				return true;
				
			case  'protocolo_integrado_configurar_parametros':
				require_once 'protocolo_integrado_configurar_parametros.php';
				return true;
				
			case 'protocolo_integrado_monitoramento': 
			case'protocolo_integrado_forcar_reenvio':	
				require_once 'protocolo_integrado_monitoramento.php';
				return true;
			case 'protocolo_integrado_visualizar_metadados':
			case 'protocolo_integrado_visualizar_erro_envio_metadados':	
				require_once 'protocolo_integrado_acesso_arquivo_metadados.php';
				return true;
		}
    
    	return false;
	}
}
?>