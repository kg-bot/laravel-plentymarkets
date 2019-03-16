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
        $filters[] = [ 'itemsPerPage', 250 ];

        $urlFilters = $this->parseFilters( $filters );

        return $this->request->handleWithExceptions( function () use ( $urlFilters ) {

            $response     = $this->request->client->get( "{$this->entity}{$urlFilters}" );
            $responseData = json_decode( (string) $response->getBody() );
            $fetchedItems = collect( $responseData->entries );
            $items        = collect( [] );
            $pages        = $responseData->lastPageNumber;


            foreach ( $fetchedItems->first() as $index => $item ) {


                /** @var Model $model */
                $model = new $this->model( $this->request, $item );

                $items->push( $model );


            }

            return $items;
        } );
    }

    protected function parseFilters( $filters = [] )
    {

        $urlFilters = '';

        if ( count( $filters ) > 0 ) {

            $i = 1;

            $urlFilters .= '?';

            foreach ( $filters as $filter ) {

                $urlFilters .= $filter[ 0 ] . '=' . $this->escapeFilter( $filter[ 1 ] );

                if ( count( $filters ) > $i ) {

                    $urlFilters .= '&';
                }

                $i++;
            }
        }

        return $urlFilters;
    }

    private function escapeFilter( $variable )
    {
        $escapedStrings    = [
            "$",
            '(',
            ')',
            '*',
            '[',
            ']',
            ',',
        ];
        $urlencodedStrings = [
            '+',
            ' ',
        ];
        foreach ( $escapedStrings as $escapedString ) {

            $variable = str_replace( $escapedString, '$' . $escapedString, $variable );
        }
        foreach ( $urlencodedStrings as $urlencodedString ) {

            $variable = str_replace( $urlencodedString, urlencode( $urlencodedString ), $variable );
        }

        return $variable;
    }

    public function find( $id )
    {
        return $this->request->handleWithExceptions( function () use ( $id ) {

            $response     = $this->request->client->get( "{$this->entity}/{$id}" );
            $responseData = collect( json_decode( (string) $response->getBody() ) );

            return new $this->model( $this->request, $responseData->first() );
        } );
    }

    public function create( $data )
    {
        return $this->request->handleWithExceptions( function () use ( $data ) {

            $response = $this->request->client->post( "{$this->entity}", [
                'json' => $data,
            ] );

            $responseData = collect( json_decode( (string) $response->getBody() ) );

            return new $this->model( $this->request, $responseData->first() );
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

    public function all( $filters = [] )
    {
        $page = 1;

        $items = collect();

        $response = function ( $filters, $page ) {

            $filters[] = [ 'itemsPerPage', 250 ];
            $filters[] = [ 'page', $page ];

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