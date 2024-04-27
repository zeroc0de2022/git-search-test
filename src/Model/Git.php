<?php
declare(strict_types = 1);
/****
 * Author: zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
 */

namespace Routim\Model;

use Routim\Curl;

/**
 * Class Git/
 * Handles requests to GitHub API and returns formatted response
 */
class Git
{
    private Curl $curl;

    private string $apiLink = 'https://api.github.com/search/repositories';

    /**
     * Constructor
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }


    /**
     * Handle request to GitHub API and return formatted response
     * @param $params
     * @return array
     */
    public function handleRequest($params): array
    {
        $content    = ['error' => 0, 'message' => 'No results found', 'source' => 'github', 'status' => 0, 'keyword' => $params['keyword']];
        $jsonResult = json_decode($this->request($params), true);
        if(isset($jsonResult['total_count'])) {
            $content['total'] = $jsonResult['total_count'];
            if($jsonResult['total_count']) {
                $content['message'] = 'Results found';
                $content['status']  = 1;
                $items              = $jsonResult['items'];
                array_walk($items, function($val) use (&$content)
                {
                    $num = isset($content['items'])
                        ? count($content['items'])
                        : 0;

                    $content['items'][$num]['name']       = $val['name'];
                    $content['items'][$num]['login']      = $val['owner']['login'];
                    $content['items'][$num]['url']        = $val['html_url'];
                    $content['items'][$num]['stargazers'] = $val['stargazers_count'];
                    $content['items'][$num]['watchers']   = $val['watchers_count'];
                    $content['items'][$num]['descr']      = !empty($val['description'])
                        ? $val['description']
                        : 'No description';
                });
            }
        }
        return $content;
    }

    /**
     * Make request to GitHub API and return response
     *
     * @param array $data
     * @return string
     */
    private function request(array $data): string
    {
        $page     = abs((int)$data['page']);
        $per_page = (is_numeric($data['per_page']))
            ? (int)$data['per_page']
            : 10;
        $query    = ['page'     => $page
            ?: !$page,
                     'per_page' => $per_page,
                     'sort'     => 'created',
                     'order'    => 'asc',
                     'q'        => $data['keyword']];
        $responce = $this->curl->request(['url'       => $this->apiLink . '?' . http_build_query($query),
                                          'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0 Cyberfox/52.9.1',
                                          'headers'   => ['Content-Type: application/json', 'Accept: application/json']]);
        return $responce['body'];
    }
}