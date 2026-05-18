<?php

use PHPUnit\Extensions\Selenium2TestCase\Keys as Keys;

class PaginaPreparacaoListagemEliminacao extends PaginaTeste
{
    /**
     * Método construtor
     * 
     * @return void
     */
  public function __construct($test)
    {
      parent::__construct($test);
  }

  public function navegarPreparacaoListagemEliminacao()
    {
      $this->test->byId("txtInfraPesquisarMenu")->value(mb_convert_encoding('Preparação da Listagem', 'UTF-8', 'ISO-8859-1'));
      $this->test->byXPath("//a[@link='gd_lista_eliminacao_preparacao_listar']")->click();
  }

    /** 
     * Executa ação de gerarListagemEliminacao
     */
  public function gerarListagemEliminacao()
    {
      $button = $this->test->byXPath('//*[@id="btnGerarListagem"]');
      $button->click();

  }

  public function selecionarTodosCheckbox()
    {
      $button = $this->test->byXPath('//*[@id="imgInfraCheck"]');
      $button->click();
  }   

  private function listarProcessos()
    {
      $linhasListagem = $this->test->elements($this->test->using('xpath')->value('//table[contains(@class, "infraTable")]/tbody/tr'));
      unset($linhasListagem[0]);

      return $linhasListagem;
  }

    /**
     * Verificar se a tabela é exibida
     *
     * @return bool
     */
  public function existeTabela()
    {
    try {
        $trTh = $this->test->byXPath('//*[@id="divInfraAreaTabela"]/table/tbody/tr[1]/th[2]')->text();
        return !empty($trTh) && !is_null($trTh);
    } catch (Exception $ex) {
        return false;
    }
  }


    // public function prepararListagemDeEliminacao($processo, $gerarListagemDeEliminacao = false)
    // {
    //     $linhasListagemDeAvaliacao = $this->listarProcessos();

    //     foreach($linhasListagemDeAvaliacao as $idx => $linha) {
    //         $colunaNProcesso = $linha->byXPath('./td[4]');

    //         if ($colunaNProcesso->text() === $processo) {
    //             if (!$gerarListagemDeEliminacao) {
    //                 return true;
    //             }

    //             $this->test->byXPath("(//label[@class='infraCheckboxLabel'])[$idx]")->click();
    //             $this->test->byId('sbmEliminacao')->click();
    //             sleep(5);
    //             $bolExisteAlerta = $this->alertTextAndClose();
    //             if ($bolExisteAlerta != null) $this->test->keys(Keys::ENTER);
    //         }
    //     }
    // }

    // public function navegarAvaliarProcesso($processo)
    // {
    //     $linhasListagemDeAvaliacao = $this->listarProcessos();

    //     $processoIdx = 0;
    //     foreach($linhasListagemDeAvaliacao as $idx => $linha) {
    //         $colunaNProcesso = $linha->byXPath('./td[4]');

    //         if ($colunaNProcesso->text() === $processo) {
    //             $processoIdx = $idx;
    //         }
    //     }

    //     $this->test->byXPath("(//img[@alt='Avaliar Processo'])[$processoIdx]")->click();
    // }

  public function salvarAlteracao()
    {
      $this->test->byXPath("//button[@id='btnSalvar']")->click();
      sleep(2);
      $this->test->acceptAlert();
      sleep(5);
  }

}