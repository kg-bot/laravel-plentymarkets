<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.32
 */

namespace KgBot\PlentyMarket\Builders;


use KgBot\PlentyMarket\Models\Item;

class ItemBuilder extends Builder
{
    protected $entity       = 'items';
    protected $model        = Item::class;
    protected $resource_key = 'entries';
}