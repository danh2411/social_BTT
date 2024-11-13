<?php

use Dotenv\Dotenv;
use Illuminate\Support\Env;

Dotenv::create(
    Env::getRepository(),
    realpath(base_path('./')),
    '.env'
)->safeLoad();

app()->useEnvironmentPath(base_path('env'));
app()->loadEnvironmentFrom('.env_' . env('APP_ENV', 'local'));
