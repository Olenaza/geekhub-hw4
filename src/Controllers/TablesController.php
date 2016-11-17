<?php

namespace Controllers;

use Repositories\TablesRepository;

class TablesController
{
    private $repository;
    private $loader;
    private $twig;

    /**
     * TablesController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new TablesRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Add tables to the database.
     *
     * @return string
     */
    public function createAction()
    {
        $this->repository->createTables();

        return $this->twig->render('tables.html.twig', ['message' => 'Tables have been successfully created.']);
    }
}
