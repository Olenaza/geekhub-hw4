<?php

namespace Repositories;

class Connector
{
    private $pdo;

    /**
     * UniversitiesRepository constructor.
     * Create the database if not exists and initialize the database connection with sql server via given credentials
     * @param $databasename
     * @param $user
     * @param $pass
     */
    public function __construct($databasename = 'universities_db', $user = 'root', $pass = 'olenaza')
    {
        $pdo = new \PDO('mysql:host=localhost;charset=utf8', $user, $pass);

        //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $databasename = "`".str_replace("`","``",$databasename)."`";

        $pdo->query("CREATE DATABASE IF NOT EXISTS $databasename");

        $pdo->query("use $databasename");

        $this->pdo = $pdo;

        if (!$this->pdo) {
            return false;
            //throw new Exception('Error connecting to the database');
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}