<?php
declare(strict_types = 1);

namespace Routim\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Routim\Model\Search;
use Routim\Session;

/**
 *
 */
class ApiPage
{

    private Search $search;

    private Session $session;

    /**
     *  Constructor
     */
    public function __construct(Search $search, Session $session)
    {
        $this->search  = $search;
        $this->session = $session;
    }

    /**
     * Dispatch GET/POST requests to appropriate handler methods
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws Exception
     */
    public function dispatchRequest(Request $request, Response $response, array $args = []): Response
    {

        $method = $request->getMethod();
        if(isset($args['keyword']) && $method !== 'DELETE') {
            throw new Exception('Invalid request method');
        }
        return match ($request->getMethod()) {
            'POST'   => $this->handlePost($request, $response),
            'GET'    => $this->handleGet($response),
            'DELETE' => $this->handleDelete($request, $response, $args),
            default  => $response->withHeader('Location', '/')
                                 ->withStatus(302)
        };
    }

    /**
     *  Handle POST request for search
     *
     * @param Request  $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function handlePost(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        if(count($params) === 4 && isset($params['keyword'], $params['per_page'], $params['page'], $params['source'])) {
            $this->session->setData('key', $params['keyword']);
            $result = $this->search->handleSource($params);
        }
        else {
            $result = ['error' => 1, 'message' => 'Invalid request parameters'];
        }
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        $response->getBody()
                 ->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     *  Handle GET requests
     *
     * @param Response $response
     * @return Response
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    private function handleGet(Response $response): Response
    {
        $result = $this->search->database->getAllRecords();
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        $response->getBody()
                 ->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Exception
     * @noinspection PhpUnusedParameterInspection
     */
    public function handleDelete(Request $request, Response $response, array $args = []): Response
    {
        $deleted = $this->search->database->deleteItemByKey($args['keyword']);
        $params = compact('deleted');
        $json   = json_encode($params, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }





}