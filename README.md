# Omnipay: Pesapal

**Skeleton gateway for the Omnipay PHP payment processing library**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fruitcakestudio/omnipay-pesapal.svg?style=flat-square)](https://packagist.org/packages/fruitcakestudio/omnipay-pesapal)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/fruitcakestudio/omnipay-pesapal/master.svg?style=flat-square)](https://travis-ci.org/fruitcakestudio/omnipay-pesapal)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/fruitcakestudio/omnipay-pesapal.svg?style=flat-square)](https://scrutinizer-ci.com/g/fruitcakestudio/omnipay-pesapal/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/fruitcakestudio/omnipay-pesapal.svg?style=flat-square)](https://scrutinizer-ci.com/g/fruitcakestudio/omnipay-pesapal)
[![Total Downloads](https://img.shields.io/packagist/dt/fruitcakestudio/omnipay-pesapal.svg?style=flat-square)](https://packagist.org/packages/fruitcakestudio/omnipay-pesapal)


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements pesapal support for Omnipay.

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require fruitcakestudio/omnipay-pesapal
```

## Usage

The following gateways are provided by this package:

 * Pesapal

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

## Example

```php
 $gateway = \Omnipay\Omnipay::create('Pesapal');
    $gateway->initialize(array(
        'key' => 'your-consumer-key',
        'secret' => 'your-consumer-secret',
        'testMode' => false,
    ));

    // Start the purchase
    if(!isset($_GET['pesapal_merchant_reference'])){
        // Send back to this URL
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        // Make a purchase request
        $response = $gateway->purchase(array(
            'amount' => "6.84",
            'description' => "Testorder #1234",
            'currency' => 'USD',
            'card' => array(
                'email' => 'barry@fruitcakestudio.nl',
                'firstName' => 'Barry',
                'lastName' => 'vd. Heuvel',
                'phone' => '+1234567890',
            ),
            'returnUrl' => $url,
        ))->send();

        $transactionId = $response->getTransactionId();

        if ($response->isRedirect()) {
            // redirect to offsite payment gateway
            $response->redirect();
        } else {
            // payment failed: display message to customer
            return "Error " .$response->getCode() . ': ' . $response->getMessage();
        }
    } else {
        // Check the payment status
        $response = $gateway->completePurchase()->send();
        if($response->isSuccessful()){
            $reference = $response->getTransactionReference();  // TODO; Check the reference/id with your database
            return "Transaction '" . $response->getTransactionId() . "' succeeded!";
        } else {
            return "Status: " .$response->getCode() . ': ' . $response->getMessage();
        }
    }
```

The transactionReference is `pesapal_transaction_tracking_id`, which is set by Pesapal.
the transactionId is your own id (`pesapal_merchant_reference`), which will be generated if not provided.

**Note, transactionReference, transactionId and paymentMethod are only available in the CompletePurchaseResponse
when both `pesapal_transaction_tracking_id` and `pesapal_merchant_reference` are set in the query,
or the transactionId and transactionReference are set as parameters. **

See the documentation on [http://developer.pesapal.com/how-to-integrate/step-by-step](http://developer.pesapal.com/how-to-integrate/step-by-step)

### Sandbox / Demo

When you set `testMode` to `true`, the [Demo sandbox](http://demo.pesapal.com/) will be used.

You need a different consumer key/secret. Follow these steps to use the testMode:

 - Create a *business* account on [http://demo.pesapal.com/](http://demo.pesapal.com/)
 - Login to your demo account, the key/secret are on the dashboard.
 - Start a transaction and [send dummy money](http://demo.pesapal.com/MobileMoneyTest)
 - Use the same phonenumber (eg. `700123456`) and amount. Copy the confirmation code after submitting.


## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/fruitcakestudio/omnipay-pesapal/issues),
or better yet, fork the library and submit a pull request.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@fruitcake.nl instead of using the issue tracker.

## Credits

- [Fruitcake](https://github.com/fruitcakestudio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
