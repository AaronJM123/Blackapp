<?php

namespace App\Providers;

use ReflectionException;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use App\JsonApi\Mixins\JsonApiRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\JsonApi\Mixins\JsonApiQueryBuilder;
use App\JsonApi\Mixins\JsonApiTestResponse;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            \App\Exceptions\Handler::class,
            \App\JsonApi\Exceptions\Handler::class
        );
    }

    /**
     * @throws ReflectionException
     */
    public function boot()
    {
        Builder::mixin(new JsonApiQueryBuilder());

        TestResponse::mixin(new JsonApiTestResponse());

        Request::mixin(new JsonApiRequest());
    }
}
