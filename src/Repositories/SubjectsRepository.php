<?php

namespace Repositories;

class SubjectsRepository implements RepositoryInterface
{
    private $connector;

    /**
     * SubjectsRepository constructor.
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
            'SELECT subjects.id AS subj_id, 
              subjects.name AS subj_name,
              subjects.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM subjects 
              JOIN departments ON subjects.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id 
              LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fetchSubjectData($statement);
    }

    private function fetchSubjectData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'id' => $result['subj_id'],
                'name' => $result['subj_name'],
                'department_id' => $result['department_id'],
                'department_name' => $result['department_name'],
                'university_id' => $result['university_id'],
                'university_name' => $result['university_name'],
            ];
        }

        return $results;
    }

    public function insert(array $subjectData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'INSERT INTO subjects (name, department_id) VALUES(:name, :department_id)');
        $statement->bindValue(':name', $subjectData['name']);
        $statement->bindValue(':department_id', $subjectData['department_id']);

        return $statement->execute();
    }

    public function find($id)
    {
        $statement = $this->connector->getPdo()->prepare(
            'SELECT subjects.id AS subj_id, 
              subjects.name AS subj_name,  
              subjects.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM subjects 
              JOIN departments ON subjects.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id
              WHERE subjects.id = :id 
              LIMIT 1'
        );
        $statement->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $statement->execute();
        $subjectData = $this->fetchSubjectData($statement);

        return $subjectData[0];
    }

    public function update(array $subjectData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'UPDATE subjects 
              SET name = :name, 
              department_id = :department_id 
              WHERE id = :id');
        $statement->bindValue(':name', $subjectData['name'], \PDO::PARAM_STR);
        $statement->bindValue(':department_id', (int) $subjectData['department_id'], \PDO::PARAM_INT);
        $statement->bindValue(':id', $subjectData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function remove(array $subjectData)
    {
        $statement = $this->connector->getPdo()->prepare('DELETE FROM subjects WHERE id = :id');
        $statement->bindValue(':id', $subjectData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function findBy($criteria = [])
    {
        $selectList = 'SELECT subjects.id AS subj_id, 
              subjects.name AS subj_name, 
              subjects.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM subjects 
              JOIN departments ON subjects.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id 
              WHERE subjects.name LIKE :name ';

        if (empty($criteria['search_department_id'])) {
            if (empty($criteria['search_university_id'])) {
                $selectList .= 'LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
            } else {
                $selectList .= 'AND departments.university_id = :university_id LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
                $statement->bindValue(':university_id', $criteria['search_university_id'], \PDO::PARAM_INT);
            }
        } else {
            if (empty($criteria['search_university_id'])) {
                $selectList .= 'AND subjects.department_id = :department_id LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
            } else {
                $selectList .= 'AND subjects.department_id = :department_id AND departments.university_id = :university_id LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
                $statement->bindValue(':university_id', $criteria['search_university_id'], \PDO::PARAM_INT);
            }
            $statement->bindValue(':department_id', $criteria['search_department_id'], \PDO::PARAM_INT);
        }

        $searchName = '%'.$criteria['search_name'].'%';
        $statement->bindValue(':name', $searchName, \PDO::PARAM_STR);

        $statement->execute();

        return $this->fetchSubjectData($statement);
    }
}
