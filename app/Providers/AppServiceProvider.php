<?php

namespace App\Providers;

use App\Observers\ProductObserver;
use App\Product;
use Illuminate\Support\ServiceProvider;
use App\Section;
use App\Cart;
use App\Http\Controllers\Front\MathController;
use App\Http\Controllers\Front\DiffMathController;
use App\Services\Contracts\MathHelper;
use App\Services\SumMathHelper;
use App\Services\DiffMathHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(MathController::class)
            ->needs(MathHelper::class)
            ->give(SumMathHelper::class);

        $this->app->when(DiffMathController::class)
            ->needs(MathHelper::class)
            ->give(DiffMathHelper::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*DB::listen(function ($query) {
            echo '<pre>';
            var_dump(
                $query->sql,
                $query->bindings,
                $query->time
            );
            echo '</pre>';
        });*/

        Product::observe(ProductObserver::class);

        view()->composer(['layouts.front.app'], function ($view) {
            $view->with('sections', Section::all());
            $view->with('cartCounts', $this->getCartCounts());
        });
    }

    public function getCartCounts()
    {
        $cart = new Cart();

        return $cart->count();
    }
}
