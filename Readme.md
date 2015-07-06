# Payzen One Off SEPA

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
