<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.32
 */

namespace KgBot\PlentyMarket\Builders;


use KgBot\PlentyMarket\Models\Contact;

class ContactBuilder extends Builder
{
    protected $entity       = 'accounts/contacts';
    protected $model        = Contact::class;
    protected $resource_key = 'entries';
}