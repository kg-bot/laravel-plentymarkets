<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.30
 */

namespace KgBot\PlentyMarket\Models;


use KgBot\PlentyMarket\Utils\Model;

class Order extends Model
{
    protected $entity       = 'orders';
    protected $primaryKey   = 'id';
    protected $resource_key = 'entries';

    /**
     * @return mixed
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketClientException
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketRequestException
     */
    public function payments()
    {

        return $this->request->handleWithExceptions( function () {

            $response     = $this->request->client->get( "payments/orders/{$this->{$this->primaryKey}}" );
            $responseData = json_decode( (string) $response->getBody() );

            $fetchedItems = $responseData;
            $items        = collect( [] );

            foreach ( $fetchedItems as $index => $item ) {


                /** @var Model $model */
                $model = new Payment( $this->request, $item );

                $items->push( $model );
            }

            return $items;

        } );
    }
}
