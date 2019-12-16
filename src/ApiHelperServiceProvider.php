<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/13/19
 * Time: 3:36 PM
 */

namespace Tech\APIHelper;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Tech\APIHelper\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Tech\APIHelper\Database\RepositoryInterface;
use Tech\APIHelper\Database\Eloquent\BaseRepository;

/**
 * Class APIHelperServiceProvider
 * @package Tech\APIHelper\Providers
 */
class ApiHelperServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Service Provider
        $this->registerServiceProvider();

        // Register Repository;
        $this->registerRepository();
    }

    /**
     * Bootstrap application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the api-helper config file.
        if ($this->app->runningInConsole()) :
            $this->publishes([
                __DIR__ . "/../config/api.php" => config_path("api.php"),
                __DIR__ . "/../config/jwt.php" => config_path("jwt.php"),
                __DIR__ . "/../config/cors.php" => config_path("cors.php"),
            ], "api-helper");
        endif;

        // Register Handler
        $this->registerHandler();

        // Register Facade.
        $this->registerFacades();
    }

    /**
     * Register Exception Handler
     *
     * @return void
     */
    protected function registerHandler()
    {
        // Exception Handler
        $this->app->bind(ExceptionHandler::class, Handler::class);
    }

    /**
     * Register Service Provider
     *
     * @return void
     */
    protected function registerServiceProvider()
    {
        // CORS
        $this->app->register("Barryvdh\Cors\ServiceProvider");

        // JWTAuth
        $this->app->register("Tymon\JWTAuth\Providers\LaravelServiceProvider");
    }

    /**
     * Register Repository
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton(RepositoryInterface::class, BaseRepository::class);
    }

    /**
     * Register Facades
     *
     * @return void
     */
    protected function registerFacades()
    {
        AliasLoader::getInstance()->alias("JWTAuth", "Tymon\JWTAuth\Facades\JWTAuth");
        AliasLoader::getInstance()->alias("JWTFactory", "Tymon\JWTAuth\Facades\JWTFactory");
    }
}