<?php

namespace Repositories;

class RegistrationsRepository implements RepositoryInterface
{
    private $connector;

    /**
     * RegistrationsRepository constructor.
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
            'SELECT registrations.student_id AS student_id, 
              registrations.subject_id AS subj_id, 
              students.first_name AS student_first_name, 
              students.last_name AS student_last_name, 
              subjects.name AS subj_name 
              FROM registrations 
              JOIN students ON registrations.student_id=students.id 
              JOIN subjects ON registrations.subject_id=subjects.id 
              LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fetchRegistrationData($statement);
    }

    private function fetchRegistrationData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'student_id' => $result['student_id'],
                'subject_id' => $result['subj_id'],
                'student_first_name' => $result['student_first_name'],
                'student_last_name' => $result['student_last_name'],
                'subject_name' => $result['subj_name'],
            ];
        }

        return $results;
    }

    public function insert(array $registrationData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'INSERT INTO registrations (student_id, subject_id) VALUES(:student_id, :subject_id)');
        $statement->bindValue(':student_id', $registrationData['student_id']);
        $statement->bindValue(':subject_id', $registrationData['subject_id']);

        return $statement->execute();
    }

    public function remove(array $registrationData)
    {
        $statement = $this->connector->getPdo()->prepare('DELETE FROM registrations WHERE student_id = :student_id AND subject_id = :subject_id');
        $statement->bindValue(':student_id', $registrationData['student_id'], \PDO::PARAM_INT);
        $statement->bindValue(':subject_id', $registrationData['subject_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function update(array $entityData)
    {
        // TODO: Implement update() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findBy($criteria = [])
    {
        // TODO: Implement findBy() method.
    }
}
