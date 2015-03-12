<?php namespace Serverfireteam\Panel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Translation;
use Serverfireteam\Panel\libs;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation;
use Serverfireteam\Panel\Commands;

class PanelServiceProvider extends ServiceProvider
{
    protected $defer = false;
        
    public function register()
    {
        // register  zofe\rapyd
        $this->app->register('Zofe\Rapyd\RapydServiceProvider');
        // register html service provider 
        $this->app->register('Illuminate\Html\HtmlServiceProvider');

       // 'Maatwebsite\Excel\ExcelServiceProvider'
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');        
        
        
        /*
         * Create aliases for the dependency.
         */
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', 'Illuminate\Html\FormFacade');
        $loader->alias('Html', 'Illuminate\Html\HtmlFacade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');

        include __DIR__."/Commands/ServerfireteamCommand.php";
        $this->app['panel::install'] = $this->app->share(function()
        {
            return new panelCommand();
        });
        
        include __DIR__."/Commands/CrudCommand.php";
        $this->app['panel::crud'] = $this->app->share(function()
        {
            return new CrudCommand();
        });
        
        
        include __DIR__."/Commands/CreateModelCommand.php";
        $this->app['panel::createmodel'] = $this->app->share(function()
        {
           $fileSystem = new Filesystem(); 
           
           return new CreateModelCommand($fileSystem);
        });
        
        include __DIR__."/Commands/CreateControllerCommand.php";
        $this->app['panel::createcontroller'] = $this->app->share(function()
        {
           $fileSystem = new Filesystem(); 
           
           return new CreateControllerPanelCommand($fileSystem);
        });

        $this->commands('panel::createmodel');
        
        $this->commands('panel::createcontroller');
         
        $this->commands('panel::install');

        $this->commands('panel::crud');
        $this->publishes([
            __DIR__ . '/../../../public' => public_path('packages/serverfireteam/panel')
        ]);
        $this->publishes([
            __DIR__.'/config/panel.php' => config_path('panel.php'),
        ]);
    }
        
    public function boot()
    {        

     
        $this->loadViewsFrom(__DIR__.'/../../views', 'panelViews');
        $this->publishes([
            __DIR__.'/../../views' => base_path('resources/views/vendor/panelViews'),
        ]);
        
        include __DIR__."/../../routes.php";

        $this->loadTranslationsFrom(base_path() . '/vendor/serverfireteam/panel/src/lang', 'panel');
        $this->loadTranslationsFrom(base_path() . '/vendor/serverfireteam/rapyd-laravel/lang', 'rapyd');

        AliasLoader::getInstance()->alias('Serverfireteam', 'Serverfireteam\Panel\Serverfireteam');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
    
    
}
