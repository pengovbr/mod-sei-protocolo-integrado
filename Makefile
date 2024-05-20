.PHONY: .env all help clean dist install restart down destroy up up-background up-foreground config check-super-isalive

-include .env
-include .modulo.env

# Parâmetros de configuração

# Opções possíveis para spe (sistema de proc eletronico): sei4, sei41, super e sei5
sistema = super
base = mysql

MODULO_NOME = protocolo-integrado
MODULO_PASTAS_CONFIG = mod-$(MODULO_NOME)
MODULO_PASTA_NOME = $(notdir $(shell pwd))
VERSAO_MODULO := $(shell grep 'define."VERSAO_MODULO_PI"' src/ProtocoloIntegradoIntegracao.php | cut -d'"' -f4)
SEI_SCRIPTS_DIR = dist/sei/scripts/$(MODULO_PASTAS_CONFIG)
SEI_CONFIG_DIR = dist/sei/config/$(MODULO_PASTAS_CONFIG)
SEI_MODULO_DIR = dist/sei/web/modulos/$(MODULO_NOME)
SIP_SCRIPTS_DIR = dist/sip/scripts/$(MODULO_PASTAS_CONFIG)

ARQUIVO_CONFIG_SEI=$(SEI_PATH)/sei/config/ConfiguracaoSEI.php
MSG_ORIENTACAO_CONFIGRACAO=CONFIGURACAO PENDENTE
MODULO_COMPACTADO = mod-sei-$(MODULO_NOME)-v$(VERSAO_MODULO).zip

CMD_INSTALACAO_SEI = echo -ne '$(SEI_DATABASE_USER)\n$(SEI_DATABASE_PASSWORD)\n' | php atualizar_versao_sei.php
CMD_INSTALACAO_SIP = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php atualizar_versao_sip.php
CMD_INSTALACAO_RECURSOS_SEI = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php atualizar_recursos_sei.php
CMD_INSTALACAO_SEI_MODULO = echo -ne '$(SEI_DATABASE_USER)\n$(SEI_DATABASE_PASSWORD)\n' | php sei_atualizar_versao_modulo_protocolo_integrado.php
CMD_INSTALACAO_SIP_MODULO = echo -ne '$(SIP_DATABASE_USER)\n$(SIP_DATABASE_PASSWORD)\n' | php sip_atualizar_versao_modulo_protocolo_integrado.php

CMD_CURL_SUPER_LOGIN = curl -s -L $(SEI_HOST)/sei | grep -q "input.*txtUsuario.*"
SUCCESS=\033[0;32m
ERROR=\033[0;31m
WARNING=\033[1;33m
NC=\033[0m

MENSAGEM_AVISO_MODULO = $(ERROR)[ATENÇÃO]:$(NC)$(YELLOW) Necessário configurar a chave de configuração do módulo no arquivo de configuração do SEI (ConfiguracaoSEI.php) $(NC)\n               $(YELLOW)'Modulos' => array('ProtocoloIntegradoIntegracao' => 'protocolo-integrado') $(NC)
MENSAGEM_AVISO_ENV = $(ERROR)[ATENÇÃO]:$(NC)$(YELLOW) Configurar parâmetros de autenticação do ambiente de testes do Protocolo Integrado no arquivo .modulo.env $(NC)

ifeq (, $(shell groups |grep docker))
 CMD_DOCKER_SUDO=sudo
else
 CMD_DOCKER_SUDO=
endif

ifeq (, $(shell which docker-compose))
 CMD_DOCKER_COMPOSE=$(CMD_DOCKER_SUDO) docker compose
else
 CMD_DOCKER_COMPOSE=$(CMD_DOCKER_SUDO) docker-compose
endif

all: clean build

dist: 
	@mkdir -p $(SEI_SCRIPTS_DIR)
	@mkdir -p $(SEI_CONFIG_DIR)
	@mkdir -p $(SEI_MODULO_DIR)
	@mkdir -p $(SIP_SCRIPTS_DIR)
	@cp -Rf src/* $(SEI_MODULO_DIR)/
	@cp docs/INSTALACAO.md dist/INSTALACAO.md
	@cp docs/MIGRACAO.md dist/MIGRACAO.md
	@cp docs/Manual_de_Uso.pdf dist/Manual_de_Uso.pdf
	@cp docs/changelogs/CHANGELOG-$(VERSAO_MODULO).md dist/NOTAS_VERSAO.md
	@mv $(SEI_MODULO_DIR)/scripts/sei_atualizar_versao_modulo_protocolo_integrado.php $(SEI_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/scripts/sip_atualizar_versao_modulo_protocolo_integrado.php $(SIP_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/config/ConfiguracaoModProtocoloIntegrado.exemplo.php $(SEI_CONFIG_DIR)/
	@rm -rf $(SEI_MODULO_DIR)/config
	@rm -rf $(SEI_MODULO_DIR)/scripts
	@cd dist/ && zip -r $(MODULO_COMPACTADO) Manual_de_Uso.pdf INSTALACAO.md MIGRACAO.md NOTAS_VERSAO.md sei/ sip/	
	@rm -rf dist/sei dist/sip dist/INSTALACAO.md dist/MIGRACAO.md dist/Manual_de_Uso.pdf
	@echo "Construção do pacote de distribuição finalizada com sucesso"


clean:  ## Limpa o diretório contendo arquivos temporários de construção do pacote de distribuição
	@rm -rf dist
	@rm -rf dist
	@echo "Limpeza do diretório de distribuição do realizada com sucesso"

.modulo.env:
	cp -n envs/modulo.env .modulo.env

check-super-isalive: ## Target de apoio. Acessa o Super e verifica se esta respondendo a tela de login
	@echo ""
	@echo "$(WARNING)Aguardando inicialização do ambiente de desenvolvimento...$(NC)"
	@for i in `seq 1 10`; do \
	    echo "Tentativa $$i/10";  \
		if $(CMD_CURL_SUPER_LOGIN); then \
				echo 'Página de login carregada!' ; \
				break ; \
		fi; \
		sleep 5; \
	done; \
	if ! $(CMD_CURL_SUPER_LOGIN); then echo '$(ERROR)Ambiente de desenvolvimento não pôde ser carregado corretamente.$(NC)'; exit 1 ; fi;


install: ## Instala e atualiza as tabelas do módulo na base de dados do sistema
	$(CMD_DOCKER_COMPOSE) exec -w /opt/sei/scripts/$(MODULO_PASTAS_CONFIG) httpd bash -c "$(CMD_INSTALACAO_SEI_MODULO)"; true
	$(CMD_DOCKER_COMPOSE) exec -w /opt/sip/scripts/$(MODULO_PASTAS_CONFIG) httpd bash -c "$(CMD_INSTALACAO_SIP_MODULO)"; true 
	@echo ""
	@echo "==================================================================================================="
	@if ! grep -q ProtocoloIntegradoIntegracao "$(ARQUIVO_CONFIG_SEI)" ; then echo '$(MENSAGEM_AVISO_MODULO)\n'; fi
	@if echo "$(MSG_ORIENTACAO_CONFIGRACAO)" | grep -qw "$(PROTOCOLO_INTEGRADO_LOGIN)" ; then echo '$(MENSAGEM_AVISO_ENV)\n'; fi
	@echo ""
	@echo "Fim da instalação do módulo"


update: ## Atualiza banco de dados através dos scripts de atualização do sistema
	$(CMD_DOCKER_COMPOSE) run --rm -w /opt/sei/scripts/ httpd bash -c "$(CMD_INSTALACAO_SEI)"; true
	$(CMD_DOCKER_COMPOSE) run --rm -w /opt/sip/scripts/ httpd bash -c "$(CMD_INSTALACAO_SIP)"; true
	$(CMD_DOCKER_COMPOSE) run --rm -w /opt/sip/scripts/ httpd bash -c "$(CMD_INSTALACAO_RECURSOS_SEI)"; true


up: up-backgound  ## Inicia ambiente de desenvolvimento local (docker) no endereço http://localhost:8000


up-backgound: .env .modulo.env  ## Inicia ambiente de desenvolvimento local (docker) no endereço http://localhost:8000
	@if [ ! -f ".env" ]; then cp envs/$(base).env .env; fi
	$(CMD_DOCKER_COMPOSE) up -d
	make check-super-isalive
	@echo "$(SUCCESS)Ambiente de desenvolvimento iniciado com sucesso: $(SEI_HOST)/sei$(NC)"


up-foreground: .env  ## Inicia ambiente de desenvolvimento local (docker) em primeiro plano no endereço http://localhost:8000
	$(CMD_DOCKER_COMPOSE) up


config:  ## Configura o ambiente para outro banco de dados (mysql|sqlserver|oracle). Ex: make config base=oracle 
	@cp -f envs/$(base).env .env
	@echo "Ambiente configurado para utilizar a base de dados $(base). (base=[mysql|oracle|sqlserver])"

down:   ## Interrompe execução do ambiente de desenvolvimento local em docker
	$(CMD_DOCKER_COMPOSE) down


restart: down up ## Reinicia execução do ambiente de desenvolvimento local em docker


destroy:   ## Destrói ambiente de desenvolvimento local, junto com os dados armazenados em banco de dados
	$(CMD_DOCKER_COMPOSE) down --volumes


help:
	@echo "Usage: make [target] ... \n"
	@grep -E '^[a-zA-Z_-]+[[:space:]]*:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
 

tests-functional: check-super-isalive	
	@echo "Vamos iniciar a execucao do teste."	
	@pytest tests/SeleniumIDE/$(sistema)