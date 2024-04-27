<?php
declare(strict_types = 1);

namespace Routim\Model;

/**
 * Class Search - модель для работы с базой данных и запросами к ней
 */
class Search
{
    private Git     $git;
    public Database $database;

    /**
     * @param Git      $git
     * @param Database $database
     */
    public function __construct(Git $git, Database $database)
    {
        $this->git      = $git;
        $this->database = $database;
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function handleSource(array $params): array
    {
        if($params['source'] === 'database') {
            // есть ли в базе данные по keyword -
            $result = $this->database->getRecordsByKey($params);
            // если нет, то делаем запрос в гитхаб
            if(!count($result['items'])) {
                $result = $this->gitSaver($params);
            }
        }
        else {
            // если источник не база данных, то сразу запрашиваем у гитхаба
            $result = $this->gitSaver($params);
        }
        return $result;
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function gitSaver(array $params): array
    {
        $result = $this->git->handleRequest($params);
        if($result['status'] === 1) {
            // если данные из гитхаб получены, то записываем в базу
            $this->database->save($result);
        }
        return $result;
    }


}