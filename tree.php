<?php

class Tree
{
    // TODO: あとでアクセサは調整
    public $tree = null;

    public function add($nodeList)
    {
        for ($i=0; $i < count($nodeList); $i++) {
            if (count($this->tree) == 0) {
                $this->tree['/'] = [];
                $ref = &$this->tree['/'];
            } else {
                if (isset($nodeList[$i]['END_POINT'])) {
                    $ref['END_POINT'] = $nodeList[$i]['END_POINT'];

                } else {
                    $ref[$nodeList[$i]] = [];
                    $ref = &$ref[$nodeList[$i]];
                    var_dump($this->tree);
                }
            }
        }
    }

// 参照を使えば連想配列の一部分を更新できる
//     $array = [
//     'id' => [
//         'post' => [
//             'cate' => 'hoge'
//         ]
//     ]
// ];

// $ref = &$array['id']['post']['cate'];
// $ref = 'moge';
// var_dump($array);

    /**
    * Create a node from path
    * ex. add a END_POINT to leaf
    * / -> [‘/’, ‘END_POINT’]
    * /posts/:id -> [‘/’, ‘posts’, ‘:id’, ['END_POINT' => ['GET' => 'PostController@show']]
    *
    * @param string $nodeKey
    * @param string $nodeMethod
    * @param string $nodeAction
    * @return array
    */
    public function createNodeList($nodeKey, $nodeMethod, $nodeAction)
    {
        $nodeList = [];
        $target = 0;

        if ($nodeKey == '/') {
            $nodeList[] = '/';
        } else {
            for ($i = 0; $i < strlen($nodeKey); $i++) {
                // 先頭の/はroot
                if ($i == 0) {
                    if ($nodeKey{$i} == '/') {
                        $nodeList[] = '/';
                        $nodeList[] = '';
                        $target = 1;
                    }
                } else {
                    if ($nodeKey{$i} == '/') {
                        $nodeList[] = '';
                        ++$target;
                    } else {
                        $nodeList[$target] .= $nodeKey{$i};
                    }
                }
            }
        }

        $nodeList[count($nodeList)] = ['END_POINT' => [
            $nodeMethod => $nodeAction
        ]];

        return $nodeList;
    }
}

$tree = new Tree();
$nodeList = $tree->createNodeList('/posts/:id', 'GET', 'PostController@show');
// $nodeList = $tree->createNodeList('/', 'GET', 'IndexController@show');
$tree->add($nodeList);


ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

var_dump($tree->tree);

// private $routes = [
//     ‘/’ => [
//         ‘END_POINT’ => [
//             ‘GET’ => ‘IndexController@index’,
//         ],
//         ‘posts’ => [
//             ‘END_POINT’ => [
//                 ‘GET’ => ‘PostController@index’,
//                 ‘POST’ => ‘PostController@store’,
//             ],
//             ‘:id’ => [
//                 ‘END_POINT’ => [
//                     ‘GET’ => ‘PostController@show’,
//                     ‘POST’ => ‘PostController@update’,
//                 ],
//                 ‘:token’ =>  [
//                     ‘END_POINT’ => [
//                         ‘GET’ => ‘PostController@preview’,
//                     ],
//                 ],
//             ],
//             ‘:category’ => [
//                 ‘:id’ => [
//                     ‘END_POINT’ => [
//                         ‘GET’ => ‘PostController@showByCategory’,
//                     ],
//                 ],
//             ],
//         ],
//         ‘categories’ => [
//             ‘END_POINT’ => [
//                 ‘GET’ => ‘CategoryController@index’,
//             ],
//         ],
//     ],
// ];
// private $routes = [
//     ‘/’ => [
//         ‘END_POINT’ => [
//             ‘GET’ => ‘IndexController@index’,
//         ],
//         ‘posts’ => [
//             ‘END_POINT’ => [
//                 ‘GET’ => ‘PostController@index’,
//                 ‘POST’ => ‘PostController@store’,
//             ],
//             ‘:id’ => [
//                 ‘END_POINT’ => [
//                     ‘GET’ => ‘PostController@show’,
//                     ‘POST’ => ‘PostController@update’,
//                 ],
//                 ‘:token’ =>  [
//                     ‘END_POINT’ => [
//                         ‘GET’ => ‘PostController@preview’,
//                     ],
//                 ],
//             ],
//             ‘:category’ => [
//                 ‘:id’ => [
//                     ‘END_POINT’ => [
//                         ‘GET’ => ‘PostController@showByCategory’,
//                     ],
//                 ],
//             ],
//         ],
//         ‘categories’ => [
//             ‘END_POINT’ => [
//                 ‘GET’ => ‘CategoryController@index’,
//             ],
//         ],
//     ],
// ];
//            /
//     posts              categories
//
// :id       :category
//
// :token    :id
//
// それぞれのノードでvalue(メソッドとアクション)を持つ

// /
// /posts
// /posts/:id
// /posts/:id/:token
// /posts/:category/:id
// /categories

// $routeDefinition = [
//     [‘/’, ‘GET’, ‘IndexControlle@index’],
//     [‘/posts’, ‘GET’, ‘PostController@index’],
//     [‘/posts’, ‘POST’, ‘PostController@store’],
//     [‘/posts/:id’, ‘GET’, ‘PostController@show’],
//     [‘/posts/:id’, ‘POST’, ‘PostController@update’],
//     [‘/posts/:id/:token’, ‘GET’, ‘PostController@preview’],
//     [‘/posts/:category/:id’, ‘GET’, ‘PostController@showByCategory’],
//     [‘/categories’, ‘GET’, ‘CategoryController@index’],
