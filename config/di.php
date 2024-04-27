<?php
declare(strict_types = 1);

use Routim\Connect;
use Routim\Model\Twig\AssetExtension;
use Routim\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function Di\autowire;
use function DI\get;

/** @noinspection IncorrectFormatting */
return [
    'server.params'         => $_SERVER,
    FilesystemLoader::class => autowire()->constructorParameter('paths', 'src/View'),
    Environment::class      => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class))
        ->method('addExtension', get(AssetExtension::class)),
    Connect::class         => autowire()
        ->constructorParameter('connection', get(PDO::class)),
    PDO::class              => autowire()
        ->constructor(
            "mysql:host=" . getenv('DATABASE_HOST') . ";dbname=" . getenv('DATABASE_NAME'),
            getenv('DATABASE_USERNAME'),
            getenv('DATABASE_PASSWORD'),
            [PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES   => false,])
        ->method('exec', 'set names utf8mb4'),
    AssetExtension::class   => autowire()->constructorParameter(
        'serverParams',
        get('server.params')),
    Session::class => autowire(),



];