<?php

require_once DIR_SEI_WEB . '/SEI.php';

session_start();

$objPaginaSEI = PaginaSEI::getInstance();
$objSessaoSEI = SessaoSEI::getInstance();

$objSessaoSEI->validarLink();
$objSessaoSEI->validarPermissao('md_pi_acesso_arquivo_metadados');

try {

  switch ($_GET['acao']) {
    case 'md_pi_visualizar_metadados':
      $idPacote = $_REQUEST['id_pacote'];
      $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
      $objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
      $objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($idPacote);
      $objPacoteDTO->retStrArquivoMetadados();
      $objPacoteDTO->retStrProtocoloFormatado();
      $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();

      $objRetornoPacote =  $objPacoteRN->consultar($objPacoteDTO);

      $array = json_decode($objRetornoPacote->getStrArquivoMetadados(), true);
      
      // header("Content-Type: text/xml");
      $nomeXML = $objRetornoPacote->getNumIdProtocoloIntegradoPacoteEnvio() . '_' . date('YmdHis') . '_pi.xml';
      header('Content-Disposition: attachment; filename="' . $nomeXML . '"');

      print '<RECIBO>';
      print !isset($array['ASSUNTO']) ? '<ASSUNTO>' . mb_convert_encoding($array['assunto'], 'UTF-8', 'ISO-8859-1') . '</ASSUNTO>' : '';
      print !isset($array['PROTOCOLO']) ? '<PROTOCOLO>' . mb_convert_encoding($array['protocolo'], 'UTF-8', 'ISO-8859-1') . '</PROTOCOLO>' : '';
      print !isset($array['ESPECIE']) ? '<ESPECIE>' . mb_convert_encoding($array['especie'], 'UTF-8', 'ISO-8859-1') . '</ESPECIE>' : '';
      print !isset($array['DATA_HORA_PRODUCAO']) ? '<DATA_HORA_PRODUCAO>' . mb_convert_encoding($array['dataHoraProducao'], 'UTF-8', 'ISO-8859-1') . '</DATA_HORA_PRODUCAO>' : '';
      print !isset($array['INTERESSADOS']) ? '<INTERESSADOS>' : '';
      if (!isset($array['INTERESSADOS'])) {
        foreach ($array['interessados'] as $interessado) {
          print !isset($array['INTERESSADO']) ? '<INTERESSADO>' : '';
          print !isset($array['NOME']) ? '<NOME>' . mb_convert_encoding($interessado['nome'], 'UTF-8', 'ISO-8859-1') . '</NOME>' : '';
          print !isset($array['INTERESSADO']) ? '</INTERESSADO>' : '';
        }
      }
      print !isset($array['INTERESSADOS']) ? '</INTERESSADOS>' : '';
      print !isset($array['HISTORICO']) ? '<HISTORICO>' : '';
      if (!isset($array['HISTORICO'])) {
        foreach ($array['historico'] as $historico) {
          print !isset($array['HISTORICO_ITEM']) ? '<HISTORICO_ITEM>' : '';
          print !isset($array['UNIDADE']) ? '<UNIDADE>' . mb_convert_encoding($historico['unidade'], 'UTF-8', 'ISO-8859-1') . '</UNIDADE>' : '';
          print !isset($array['OPERACAO']) ? '<OPERACAO>' . mb_convert_encoding($historico['operacao'], 'UTF-8', 'ISO-8859-1') . '</OPERACAO>' : '';
          print !isset($array['DATA_HORA_OPERACAO']) ? '<DATA_HORA_OPERACAO>' . mb_convert_encoding($historico['dataHoraOperacao'], 'UTF-8', 'ISO-8859-1') . '</DATA_HORA_OPERACAO>' : '';
          print !isset($array['HISTORICO_ITEM']) ? '</HISTORICO_ITEM>' : '';
        }
      }
      print !isset($array['HISTORICO']) ? '</HISTORICO>' : '';
      print '</RECIBO>';

        die;
    case 'md_pi_visualizar_erro_envio_metadados':
      $idPacote = $_REQUEST['id_pacote'];
      $objPacoteDTO = new ProtocoloIntegradoPacoteEnvioDTO();
      $objPacoteRN = new ProtocoloIntegradoPacoteEnvioRN();
      $objPacoteDTO->setNumIdProtocoloIntegradoPacoteEnvio($idPacote);

      $objPacoteDTO->retStrArquivoErro();
      $objPacoteDTO->retNumIdProtocoloIntegradoPacoteEnvio();

      $objRetornoPacote =  $objPacoteRN->consultar($objPacoteDTO);
      
      $nomeXML = $objRetornoPacote->getNumIdProtocoloIntegradoPacoteEnvio() . '_' . date('YmdHis') . '_erro_pi.xml';
      header('Content-Disposition: attachment; filename="' . $nomeXML .'"');
      print($objRetornoPacote->getStrArquivoErro());
        die;

    default:
        throw new InfraException("Módulo Protocolo Integrado: Ação '".$_GET['acao']."' não reconhecida.");
            
  }
} catch (Exception $e) {
  $objPaginaSEI->processarExcecao($e);
}
