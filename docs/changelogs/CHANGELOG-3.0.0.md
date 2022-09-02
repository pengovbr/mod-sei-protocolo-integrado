# NOTAS DE VERSÃO MOD-SEI-PROTOCOLO-INTEGRADO (versão 3.0.0)

Este documento descreve as principais mudanças aplicadas nesta versão do módulo de integração do SEI com o Protocolo Integrado do Governo Federal.

As melhorias entregues em cada uma das versões são cumulativas, ou seja, contêm todas as implementações realizada em versões anteriores.


## Compatibilidade de versões
* O módulo é compatível com as seguintes versões:
    * SEI: 4.0.x e versões superiores
    
| Versão SEI/SUPER | Versão módulo mod-wssei                       |
| ---              | ---                                           |
| 3.1.x            | mod-sei-protocolo-integrado 2.1.3 ou superior |
| 4.0.x            | mod-sei-protocolo-integrado 3.0.0 ou superior |

Para maiores informações sobre os procedimentos de instalação ou atualização, acesse os seguintes documentos localizados no pacote de distribuição mod-sei-protocolo-integrado-VERSAO.zip:
> Atenção: É impreterível seguir rigorosamente o disposto no README.md do Módulo para instalação ou atualização com sucesso.

* **INSTALACAO.md** - Procedimento de instalação e configuração do módulo
* **ATUALIZACAO.md** - Procedimento específicos para atualização de uma versão anterior

### Lista de melhorias e correções de problemas

Todas as atualizações podem incluir itens referentes à segurança, requisito em permanente monitoramento e evolução, motivo pelo qual a atualização com a maior brevidade possível é sempre recomendada.


#### Modificação dos procedimentos de configuração

Os parâmetros de configuração do módulo de integração do Protocolo Integrado foram movidos para o arquivo próprio para este propósito, localizado em [PASTA DE INSTALAÇÃO DO sei]/config/mod-protocolo-integrado/ConfiguracaoModProtocoloIntegrado.php. Esta alteração implica a reconfiguração dos parâmetros de conexão ao Protocolo Integrado anteriormente definidos através da funcionalidade *SEI > Administração > Protocolo Integrado > Parâmetros*. 


#### Modificação dos procedimentos de atualização

Atualizado procedimentos de instalação e atualização do módulo para serem compatíveis com os demais módulos do Processo Eletrônico Nacional e à nova estrutura do SEI 4.0 e SUPER.GOV.BR

#### Adaptação do script de atualização para modelo SEI 4 (#160)

Modificado script de atualização de banco de dados para o módulo para ser compatível simultaneamente com SEI 3.1 e 4.0


### Atualização de Versão

**ATENÇÂO**: A partir da versão **mod-sei-protocolo-integrado 3.0.0**, houve quebra de compatibilidade com as versões anteriores, o que gera a necessidade de refazer todos os passos da instalação e configuração descritos no [Manual de Instalação do Módulo](../INSTALACAO.md).   

#### Instruções

> :warning: **Atenção**: Caso esteja sendo realizada a atualização da versão 2.1.x do módulo para uma versão 3.0 compatível com o SEI ou SUPER 4.0, deverão ser aplicados todos os procedimentos de migração descritos no [Manual de Migração](https://github.com/spbgovbr/mod-sei-protocolo-integrado/blob/atualizacao_pi/docs/MIGRACAO.md). 

 

1. Baixar a última versão do módulo de instalação do sistema (arquivo `mod-sei-protocolo-integrado-[VERSÃO].zip`) localizado na página de [Releases do projeto MOD-SEI-PROTOCOLO-INTEGRADO](https://github.com/spbgovbr/mod-sei-protocolo-integrado/releases), seção **Assets**. _Somente usuários autorizados previamente pela Coordenação-Geral do Processo Eletrônico Nacional podem ter acesso às versões._

2. Fazer backup dos diretórios "sei", "sip" e "infra" do servidor web;

3. Descompactar o pacote de instalação `mod-sei-protocolo-integrado-[VERSÃO].zip`;

4. Copiar os diretórios descompactados "sei", "sip" para os servidores, sobrescrevendo os arquivos existentes;

5. Executar o script de instalação/atualização `sei_atualizar_versao_modulo_protocolo_integrado.php` do módulo para o SEI localizado no diretório `sei/scripts/mod-protocolo-integrado/`

```bash
php -c /etc/php.ini <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sei/scripts/mod-protocolo-integrado/sei_atualizar_versao_modulo_protocolo_integrado.php
```

6. Executar o script de instalação/atualização `sip_atualizar_versao_modulo_protocolo_integrado.php` do módulo para o SIP localizado no diretório `sip/scripts/mod-protocolo-integrado/`

```bash
php -c /etc/php.ini <DIRETÓRIO RAIZ DE INSTALAÇÃO DO SEI E SIP>/sip/scripts/mod-protocolo_integrado/sip_atualizar_versao_modulo_protocolo_integrado.php
```
