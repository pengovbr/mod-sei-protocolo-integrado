<?

/**
 * Arquivo de configuração do Módulo de Integração do SEI com o Protocolo Integrado do Governo Federal
 *
 * Seu desenvolvimento seguiu os mesmos padrões de configuração implementado pelo SEI e SIP e este
 * arquivo precisa ser adicionado à pasta de configurações do SEI para seu correto carregamento pelo módulo.
 */

class ConfiguracaoModProtocoloIntegrado extends InfraConfiguracao  {

	private static $instance = null;

    /**
     * Obtém instância única (singleton) dos dados de configuração do módulo de integração
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
     * Definição dos parâmetroúde configuração do módulo
     *
     * @return array
     */
    public function getArrConfiguracoes()
    {
        return array(
            "ProtocoloIntegrado" => array(

                // Endereço da nova Api de integração com o Protocolo Integrado
                // Os endereços disponíveis são os seguintes (verifique se houve atualizações durante o procedimento de instalação):
                //    - Homologação: https://protocolointegrado.preprod.nuvem.gov.br/ ??
                //    - Produção: https://protocolointegrado.gov.br/ ??
                "ApiRest" => "",

                // Login do usuário a ser utilizado na autenticação com a Api Rest
                "UsuarioApiRest" => "",

                // Senha do usuário a ser utilizado na autenticação com a Api Rest
                "SenhaApiRest" => "",

                // Endereço do Web Service principal de integração com o Protocolo Integrado
                // Os endereços disponíveis são os seguintes (verifique se houve atualizações durante o procedimento de instalação):
                //    - Homologação: https://protocolointegrado.preprod.nuvem.gov.br/ProtocoloWS/integradorService?wsdl
                //    - Produção: https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl
                "WebService" => "",

                // Login do usuário a ser utilizado na autenticação com o Webservice
                "UsuarioWebService" => "",

                // Senha do usuário a ser utilizado na autenticação com o Webservice
                "SenhaWebService" => "",

                // Número de Tentativas para Reenvio dos Metadados
                // Quando o envio de processos para o Protocolo Integrado for malsucedido, o módulo tentará reenviá-los respeitando 
                // a quantidade de vezes especificada abaixo. 
                "TentativasReenvio" => 15,

                // Número máximo de andamentos a enviar por vez
                // Quando o agendamento for executado, este parâmetro utilizado como número máximo de andamentos de processos a ser enviado.
                "QuantidadeAndamentosEnvio" => 100000,

                // Publicar informações sobre processos restritos
                "PublicarProcessosRestritos" => false
            )
        );
    }
}
