## Novidades da versão 3.0.1

Este documento descreve as principais mudanças aplicadas nesta versão do módulo de integração do SEI com o Protocolo Integrado do Governo Federal.

As melhorias entregues em cada uma das versões são cumulativas, ou seja, contêm todas as implementações realizada em versões anteriores.


## Compatibilidade de versões
* O módulo é compatível com as seguintes versões:
    * SEI: 4.0.x e versões superiores
    
| Versão SEI/SUPER | Versão módulo mod-sei-protocolo-integrado                       |
| ---              | ---                                           |
| 3.1.x            | mod-sei-protocolo-integrado 2.1.3 ou superior |
| 4.0.x            | mod-sei-protocolo-integrado 3.0.0 ou superior |

Para maiores informações sobre os procedimentos de instalação ou atualização, acesse os seguintes documentos localizados no pacote de distribuição mod-sei-protocolo-integrado-VERSAO.zip:
> Atenção: É impreterível seguir rigorosamente o disposto no README.md do Módulo para instalação ou atualização com sucesso.

* **INSTALACAO.md** - Procedimento de instalação e configuração do módulo
* **ATUALIZACAO.md** - Procedimento específicos para atualização de uma versão anterior

### Lista de melhorias e correções de problemas

Todas as atualizações podem incluir itens referentes à segurança, requisito em permanente monitoramento e evolução, motivo pelo qual a atualização com a maior brevidade possível é sempre recomendada.

#### Remover configurações SELF-SIGNED das chamadas de webservices dos módulos (#7)

Não é mais permitido certificados SSL inválidos ao chamar os serviços do protocolo integrado.

#### Layout da tela de monitoramento quebrando dados (#8)

Correção do layout na tela de configuração e de pesquisa.

#### Compatibilidade com PHP 7.3 (#22)

Corrige bug do 'Call to undefined function ereg_replace()'. 

#### Erro ao tentar instalar versão mais recente do módulo do Protocolo Integrado (#38)

Corrige erro ao tentar instalar a versão 3.0.1 a partir de uma versão 2.1.x.


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
