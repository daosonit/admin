<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mytour\Classes\Template;
use Blade;

class TemplateServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bladeExtend();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('template', function () {
            return new Template();
        });

    }

    private function bladeExtend()
    {
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('text');
            return preg_replace($pattern, '$1<?php echo Grid::text($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('textarea');
            return preg_replace($pattern, '$1<?php echo Grid::textarea($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('select');
            return preg_replace($pattern, '$1<?php echo Grid::select($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('files');
            return preg_replace($pattern, '$1<?php echo Grid::files($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('checkbox');
            return preg_replace($pattern, '$1<?php echo Grid::checkbox($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('submit');
            return preg_replace($pattern, '$1<?php echo Grid::submit($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('error');
            return preg_replace($pattern, '$1<?php echo Grid::error($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('status');
            return preg_replace($pattern, '$1<?php echo Grid::status($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('delete');
            return preg_replace($pattern, '$1<?php echo Grid::delete($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('update');
            return preg_replace($pattern, '$1<?php echo Grid::update($2); ?>', $view);
        });
        Blade::extend(function ($view, $compiler) {
            $pattern = $compiler->createMatcher('active');
            return preg_replace($pattern, '$1<?php echo Grid::active($2); ?>', $view);
        });
    }
}
