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
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          city VARCHAR(50) NOT NULL,
          site VARCHAR(50),
          CONSTRAINT name_unique UNIQUE (name)) CHARACTER SET utf8');

        $statementCreateUniversitiesTable->execute();

        $statementCreateDepartmentsTable = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS departments(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          university_id INT(10) NOT NULL,
          CONSTRAINT name_university_unique UNIQUE (name, university_id)) CHARACTER SET utf8');

        $statementCreateDepartmentsTable->execute();

        return;
    }
}
