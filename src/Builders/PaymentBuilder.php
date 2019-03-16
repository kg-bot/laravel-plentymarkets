<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.32
 */

namespace KgBot\PlentyMarket\Builders;


use KgBot\PlentyMarket\Models\Payment;

class PaymentBuilder extends Builder
{
    protected $entity        = 'payments';
    protected $model         = Payment::class;
    protected $resource_name = null;
}