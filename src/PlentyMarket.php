<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 15.12
 */

namespace KgBot\PlentyMarket;

use KgBot\PlentyMarket\Builders\OrderBuilder;
use KgBot\PlentyMarket\Builders\PaymentBuilder;
use KgBot\PlentyMarket\Utils\Request;

class PlentyMarket
{
    /**
     * @var $request Request
     */
    protected $request;

    /**
     * PlentyMarket constructor.
     *
     * @param null  $token   API token
     * @param array $options Custom Guzzle options
     * @param array $headers Custom Guzzle headers
     */
    public function __construct( $username = null, $password = null, $options = [], $headers = [] )
    {
        $this->initRequest( $username, $password, $options, $headers );
    }

    private function initRequest( $username, $password, $options = [], $headers = [] )
    {
        $this->request = new Request( $username, $password, $options, $headers );
    }

    /**
     * @return \KgBot\PlentyMarket\Builders\OrderBuilder
     */
    public function orders()
    {
        return new OrderBuilder( $this->request );
    }

    /**
     * @return \KgBot\PlentyMarket\Builders\PaymentBuilder
     */
    public function payments()
    {

        return new PaymentBuilder( $this->request );
    }
}