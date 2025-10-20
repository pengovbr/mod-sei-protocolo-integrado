<?

/**
 * Arquivo de configura��o do M�dulo de Integra��o do SEI com o Protocolo Integrado do Governo Federal
 *
 * Seu desenvolvimento seguiu os mesmos padr�es de configura��o implementado pelo SEI e SIP e este
 * arquivo precisa ser adicionado � pasta de configura��es do SEI para seu correto carregamento pelo m�dulo.
 */

class ConfiguracaoModProtocoloIntegrado extends InfraConfiguracao  {

  private static $instance = null;

    /**
     * Obt�m inst�ncia �nica (singleton) dos dados de configura��o do m�dulo de integra��o
     *
     *
     * @return ConfiguracaoModProtocoloIntegrado
     */
  public static function getInstance()
    {
    if (ConfiguracaoModProtocoloIntegrado::$instance == null) {
        ConfiguracaoModProtocoloIntegrado::$instance = new ConfiguracaoModProtocoloIntegrado();
    }
      return ConfiguracaoModProtocoloIntegrado::$instance;
  }

    /**
     * Defini��o dos par�metros de configura��o do m�dulo
     *
     * @return array
     */
  public function getArrConfiguracoes()
    {
        return array(
            "ProtocoloIntegrado" => array(
                                
                // Endere�o da nova Api de integra��o com o Protocolo Integrado
                // Os endere�os dispon�veis s�o os seguintes (verifique se houve atualiza��es durante o procedimento de instala��o):
                //    - Homologa��o: https://protocolointegrado.preprod.nuvem.gov.br/ ??
                //    - Produ��o: https://protocolointegrado.gov.br/ ??
                "ApiRest" => getenv('PROTOCOLO_INTEGRADO_API_REST') ?: getenv('MODULO_PI_URL'),

                // Login do usu�rio a ser utilizado na autentica��o com a Api Rest
                "UsuarioApiRest" => getenv('PROTOCOLO_INTEGRADO_API_REST_LOGIN') ?: getenv('MODULO_PI_USUARIO'),

                // Senha do usu�rio a ser utilizado na autentica��o com a Api Rest
                "SenhaApiRest" => getenv('PROTOCOLO_INTEGRADO_API_REST_SENHA') ?: getenv('MODULO_PI_SENHA'),
                
                // Endere�o do Web Service principal de integra��o com o Protocolo Integrado
                // Os endere�os dispon�veis s�o os seguintes (verifique se houve atualiza��es durante o procedimento de instala��o):
                //    - Homologa��o: https://protocolointegrado.preprod.nuvem.gov.br/ProtocoloWS/integradorService?wsdl
                //    - Produ��o: https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl
                "WebService" => getenv('PROTOCOLO_INTEGRADO_WEBSERVICE'),

              // Login do usu�rio a ser utilizado na autentica��o com o Webservice
              "UsuarioWebService" => getenv('PROTOCOLO_INTEGRADO_LOGIN'),

              // Senha do usu�io a ser utilizado na autentica��o com o Webservice
              "SenhaWebService" => getenv('PROTOCOLO_INTEGRADO_SENHA'),

              // N�mero de Tentativas para Reenvio dos Metadados
              // Quando o envio de processos para o Protocolo Integrado for malsucedido, o m�dulo tentar� reenvi�-los respeitando 
              // a quantidade de vezes especificada abaixo. 
              "TentativasReenvio" => 15,

              // N�mero m�ximo de andamentos a enviar por vez
              // Quando o agendamento for executado, este par�metro ser� utilizado como n�mero m�ximo de andamentos de processos a ser enviado.
              "QuantidadeAndamentosEnvio" => 1,
                
              // Publicar informa��es sobre processos restritos
              // Indica ao sistema se ele dever� publicar os dados de tr�mites de processos restritos (valores poss�veis: true ou false)
              "PublicarProcessosRestritos" => true,
          )
      );
  }
}
