<?php

class PaginaProcesso extends PaginaTeste
{
    const STA_STATUS_PROCESSO_ABERTO = 1;
    const STA_STATUS_PROCESSO_CONCLUIDO = 2;

  public function __construct($test)
    {
      parent::__construct($test);
  }

  public function concluirProcesso()
    {
      $this->test->frame(null);
      $this->test->frame("ifrConteudoVisualizacao");     

      $concluirProcessoButton = $this->test->byXPath("//img[@alt='Concluir Processo']");
      $concluirProcessoButton->click();

      $this->test->frame("ifrVisualizacao");
      $confirmarConcluirProcessoButton = $this->test->byId('sbmSalvar');
      $confirmarConcluirProcessoButton->click();

  }

  public function incluirDocumento()
    {
      $this->test->frame(null);
      $this->test->frame("ifrConteudoVisualizacao");
      $this->test->frame("ifrVisualizacao");
      $incluirDocumentoButton = $this->test->byXPath("a[1]/img[@alt='Incluir Documento']");

      $incluirDocumentoButton->click();
  }

  public function enviarProcesso()
    {
      $this->test->frame(null);
      $this->test->frame("ifrConteudoVisualizacao");     

      $this->test->byXPath("//img[@alt='Enviar Processo']")->click();
  }

  public function navegarParaAnexarProcesso()
    {
      $this->test->waitUntil(function($testCase) {
          $this->selecionarProcesso();

          $this->test->frame(null);
          $this->test->frame("ifrConteudoVisualizacao");    
          $this->editarProcessoButton = $this->test->byXPath("//img[@alt='Anexar Processo']");
          $this->editarProcessoButton->click();
          sleep(2);
  
          $this->test->frame("ifrVisualizacao"); 


          $testCase->assertStringContainsString(utf8_encode('Anexação de Processos'), $testCase->byCssSelector('body')->text());
          return true;
      }, PEN_WAIT_TIMEOUT);
  }

  public function navegarParaTramitarProcessoInterno()
    {
      $this->enviarProcesso();
  }

  public function informacao()
    {
      $this->test->frame(null);

      $this->test->frame("ifrConteudoVisualizacao");
      sleep(2);
      $this->test->frame("ifrVisualizacao");        

        
      return $this->test->byId('divArvoreInformacao')->text();
  }

  public function processoBloqueado()
    {
    try
      {
        $this->test->frame(null);
        $this->test->frame("ifrArvore");
        $this->test->byXPath("//img[@title='Processo Bloqueado']");
        return true;
    }
    catch(Exception $e)
      {
        return false;
    }
  }

  public function deveSerDocumentoAnexo($bolDevePossuir, $nomeDocumentoArvore)
    {
    try
      {
        $this->test->frame(null);
        $this->test->frame("ifrArvore");
      if($bolDevePossuir){
            $idAnexo=$this->test->byXPath("//span[contains(text(),'" . $nomeDocumentoArvore . "')]")->attribute('id');
            $idAnexo=str_replace("span", "", $idAnexo);
            $this->test->byXPath("//img[contains(@id,'iconMD_PEN_DOC_REF" . $idAnexo . "')]");
      }
        return true;
    }
    catch(Exception $e)
      {
        return false;
    }
  }

  public function ehDocumentoCancelado($nomeDocumentoArvore)
    {
    try
      {
        $to = $this->test->timeouts()->getLastImplicitWaitValue();
        $this->test->timeouts()->implicitWait(300);
        $this->test->frame(null);
        $this->test->frame("ifrArvore");
        $this->test->byLinkText($nomeDocumentoArvore)->byXPath(".//preceding-sibling::a[1]/img[contains(@src,'svg/documento_cancelado.svg?')]");
        return true;
    }
    catch(Exception $e)
      {
        return false;
    }finally{
        $this->test->timeouts()->implicitWait($to);
    }
  }

  public function ehDocumentoMovido($nomeDocumentoArvore)
    {
    try
      {
        $to = $this->test->timeouts()->getLastImplicitWaitValue();
        $this->test->timeouts()->implicitWait(300);
        $this->test->frame(null);
        $this->test->frame("ifrArvore");
        $this->test->byLinkText($nomeDocumentoArvore)->byXPath(".//preceding-sibling::a[1]/img[contains(@src,'svg/documento_movido.svg?')]");
        return true;
    }
    catch(Exception $e)
      {
        return false;
    }finally{
        $this->test->timeouts()->implicitWait($to);
    }
  }

  private function selecionarItemArvore($nomeArvore)
    {
      $this->test->frame(null);
      $this->test->frame("ifrArvore");
      $this->test->byLinkText($nomeArvore)->click();
  }

  public function selecionarDocumento($nomeDocumentoArvore)
    {
      $this->selecionarItemArvore($nomeDocumentoArvore);
  }

  public function selecionarProcesso()
    {
      $this->selecionarItemArvore($this->listarArvoreProcesso()[0]);
      sleep(1);
  }

  public function listarDocumentos()
    {
      $itens = $this->listarArvoreProcesso();
      return (count($itens) > 1) ? array_slice($itens, 1) : null;
  }

  private function listarArvoreProcesso()
    {
      $this->test->frame(null);
      $this->test->frame("ifrArvore");
      $itens = $this->test->elements($this->test->using('css selector')->value('div.infraArvore > a > span[id]'));
      return array_map(function($item) {return $item->text();
      }, $itens);
  }

}
