# Banking-enrich


## Requirements

* Works in combination with CiviBanking 0.7. See https://github.com/Project60/org.project60.banking. Use the most recent alpha (and if there is a beta even better)
* CiviCRM (*5.26.2*)

## Installation (CLI, Zip)

https://github.com/kainuk/banking-enrich/archive/default.zip

## Usage

A good introduction for CiviBanking can be found at https://github.com/Project60/org.project60.banking/blob/master/doc/civibanking-getting-started.md

## Known Issues

The enricher is made for one specific use case. Adapt as you like.

## Code of interest
[CRM_Banking_PluginImpl_Matcher_Enrich](https://github.com/kainuk/banking-enrich/blob/default/CRM/Banking/PluginImpl/Matcher/Enrich.php): splits a ISBN number from a line.
[CRM_BankingEnrich_Upgrader](https://github.com/kainuk/banking-enrich/blob/default/CRM/BankingEnrich/Upgrader.php): enables the plugin.
