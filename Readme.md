# Payzen One Off SEPA

Important : if your Payzen module is in version 1.1, use version 1.1 of this module. Version 1.0 should be used with version 1.0 of Payzen (but you should use both versions 1.1!).
Customers will be able to select SEPA (SDD) payment on your website and have it preselected on Payzen payment page.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenOneOffSEPA.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require your-vendor/payzen-one-off-sepa-module:~1.0
```

## Usage

You first have to add Payzen module on your website and configure it.
You also need to subscribe to SEPA option on your Payzen back-office.
Then, just add Payzen One Off SEPA module to you website and activate it. No configuration is needed.

## Order Status

This module adds a new status to orders : ```Waiting for payment```
SDD orders will have this status after the customer fills his bank information.
As usual, put the status to ```Paid``` when you have been paid on your PayZen back-office.

Warning : don't use this new status by yourself !
