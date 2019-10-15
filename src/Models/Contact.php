<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.30
 */

namespace KgBot\PlentyMarket\Models;


use KgBot\PlentyMarket\Utils\Model;

class Contact extends Model
{
    protected $entity       = 'accounts/contacts';
    protected $primaryKey   = 'id';
    protected $resource_key = 'entries';
}
