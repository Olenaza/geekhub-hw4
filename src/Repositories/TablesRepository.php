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
        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS universities(
          id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          city VARCHAR(50) NOT NULL,
          site VARCHAR(50),
          UNIQUE (name)
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS departments(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          university_id INT(10) NOT NULL,
          CONSTRAINT unique_department_in_university UNIQUE (name, university_id),
          FOREIGN KEY (university_id) REFERENCES universities (id) ON DELETE CASCADE
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS students(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          first_name VARCHAR(50) NOT NULL,
          last_name VARCHAR(50) NOT NULL,
          email VARCHAR(50),
          tel VARCHAR(50),
          department_id INT(10),
          CONSTRAINT unique_student UNIQUE (first_name, last_name)
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS subjects(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          department_id INT(10),
          CONSTRAINT unique_subject_with_department UNIQUE (name, department_id), 
          FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS tasks(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          subject_id INT(10),
          CONSTRAINT unique_task_with_subject UNIQUE (name, subject_id), 
          FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS registrations(
          student_id INT(10) NOT NULL, 
          subject_id INT(10) NOT NULL, 
          PRIMARY KEY (student_id, subject_id),
          FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE, 
          FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE ON UPDATE CASCADE
          ) CHARACTER SET utf8');

        $this->connector->getPdo()->query('CREATE TABLE IF NOT EXISTS results(
          student_id INT(10) NOT NULL, 
          task_id INT(10) NOT NULL, 
          submission TINYINT(1) NOT NULL,
          PRIMARY KEY (student_id, task_id),
          FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE, 
          FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE ON UPDATE CASCADE
          ) CHARACTER SET utf8');

        return;
    }
}
