<?php
declare(strict_types = 1);

namespace Routim\Model;

use Exception;
use PDO;
use PDOException;
use Routim\Connect;
use Routim\Session;

/**
 * Class Database - модель для работы с базой данных и запросами к ней
 */
class Database
{
    private PDO $connect;

    private Session $session;

    /**
     *  Constructor
     * @param Connect $connect
     * @param Session $session
     */
    public function __construct(Connect $connect, Session $session)
    {
        $this->connect = $connect->getConnection();
        $this->session = $session;
    }

    /**
     *  Save data to DB
     * @param array $result
     * @return void
     * @throws \Exception
     */
    public function save(array $result): void
    {
        $keyword = $result['keyword'];
        $stmt    = $this->connect->prepare('INSERT IGNORE INTO projects (keyword, json) VALUES (:keyword, :json)');
        foreach($result['items'] as $item) {
            $json = json_encode($item, JSON_UNESCAPED_UNICODE);
            $stmt->bindValue(':keyword', $keyword);
            $stmt->bindValue(':json', $json);
            try {
                $stmt->execute();
            }
            catch(PDOException $exception) {
                throw new Exception(__LINE__ . ' ' . $exception->getMessage());
            }
        }

    }

    /**
     * Search for a keyword in the database.
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getRecordsByKey(array $params): array
    {
        $keyword = '%' . $params['keyword'] . '%';
        $limit   = $params['per_page'] ?? 10;
        $offset  = ($params['page'] - 1) * $params['per_page'];

        if($params['page'] == 1) {
            $this->session->setData('total', $this->getRecordCount($keyword));
        }

        $stmt = $this->connect->prepare("SELECT * FROM projects WHERE JSON_EXTRACT(json, '$') LIKE :keyword LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':keyword', $keyword);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        try {
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new Exception(__LINE__ . ' ' . $e->getMessage());
        }

        $result = [];
        while($row = $stmt->fetch()) {
            $json     = json_decode($row['json'], true);
            $result[] = $json;
        }
        $isItems = count($result);
        return ['error'   => 0,
                'message' => $isItems
                    ? 'Results found'
                    : 'No results found',
                'source'  => $isItems
                    ? 'database'
                    : 'github',
                'status'  => $isItems
                    ? 1
                    : 0,
                'keyword' => $params['keyword'],
                'total'   => $this->session->getData('total'),
                'items'   => $result];
    }

    /**
     * @throws \Exception
     */
    public function getAllRecords(): array
    {
        $stmt = $this->connect->prepare("SELECT * FROM projects");
        try {
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new Exception(__LINE__ . ' ' . $e->getMessage());
        }
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = [
                'keyword'=> $row['keyword'],
                'data' => json_decode($row['json'], true),
            ];
        }
        return $result;
    }



    /**
     *  Get total found
     * @param string $keyword
     * @return mixed
     * @throws \Exception
     */
    public function getRecordCount(string $keyword): mixed
    {
        $stmt = $this->connect->prepare("SELECT COUNT(id) as total FROM projects WHERE JSON_EXTRACT(json, '$') LIKE :keyword");
        $stmt->bindValue(':keyword', $keyword);
        try {
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new Exception(__LINE__ . ' ' . $e->getMessage());
        }
        return $stmt->fetchColumn();
    }

    /**
     * @param mixed $keyword
     * @return int
     * @throws \Exception
     */
    public function deleteItemByKey(mixed $keyword): int
    {
        $stmt = $this->connect->prepare("DELETE FROM projects WHERE keyword = :keyword");
        $stmt->bindValue(':keyword', $keyword);
        try {
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new Exception(__LINE__ . ' ' . $e->getMessage());
        }
        return $stmt->rowCount();

    }

}