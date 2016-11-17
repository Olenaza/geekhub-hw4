<?php

namespace Repositories;

class TasksRepository implements RepositoryInterface
{
    private $connector;

    /**
     * TasksRepository constructor.
     * Initialize the database connection with sql server via given credentials.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    public function findAll($limit = 1000, $offset = 0)
    {
        $statement = $this->connector->getPdo()->prepare(
            'SELECT tasks.id AS task_id, 
              tasks.name AS task_name, 
              tasks.subject_id AS subject_id, 
              subjects.name AS subject_name 
              FROM tasks JOIN subjects ON tasks.subject_id=subjects.id 
              LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fetchTaskData($statement);
    }

    private function fetchTaskData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'id' => $result['task_id'],
                'name' => $result['task_name'],
                'subject_id' => $result['subject_id'],
                'subject_name' => $result['subject_name'],
            ];
        }

        return $results;
    }
    public function insert(array $taskData)
    {
        $statement = $this->connector->getPdo()->prepare('INSERT INTO tasks (name, subject_id) VALUES(:name, :subject_id)');
        $statement->bindValue(':name', $taskData['name']);
        $statement->bindValue(':subject_id', $taskData['subject_id']);

        return $statement->execute();
    }

    public function find($id)
    {
        $statement = $this->connector->getPdo()->prepare(
            'SELECT tasks.id AS task_id, 
              tasks.name AS task_name, 
              tasks.subject_id AS subject_id, 
              subjects.name AS subject_name 
              FROM tasks JOIN subjects ON tasks.subject_id=subjects.id 
              WHERE tasks.id = :id 
              LIMIT 1'
        );
        $statement->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $statement->execute();
        $taskData = $this->fetchTaskData($statement);

        return $taskData[0];
    }

    public function update(array $taskData)
    {
        $statement = $this->connector->getPdo()->prepare('UPDATE tasks SET name = :name, subject_id = :subject_id WHERE id = :id');
        $statement->bindValue(':name', $taskData['name'], \PDO::PARAM_STR);
        $statement->bindValue(':subject_id', (int) $taskData['subject_id'], \PDO::PARAM_INT);
        $statement->bindValue(':id', $taskData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function remove(array $taskData)
    {
        $statement = $this->connector->getPdo()->prepare('DELETE FROM tasks WHERE id = :id');
        $statement->bindValue(':id', $taskData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function findBy($criteria = [])
    {
        $selectList = 'SELECT tasks.id AS task_id, 
                  tasks.name AS task_name, 
                  tasks.subject_id AS subject_id, 
                  subjects.name AS subject_name 
                  FROM tasks JOIN subjects ON tasks.subject_id=subjects.id 
                  WHERE tasks.name LIKE :name ';

        if (empty($criteria['search_subject_id'])) {
            $selectList .= 'LIMIT 1000';
            $statement = $this->connector->getPdo()->prepare($selectList);
        } else {
            $selectList .= 'AND tasks.subject_id = :subject_id LIMIT 1000';
            $statement = $this->connector->getPdo()->prepare($selectList);
            $statement->bindValue(':subject_id', $criteria['search_subject_id'], \PDO::PARAM_INT);
        }

        $searchName = '%'.$criteria['search_name'].'%';
        $statement->bindValue(':name', $searchName, \PDO::PARAM_STR);

        $statement->execute();

        return $this->fetchTaskData($statement);
    }
}
