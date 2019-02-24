<?php

namespace bmfsan\AhiRouterTest;

use PHPUnit\Framework\TestCase;
use bmfsan\AhiRouter\Router;

class AhiRouterTest extends TestCase
{
    /**
     * Route definition
     * @var array
     *
     * GET    /                      IndexController@getIndex
     * GET    /posts                 PostController@getPosts
     * GET    /posts/:title          PostController@getPostByPostTitle
     * POST   /posts/:title          PostController@getPostByPostTitle
     * GET    /posts/:title/:token   PostController@getPostByToken
     * GET    /posts/:category_name  PostController@getPostsByCategoryName
     */
    // TODO: あとで名前は最適化する
    private $routeMap = [
        '/' => [
            // NOTE: END_POINTという表現は微妙な気がする。ルーティング内部の処理で使うデータ構造なので良しとしたい。アルゴリズムの最適化をするとこの辺は変わるはず。
            'END_POINT' => [
                'GET' => 'IndexController@getIndex',
            ],
            'posts' => [
                'END_POINT' => [
                    'GET' => 'PostController@getPosts',
                ],
                ':title' => [
                    'END_POINT' => [
                        'GET' => 'PostController@getPostByPostTitle',
                        'POST' => 'PostController@postPostByPostTitle',
                    ],
                    ':token' =>  [
                        'END_POINT' => [
                            'GET' => 'PostController@getPostByToken',
                        ],
                    ],
                ],
                ':category_name' => [
                    'END_POINT' => [
                        'GET' => 'PostController@getPostsByCategoryName',
                    ],
                ],
            ],
        ],
    ];

    // TODO: to implement
    private $routeDefinition = [
        ['/', 'GET', 'IndexControlle@getIndex'],
        ['/posts', 'GET', 'PostController@getPosts'],
        ['/posts/:title', 'GET', 'PostController@getPostByPostTitle'],
        ['/posts/:title', 'Post', 'PostController@postPostByPostTitle'],
    ];

    // TODO: to implement
    public function testCreateTreeFromRouteDefinition()
    {
        $router = new Router();

        $router->createTreeFromRouteDefinition($this->routeDefinition);
    }

    /**
    * @test
    * @dataProvider createPathArrayProvider
    */
    public function testCreateArrayFromCurrentPath($currentPath, $expectedPathArray)
    {
        $router = new Router();

        $arrayFromCurrentPath = $router->createArrayFromCurrentPath($currentPath);

        $this->assertEquals($expectedPathArray, $arrayFromCurrentPath);
    }

    /**
    * @test
    * @dataProvider searchProvider
    */
    public function testSearch($currentPath, $requestMethod, $targetParams, $expectedAction, $expectedParams)
    {
        $router = new Router();

        $arrayFromCurrentPath = $router->createArrayFromCurrentPath($currentPath);

        $result = $router->search($this->routeMap, $arrayFromCurrentPath, $requestMethod, $targetParams);

        $this->assertSame($expectedAction, $result['action']);
        $this->assertEquals($expectedParams, $result['params']);
    }

    /**
    * DataProvider for testCreateArrayFromCurrentPath
    *
    * @return array
    */
    public function createPathArrayProvider(): array
    {
        /**
         * dataset name => [
         *     route, [expected]
         * ]
         */
        return [
            'Route:/' => [
                '/', ['/']
            ],
            'Route:/posts' => [
                '/posts', ['posts']
            ],
            'Route:/posts/:title[int]' => [
                '/posts/1', ['posts', 1]
            ],
            'Route:/posts/:title[string]' => [
                '/posts/foo', ['posts', 'foo']
            ],
            'Route:/posts/:title[int]/:token[int]' => [
                '/posts/1/123', ['posts', '1', '123']
            ],
            'Route:/posts/:title[string]/:token[string]' => [
                '/posts/foo/bar', ['posts', 'foo', 'bar']
            ],
            'Route:/posts/:title[int]/:token[string]' => [
                '/posts/1/foo', ['posts', 1, 'foo']
            ],
            'Route:/posts/:title[string]/:token[int]' => [
                '/posts/foo/123', ['posts', 'foo', 123]
            ],
            'Route:/posts/:category_name[int]' => [
                '/posts/1', ['posts', 1]
            ],
            'Route:/posts/:category_name[string]' => [
                '/posts/foo', ['posts', 'foo']
            ],
        ];
    }

    /**
    * DataProvider for testSearch
    * *
    * @return array
    */
    public function searchProvider(): array
    {
        /**
         * dataset name => [
         *     route, method, [params], expected action, expected params
         * ]
         */
        return [
            'Route:/' => [
                '/', 'GET', [], 'IndexController@getIndex', []
            ],
            'Route:/posts' => [
                '/posts', 'GET', [], 'PostController@getPosts', []
            ],
            'Route:/posts/:title[int]' => [
                '/posts/1', 'GET', [':title'], 'PostController@getPostByPostTitle', [':title' => 1]
            ],
            'Route:/posts/:title[string]' => [
                '/posts/foo', 'GET', [':title'], 'PostController@getPostByPostTitle', [':title' => 'foo']
            ],
            'Route:/posts/:title[int]' => [
                '/posts/1', 'POST', [':title'], 'PostController@postPostByPostTitle', [':title' => 1]
            ],
            'Route:/posts/:title[string]' => [
                '/posts/foo', 'POST', [':title'], 'PostController@postPostByPostTitle', [':title' => 'foo']
            ],
            'Route:/posts/:title[int]/:token[int]' => [
                '/posts/1/123', 'GET', [':title', ':token'], 'PostController@getPostByToken', [':title' => 1, ':token' => 123]
            ],
            'Route:/posts/:title[string]/:token[string]' => [
                '/posts/foo/bar', 'GET', [':title', ':token'], 'PostController@getPostByToken', [':title' => 'foo', ':token' => 'bar']
            ],
            'Route:/posts/:title[int]/:token[string]' => [
                '/posts/1/foo', 'GET', [':title', ':token'], 'PostController@getPostByToken', [':title' => 1, ':token' => 'foo']
            ],
            'Route:/posts/:title[string]/:token[int]' => [
                '/posts/foo/123', 'GET', [':title', ':token'], 'PostController@getPostByToken', [':title' => 'foo', ':token' => 123]
            ],
            'Route:/posts/:category_name[int]' => [
                '/posts/1', 'GET', [':category_name'], 'PostController@getPostsByCategoryName', [':category_name' => 1]
            ],
            'Route:/posts/:category_name[string]' => [
                '/posts/foo', 'GET', [':category_name'], 'PostController@getPostsByCategoryName', [':category_name' => 'foo']
            ]
        ];
    }
}
