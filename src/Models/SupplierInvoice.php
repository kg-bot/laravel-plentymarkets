<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 16.48
 */

namespace Rackbeat\Models;


use Rackbeat\Utils\Model;

class SupplierInvoice extends Model
{

    protected $entity     = 'supplier-invoices';
    protected $primaryKey = 'number';
}