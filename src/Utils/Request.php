<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 16.53
 */

namespace KgBot\PlentyMarket\Utils;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use KgBot\PlentyMarket\Exceptions\PlentyMarketClientException;
use KgBot\PlentyMarket\Exceptions\PlentyMarketRequestException;

class Request
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    protected $base_uri;

    protected $refresh_token;

    protected $access_token;

    /**
     * Request constructor.
     *
     * @param null  $token
     * @param array $options
     * @param array $headers
     */
    public function __construct( $username = null, $password = null, $options = [], $headers = [] )
    {
        $username       = $username ?? config( 'plentymarket.username' );
        $password       = $password ?? config( 'plentymarket.password' );
        $base_uri       = trim( url( $options[ 'base_uri' ] ?? config( 'plentymarket.base_uri' ) ), '/' );
        $this->base_uri = $base_uri . '/';

        $this->getToken( $username, $password );

        $headers = array_merge( $headers, [

            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->access_token,
        ] );

        $options      = array_merge( $options, [

            'base_uri' => $this->base_uri . 'rest/',
            'headers'  => $headers,
        ] );
        $this->client = new Client( $options );
    }

    /**
     * @param $username
     * @param $password
     */
    protected function getToken( $username, $password )
    {
        $url = $this->base_uri . "rest/login?username={$username}&password={$password}";

        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response = json_decode( curl_exec( $ch ) );

        $this->refresh_token = $response->refresh_token;

        $this->access_token = $response->access_token;
    }

    /**
     * @param $callback
     *
     * @return mixed
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketClientException
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketRequestException
     */
    public function handleWithExceptions( $callback )
    {
        try {
            return $callback();

        } catch ( ClientException $exception ) {

            $message = $exception->getMessage();
            $code    = $exception->getCode();

            if ( $exception->hasResponse() ) {

                $message = (string) $exception->getResponse()->getBody();
                $code    = $exception->getResponse()->getStatusCode();
            }

            throw new PlentyMarketRequestException( $message, $code );

        } catch ( ServerException $exception ) {

            $message = $exception->getMessage();
            $code    = $exception->getCode();

            if ( $exception->hasResponse() ) {

                $message = (string) $exception->getResponse()->getBody();
                $code    = $exception->getResponse()->getStatusCode();
            }

            throw new PlentyMarketRequestException( $message, $code );

        } catch ( \Exception $exception ) {

            $message = $exception->getMessage();
            $code    = $exception->getCode();

            throw new PlentyMarketClientException( $message, $code );
        }
    }
}