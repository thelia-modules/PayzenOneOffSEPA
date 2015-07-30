# Payzen One Off SEPA

Customers will be able to select SEPA (SDD) payment on your website and have it preselected on Payzen payment page.

**Important** : this version is meant to be used with Payzen module, version 1.1 or higher. Not compatible with Payzen version 1.0.
Please update you Payzen module to last version.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenOneOffSEPA.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/payzen-one-off-sepa-module:~1.*
```

## Usage

You first have to add Payzen module to your website and configure it.

You also need to subscribe SEPA option on your Payzen back-office.

Then, just add Payzen One Off SEPA module to you website and activate it. No configuration is needed.

## Order Status

This module adds a new status to orders : ```Waiting for payment```

SDD orders will have this status after the customer fills his bank information on Payzen payment page.
As usual, set the status to ```Paid``` when you see you have been paid on your Payzen back-office.

**Warning** : don't set this new status by yourself! Let the module handle it for you to avoid problems ;-)
