<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 17.00
 */

namespace KgBot\PlentyMarket\Builders;

use KgBot\PlentyMarket\Utils\Model;
use KgBot\PlentyMarket\Utils\Request;


class Builder
{
    protected $entity;
    /** @var Model */
    protected $model;
    protected $resource_key;
    private   $request;

    public function __construct( Request $request )
    {
        $this->request = $request;
    }

    /**
     * @param array $filters
     *
     * @return \Illuminate\Support\Collection|Model[]
     */
    public function get( $filters = [] )
    {
        $urlFilters = $this->parseFilters( $filters );

        return $this->request->handleWithExceptions( function () use ( $urlFilters ) {

            $response     = $this->request->client->get( "{$this->entity}{$urlFilters}" );
            $responseData = json_decode( (string) $response->getBody() );
            $fetchedItems = collect( $responseData->entries );
            $items        = collect( [] );


            foreach ( $fetchedItems as $index => $item ) {


                /** @var Model $model */
                $model = new $this->model( $this->request, $item );

                $items->push( $model );


            }

            return $items;
        } );
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function parseFilters( $filters = [] ):string
    {
        if(!isset($filters['itemsPerPage'])) {

            $filters['itemsPerPage'] = 250;
        }

        return '?'.http_build_query($filters);

    }

    /**
     * @param $id
     * @return Model
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketClientException
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketRequestException
     */
    public function find( $id, $filters = [] ):Model
    {
        $urlFilters = $this->parseFilters($filters);

        return $this->request->handleWithExceptions( function () use ( $id, $urlFilters ) {

            $response     = $this->request->client->get( "{$this->entity}/{$id}{$urlFilters}" );
            $responseData = collect( json_decode( (string) $response->getBody() ) );

            return new $this->model( $this->request, $responseData );
        } );
    }

    /**
     * @param $data
     * @return Model
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketClientException
     * @throws \KgBot\PlentyMarket\Exceptions\PlentyMarketRequestException
     */
    public function create( $data ):Model
    {
        return $this->request->handleWithExceptions( function () use ( $data ) {

            $response = $this->request->client->post( "{$this->entity}", $data);

            $responseData = collect( json_decode( (string) $response->getBody() ) );

            return new $this->model( $this->request, $responseData );
        } );
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity( $new_entity )
    {
        $this->entity = $new_entity;

        return $this->entity;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function all( $filters = [] )
    {
        $page = 1;

        $items = collect();

        $response = function ( $filters, $page ) {

            $filters['page'] = $page;

            $urlFilters = $this->parseFilters( $filters );

            return $this->request->handleWithExceptions( function () use ( $urlFilters ) {

                $response     = $this->request->client->get( "{$this->entity}{$urlFilters}" );
                $responseData = json_decode( (string) $response->getBody() );

                $fetchedItems = collect( ( $this->resource_key !== null ) ?
                    $responseData->{$this->resource_key} : $responseData );
                $items        = collect( [] );
                $pages        = $responseData->lastPageNumber ?? 1;


                foreach ( $fetchedItems as $index => $item ) {


                    /** @var Model $model */
                    $model = new $this->model( $this->request, $item );

                    $items->push( $model );


                }

                return (object) [

                    'items' => $items,
                    'pages' => $pages,
                ];
            } );
        };

        do {

            $resp = $response( $filters, $page );

            $items = $items->merge( $resp->items );
            $page++;
            sleep( 2 );

        } while ( $page <= $resp->pages );


        return $items;

    }
}