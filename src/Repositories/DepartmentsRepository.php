<?php

namespace Repositories;

class DepartmentsRepository implements RepositoryInterface
{
    private $connector;

    /**
     * DepartmentsRepository constructor.
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
            'SELECT departments.id AS dept_id, 
              departments.name AS dept_name, 
              departments.university_id AS university_id, 
              universities.name AS university_name 
              FROM departments LEFT JOIN universities ON departments.university_id=universities.id 
              LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fetchDepartmentsData($statement);
    }

    private function fetchDepartmentsData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'id' => $result['dept_id'],
                'name' => $result['dept_name'],
                'university_id' => $result['university_id'],
                'university_name' => $result['university_name'],
            ];
        }

        return $results;
    }
    public function insert(array $departmentData)
    {
        $statement = $this->connector->getPdo()->prepare('INSERT INTO departments (name, university_id) VALUES(:name, :university_id)');
        $statement->bindValue(':name', $departmentData['name']);
        $statement->bindValue(':university_id', $departmentData['university_id']);

        return $statement->execute();
    }

    public function find($id)
    {
        $statement = $this->connector->getPdo()->prepare(
            'SELECT departments.id AS dept_id, 
              departments.name AS dept_name, 
              departments.university_id AS university_id, 
              universities.name AS university_name 
              FROM departments LEFT JOIN universities ON departments.university_id=universities.id 
              WHERE departments.id = :id 
              LIMIT 1'
        );
        $statement->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $statement->execute();
        $departmentsData = $this->fetchDepartmentsData($statement);

        return $departmentsData[0];
    }

    public function update(array $departmentData)
    {
        $statement = $this->connector->getPdo()->prepare('UPDATE departments SET name = :name, university_id = :university_id WHERE id = :id');
        $statement->bindValue(':name', $departmentData['name'], \PDO::PARAM_STR);
        $statement->bindValue(':university_id', (int) $departmentData['university_id'], \PDO::PARAM_INT);
        $statement->bindValue(':id', $departmentData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function remove(array $departmentData)
    {
        $statement = $this->connector->getPdo()->prepare('DELETE FROM departments WHERE id = :id');
        $statement->bindValue(':id', $departmentData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function findBy($criteria = [])
    {
        $selectList = 'SELECT departments.id AS dept_id, 
                  departments.name AS dept_name, 
                  departments.university_id AS university_id, 
                  universities.name AS university_name 
                  FROM departments LEFT JOIN universities ON departments.university_id=universities.id 
                  WHERE departments.name LIKE :name ';

        if (empty($criteria['search_university_id'])) {
            $selectList .= 'LIMIT 1000';
            $statement = $this->connector->getPdo()->prepare($selectList);
        } else {
            $selectList .= 'AND departments.university_id = :university_id LIMIT 1000';
            $statement = $this->connector->getPdo()->prepare($selectList);
            $statement->bindValue(':university_id', $criteria['search_university_id'], \PDO::PARAM_INT);
        }

        $searchName = '%'.$criteria['search_name'].'%';
        $statement->bindValue(':name', $searchName, \PDO::PARAM_STR);

        $statement->execute();

        return $this->fetchDepartmentsData($statement);
    }
}
