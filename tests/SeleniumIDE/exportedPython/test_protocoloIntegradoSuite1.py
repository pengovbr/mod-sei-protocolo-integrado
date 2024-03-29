# Generated by Selenium IDE
import pytest
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities

class TestProtocoloIntegradoSuite1():
  def setup_method(self, method):
    self.driver = webdriver.Remote(command_executor='http://seleniumhub:4444/wd/hub', desired_capabilities=DesiredCapabilities.CHROME)
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
    self.driver.get("http://sei3.nuvem.gov.br/sip/login.php?sigla_orgao_sistema=ME&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmLogin").click()
    WebDriverWait(self.driver, 30000).until(expected_conditions.presence_of_element_located((By.XPATH, "//a[contains(text(),\'Agendamentos\')]")))
    self.driver.find_element(By.XPATH, "//ul[@id=\'main-menu\']/li[24]/a").click()
    self.driver.find_element(By.LINK_TEXT, "Agendamentos").click()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
  
  def test_20AgendamentoEnviar(self):
    self.driver.get("http://sei3.nuvem.gov.br/sip/login.php?sigla_orgao_sistema=ME&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").click()
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "pwdSenha")))
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmLogin").click()
    WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "selInfraUnidades")))
    dropdown = self.driver.find_element(By.ID, "selInfraUnidades")
    dropdown.find_element(By.XPATH, "//option[. = 'TESTE_1_2']").click()
    self.driver.find_element(By.ID, "selInfraUnidades").click()
    for i in range(0, 5):
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.LINK_TEXT, "Iniciar Processo")))
      self.driver.find_element(By.LINK_TEXT, "Iniciar Processo").click()
      self.vars["error"] = len(self.driver.find_elements(By.XPATH, "//a[contains(text(), \'Acesso à Informação: Demanda do e-SIC\')]"))
      if self.driver.execute_script("return (arguments[0]==0)", self.vars["error"]):
        self.driver.find_element(By.ID, "imgExibirTiposProcedimento").click()
      self.driver.find_element(By.LINK_TEXT, "Acesso à Informação: Demanda do e-SIC").click()
      WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "txtDescricao")))
      self.driver.find_element(By.ID, "txtDescricao").send_keys("Teste Selenium Modulo Protocolo Integrado")
      self.driver.find_element(By.ID, "txtInteressadoProcedimento").click()
      self.driver.find_element(By.ID, "txtInteressadoProcedimento").send_keys("Modulo do Protocolo Integrado do SEI")
      self.driver.find_element(By.ID, "txtInteressadoProcedimento").send_keys(Keys.ENTER)
      self.driver.switch_to.alert.accept()
      self.driver.find_element(By.ID, "optPublico").click()
      self.driver.find_element(By.CSS_SELECTOR, "#divInfraBarraComandosInferior > #btnSalvar > .infraTeclaAtalho").click()
      self.driver.switch_to.frame(0)
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//a[contains(., \"9999\")]")))
      self.driver.find_element(By.XPATH, "//a[contains(., \"9999\")]").click()
      time.sleep(2)
      self.driver.switch_to.default_content()
      self.driver.switch_to.frame(1)
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.CSS_SELECTOR, ".botaoSEI:nth-child(1) > .infraCorBarraSistema")))
      self.driver.find_element(By.CSS_SELECTOR, ".botaoSEI:nth-child(1) > .infraCorBarraSistema").click()
      self.vars["error"] = len(self.driver.find_elements(By.XPATH, "//a[contains(text(), \'Despacho\')]"))
      if self.driver.execute_script("return (arguments[0]==0)", self.vars["error"]):
        self.driver.find_element(By.ID, "imgExibirSeries").click()
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.LINK_TEXT, "Despacho")))
      self.driver.find_element(By.LINK_TEXT, "Despacho").click()
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "optPublico")))
      self.driver.find_element(By.ID, "optPublico").click()
      self.vars["window_handles"] = self.driver.window_handles
      self.driver.find_element(By.ID, "btnSalvar").click()
      self.vars["win3408"] = self.wait_for_window(10000)
      self.vars["root"] = self.driver.current_window_handle
      self.driver.switch_to.window(self.vars["win3408"])
      self.driver.close()
      self.driver.switch_to.window(self.vars["root"])
      self.driver.switch_to.frame(0)
      self.driver.switch_to.default_content()
      self.driver.switch_to.frame(1)
      self.vars["window_handles"] = self.driver.window_handles
      self.driver.find_element(By.CSS_SELECTOR, ".botaoSEI:nth-child(6) > .infraCorBarraSistema").click()
      self.vars["win3388"] = self.wait_for_window(2000)
      self.driver.switch_to.window(self.vars["win3388"])
      WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "selCargoFuncao")))
      self.driver.find_element(By.ID, "selCargoFuncao").click()
      dropdown = self.driver.find_element(By.ID, "selCargoFuncao")
      dropdown.find_element(By.XPATH, "//option[. = 'Corregedor']").click()
      self.driver.find_element(By.ID, "pwdSenha").click()
      self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
      self.driver.find_element(By.ID, "btnAssinar").click()
      self.driver.switch_to.window(self.vars["root"])
      self.driver.switch_to.frame(0)
      self.driver.switch_to.default_content()
      self.driver.switch_to.frame(1)
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//img[@alt=\'Concluir Processo\']")))
      time.sleep(2)
      self.driver.switch_to.default_content()
      self.driver.switch_to.frame(0)
      self.driver.find_element(By.XPATH, "//span[contains(.,\'99992\')]").click()
      self.driver.switch_to.default_content()
      self.driver.switch_to.frame(1)
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.ID, "divInformacao")))
      self.driver.find_element(By.CSS_SELECTOR, ".botaoSEI:nth-child(6) > .infraCorBarraSistema").click()
      self.driver.find_element(By.ID, "txtUnidade").send_keys("TESTE")
      time.sleep(2)
      element = self.driver.find_element(By.CSS_SELECTOR, "li:nth-child(2) > a")
      actions = ActionChains(self.driver)
      actions.move_to_element(element).click_and_hold().perform()
      element = self.driver.find_element(By.ID, "selUnidades")
      actions = ActionChains(self.driver)
      actions.move_to_element(element).release().perform()
      self.driver.find_element(By.CSS_SELECTOR, "body").click()
      self.driver.find_element(By.ID, "sbmEnviar").click()
      WebDriverWait(self.driver, 30000).until(expected_conditions.visibility_of_element_located((By.XPATH, "//img[@alt=\'Reabrir Processo\']")))
      self.driver.find_element(By.XPATH, "//img[@alt=\'Controle de Processos\']").click()
      self.driver.switch_to.default_content()
  
  def test_30Agendamentos(self):
    self.driver.get("http://sei3.nuvem.gov.br/sip/login.php?sigla_orgao_sistema=ME&sigla_sistema=SEI&infra_url=L3NlaS8=")
    WebDriverWait(self.driver, 30000).until(expected_conditions.element_to_be_clickable((By.ID, "txtUsuario")))
    self.driver.find_element(By.ID, "txtUsuario").send_keys("teste")
    self.driver.find_element(By.ID, "pwdSenha").click()
    self.driver.find_element(By.ID, "pwdSenha").send_keys("teste")
    self.driver.find_element(By.ID, "sbmLogin").click()
    WebDriverWait(self.driver, 30000).until(expected_conditions.presence_of_element_located((By.XPATH, "//a[contains(text(),\'Agendamentos\')]")))
    self.driver.find_element(By.XPATH, "//ul[@id=\'main-menu\']/li[24]/a").click()
    self.driver.find_element(By.LINK_TEXT, "Agendamentos").click()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarNovosPacotesNaoSendoGerados\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: notificarProcessosComFalhaPublicacaoProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
    self.driver.find_element(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[7]/a/img").click()
    self.driver.switch_to.alert.accept()
    elements = self.driver.find_elements(By.XPATH, "//td[contains(.,\'ProtocoloIntegradoAgendamentoRN :: publicarProtocoloIntegrado\')]/../td[contains(.,\'Sucesso\')]")
    assert len(elements) > 0
  
