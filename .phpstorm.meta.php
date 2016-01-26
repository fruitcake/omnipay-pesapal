<?php

namespace PHPSTORM_META {

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpUnusedLocalVariableInspection */
    $STATIC_METHOD_TYPES = [
      \Omnipay\Omnipay::create('') => [
        'Pesapal' instanceof \Omnipay\Pesapal\Gateway,
      ],
      \Omnipay\Common\GatewayFactory::create('') => [
        'Pesapal' instanceof \Omnipay\Pesapal\Gateway,
      ],
    ];
}
