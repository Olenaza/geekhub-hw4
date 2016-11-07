<?php

namespace Repositories;

class TablesRepository
{
    private $connector;

    /**
     * TablesRepository constructor.
     * Initialize the database connection with sql server via given credentials
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    /**
     * Create tables if not exist
     * @return mixed
     */
    public function createTables()
    {
        $statement = $this->connector->getPdo()->prepare('CREATE TABLE IF NOT EXISTS universities(
          id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          city VARCHAR(50) NOT NULL,
          site VARCHAR(50)) CHARACTER SET utf8');
        return $statement->execute();
    }
}

