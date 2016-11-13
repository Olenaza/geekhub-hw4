<?php

namespace Repositories;

class StudentsRepository implements RepositoryInterface
{
    private $connector;

    /**
     * StudentsRepository constructor.
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
            'SELECT students.id AS id, 
              students.first_name AS first_name, 
              students.last_name AS last_name, 
              students.email AS email, 
              students.tel AS tel, 
              students.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM students 
              JOIN departments ON students.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id 
              LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fetchStudentsData($statement);
    }

    private function fetchStudentsData($statement)
    {
        $results = [];
        while ($result = $statement->fetch()) {
            $results[] = [
                'id' => $result['id'],
                'first_name' => $result['first_name'],
                'last_name' => $result['last_name'],
                'email' => $result['email'],
                'tel' => $result['tel'],
                'department_id' => $result['department_id'],
                'department_name' => $result['department_name'],
                'university_id' => $result['university_id'],
                'university_name' => $result['university_name'],
            ];
        }

        return $results;
    }

    public function insert(array $studentData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'INSERT INTO students (first_name, last_name, email, tel, department_id) 
              VALUES(:first_name, :last_name, :email, :tel, :department_id)');
        $statement->bindValue(':first_name', $studentData['first_name']);
        $statement->bindValue(':last_name', $studentData['last_name']);
        $statement->bindValue(':email', $studentData['email']);
        $statement->bindValue(':tel', $studentData['tel']);
        $statement->bindValue(':department_id', $studentData['department_id']);

        return $statement->execute();
    }

    public function find($id)
    {
        $statement = $this->connector->getPdo()->prepare(
            'SELECT students.id AS id, 
              students.first_name AS first_name, 
              students.last_name AS last_name, 
              students.email AS email, 
              students.tel AS tel, 
              students.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM students 
              JOIN departments ON students.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id
              WHERE students.id = :id 
              LIMIT 1'
        );
        $statement->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $statement->execute();
        $studentsData = $this->fetchStudentsData($statement);

        return $studentsData[0];
    }

    public function update(array $studentData)
    {
        $statement = $this->connector->getPdo()->prepare(
            'UPDATE students 
              SET first_name = :first_name, 
              last_name = :last_name, 
              email = :email, 
              tel = :tel, 
              department_id = :department_id 
              WHERE id = :id');
        $statement->bindValue(':first_name', $studentData['first_name'], \PDO::PARAM_STR);
        $statement->bindValue(':last_name', $studentData['last_name'], \PDO::PARAM_STR);
        $statement->bindValue(':email', $studentData['email'], \PDO::PARAM_STR);
        $statement->bindValue(':tel', $studentData['tel'], \PDO::PARAM_STR);
        $statement->bindValue(':department_id', (int) $studentData['department_id'], \PDO::PARAM_INT);
        $statement->bindValue(':id', $studentData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function remove(array $studentData)
    {
        $statement = $this->connector->getPdo()->prepare('DELETE FROM students WHERE id = :id');
        $statement->bindValue(':id', $studentData['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function findBy($criteria = [])
    {
        $selectList = 'SELECT students.id AS id, 
              students.first_name AS first_name, 
              students.last_name AS last_name, 
              students.email AS email, 
              students.tel AS tel, 
              students.department_id AS department_id, 
              departments.name AS department_name, 
              departments.university_id AS university_id,
              universities.name AS university_name 
              FROM students 
              JOIN departments ON students.department_id=departments.id 
              JOIN universities ON departments.university_id=universities.id 
              WHERE students.first_name LIKE :fname AND students.last_name LIKE :lname ';

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
                $selectList .= 'AND students.department_id = :department_id LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
            } else {
                $selectList .= 'AND students.department_id = :department_id AND departments.university_id = :university_id LIMIT 1000';
                $statement = $this->connector->getPdo()->prepare($selectList);
                $statement->bindValue(':university_id', $criteria['search_university_id'], \PDO::PARAM_INT);
            }
            $statement->bindValue(':department_id', $criteria['search_department_id'], \PDO::PARAM_INT);
        }

        $searchFirstName = '%'.$criteria['search_first_name'].'%';
        $searchLastName = '%'.$criteria['search_last_name'].'%';
        $statement->bindValue(':fname', $searchFirstName, \PDO::PARAM_STR);
        $statement->bindValue(':lname', $searchLastName, \PDO::PARAM_STR);

        $statement->execute();

        return $this->fetchStudentsData($statement);
    }
}
