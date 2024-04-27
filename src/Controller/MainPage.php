<?php
declare(strict_types = 1);

/**
 * Author: zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
 */

namespace Routim\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Routim\Session;
use Twig\Environment;

/**
 * Class MainPage handles GET requests to main page
 * Handle GET requests to /search
 */
class MainPage
{

    private Environment $view;

    private Session $session;

    /**
     *  Constructor
     */
    public function __construct(Environment $view, Session $session)
    {
        $this->view    = $view;
        $this->session = $session;
    }

    /**
     * Handle GET request to main page
     * @param Request  $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function handleGet(Request $request, Response $response): Response
    {
        try {
            $body = $this->view->render('search.twig', ['key' => $this->session->getData('key')]);
        }
        catch(Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        $response->getBody()
                 ->write($body);
        return $response;
    }

}
