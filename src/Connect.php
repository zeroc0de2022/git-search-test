<?php
declare(strict_types = 1);
/****
 * Author: zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
 */

namespace Routim;

/**
 * Connect
 */

use PDO;

/**
 * Class Connect
 */
class Connect
{
    /**
     * @var PDO $connection
     */
    private PDO $connection;

    /**
     * Connect constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        if(!isset($this->connection)) {
            $this->connection = $connection;
        }
    }

    /**
     * Get the PDO connection
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}