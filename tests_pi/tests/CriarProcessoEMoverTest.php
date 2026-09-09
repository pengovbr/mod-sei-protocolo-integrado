<?php

use PHPUnit\Framework\TestCase;

class CriarProcessoEMoverTest extends FixtureCenarioBaseTestCase
{
    public static $contextoTeste;

    public static $dadosProcessoTeste;

    public function testExecutarAgendamentosIniciais()
    {
        self::$contextoTeste = $this->definirContextoTeste(CONTEXTO_SEI);

        $this->acessarSistema(self::$contextoTeste['URL'], self::$contextoTeste['SIGLA_UNIDADE_SECUNDARIA'], self::$contextoTeste['LOGIN'], self::$contextoTeste['SENHA']);
        
        $this->paginaAgendamentos->navegarAgendamento();

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado');

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados');

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado');
    }

    public function testCriarProcessoEMoverParaOutraUnidade()
    {
        self::$dadosProcessoTeste = $this->gerarDadosProcessoTeste(self::$contextoTeste);
        
        // Acessar a página de login
        $this->acessarSistema(self::$contextoTeste['URL'], self::$contextoTeste['SIGLA_UNIDADE_SECUNDARIA'], self::$contextoTeste['LOGIN'], self::$contextoTeste['SENHA']);

        $this->paginaIniciarProcesso->gerarProcessoTeste(self::$contextoTeste);

        $this->paginaIncluirDocumento->gerarDocumentoTeste(self::$dadosProcessoTeste);

        $this->tramitarProcessoInternamente(self::$contextoTeste['SIGLA_UNIDADE'], false);

        $this->waitUntil(function() {
            sleep(5);
            $this->paginaBase->refresh();
          try { 
              $this->assertStringContainsString("Processo aberto somente na unidade " . self::$contextoTeste['SIGLA_UNIDADE'] . ".", $this->paginaProcesso->informacao());
              return true;
          } catch (AssertionFailedError $e) {
              return false;
          }
        }, 30000);

        // Teste mínimo para checar o 'health' do sistema
        $this->assertTrue(true);
    }

    public function testExecutarAgendamentosFinais()
    {
        $this->acessarSistema(self::$contextoTeste['URL'], self::$contextoTeste['SIGLA_UNIDADE_SECUNDARIA'], self::$contextoTeste['LOGIN'], self::$contextoTeste['SENHA']);
        
        $this->paginaAgendamentos->navegarAgendamento();

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado');

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados');

        $this->paginaAgendamentos->executarAgendamento('ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado');
    }
}