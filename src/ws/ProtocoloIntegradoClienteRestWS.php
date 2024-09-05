<?php

ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';
require_once dirname(__FILE__).'/Encoding.php';

class ProtocoloIntegradoClienteRestWS {

	 private $context;
	 private $acao;
	 private $login;
	 private $senha;
	 private $url;
	 private $listaDocumentosFormatada;
	 private $token;

	 public function __construct($url,$login,$senha,$opcoes) {

		try {

			$this->login = $login;
			$this->senha = $senha;
			$this->url  =  $url;
	        // Create the stream_context and add it to the options
	        $this->context = stream_context_create(
                array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
            ));
	        $opcoes = array_merge($opcoes, array('stream_context' => $this->context));

			$this->validarConexaoApi();

		} catch (Exception $e) {
	      	throw new InfraException('Erro ao se conectar a Api',$e);
	    }

    }

	private function validarConexaoApi() {

		$autentica = $this->autenticarPorOrgao();

		if ($autentica===false) {
			throw new InfraException("Não foi possível testar a Api do Protocolo Integrado");
		}

	}

	private function autenticarPorOrgao() {
		$urlService = $this->url . 'autenticarPorOrgao';
	
		$data = [
			"codigoOrgao" => $this->login,
			"senha" => $this->senha,
			"lembrarMe" => true
		];
	
		$jsonData = json_encode($data);
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $urlService);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_POST, true);            
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', 
			'Content-Length: ' . strlen($jsonData)
		]);
	
		$response = curl_exec($ch);
	
		if ($response === false) {
			$error = curl_error($ch);
			curl_close($ch);
			throw new InfraException("Erro na Api do PI: $error");
		}
		
		$data = json_decode($response, true);
			
		if (isset($data['id_token'])) {
			echo "ID Token: " . $data['id_token'];
		} else if (isset($data['detail'])) {
			throw new InfraException(mb_convert_encoding($data['detail'], 'ISO-8859-1', 'auto'));
		} else {
			throw new InfraException("Erro Inesperado na Api " . $response);
		}		
	
		curl_close($ch);

		$this->token = $data['id_token'];

		return $data['id_token'];
	}

	public function getQuantidadeMaximaDocumentosPorRequisicaoServidor(){

	  	try {
			$urlService = $this->url . 'getQuantidadeMaximaDocumentosPorRequisicao';
	
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_URL, $urlService);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json', 
				'Authorization: Bearer ' . $this->token
			]);
		
			$response = curl_exec($ch);
		
			if ($response === false) {
				$error = curl_error($ch);
				curl_close($ch);
				throw new InfraException("Erro na Api do PI: $error");
			}
			
			$data = json_decode($response, true);
				
			if (isset($data['quantidade'])) {
				echo "Quantidade: " . $data['quantidade'];
			} else {
				echo "Quantidade não retornada na resposta.";
			}		
		
			curl_close($ch);

			$result = new stdClass();
			$result->NumeroMaximoDocumentos = (int)$data['quantidade'];
			return $result;
	  	} catch(Exception $e){
	      return $e->getMessage();
	    }

		return null;
	}

	public function enviarListaDocumentosServidor($data){

	  	try {
			$urlService = $this->url . 'enviarListaDocumentos';
			
			$jsonData = json_encode($data, JSON_INVALID_UTF8_IGNORE);
		
			if ($jsonData === false) {
				throw new InfraException('JSON encoding failed: ' . json_last_error_msg());
			}
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_URL, $urlService);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_POST, true);            
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json', 
				'Content-Length: ' . strlen($jsonData),
				'Authorization: Bearer ' . $this->token
			]);
		
			$response = curl_exec($ch);
		
			if ($response === false) {
				$error = curl_error($ch);
				curl_close($ch);
				throw new InfraException("Erro na Api do PI: $error");
			}
			
			$data = json_decode($response, true);
			
			if (isset($data['detail'])) {
				throw new InfraException(mb_convert_encoding($data['detail'], 'ISO-8859-1', 'auto'));
			}		
		
			curl_close($ch);
	
			return $data;
	  	} catch(Exception $e) {
	  	  	error_log('Exce��o:'.$e->getMessage());
	      	return $e;
	    }

		return null;
	}

}
