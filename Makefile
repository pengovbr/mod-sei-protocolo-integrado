.PHONY: .env .modulo.env help clean build all install restart down destroy up config test-functional-pi install-phpunit-vendor vendor

-include .env
-include .modulo.env

# Parâmetros de configuração
# Opções possíveis para spe (sistema de proc eletronico): sei41, super
sistema=super
base=mysql
PI_TEST_FUNC=tests_pi

-include  $(PI_TEST_FUNC)/.env
-include  $(PI_TEST_FUNC)/.modulo.env

ifeq (, $(shell groups |grep docker))
 CMD_DOCKER_SUDO=sudo
else
 CMD_DOCKER_SUDO=
endif

ifeq (, $(shell which docker-compose))
 CMD_DOCKER_COMPOSE=$(CMD_DOCKER_SUDO) docker compose
 CMD_COMPOSE_FUNC = $(CMD_DOCKER_COMPOSE) -f $(PI_TEST_FUNC)/docker-compose.yaml --env-file $(PI_TEST_FUNC)/.env
else
 CMD_DOCKER_COMPOSE=$(CMD_DOCKER_SUDO) docker-compose
 CMD_COMPOSE_FUNC = $(CMD_DOCKER_COMPOSE) -f $(PI_TEST_FUNC)/docker-compose.yaml --env-file $(PI_TEST_FUNC)/.env
endif

MODULO_NOME = protocolo-integrado
MODULO_PASTAS_CONFIG = mod-$(MODULO_NOME)
MODULO_PASTA_NOME = $(notdir $(shell pwd))
VERSAO_MODULO := $(shell grep 'define."VERSAO_MODULO_PI"' ./src/ProtocoloIntegradoIntegracao.php | cut -d'"' -f4)
SEI_SCRIPTS_DIR = dist/sei/scripts/$(MODULO_PASTAS_CONFIG)
SEI_CONFIG_DIR = dist/sei/config/$(MODULO_PASTAS_CONFIG)
SEI_MODULO_DIR = dist/sei/web/modulos/$(MODULO_NOME)
SIP_SCRIPTS_DIR = dist/sip/scripts/$(MODULO_PASTAS_CONFIG)
FILE_VENDOR_FUNCIONAL = $(PI_TEST_FUNC)/vendor/bin/phpunit

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

MENSAGEM_AVISO_ENV = $(ERROR)[ATENÇÃO]:$(NC)$(YELLOW) Configurar parâmetros de autenticação do ambiente de testes do Protocolo Integrado no arquivo .modulo.env $(NC)

up: .env .modulo.env ## Inicia ambiente de desenvolvimento local (docker)
	$(CMD_COMPOSE_FUNC) up -d
	make check-super-isalive

update: ## Atualiza banco de dados através dos scripts de atualização do sistema
	$(CMD_COMPOSE_FUNC) run --rm -w /opt/sei/scripts/ org-http sh -c "$(CMD_INSTALACAO_SEI)"; true
	$(CMD_COMPOSE_FUNC) run --rm -w /opt/sip/scripts/ org-http sh -c "$(CMD_INSTALACAO_SIP)"; true
	$(CMD_COMPOSE_FUNC) run --rm -w /opt/sip/scripts/ org-http sh -c "$(CMD_INSTALACAO_RECURSOS_SEI)"; true

install: check-super-isalive ## Instala e atualiza as tabelas do módulo na base de dados do sistema
	$(CMD_COMPOSE_FUNC) run --rm -w /opt/sei/scripts/ org-http bash -c "$(CMD_ATUALIZACAO_SEQ_SEI)"; true
	$(CMD_COMPOSE_FUNC) exec -T -w /opt/sei/scripts/$(MODULO_PASTAS_CONFIG) org-http bash -c "$(CMD_INSTALACAO_SEI_MODULO)";
	$(CMD_COMPOSE_FUNC) exec -T -w /opt/sip/scripts/$(MODULO_PASTAS_CONFIG) org-http bash -c "$(CMD_INSTALACAO_SIP_MODULO)";
	@echo "==================================================================================================="
	@echo ""
	@echo "Fim da instalação do módulo"

.env:
	@if [ ! -f "$(PI_TEST_FUNC)/.env" ]; then cp envs/$(base).env $(PI_TEST_FUNC)/.env; fi

.modulo.env:
	@if [ ! -f "$(PI_TEST_FUNC)/.modulo.env" ]; then \
	cp envs/modulo.env $(PI_TEST_FUNC)/.modulo.env; \
	echo "Arquivo  $(PI_TEST_FUNC)/.modulo.env nao existia. Copiado o arquivo default da pasta envs."; \
	fi

check-super-isalive: ## Target de apoio. Acessa o Super e verifica se esta respondendo a tela de login
	@echo ""
	@echo "Vamos tentar acessar a pagina de login do $(sistema), vamos aguardar por 45 segs."
	@for number in 1 2 3 4 5 6 7 8 9 ; do \
	    echo 'Tentando acessar...'; \
		echo '$(CMD_CURL_SUPER_LOGIN)'; \
			if $(CMD_CURL_SUPER_LOGIN); then \
					echo 'Pagina respondeu com tela de login' ; \
					break ; \
			else \
			    echo 'Aguardando resposta ...'; \
			fi; \
			sleep 5; \
	done

destroy: .env .modulo.env ## Destrói ambiente de desenvolvimento local, junto com os dados armazenados em banco de dados
	@if [ $(docker ps -a --filter="name=funcional-org1-http-1" | grep "Up" | awk '{split($0,a,"   "); print a[1]}') ]; then \
		$(CMD_COMPOSE_FUNC) exec org1-http bash -c "rm -rf /var/sei/arquivos/*"; \
		$(CMD_COMPOSE_FUNC) exec org2-http bash -c "rm -rf /var/sei/arquivos/*"; \
	fi; \
	$(CMD_COMPOSE_FUNC) down --volumes;

down: .env .modulo.env ## Interrompe execução do ambiente de desenvolvimento local em docker
	$(CMD_COMPOSE_FUNC) stop

check-module-config:
	@docker cp utils/verificar_modulo.php $(shell docker ps --format "{{.Names}}" | grep org-http):/
	$(CMD_COMPOSE_FUNC) exec -T org-http bash -c "php /verificar_modulo.php" ; exit 1; fi

test-functional-pi: .env $(FILE_VENDOR_FUNCIONAL) up vendor
	$(CMD_COMPOSE_FUNC) run --rm php-test-functional /tests/vendor/bin/phpunit -c /tests/phpunit.xml --testdox /tests/tests/$(addsuffix .php,$(teste)) ;

$(FILE_VENDOR_FUNCIONAL): ## target de apoio verifica se o build do phpunit foi feito e executa apenas caso n exista
	make install-phpunit-vendor

install-phpunit-vendor: ## instala os pacotes composer referentes aos testes via phpunit
	$(CMD_COMPOSE_FUNC) -f $(PI_TEST_FUNC)/docker-compose.yaml run --rm -w /tests php-test-functional bash -c './composer.phar install'

vendor: composer.json
	$(CMD_COMPOSE_FUNC) run -w /tests php-test-functional bash -c './composer.phar install'

all: clean build

dist: cria_json_compatibilidade
	@mkdir -p $(SEI_SCRIPTS_DIR)
	@mkdir -p $(SEI_CONFIG_DIR)
	@mkdir -p $(SEI_MODULO_DIR)
	@mkdir -p $(SIP_SCRIPTS_DIR)
	@cp -Rf src/* $(SEI_MODULO_DIR)/
	@cp docs/INSTALACAO.md dist/INSTALACAO.md
	@cp docs/MIGRACAO.md dist/MIGRACAO.md
	@cp docs/Manual_de_Uso.pdf dist/Manual_de_Uso.pdf
	@cp docs/changelogs/CHANGELOG-$(VERSAO_MODULO).md dist/NOTAS_VERSAO.md
	@cp compatibilidade.json dist/compatibilidade.json
	@mv $(SEI_MODULO_DIR)/scripts/sei_atualizar_versao_modulo_protocolo_integrado.php $(SEI_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/scripts/sip_atualizar_versao_modulo_protocolo_integrado.php $(SIP_SCRIPTS_DIR)/
	@mv $(SEI_MODULO_DIR)/config/ConfiguracaoModProtocoloIntegrado.exemplo.php $(SEI_CONFIG_DIR)/
	@rm -rf $(SEI_MODULO_DIR)/config
	@rm -rf $(SEI_MODULO_DIR)/scripts
	@cd dist/ && zip -r $(MODULO_COMPACTADO) Manual_de_Uso.pdf INSTALACAO.md MIGRACAO.md NOTAS_VERSAO.md compatibilidade.json sei/ sip/	
	@rm -rf dist/sei dist/sip dist/INSTALACAO.md dist/MIGRACAO.md dist/Manual_de_Uso.pdf
	@echo "Construção do pacote de distribuição finalizada com sucesso"

clean:  ## Limpa o diretório contendo arquivos temporários de construção do pacote de distribuição
	@rm -rf dist
	@rm -rf dist
	@echo "Limpeza do diretório de distribuição do realizada com sucesso"

up-backgound: .env ## Inicia ambiente de desenvolvimento local (docker) no endereço http://localhost:8000
	@if [ ! -f ".env" ]; then cp envs/$(base).env .env; fi
	$(CMD_DOCKER_COMPOSE) up -d
	make check-super-isalive
	@echo "$(SUCCESS)Ambiente de desenvolvimento iniciado com sucesso: $(SEI_HOST)/sei$(NC)"

up-foreground: .env  ## Inicia ambiente de desenvolvimento local (docker) em primeiro plano no endereço http://localhost:8000
	$(CMD_DOCKER_COMPOSE) up

config:  ## Configura o ambiente para outro banco de dados (mysql|sqlserver|oracle). Ex: make config base=oracle 
	@cp -f envs/$(base).env .env
	@echo "Ambiente configurado para utilizar a base de dados $(base). (base=[mysql|oracle|sqlserver])"

restart: down up ## Reinicia execução do ambiente de desenvolvimento local em docker

help:
	@echo "Usage: make [target] ... \n"
	@grep -E '^[a-zA-Z_-]+[[:space:]]*:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
 

tests-functional: check-super-isalive	
	@echo "Vamos iniciar a execucao do teste."	
	@pytest tests/SeleniumIDE/$(sistema)

generate-der: up
	docker run --network host --rm -v .:/work -w /work ghcr.io/k1low/tbls doc --rm-dist mariadb://$(SEI_DATABASE_USER):$(SEI_DATABASE_PASSWORD)@localhost:3306/sei

cria_json_compatibilidade:
	$(shell ./gerar_json_compatibilidade.sh)
