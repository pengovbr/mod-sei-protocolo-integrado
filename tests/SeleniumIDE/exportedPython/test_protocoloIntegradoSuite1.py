# Generated by Selenium IDE
import pytest
import time
import json
from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities

class TestProtocoloIntegradoSuite1():
  def setup_method(self, method):
    self.driver = webdriver.Chrome()
    self.vars = {}
  
  def teardown_method(self, method):
    self.driver.quit()
  
  def wait_for_window(self, timeout = 2):
    time.sleep(round(timeout / 1000))
    wh_now = self.driver.window_handles
    wh_then = self.vars["window_handles"]
    if len(wh_now) > len(wh_then):
      return set(wh_now).difference(set(wh_then)).pop()
  
  def test_10Agendamentos(self):
    self.driver.get("http://localhost:8000/sip/login.php?sigla_orgao_sistema=ABC&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmAcessar").click()
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").click()
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").send_keys("Agendamentos")
    self.driver.find_element(By.XPATH, "(//span[normalize-space()='Agendamentos'])").click()
    elements = self.driver.find_elements(By.XPATH, "(//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados')])[1]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
  
  def test_20AgendamentoEnviar(self):
    self.driver.get("http://localhost:8000/sip/login.php?sigla_orgao_sistema=ABC&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").click()
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "pwdSenha")))
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmAcessar").click()    
    #Troca para a unidade TESTE_1_2, caso esteja em outra unidade
    html = self.driver.page_source
    soup = BeautifulSoup(html, 'html.parser')    
    unidadeAtual = soup.findAll("a", {"id": "lnkInfraUnidade"})[0].string
    if unidadeAtual != 'TESTE_1_2':
      self.driver.find_element(By.CSS_SELECTOR, ".d-none #lnkInfraUnidade").click()
      self.driver.find_element(By.XPATH, "//td[contains(text(),'TESTE_1_2')]/../td[1]/div/label").click()    
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").click()
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").send_keys("Iniciar Processo")
    WebDriverWait(self.driver, 5000)
    #Inicia novo processo
    self.driver.find_element(By.XPATH, "//span[contains(text(),'Iniciar Processo')][1]").click()
    self.driver.find_element(By.LINK_TEXT, "Acesso à Informação: Demanda do e-SIC").click()
    element = self.driver.find_element(By.ID, "selTipoProcedimento")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).perform()
    element = self.driver.find_element(By.ID, "selTipoProcedimento")
    self.driver.find_element(By.ID, "txtDescricao").click()
    self.driver.find_element(By.ID, "txtDescricao").send_keys("Teste PI")
    self.driver.find_element(By.ID, "txtInteressadoProcedimento").click()
    self.driver.find_element(By.ID, "txtInteressadoProcedimento").send_keys("Modulo do Protocolo Integrado do SEI")
    self.driver.find_element(By.CSS_SELECTOR, "#divOptPublico .infraRadioLabel").click()
    self.driver.find_element(By.CSS_SELECTOR, "#divInfraBarraComandosInferior > #btnSalvar").click()
    self.driver.switch_to.frame(1)
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//*[@id='divArvoreAcoes']/a[1]/img[@title='Incluir Documento']")))
    #Inclui algum documento no processo
    self.driver.find_element(By.XPATH, "//*[@id='divArvoreAcoes']/a[1]/img[@title='Incluir Documento']").click()
    self.driver.switch_to.frame(0)
    self.driver.find_element(By.XPATH, "//a[normalize-space()='Despacho']").click()
    self.driver.find_element(By.ID, "txtDescricao").click()
    self.driver.find_element(By.ID, "txtDescricao").send_keys("Despacho")
    self.driver.find_element(By.CSS_SELECTOR, "#divOptPublico .infraRadioLabel").click()
    self.vars["window_handles"] = self.driver.window_handles
    self.driver.find_element(By.CSS_SELECTOR, "#divInfraBarraComandosInferior > #btnSalvar").click()
    self.vars["win8082"] = self.wait_for_window(2000)
    self.vars["root"] = self.driver.current_window_handle
    self.driver.switch_to.window(self.vars["win8082"])
    self.driver.close()
    self.driver.switch_to.window(self.vars["root"])
    self.driver.switch_to.frame(1)
    #Assina o processo
    self.driver.find_element(By.CSS_SELECTOR, "a:nth-child(7) > img").click() ##btn assinar
    self.driver.switch_to.default_content()
    self.driver.switch_to.frame(2)
    dropdown = self.driver.find_element(By.ID, "selCargoFuncao")
    dropdown.find_element(By.XPATH, "//option[. = 'Agente Fiscalizador de Contrato']").click()
    element = self.driver.find_element(By.ID, "selCargoFuncao")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).click_and_hold().perform()
    element = self.driver.find_element(By.ID, "selCargoFuncao")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).perform()
    element = self.driver.find_element(By.ID, "selCargoFuncao")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).release().perform()
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "btnAssinar").click()
    self.driver.switch_to.frame(0)
    self.driver.switch_to.default_content()
    self.driver.switch_to.frame(1)
    #Conclui processo
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//img[@alt=\'Concluir Processo\']")))
    time.sleep(2)
    self.driver.switch_to.default_content()
    self.driver.switch_to.frame(0)
    self.driver.find_element(By.XPATH, "//span[contains(.,\'99992\')]").click()    
    self.driver.switch_to.default_content()    
    self.driver.switch_to.frame(1)
    #Envia processo para Unidade TESTE
    time.sleep(2)
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.CSS_SELECTOR, "#divArvoreAcoes img[title='Enviar Processo']")))
    self.driver.find_element(By.CSS_SELECTOR, "#divArvoreAcoes img[title='Enviar Processo']").click()
    time.sleep(2)
    self.driver.switch_to.frame(0)
    self.driver.find_element(By.ID, "txtUnidade").click()
    self.driver.find_element(By.ID, "txtUnidade").send_keys("TESTE")
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//a[contains(.,\'TESTE - Unidade de Teste 1\')]")))
    element = self.driver.find_element(By.XPATH, "//a[contains(.,\'TESTE - Unidade de Teste 1\')]")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).click_and_hold().perform()
    element = self.driver.find_element(By.XPATH, "//option[contains(.,\'TESTE - Unidade de Teste 1\')]")
    actions = ActionChains(self.driver)
    actions.move_to_element(element).release().perform()
    self.driver.find_element(By.ID, "divInfraAreaTelaD").click()
    self.driver.find_element(By.ID, "sbmEnviar").click()
    time.sleep(2)
    self.driver.switch_to.default_content()    
    self.driver.switch_to.frame(1)
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//img[@alt=\'Reabrir Processo\']")))
    #self.driver.find_element(By.XPATH, "//img[@alt=\'Reabrir Processo\']").click()
    self.driver.find_element(By.XPATH, "//img[@alt=\'Controle de Processos\']").click()
    self.driver.switch_to.default_content()
  
  def test_30Agendamentos(self):
    self.driver.get("http://localhost:8000/sip/login.php?sigla_orgao_sistema=ABC&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmAcessar").click()
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").click()
    self.driver.find_element(By.ID, "txtInfraPesquisarMenu").send_keys("Agendamentos")
    self.driver.find_element(By.XPATH, "(//span[normalize-space()='Agendamentos'])").click()
    elements = self.driver.find_elements(By.XPATH, "(//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados')])[1]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(text(),'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado')]/../td[7]/a/img[@title='Executar Agendamento']").click() #//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[7]/a/img
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
  
