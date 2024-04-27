<?php
declare(strict_types = 1);

use Routim\Controller\ApiPage;
use Routim\Controller\MainPage;
use Routim\Controller\NotFoundPage;


return [
    '/api/find/{keyword}' => [ApiPage::class, 'dispatchRequest'],
    '/api/find[/]' => [ApiPage::class, 'dispatchRequest'],
    '/{url_key}'            => NotFoundPage::class . ':handleRequest',
    '/'                     => MainPage::class . ':handleGet'
];