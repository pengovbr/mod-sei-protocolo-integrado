.PHONY: .env help clean build all install restart down destroy up config

include .env
include .modulo.env

# Parâmetros de configuração
base = mysql

MODULO_NOME = protocolo-integrado
MODULO_PASTAS_CONFIG = mod-$(MODULO_NOME)
MODULO_PASTA_NOME = $(notdir $(shell pwd))
VERSAO_MODULO := $(shell grep 'const VERSAO_MODULO' ./src/ProtocoloIntegradoIntegracao.php | cut -d'"' -f2)
SEI_SCRIPTS_DIR = dist/sei/scripts/$(MODULO_PASTAS_CONFIG)
SEI_CONFIG_DIR = dist/sei/config/$(MODULO_PASTAS_CONFIG)
SEI_MODULO_DIR = dist/sei/web/modulos/$(MODULO_NOME)
SIP_SCRIPTS_DIR = dist/sip/scripts/$(MODULO_PASTAS_CONFIG)

ARQUIVO_CONFIG_SEI=$(SEI_PATH)/sei/config/ConfiguracaoSEI.php
MSG_ORIENTACAO_CONFIGRACAO=CONFIGURACAO PENDENTE
MODULO_COMPACTADO = mod-sei-$(MODULO_NOME)-$(VERSAO_MODULO).zip

CMD_INSTALACAO_SEI = echo -ne '$(SEI_DATABASE_USER)\n$(SEI_DATABASE_PASSWORD)\n' | php atualizar_versao_sei.php
CMD_INSTALACAO_SIP = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php atualizar_versao_sip.php
CMD_INSTALACAO_RECURSOS_SEI = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php atualizar_recursos_sei.php
CMD_INSTALACAO_SEI_MODULO = echo -ne '$(SEI_DATABASE_USER)\n$(SEI_DATABASE_PASSWORD)\n' | php sei_atualizar_versao_modulo_protocolo_integrado.php
CMD_INSTALACAO_SIP_MODULO = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php sip_atualizar_versao_modulo_protocolo_integrado.php
RED=\033[0;31m
NC=\033[0m
YELLOW=\033[1;33m

MENSAGEM_AVISO_MODULO = $(RED)[ATENÇÃO]:$(NC)$(YELLOW) Necessário configurar a chave de configuração do módulo no arquivo de configuração do SEI (ConfiguracaoSEI.php) $(NC)\n               $(YELLOW)'Modulos' => array('ProtocoloIntegradoIntegracao' => 'protocolo-integrado') $(NC)
MENSAGEM_AVISO_ENV = $(RED)[ATENÇÃO]:$(NC)$(YELLOW) Configurar parâmetros de autenticação do ambiente de testes do Protocolo Integrado no arquivo .modulo.env $(NC)

all: clean build

build: 
	@mkdir -p $(SEI_SCRIPTS_DIR)
	@mkdir -p $(SEI_CONFIG_DIR)
	@mkdir -p $(SEI_MODULO_DIR)
	@mkdir -p $(SIP_SCRIPTS_DIR)
	@cp -Rf src/* $(SEI_MODULO_DIR)/
	@cp docs/INSTALL.md dist/INSTALACAO.md
	@cp docs/UPGRADE.md dist/ATUALIZACAO.md
	@cp docs/modelo_plano_integracao.doc dist/Modelo_PlanodeIntegracao_LOGINUNICO.doc
	@cp docs/changelogs/CHANGELOG-$(VERSAO_MODULO).md dist/NOTAS_VERSAO.md
	@mv $(SEI_MODULO_DIR)/scripts/sei_atualizar_versao_modulo_loginunico.php $(SEI_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/scripts/sip_atualizar_versao_modulo_loginunico.php $(SIP_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/config/ConfiguracaoModLoginUnico.exemplo.php $(SEI_CONFIG_DIR)/
	@rm -rf $(SEI_MODULO_DIR)/config
	@rm -rf $(SEI_MODULO_DIR)/scripts
	@cd dist/ && zip -r $(MODULO_COMPACTADO) INSTALACAO.md ATUALIZACAO.md NOTAS_VERSAO.md Modelo_PlanodeIntegracao_LOGINUNICO.doc sei/ sip/	
	@rm -rf dist/sei dist/sip dist/INSTALACAO.md dist/ATUALIZACAO.md
	@echo "Construção do pacote de distribuição finalizada com sucesso"


clean:  ## Limpa o diretório contendo arquivos temporários de construção do pacote de distribuição
	@rm -rf dist
	@rm -rf dist
	@echo "Limpeza do diretório de distribuição do realizada com sucesso"

.modulo.env:
	cp -n envs/modulo.env .modulo.env

install: ## Instala e atualiza as tabelas do módulo na base de dados do sistema
	docker-compose exec -w /opt/sei/scripts/$(MODULO_PASTAS_CONFIG) httpd bash -c "$(CMD_INSTALACAO_SEI_MODULO)"; true
	docker-compose exec -w /opt/sip/scripts/$(MODULO_PASTAS_CONFIG) httpd bash -c "$(CMD_INSTALACAO_SIP_MODULO)"; true 
	@echo ""
	@echo "==================================================================================================="
	@if ! grep -q ProtocoloIntegradoIntegracao "$(ARQUIVO_CONFIG_SEI)" ; then echo '$(MENSAGEM_AVISO_MODULO)\n'; fi
	@if echo "$(MSG_ORIENTACAO_CONFIGRACAO)" | grep -qw "$(PROTOCOLO_INTEGRADO_LOGIN)" ; then echo '$(MENSAGEM_AVISO_ENV)\n'; fi
	@echo ""
	@echo "Fim da instalação do módulo"


update: ## Atualiza banco de dados através dos scripts de atualização do sistema
	docker-compose run --rm -w /opt/sei/scripts/ httpd bash -c "$(CMD_INSTALACAO_SEI)"; true
	docker-compose run --rm -w /opt/sip/scripts/ httpd bash -c "$(CMD_INSTALACAO_SIP)"; true
	docker-compose run --rm -w /opt/sip/scripts/ httpd bash -c "$(CMD_INSTALACAO_RECURSOS_SEI)"; true

up: .modulo.env  ## Inicia ambiente de desenvolvimento local (docker) no endereço http://localhost:8000
	@if [ ! -f ".env" ]; then cp envs/$(base).env .env; fi
	docker-compose up -d

config:  ## Configura o ambiente para outro banco de dados (mysql|sqlserver|oracle). Ex: make config base=oracle 
	@cp -f envs/$(base).env .env
	@echo "Ambiente configurado para utilizar a base de dados $(base). (base=[mysql|oracle|sqlserver])"

down:   ## Interrompe execução do ambiente de desenvolvimento local em docker
	docker-compose down


restart: down up ## Reinicia execução do ambiente de desenvolvimento local em docker


destroy:   ## Destrói ambiente de desenvolvimento local, junto com os dados armazenados em banco de dados
	docker-compose down --volumes


help:
	@echo "Usage: make [target] ... \n"
	@grep -E '^[a-zA-Z_-]+[[:space:]]*:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
