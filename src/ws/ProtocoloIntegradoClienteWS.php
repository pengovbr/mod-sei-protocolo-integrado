<?php

// ini_set('soap.wsdl_cache_enabled', 0);
// ini_set('soap.wsdl_cache_ttl', 0);
// require_once dirname(__FILE__).'/../../../../SEI.php';
require_once DIR_SEI_WEB.'/SEI.php';
require_once dirname(__FILE__).'/Enconding.php';

class ProtocoloIntegradoClienteWS extends SoapClient {

     private $context;
     private $acao;
     private $login;
     private $senha;
     private $url;
     private $listaDocumentosFormatada;
     private $certificado; //deprecated nao usar mais

  public function __construct($url, $login, $senha, $opcoes) {

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
                ),
        ));

        $opcoes = array_merge(
            $opcoes, array('stream_context' => $this->context, 
          'location' => str_replace("?wsdl", "", $url),
          'login' => $this->login,
          'password' => $this->senha,
          'use' => SOAP_LITERAL)
        );

        $this->validarConexaoWebService();
        parent::__construct($url, $opcoes);

    } catch (Exception $e) {
           throw new InfraException('Erro ao se conectar ao Webservice', $e);
    }

  }

  private function validarConexaoWebService() {

      $ch = curl_init();

      try{
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_exec($ch);
                        
        $curl_error = null;
        if (curl_errno($ch)){
          $curl_error = curl_error($ch);
        }

        if ($curl_error) {       
          throw new Exception($this->url." Erro ao obter requisição CURL. Erro detalhado: " . $curl_error );
        }
      }finally{
        curl_close($ch);
      } 
  }

    // Override doRequest to calculate the authentication hash from the $request.
  function __doRequest($request, $location, $action, $version, $one_way = 0) {
    if ($this->acao=='enviarListaDocumentosServidor') {
        $request = $this->listaDocumentosFormatada;
    }
    $response = parent::__doRequest($request, $location, $action, $version, $one_way);
    return $response;
  }

  public function getQuantidadeMaximaDocumentosPorRequisicaoServidor(){

    try {
        $parametros = new stdClass();
        $numMaxDocumentos = $this->getQuantidadeMaximaDocumentosPorRequisicao($parametros);
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

    for ($it=0; $it<count($elementos); $it++) {
        $this->formatarElementoXML($param, $elementos[$it]);
    }
      $sax = xml_parser_create();

      $xml = $param->saveXML();
      $pos = strpos($xml, '<ListaDocumentos>');
      $xml = substr($xml, $pos, strlen($xml));
    for ($control = 0; $control < 32; $control++) {
        $xml = str_replace(chr($control), "", $xml);
    }
      $this->listaDocumentosFormatada = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:end="http://endpoint.ws.protocolo.gov.br/">
				   		 <soapenv:Header/>
				  		 <soapenv:Body> <end:enviarListaDocumentos>'.($xml).' </end:enviarListaDocumentos></soapenv:Body>
					</soapenv:Envelope>';

      return $this->__soapCall('EnviarListaDocumentos', array());
  }

    //Converte elementos(tags) do XML com caracteres especiais (acentos,pontuação,etc.) para formato de enconding aceito pelo PI
  private function formatarElementoXML($xml, $elemento){
      $objetos = $xml->getElementsByTagName($elemento);
    if ($objetos!=null) {
      for ($ite=0; $ite<$objetos->length; $ite++) {
        $objetos->item($ite)->nodeValue = InfraString::formatarXML(Encoding::fixUTF8($objetos->item($ite)->nodeValue));
      }
    }
  }

}
