<?php

namespace Repositories;

class ResultsRepository
{
    private $connector;

    /**
     * ResultsRepository constructor.
     * Initialize the database connection with sql server via given credentials.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    /**
     * Insert new submission data to the database.
     *
     * @param array $resultData
     *
     * @return mixed
     */
    public function insert(array $resultData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'INSERT INTO results (student_id, task_id, submission) VALUES(:student_id, :task_id, :submission)');
        $statement->bindValue(':student_id', $resultData['student_id']);
        $statement->bindValue(':task_id', $resultData['task_id']);
        $statement->bindValue(':submission', $resultData['submission']);

        return $statement->execute();
    }

    /**
     * Update students results in the database.
     *
     * @param array $resultData
     *
     * @return mixed
     */
    public function update(array $resultData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'UPDATE results SET submission = :submission WHERE student_id = :student_id AND task_id = :task_id');
        $statement->bindValue(':submission', $resultData['submission']);
        $statement->bindValue(':student_id', (int) $resultData['student_id'], \PDO::PARAM_INT);
        $statement->bindValue(':task_id', (int) $resultData['task_id'], \PDO::PARAM_INT);

        echo 'Success';

        return $statement->execute();
    }

    /**
     * Search students results for a given task.
     *
     * @param $taskId
     *
     * @return array
     */
    public function findBy($taskId)
    {
        $statement = $this->connector->getPdo()->prepare('SELECT 
              results.student_id AS student_id, 
              results.submission AS submission, 
              CONCAT(students.first_name, " ", students.last_name) AS student_name 
              FROM results 
              JOIN students ON results.student_id = students.id  
              WHERE results.task_id = :task_id 
              LIMIT 1000');

        $statement->bindValue(':task_id', $taskId, \PDO::PARAM_INT);

        $statement->execute();

        return $this->fetchResultData($statement);
    }

    /**
     * Convert results of a query into an array.
     *
     * @param $statement
     *
     * @return array
     */
    private function fetchResultData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'student_id' => $result['student_id'],
                'submission' => $result['submission'],
                'student_name' => $result['student_name'],
            ];
        }

        return $results;
    }
}
