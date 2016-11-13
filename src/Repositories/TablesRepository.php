<?php

namespace Repositories;

class TablesRepository
{
    private $connector;

    /**
     * TablesRepository constructor.
     * Initialize the database connection with sql server via given credentials.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    /**
     * Create tables if not exist.
     *
     * @return mixed
     */
    public function createTables()
    {
        $statementCreateUniversitiesTable = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS universities(
          id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          city VARCHAR(50) NOT NULL,
          site VARCHAR(50),
          UNIQUE (name)
          ) CHARACTER SET utf8');
        $statementCreateUniversitiesTable->execute();

        $statementCreateDepartmentsTable = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS departments(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          university_id INT(10) NOT NULL,
          CONSTRAINT unique_department_in_university UNIQUE (name, university_id),
          FOREIGN KEY (university_id) REFERENCES universities (id) ON DELETE CASCADE
          ) CHARACTER SET utf8');
        $statementCreateDepartmentsTable->execute();

        $statementCreateStudentsTable = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS students(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          first_name VARCHAR(50) NOT NULL,
          last_name VARCHAR(50) NOT NULL,
          email VARCHAR(50),
          tel VARCHAR(50),
          department_id INT(10),
          CONSTRAINT unique_student UNIQUE (first_name, last_name)
          ) CHARACTER SET utf8');
        $statementCreateStudentsTable->execute();

        $statementCreateSubjectsTable = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS subjects(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          department_id INT(10),
          CONSTRAINT unique_subject_in_department UNIQUE (name, department_id), 
          FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE
          ) CHARACTER SET utf8');
        $statementCreateSubjectsTable->execute();

        return;
    }
}
