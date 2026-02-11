<?php

namespace Hercilio\SimpleUpload;

use Illuminate\Support\ServiceProvider;

class SimpleUploadServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('simpleupload', function ($app) {
            return new SimpleUpload();
        });
    }

    public function boot()
    {
        //
    }
}