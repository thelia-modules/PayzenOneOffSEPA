# Payzen One Off SEPA 1.0

Customers will be able to select SEPA (SDD) payment on your website and have it preselected on Payzen payment page.

**Important** : if your Payzen module version is 1.0, you have to use this version of Payzen One Off SEPA. But you should update your Payzen module and use 1.1 version of Payzen One Off SEPA.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenOneOffSEPA.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/payzen-one-off-sepa-module:~1.0
```

## Usage

You first have to add Payzen module on your website and configure it.

You also need to subscribe to SEPA option on your Payzen back-office.

Then, just add Payzen One Off SEPA module to you website and activate it. No configuration is needed.

## Order Status

This module adds a new status to orders : ```Waiting for payment```

SDD orders will have this status after the customer fills his bank information on Payzen payment page. 

As usual, set the status to ```Paid``` when you see you have been paid on your Payzen back-office.

**Warning** : don't set this new status by yourself! Let the module handle it for you to avoid problems ;-)
