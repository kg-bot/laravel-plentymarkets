<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 2.4.18.
 * Time: 01.02
 */

namespace KgBot\PlentyMarket;


use Illuminate\Support\ServiceProvider;

class PlentyMarketServiceProvider extends ServiceProvider
{
    /**
     * Boot.
     */
    public function boot()
    {
        $configPath = __DIR__ . '/config/plentymarket.php';

        $this->mergeConfigFrom( $configPath, 'plentymarket' );

        $configPath = __DIR__ . '/config/plentymarket.php';

        if ( function_exists( 'config_path' ) ) {

            $publishPath = config_path( 'plentymarket.php' );

        } else {

            $publishPath = base_path( 'config/plentymarket.php' );

        }

        $this->publishes( [ $configPath => $publishPath ], 'config' );
    }

    public function register()
    {
    }
}