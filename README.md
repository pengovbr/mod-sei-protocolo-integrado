# Módulo do Protocolo Integrado para o SEI

## Orientações Iniciais

### Compatibilidade do Módulo x Versões do SEI

SEI 4.x - Módulo na versão 3.x - *a ser liberado em breve*
SEI 3.x - Módulo na versão 2.x
SEI 2.x - Módulo na versão 1.x


## Manuais

Para uma instalação rápida basta seguir a seção abaixo: **Instalação Rápida**

### Manual de Instalação Antigo:
[Baixar aqui](docs/Manual_de_Instalacao_2.0.1.pdf)

Esse manual está deprecado. Ele foi atualizado até a versão 2.0.1 do módulo. Portanto caso haja alguma divergência com as novas versões vale o que está escrito aqui nesse README na seção **Instalação Rápida**. Use-o apenas como um complemento as orientações de instalação

### Manual de Uso
[Baixar aqui](docs/Manual_de_Uso.pdf)

Esse manual ensina como o módulo funciona e como o administrador de protocolo pode configurar os dados de envio para o Protocolo Integrado


## Observações

A partir da versão 2.0.2 não é mais necessário configurar certificado de cliente como consta no manual antigo. Pode ignorar essa etapa.
A conexão será feita usando apenas os parâmetros informados na tela de configurações do módulo no próprio SEI (url, usuário e senha)



## Instalação Rápida

Aqui um passo a passo simples para instalar/atualizar o módulo rapidamente

***Pré-Requisito:** todas as precauções de backup, teste em homologação devem ser levadas em consideração antes de atualizar qualquer sistema*

 1. baixe o código fonte
	 - use o link de releases aqui do projeto https://github.com/spbgovbr/mod-sei-protocolo-integrado/releases, selecione a release desejada e descompacte; ou ...
	 - usando o próprio git rode um "git clone" e depois "git checkout" na versão desejada
	 
 2. caso tenha baixado pela release renomeie a pasta para mod-sei-protocolo-integrado
	Importante: caso esteja atualizando, renomeie para o nome que vc já utiliza atualmente em sua instalação
	
 3. vá até a pasta de módulos do sei, geralmente fica em /opt/sei/web/modulos:
```
cd /opt/sei/web/modulos/
```
 
 5. esse módulo foi feito inicialmente para rodar dentro de outra pasta, portanto dentro da pasta módulo crie a pasta mp. Novamente, se vc estiver atualizando, verifique se ela possa já existir com outro nome. Nesse caso basta usar esse nome existente
```
mkdir -p /opt/sei/web/modulos/mp
```
  
 6. copie para dentro dessa pasta mp o código fonte do módulo
 
 7. a árvore vai ficar assim:
 ![Árvore de Diretório do PI](docs/images/manualinstalacao_01)
 
 8. agora ajuste o array de módulos do SEI para ele reconhecer o módulo. Atenção esse caminho é importante, um ajuste errado aqui tira o SEI inteiro do ar. Esse ajuste é muito simples, apenas tome cuidado com os parênteses e formatação php do arquivo para não quebrar. 
 No arquivo ConfiguracaoSEI.php adicione o módulo do protocolo integrado na sequência de array. Vai ficar assim, caso esteja usando a pasta mp e o nome do módulo com mod-sei-protocolo-integrado:
 ![ConfiguracaoSEI.php](docs/images/manualinstalacao_02)
 
 9. Após a alteração do arquivo de configuração, se achar necessário, visite a página incial do SEI, se ela abrir significa que o arquivo que vc acabou de mecher está íntegro. Um problema de má formatação vai acarretar em erro logo na página inicial
  
 10. Vamos agora rodar os scripts de instalação e atualização do módulo. Primeiro vamos mover os scripts para as respectivas pastas scripts do SEI e SIP, em seguida vamos rodar a instalação/atualização
 
	 - Durante a execução de cada script verifique se aparece a mensagem de execução finalizada com sucesso
	 - Observe se o usuário configurado para acesso ao banco no SEI e SIP (arquivos de configuração) tem permissão para criar tabelas, sequences, etc em seus respectivos databases e schemas
	 - É possível que na sua instalação seja necessário adicionar o parâmetro "-c /etc/php.ini" ou o caminho correto do seu arquivo de configuração do php
 
```
mv /opt/sei/web/modulos/mp/mod-sei-protocolo-integrado/protocolo_integrado_atualizar_versao.php /opt/sei/scripts/
mv /opt/sei/web/modulos/mp/mod-sei-protocolo-integrado/protocolo_integrado_atualizar_versao_sip.php /opt/sip/scripts/
``` 

```
php /opt/sei/scripts/protocolo_integrado_atualizar_versao.php
php /opt/sip/scripts/protocolo_integrado_atualizar_versao_sip.php
``` 

 
11. Uma vez o módulo instalado, vá até o SEI como administrador e entre no menu: Administração -> Protocolo Integrado -> Parâmetros. 
Configure os campos: 
	- **url do serviço de envio do protocolo integrado:** url para o serviço de envio de protocolos
		- Produção: https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl
		- Homologação: https://protocolointegrado.preprod.nuvem.gov.br/ProtocoloWS/integradorService?wsdl
	- **usuário:** usuário para conectar no serviço de envio
	- **senha:** senha para conectar no serviço de envio
	- **email do administrador da integração:** email de algum responsável que receberá notificações dos agendamentos

Para maiores informações nessa tela consulte o manual completo. Links aqui nesse documento.

 13. Agora vá a tela de agendamentos do SEI
	 - habilite os agendamentos: 
		 - ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados
		 - ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado
		 - ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado
	
	 - altere cada agendamento e adicione um email responsável
	 - o agendamento publicarProtocoloIntegrado aconselhamos que rode uma vez ao dia e pela madrugada
	 - Para maiores informações de agendamento consulte o manual completo. Links aqui nesse documento
	

## SUPORTE

Em caso de dúvidas ou problemas, favor entrar em conta pelos canais na Central de Atendimento do Processo Eletrônico Nacional.

Para mais informações, contate a equipe responsável por meio dos seguintes canais:

-   [Portal de Atendimento (PEN): Canal de Atendimento](https://portaldeservicos.economia.gov.br/)  - Módulo do Protocolo Integrado
-   Telefone: 0800 978 9005