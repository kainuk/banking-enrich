<?php
use CRM_BankingEnrich_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_BankingEnrich_Upgrader extends CRM_BankingEnrich_Upgrader_Base {

  /**
   * Make sure our module's in the list
   */
  public function enable() {
    // make sure our new plugins are registered
    // because the plugin name starts with analyse it will show up in the analyser and matcher list
    $this->registerPlugin('analyse_custom_enrich', 'Analyses and enriches import', 'CRM_Banking_PluginImpl_Matcher_Enrich');
  }

  /**
   * Helper functions for plugin registration
   */
  protected function registerPlugin($name, $label, $value) {
    // first: see if it's there already
    $plugins = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => 'civicrm_banking.plugin_types',
      'name'            => $name
    ]);

    if ($plugins['count'] == 1) {
      // already there, nothing to do
    } elseif ($plugins['count'] == 0) {
      // not there: create entry!
      $plugin = civicrm_api3('OptionValue', 'create', [
        'option_group_id' => 'civicrm_banking.plugin_types',
        'name'            => $name,
        'label'           => $label,
        'value'           => $value
      ]);
    } else {
      // there's multiple ones... that can't be good:
      throw new Exception("Name '{$name}' used multiple times in option group 'civicrm_banking.plugin_types'!");
    }
  }

}
