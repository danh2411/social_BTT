# Laravel-Elasticsearch By Quocdaijr

Package is based on [elasticsearch-php](https://github.com/elastic/elasticsearch-php), making it easy to connect, query
and easily extend

## Installation and Configuration

#### Requirement

PHP >= 8.0, Elasticsearch >= 7.13, Laravel >= 8.0

Install via github:

- Edit file `composer.json`

```
"repositories": [
        ...,
        {
            "type": "vcs",
            "url": "git@github.com:quocdaijr/laravel-elasticsearch.git"
        }
    ]
```

- Run `composer require quocdaijr/laravel-elasticsearch *@dev` to install package.

The package's service provider will automatically register its service provider.

Publish the config file:

```
php artisan vendor:publish --provider="Quocdaijr\Elasticsearch\Providers\ElasticsearchServiceProvider"
```

Alternative configuration method via .env file:

```
ALLOWED_ELASTICSEARCH = true
ELASTICSEARCH_CONNECTION=default
ELASTICSEARCH_HOST=127.0.0.1
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_USERNAME=
ELASTICSEARCH_PASSWORD=
ELASTICSEARCH_PREFIX=
```

## Usage

### Create Index

It is similar to an Eloquent, where index, setting, mapping are declared.

Example file:

```
<?php

namespace Quocdaijr\Elasticsearch\Examples;

use Quocdaijr\Elasticsearch\Indexes\IndexAbstract;

class ExampleIndex extends IndexAbstract
{
    public function __construct()
    {
        return parent::__construct('id');
    }

    public function index(): string
    {
        return 'example_index';
    }

    public function mapping(): array
    {
        return [
            'id' => [
                'type' => 'long',
            ],
            'name' => [
                'type' => 'text',
                'fielddata' => true,
                "fields" => [
                    'keyword' => [
                        'type' => 'keyword',
                        "ignore_above" => 256
                    ]
                ],
                'analyzer' => 'vietnamese_standard'
            ],
            'status' => [
                'type' => 'integer'
            ],
            'created_at' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ],
            'thumbnail' => [
                'type' => 'keyword'
            ],
            'categories' => [
                'type' => 'nested',
                'properties' => [
                    'id' => [
                        'type' => 'integer'
                    ],
                    'name' => [
                        'type' => 'keyword'
                    ],
                ]
            ],
            ...
        ];
    }

    public function settings(): array
    {
        return [
            'number_of_shards' => 5,
            'number_of_replicas' => 3,
            ...
        ];
    }
}
```

### Create Repository

It is similar to a Repository, where methods get or set data with index are declared.

Example file:

```
<?php

namespace Quocdaijr\Elasticsearch\Examples;

use Quocdaijr\Elasticsearch\Repositories\ElasticsearchRepositoryInterface;

interface ExampleRepositoryInterface extends ElasticsearchRepositoryInterface
{
    // declare other methods
}
```

```
<?php

namespace Quocdaijr\Elasticsearch\Examples;

use Quocdaijr\Elasticsearch\Repositories\ElasticsearchRepositoryAbstract;

class ExampleRepository extends ElasticsearchRepositoryAbstract implements ExampleRepositoryInterface
{
    // Code other methods
}
```

```
public function test()
{
    $exampleRepository = new ExampleRepository(new ExampleIndex());

    $exampleRepository->create([
        'id' => 1,
        'name' => 'This is example',
        'created_at' => '2021-11-01 13:50:55'
    ]);
    $test = $exampleRepository->find(1);
    print_r($test);
}

test();
```

You can register repository to service provider as below:

```
<?php

use Quocdaijr\Elasticsearch\Examples\ExampleRepositoryInterface;
use Quocdaijr\Elasticsearch\Examples\ExampleRepository;
use Quocdaijr\Elasticsearch\Examples\ExampleIndex;
...
public function register()
{
    ...
    $this->app->bind(ExampleRepositoryInterface::class, function () {
        return new ExampleRepository(new ExampleIndex());
    });
}
...
```

And use something like

```
public function test(ExampleRepository $exampleRepository)
{
    $exampleRepository->create([
        'id' => 1,
        'name' => 'This is example',
        'created_at' => '2021-11-01 13:50:55'
    ]);
    $test = $exampleRepository->find(1);
    print_r($test);
}

public function test2(ExampleRepository $exampleRepository)
{
    $page = 1;
    $perPage = 10;
    $sortAttribute = 'created_at';
    $sortValue = 'desc';

    $baseQuery = [
        'body' => [
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
            'sort' => [$sortAttribute => $sortValue],
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'created_at' => [
                                    'lte' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'term' => [
                                'status' => 1
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
    $test2 = $exampleRepository->search($baseQuery);

    print_r($test2);
}

test();
test2();
```

You can read Repository Pattern In Laravel to understand better.

## Contributes

I wrote this package for personal use, and it may not be suitable for everyone.

If possible, you can help me improve this code. Thank you!
