<?php

use PHPUnit\Framework\TestCase;

class ChecarSaudeSistemaTest extends FixtureCenarioBaseTestCase
{
    public static $contextoTeste;

    public function testLoginEChecarSaudeSistema()
    {
        self::$contextoTeste = $this->definirContextoTeste(CONTEXTO_SEI);

        // Acessar a página de login
        $this->acessarSistema(self::$contextoTeste['URL'], self::$contextoTeste['SIGLA_UNIDADE'], self::$contextoTeste['LOGIN'], self::$contextoTeste['SENHA']);

        // Esperar que o login tenha sucesso, por exemplo, verificando um elemento visível da tela principal
        $this->waitUntil(function () {
            try {
                $element = $this->byXPath("//h6[contains(text(),'ORGAO')]");
                return $element !== null;
            } catch (\Exception $e) {
                return null;
            }
        }, 30000);

        // Teste mínimo para checar o 'health' do sistema
        $this->assertTrue(true);
    }
}