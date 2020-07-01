<?php

class CRM_Banking_PluginImpl_Matcher_Enrich  extends CRM_Banking_PluginModel_Analyser {

  function __construct($config_name) {
    parent::__construct($config_name);
    // read config, set defaults
    $config = $this->_plugin_config;
  }

  /**
   * @param $line
   *
   * @return bool|false|string
   */
  private function extractIBAN($line){
    // the assumption is that IBAN is just between the text and BIC
    // like
    //   IBAN Auftraggeber: AZ96AZEJ00000000001234567890 BIC:
    //   if no IBAN is found this function returns false
    $pos = strpos($line,'IBAN Auftraggeber:');
    $bicPos=strpos($line,'BIC');
    if($pos && $bicPos){
      return substr($line,$pos+19,($bicPos-1)-($pos+19));
    } else {
      return false;
    }
  }

  /**
   * @param \CRM_Banking_BAO_BankTransaction $btx
   * @param \CRM_Banking_Matcher_Context $context
   */
  public function analyse(CRM_Banking_BAO_BankTransaction $btx, CRM_Banking_Matcher_Context $context) {
    // read the extractred data from the database
    $data = $btx->getDataParsed();
    // the IBAN should be in the function line
    $line=$data['line'];
    // if a iban is found add it to the
    // transaction data
    if($iban=$this->extractIBAN($line)){
      $data['_party_IBAN']=$iban;
    }
    // write everything back
    $btx->setDataParsed($data);
  }

}
