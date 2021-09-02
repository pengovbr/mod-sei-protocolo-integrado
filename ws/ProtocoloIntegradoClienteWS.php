<?php
/*
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 27/11/2006 - criado por mga
*/

ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
require_once dirname(__FILE__).'/../../../../SEI.php';
require_once dirname(__FILE__).'/Enconding.php';

class ProtocoloIntegradoClienteWS extends SoapClient {

	 private $context;
	 private $acao;
	 private $login;
	 private $senha;
	 private $url;
	 private $listaDocumentosFormatada;
	 private $certificado; //deprecated nao usar mais

	 public function __construct($url,$login,$senha,$opcoes) {

		try {

			$this->login = $login;
			$this->senha = $senha;
			$this->url  =  preg_replace("/^http:/i", "https:", $url);
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

			$this->validarConexaoWebService();
			parent::SoapClient($url, $opcoes);

		} catch (Exception $e) {
	      	throw new InfraException('Erro ao se conectar ao Webservice',$e);
	    }

    }

	private function validarConexaoWebService() {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; //Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		curl_setopt($ch, CURLOPT_URL, $this->url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$retorno = curl_exec($ch);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header = substr($retorno, 0, $headerSize);
		$e = null;

		$curl_errno = curl_errno($ch);
		$curl_error = null;
		if ($curl_errno) {
			$curl_error = curl_error($ch);
		}
		curl_close($ch);

		if (stripos( $this->url,"?wsdl")===false) {
			throw new InfraException("Endereço do serviço inválido ou serviço fora do ar.
							Verifique se este endereço está corretamente informado nos parâmetros de integração ao Protocolo Integrado.",$e);
		}

		if ($curl_errno) {
			$e =  new Exception($header."Requisição CURL resultou no seguinte erro: " . $curl_error . "(Código: " . $curl_errno . ")");
			if ($curl_errno == 60) {
			    throw new InfraException("Certificado inválido ou ausente.",$e);
			} else {
				throw new InfraException("Ocorreu um problema ao realizar a conexão ao Web Service do Protocolo Integrado. Acesse o log do SEI para maiores detalhes", $e);
			}
		} else {
			if ($httpCode!=200) {
				if (strlen($header)>0) {
					$e = new Exception($header);
				} else {
					$e = new Exception("503 Service Unavailable.Não foi possível conectar ao servidor");
				}
				throw new InfraException("Ocorreu um problema ao realizar a conexão ao Web Service do Protocolo Integrado. Acesse o log do SEI para maiores detalhes.", $e);
			}
		}

	}

    // Override doRequest to calculate the authentication hash from the $request.
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        // Grab all the text from the request.
        $codSiorg  = $this->login ;
	    $senha     = $this->senha;
		if ($this->acao=='enviarListaDocumentosServidor') {
			$request = $this->listaDocumentosFormatada;
		}

        // Set the HTTP headers.
        $autorizacao = "Basic ".base64_encode($codSiorg.':'.$senha);
        stream_context_set_option($this->context, array('http' => array('header' => 'Authorization:'. $autorizacao)));
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        return $response;
    }

	public function getQuantidadeMaximaDocumentosPorRequisicaoServidor(){

	  	try {
			$numMaxDocumentos = $this->getQuantidadeMaximaDocumentosPorRequisicao();
	  	  	return $numMaxDocumentos;
	  	} catch(Exception $e){
	      return $e->getMessage();
	    }

		return null;
	}

	public function enviarListaDocumentosServidor($param){

	  	try {
			$this->acao = 'enviarListaDocumentosServidor';
			$retorno = $this->formatarEnvioListaDocumentosPI($param);
			return $retorno;
	  	} catch(Exception $e) {
	  	  	error_log('Exceção:'.$e->getMessage());
	      	return $e;
	    }

		return null;
	}

	public function formatarEnvioListaDocumentosPI($param){

		$elementos = array(0=>'Assunto',1=>'NomeInteressado',2=>'Operacao',3=>'UnidadeOperacao');

		for ($it=0;$it<count($elementos);$it++) {
			$this->formatarElementoXML($param,$elementos[$it]);
		}
	    $sax = xml_parser_create();

		$xml = $param->saveXML();
		$pos = strpos($xml, '<ListaDocumentos>');
		$xml = substr($xml, $pos,strlen($xml));
		for ($control = 0; $control < 32; $control++) {
	        $xml = str_replace(chr($control), "", $xml);
		}
		$this->listaDocumentosFormatada = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:end="http://endpoint.ws.protocolo.gov.br/">
				   		 <soapenv:Header/>
				  		 <soapenv:Body> <end:enviarListaDocumentos>'.($xml).' </end:enviarListaDocumentos></soapenv:Body>
					</soapenv:Envelope>';

		return $this->__soapCall('EnviarListaDocumentos',array());
	}

	//Converte elementos(tags) do XML com caracteres especiais (acentos,pontuação,etc.) para formato de enconding aceito pelo PI
	private function formatarElementoXML($xml,$elemento){
		$objetos = $xml->getElementsByTagName($elemento);
		if ($objetos!=null) {
		    for ($ite=0;$ite<$objetos->length;$ite++) {
				$objetos->item($ite)->nodeValue = InfraString::formatarXML(Encoding::fixUTF8($objetos->item($ite)->nodeValue));
		    }
		}
	}

}
