JSON_FMT='{"name":"Módulo Protocolo Integrado", "version": "%s", "compatible_with": [%s]}'
VERSAO_MODULO=$(grep 'define."VERSAO_MODULO_PI"' src/ProtocoloIntegradoIntegracao.php | cut -d'"' -f4)
VERSOES=$(sed -n -e "/COMPATIBILIDADE_MODULO_SEI = \[/,/;/ p" src/ProtocoloIntegradoIntegracao.php \
           | sed -e '1d;$d' | sed -e '/\/\//d' \
           | sed -e "s/'/\"/g"| tr -d '\n'| tr -d ' ')

printf "$JSON_FMT" "$VERSAO_MODULO" "$VERSOES" > compatibilidade.json
