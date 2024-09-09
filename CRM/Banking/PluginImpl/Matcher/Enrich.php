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
    $pos = 0;
    $bicPos= 0; 		
    $pos = strpos($line,'IBAN Auftraggeber:');
    $bicPos=strpos($line,'BIC');
    if($pos && $bicPos){
      return substr($line,$pos+19,($bicPos-1)-($pos+19));
    } else {
      // the assumption is that IBAN is just between the text and BIC
      // like
      //   IBAN AZ96AZEJ00000000001234567890 BIC:
      //   if no IBAN is found this function returns false
    $pos = 0;
    $bicPos= 0; 		
      $pos = strpos($line,'IBAN ');
      $bicPos=strpos($line,'BIC');
      if($pos && $bicPos){
        return substr($line,$pos+5,($bicPos-1)-($pos+5));
      } else {
        return false;
      }
    }
  }

/**
   * @param $line
   *
   * @return bool|false|string
   */
  private function extract_name($line){
    // the assumption is that name is just between the text and Verwendungszweck
    //   if no name is found try V2
    $pos = 0;
    $endPos= 0; 		
    $pos = strpos($line,'uftraggeber:'); //mit "A" vorne gibts warum auch immer Problem (läuft zwar durch aber geht nicht!)
    $endPos=strpos($line,'Verwendungszweck:');
    if($pos && $endPos){
      return substr($line,$pos+13,($endPos-1)-($pos+13));
    } else {
      // the assumption is that name is just between the text and Zahlungsreferenz
      //   if no name is found try V3
      $endPos=strpos($line,'Zahlungsreferenz:');
      if($pos && $endPos){
        return substr($line,$pos+13,($endPos-1)-($pos+13));
      } else {
        // the assumption is that name is just between the text and IBAN
        //   if no name is found this function returns false
        $endPos=strpos($line,'IBAN Auftraggeber');
        if($pos && $endPos){
          return substr($line,$pos+13,($endPos-1)-($pos+13));
        } else {
          return false;
        }
      }
    } 
  }

/**
   * @param $line
   *
   * @return bool|false|string
   */
  private function extract_purpose($line){
    // the assumption is that purpose is just between the text and IBAN
    // like
    //   Verwendungszweck: Test Spende IBAN Auftraggeber:
    //   if no purpose is found this function returns false
    $pos = 0;
    $endPos= 0; 		
    $pos = strpos($line,'Verwendungszweck:');
    $endPos=strpos($line,'IBAN Auftraggeber');
    if($pos && $endPos){
      return substr($line,$pos+18,($bicPos-1)-($pos+18));
    } else {
      // the assumption is that IBAN is just between the text and IBAN
      //   if no IBAN is found this function returns false
      $pos = 0;
      $endPos= 0; 		
      $pos = strpos($line,'Zahlungsreferenz:');
      $endPos=strpos($line,'IBAN Auftraggeber');
      if($pos && $endPos){
        return substr($line,$pos+18,($endPos-1)-($pos+18));
      } else {
        return false;
      }
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
    // if a purpose is found add it to the
    // transaction data
    if($purpose=$this->extract_purpose($line)){
      $data['purpose']=$purpose;
    }
    $name=$this->extract_name($line);
 
    if($iban=$this->extractIBAN($line)){
      switch ($iban) {
	case 'DE57370100500541727506': 	
        $iban = ''; 
        $name = 'Konto Tine ->Name manuell';
        break;
        case 'AT833947500000703652':
        case 'AT883840300002085546':    //2 Konten
        $iban = '';
	$name = 'Konto Manu ->Name manuell';
        break;
	case 'AT613947900000146472':
	case 'AT652070602101008841': 	//2 Konten
        $iban = ''; 
        $name = 'Konto Belinda ->Name manuell';
        break;
	case 'DE73750903000000284475':
	case 'DE78586500301009060490': //2 Konten	
        $iban = ''; 
        $name = 'Konto Miriam ->Name manuell';
        break;
	case 'DE46370605900004063929': 	
        $iban = ''; 
        $name = 'Konto Bernhard ->Name manuell';
        break;
	case 'NL??': 	//IBAN?
        $iban = ''; 
        $name = 'Konto Wilco ->Name manuell';
        break;
	case 'AT702011182365625200': 	
        $iban = ''; 
        $name = 'Konto Maria ->Name manuell';
        break;
	case 'AT401919000055172993': 
        $iban = '';  
        $name = 'Konto Clara ->Name manuell';
        break;
	case 'AT452070604500848371': 	
        $iban = ''; 
        $name = 'Konto Bethany ->Name manuell';
        break;
	case 'AT663407500004421905': 	
        $iban = ''; 
        $name = 'Konto Ewald ->Name manuell';
        break;
      }      
      $data['_party_IBAN']=$iban;
      if($iban=='' or $data['name']==''){ //Name nur überschreiben wenn nicht schon vorbereitet (z.B. Sozialbank)
        $data['name']=$name;
      }
    }
    // if a name is found add it to the
    // transaction data
    // write everything back
    $btx->setDataParsed($data);
  }

}