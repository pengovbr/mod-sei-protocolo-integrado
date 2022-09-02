# Manual de Migração do Módulo Protocolo Integrado - PI

Este documento descreve como proceder para migrar a versão 2.x do módulo PI para a versão 3.x com suporte ao SEI/SUPER 4.0. Também sugerimos que a atualização seja executada previamente em um novo ambiente utilizando os dados de produção pois esta fase poderá antecipar eventuais problemas na infraestrutura e no processamento dos scripts.  

> **Recomendamos  que, antes  de  iniciar  efetivamente  o  processo  de  atualização, os responsáveis técnicos leiam, pelo menos uma vez, todo este documento, bem como o manual de instalação.**  

> :warning: **ATENÇÃO**: Este módulo é compatível com o SEI ou SUPER a partir da versão 4.0. Para obter uma versão do módulo de Protocolo Integrado compatível com a versão 4.0, baixe o módulo **mod-sei-protocolo-integrado** em versões com número **2.x** na [página de releases do projeto](https://github.com/spbgovbr/mod-sei-protocolo-integrado/releases?q=Vers%C3%A3o+2.1.&expanded=true) 


### Pré-requisitos
- **SEI ou SUPER, versão 4.0.x ou superior instalada**;
- Possuir a versão 2.1.x do módulo do Protocolo Integrado (verificar a versão através da funcionalidade SEI > Infra > Módulos). 
- Usuário de acesso ao banco de dados do SEI e SIP com permissões para criar novas estruturas no banco de dados;
- Credenciais (usuário e senha) para publicar no Protocolo Integrado, conforme orientações presentes no seguinte endereço: https://www.comprasgovernamentais.gov.br/index.php/pen/processo-eletronico-nacional-2/web-service/solicitacao-de-credenciais-para-uso-do-web-service .

Por questões de segurança, a equipe do Protocolo Integrado libera inicialmente o acesso ao webservice do ambiente de homologação do sistema e, posteriormente, o acesso ao webservice de produção após testes completado os testes em ambiente de homologação.

Para maiores informações, entre em contato pelo telefone 0800 978-9005 ou diretamente pela Central de Serviços do PEN, endereço https://portaldeservicos.economia.gov.br/citsmart/login/login.load.





### Procedimentos:

### 1.1. Fazer backup dos bancos de dados do SEI, SIP e dos arquivos de configuração do sistema.

Todos os procedimentos de manutenção do sistema devem ser precedidos de backup completo de todo o sistema a fim de possibilitar a sua recuperação em caso de falha. A rotina de instalação descrita abaixo atualiza tanto o banco de dados, como os arquivos pré-instalados do módulo e, por isto, todas estas informações precisam ser resguardadas.

---

### 1.2. Recuperar dos dados de configuração do módulo na versão atual

A partir da versão 3.0 do módulo Protocolo Integrado, os parâmetros técnicos de configuração deverão ser atribuídos no arquivo `ConfiguracaoModProtocoloIntegrado.php` localizado na pasta `sei/conofig/mod-protocolo-integrado`. Com isto, os valores dos parâmetros da versão anterior deverão ser recuperadas e anotadas antes de iniciar a instalação da versão 3.0. Estes dados podem ser obtidos através da página de administração do módulo em sua versão 2.1.x, mais precisamente localizado em **SEI/Administração/Protocolo Integrado/Parâmetros de COnfiguração**. Os dados de configuração também podem ser obtidos diretamente na tabela de parâmetro do módulo denominada `md_pi_parametros`.

Os parâmetros em questão são:
- URL do webservice de integração com o Protocolo Integrado
- Login para acesso aos serviços do webservice 
- Senha para acesso aos serviços do webservice
- Indicador para permitir o envio de processos restritos


---

### 1.4. Remover a pasta da versão anterior do módulo

Como os arquivos foram reorganizados, recomanda a remoção completa da pasta contendo os arquivos da versão anterior do módulo, localizada em `sei/web/modulos/<PASTA DO MOD-SEI-PROTOCOLO-INTEGRADO>`.

---

### 1.3. Baixar o arquivo de distribuição do **mod-sei-protocolo-integrado**

Necessário realizar o _download_ do pacote de distribuição do módulo **mod-sei-protocolo-integrado** para instalação ou atualização do sistema SEI. O pacote de distribuição consiste em um arquivo zip com a denominação **mod-sei-protocolo-integrado-VERSAO**.zip e sua última versão pode ser encontrada em https://github.com/spbgovbr/mod-sei-protocolo-integrado/releases

---

### 1.4. Descompactar o pacote de instalação e atualizar os arquivos do sistema

Após realizar a descompactação do arquivo .zip, será criada uma nova pasta contendo a seguinte estrutura:

```
/**mod-sei-pen**-VERSAO 
    /sei              # Arquivos do módulo posicionados corretamente dentro da estrutura do SEI
    /sip              # Arquivos do módulo posicionados corretamente dentro da estrutura do SIP
    INSTALACAO.md     # Instruções de instalação do **mod-sei-protocolo-integrado**
    MIGRACAO.md       # Instruções de atualização do **mod-sei-protocolo-integrado**    
    NOTAS_VERSAO.md   # Registros de novidades, melhorias e correções desta versão
```

Importante enfatizar que os arquivos contidos dentro dos diretórios ```sei``` e ```sip``` não substituem nenhum código-fonte original do sistema. Eles apenas posicionam os arquivos do módulos nas pastas corretas de scripts, configurações e pasta de módulos, todos posicionados dentro de um diretório específico denominado mod-pen para deixar claro quais scripts fazem parte do módulo.

Os diretórios ```sei``` e ```sip``` descompactados acima devem ser mesclados com os diretórios originais através de uma cópia simples dos arquivos.

Observação: O termo curinga VERSAO deve ser substituído nas instruções abaixo pelo número de versão do módulo que está sendo instalado

```
$ cp /tmp/**mod-sei-protocolo-integrado**-VERSAO.zip <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>
$ cd <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>
$ unzip **mod-sei-protocolo-integrado**-VERSAO.zip
```
---

### 1.4. Habilitar módulo **mod-sei-protocolo-integrado** no arquivo de configuração do SEI

Esta etapa é padrão para a instalação de qualquer módulo no SEI para que ele possa ser carregado junto com o sistema. Edite o arquivo **sei/config/ConfiguracaoSEI.php** para adicionar a referência ao módulo PEN na chave **[Modulos]** abaixo da chave **[SEI]**:    

```php
'SEI' => array(
    'URL' => ...,
    'Producao' => ...,
    'RepositorioArquivos' => ...,
    'Modulos' => array('ProtocoloIntegradoIntegracao' => 'protocolo-integrado'),
    ),
```

Adicionar a referência ao módulo PEN na array da chave 'Modulos' indicada acima:

```php
'Modulos' => array('ProtocoloIntegradoIntegracao' => 'protocolo-integrado')
```

---

### 1.5. Atualizar a base de dados do SIP com as tabelas do **mod-sei-protocolo-integrado**

A atualização realizada no SIP não cria nenhuma tabela específica para o módulo, apenas é aplicada a criarção os recursos, permissões e menus de sistema utilizados pelo **mod-sei-protocolo-integrado**. Todos os novos recursos criados possuem o prefixo **protocolo_integrado_** para fácil localização pelas funcionalidades de gerenciamento de recursos do SIP.

O script de atualização da base de dados do SIP fica localizado em ```<DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sip/scripts/mod-protocolo-integrado/sip_atualizar_versao_modulo_protocolo_integrado.php```

```bash
$ php -c /etc/php.ini <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sip/scripts/mod-protocolo-integrado/sip_atualizar_versao_modulo_protocolo_integrado.php
```

---

### 1.6. Atualizar a base de dados do SEI com as tabelas do **mod-sei-protocolo-integrado**

Nesta etapa é instalado/atualizado as tabelas de banco de dados vinculadas do **mod-sei-protocolo-integrado**. Todas estas tabelas possuem o prefixo **md_pi_** para organização e fácil localização no banco de dados.

O script de atualização da base de dados do SIP fica localizado em ```<DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sei/scripts/mod-protocolo-integrado/sei_atualizar_versao_modulo_protocolo_integrado.php```

```bash
$ php -c /etc/php.ini <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sei/scripts/mod-protocolo-integrado/sei_atualizar_versao_modulo_protocolo_integrado.php
```

---

### 1.5. Configurar os parâmetros do Módulo 

Conforme mencionado na seção 1.2, os valores dos parâmetros de configuração do mod-sei-protocolo-integrado precisarão ser reconfigurados no novo arquivo de configuração localizado em **<DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI>/sei/config/mod-protocolo-integrado/**. 

O arquivo de configuração padrão criado **ConfiguracaoModProtocoloIntegrado.exemplo.php** vem com o sufixo **exemplo** justamente para não substituir o arquivo principal contendo as configurações vigentes do módulo.

Caso não exista o arquivo principal de configurações do módulo criado em **<DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sei/config/mod-protocolo-integrado/ConfiguracaoModProtocoloIntegrado.php**, renomeie o arquivo de exemplo para iniciar a parametrização da integração.

```
cd <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI>/sei/config/mod-protocolo-integrado/
mv ConfiguracaoModProtocoloIntegrado.exemplo.php ConfiguracaoModProtocoloIntegrado.php
```

Altere o arquivo de configuração específico do módulo em **<DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sei/config/mod-protocolo-integrado/ConfiguracaoModProtocoloIntegrado.php** e defina as configurações do módulo, conforme apresentado abaixo:

* **WebService**
Endereço do Web Service principal de integração com o Protocolo Integrado
Os endereços disponÃ­veis são os seguintes (verifique se houve atualizações durante o procedimento de instalação):
   - Homologação: https://protocolointegrado.preprod.nuvem.gov.br/ProtocoloWS/integradorService?wsdl
   - Produção: https://protocolointegrado.gov.br/ProtocoloWS/integradorService?wsdl


* **UsuarioWebService**  
Login do usuário a ser utilizado na autenticação com o Webservice

* **SenhaWebService**  
Senha do usuário a ser utilizado na autenticação com o Webservice

* **TentativasReenvio** 
Número de Tentativas para Reenvio dos Metadados. Quando o envio de processos para o Protocolo Integrado for malsucedido, o módulo tentará reenviá-los respeitando a quantidade de vezes especificada abaixo.

* **QuantidadeAndamentosEnvio** 
Número máximo de andamentos a enviar por vez. Quando o agendamento for executado, este parâmetro utilizado como número máximo de andamentos de processos a ser enviado.

* **PublicarProcessosRestritos**   
Publica informações de processos restritos


---

## 5. SUPORTE

Em caso de dúvidas ou problemas durante o procedimento de atualização, favor entrar em conta pelos canais de atendimento disponibilizados na Central de Atendimento do Processo Eletrônico Nacional, que conta com uma equipe para avaliar e responder esta questão de forma mais rápida possível.

Para mais informações, contate a equipe responsável por meio dos seguintes canais:
- [Portal de Atendimento (PEN): Canal de Atendimento](https://portaldeservicos.economia.gov.br) - Módulo do Barramento
- Telefone: 0800 978 9005




