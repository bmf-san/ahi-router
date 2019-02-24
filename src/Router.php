<?php

namespace bmfsan\AhiRouter;

class Router
{
    /**
     * Path parameters
     * @var array
     */
    private $params = [];

    // TODO: to implement
    public function createTreeFromRouteDefinition($routeDefinition)
    {
        /**
         * NOTE: 必要なデータ：パス、パスパラメータ、HTTPメソッド、アクション
         * GET    /                      IndexController@getIndex
         * GET    /posts                 PostController@getPosts
         * GET    /posts/:title          PostController@getPostByPostTitle
         * POST   /posts/:title          PostController@getPostByPostTitle
         * GET    /posts/:title/:token   PostController@getPostByToken
         * GET    /posts/:category_name  PostController@getPostsByCategoryName
         */

        $routeDefinition = [];
        exit();
        // TODO radix treeを構築する
        // 探索のアルゴリズムは自作でやってしまう

        for ($i = 0; $i < count($routeDefinition); $i++) {
            // NOTE: /についてはGETとPOSTだけ。/:titleとかは想定しない。仕様としてカバーしない方向で進める

            // TODO: /だったら1階層目に配列を追加
            if ($routeDefinition[$i][0] == '/') {
                $this->tree['/'] = [
                    'END_POINT' => [
                        $routeDefinition[$i][1] => $routeDefinition[$i][2],
                    ],
                ];
            } else {
                $tmpAry = $this->createArrayFromCurrentPath($routeDefinition[$i][0]);

                // TODO treeに追加したい配列の構造をつくる
                // ex.
                // /post/:title
                // ['post' => [
                //     ':title' => [
                //         'END_POINT' => [
                //             'GET' =>'PostController@getPostByToken',
                //         ],
                //     ],
                // ]];

                // TODO 配列の階層を降りていく再起処理
                for ($j=0; $i < count($tmpAry); $i++) {
                }
            }

            // TODO: /が見つからなければエラーを返す
            // 具体的なエラーハンドリングは最後に設計
        }
        // debug($this->tree);
    }

    /**
     * Create array for search path from current path
     *
     * @param  string $currentPath
     * @return array
     */
    public function createArrayFromCurrentPath($currentPath): array
    {
        $currentPathLength = strlen($currentPath);

        $arrayFromCurrentPath = [];

        for ($i = 0; $i < $currentPathLength; $i++) {
            if ($currentPathLength == 1) {
                // ルートの時
                if ($currentPath{$i} == '/') {
                    $arrayFromCurrentPath[] = '/';
                }
            } else {
                if ($currentPath{$i} == '/') {
                    $arrayFromCurrentPath[] = '';
                    $target = count($arrayFromCurrentPath) - 1;
                } else {
                    $arrayFromCurrentPath[$target] .= $currentPath{$i};
                }
            }
        }

        return $arrayFromCurrentPath;
    }

    /**
     * Search a path and return action and parameters
     *
     * @param  array $routes
     * @param  array $arrayFromCurrentPath
     * @param  string $requestMethod
     * @param  array  $targetParams
     * @return array
     */
    public function search($routes, $arrayFromCurrentPath, $requestMethod, $targetParams = []): array
    {
        $i = 0;
        while ($i < count($arrayFromCurrentPath)) {
            if ($i == 0) {
                $targetArrayDimension = $routes['/'];
            }

            // Condition for root
            if ($arrayFromCurrentPath[$i] == '/') {
                $result = $targetArrayDimension['END_POINT'];
                break;
            }

            foreach ($targetArrayDimension as $key => $value) {
                if (isset($arrayFromCurrentPath[$i])) {
                    if (isset($targetArrayDimension[$arrayFromCurrentPath[$i]])) {
                        $targetArrayDimension = $targetArrayDimension[$arrayFromCurrentPath[$i]];
                    } else {
                        // Condition for parameters
                        $targetArrayDimension = $this->createParams($targetParams, $targetArrayDimension, $arrayFromCurrentPath[$i]);
                    }
                }

                // Condition for last loop
                if ($i == count($arrayFromCurrentPath) - 1) {
                    $result = $targetArrayDimension['END_POINT'];
                }

                $i++;
            }
        }

        return [
            'action' => $result[$requestMethod],
            'params' => $this->params,
        ];
    }

    /**
     * Create parameter data
     *
     * @param  array $targetParams
     * @param  array $targetArrayDimension
     * @param  string $targetPath
     * @return array
     */
    private function createParams($targetParams, $targetArrayDimension, $targetPath)
    {
        for ($i = 0; $i < count($targetParams); $i++) {
            if (isset($targetArrayDimension[$targetParams[$i]])) {
                $this->params[$targetParams[$i]] = $targetPath;

                return $targetArrayDimension[$targetParams[$i]];
            }
        }
    }
}
