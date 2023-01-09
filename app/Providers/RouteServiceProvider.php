<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */

    /**
     * The Version of application api.
     *
     * @var string
     */

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        $this->configureRateLimiting();

        $this->mapModulesRoutes();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapSPARoutes();

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/api.php'));
    }

    /**
     * All non matchable resources we will show standard Vue page,.
     *
     * and redirect it through VueRoutes on client side
     *
     * @return void
     */
    protected function mapSPARoutes()
    {
        Route::middleware('web')
            ->group(function () {
                // Route::view('/index.'.'{any}', 'index')
                    // ->where('any', '.*');
                Route::view('/', 'index');
                    // ->where('any', '.*');
                Route::view(env('ADMIN_PANEL_PREFIX', ''), 'spa')
                    ->where('any', '.*');
                Route::view(env('ADMIN_PANEL_PREFIX', '').'/{any}', 'spa')
                    ->where('any', '.*');
            });
    }

    /**
     * Define the "modules" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapModulesRoutes()
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'routes_api.php';

            if (file_exists($routesPath)) {
                Route::prefix(env('SITE_FULL_API_PREFIX', 'api/v1'))
                    ->middleware(['api', 'auth:sanctum'])
                    ->namespace("\\App\\Modules\\$module\Controllers")
                    ->group($routesPath);
            }
        }
    }

    /**
     * @param string $modules_folder
     * @return array
     */
    private function getModulesList(string $modules_folder): array
    {
        return
            array_values(
                array_filter(
                    scandir($modules_folder),
                    function ($item) use ($modules_folder) {
                        return is_dir($modules_folder.DIRECTORY_SEPARATOR.$item) && ! in_array($item, ['.', '..']);
                    }
                )
            );
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
