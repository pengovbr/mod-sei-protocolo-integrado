<?php

use PHPUnit\Extensions\Selenium2TestCase\Keys as Keys;

class PaginaTipoProcessoReativar extends PaginaTeste
{
  public function __construct($test)
    {
      parent::__construct($test);
  }

  public function navegarTipoProcessoReativar()
    {
      $this->test->byId("txtInfraPesquisarMenu")->value("Reativar Mapeamento de Tipos de Processo");

      $this->test->byXPath("//a[@link='pen_map_tipo_processo_reativar']")->click();
  }
}
