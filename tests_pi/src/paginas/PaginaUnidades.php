<?php

use PHPUnit\Extensions\Selenium2TestCase\Keys as Keys;

class PaginaUnidades extends PaginaTeste
{
    /**
     * Método contrutor
     * 
     * @return void
     */
    public function __construct($test)
    {
        parent::__construct($test);
    }

    public function navegarListagemDeUnidades()
    {
        $this->test->byId("txtInfraPesquisarMenu")->value('Unidades');
        $this->test->byXPath("//span[text()='Unidades']")->click();
        $this->test->byXPath("//a[@link='unidade_listar']")->click();
    }

    public function desativarUnidade($unidadeParaDesativar)
    {
        $linhas = $this->test->elements($this->test->using('xpath')->value('//table[contains(@class, "infraTable")]/tbody/tr'));
        unset($linhas[0]);

        $row = 0;
        foreach($linhas as $idx => $linha) {
            $colunaNome = $linha->byXPath('./td[3]');

            if ($colunaNome->text() === $unidadeParaDesativar) {
                $row = $idx;
                break;
            }
        }

        $this->test->byXPath("(//img[@title='Desativar Unidade'])[$row]")->click();
        $bolExisteAlerta = $this->alertTextAndClose();
        if ($bolExisteAlerta != null) $this->test->keys(Keys::ENTER);
    }

    public function navegarListagemDeUnidadesDesativadas()
    {
        $this->test->byId("txtInfraPesquisarMenu")->value('Unidades');
        $this->test->byXPath("//span[text()='Unidades']")->click();
        $this->test->byXPath("//a[@link='unidade_reativar']")->click();
    }

    public function reativarUnidade($unidadeParaReativar)
    {
        $linhas = $this->test->elements($this->test->using('xpath')->value('//table[contains(@class, "infraTable")]/tbody/tr'));
        unset($linhas[0]);

        $row = 0;
        foreach($linhas as $idx => $linha) {
            $colunaNome = $linha->byXPath('./td[3]');

            if ($colunaNome->text() === $unidadeParaReativar) {
                $row = $idx;
            }
        }

        $this->test->byXPath("(//img[@title='Reativar Unidade'])[$row]")->click();
        $bolExisteAlerta = $this->alertTextAndClose();
        if ($bolExisteAlerta != null) $this->test->keys(Keys::ENTER);
    }
}
