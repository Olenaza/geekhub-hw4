<?php

namespace Controllers;

use Repositories\UniversitiesRepository;

class UniversitiesController
{
    private $repository;
    private $loader;
    private $twig;

    /**
     * UniversitiesController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new UniversitiesRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all universities in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $universitiesData = $this->repository->findAll();

        return $this->twig->render('universities.html.twig', ['universities' => $universitiesData]);
    }

    /**
     * Show form for adding a new university and add the new university to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->insert(
                [
                    'name' => $_GET['name'],
                    'city' => $_GET['city'],
                    'site' => $_GET['site'],
                ]
            );

            return $this->indexAction();
        }

        return $this->twig->render('universities_form.html.twig',
            [
                'id' => '',
                'name' => '',
                'city' => '',
                'site' => '',
                'action' => 'new',
                'button' => 'Create',
            ]
        );
    }

    /**
     * Show form for updating a university data and update the database.
     *
     * @return string
     */
    public function editAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->update(
                [
                    'name' => $_GET['name'],
                    'city' => $_GET['city'],
                    'site' => $_GET['site'],
                    'id' => (int) $_GET['id'],
                ]
            );

            return $this->indexAction();
        }
        $universitiesData = $this->repository->find((int) $_GET['id']);

        return $this->twig->render('universities_form.html.twig',
            [
                'id' => $_GET['id'],
                'name' => $universitiesData['name'],
                'city' => $universitiesData['city'],
                'site' => $universitiesData['site'],
                'action' => 'edit',
                'button' => 'Update',
            ]
        );
    }

    /**
     * Remove a university from the database.
     *
     * @return string
     */
    public function deleteAction()
    {
        if (isset($_GET['submit'])) {
            if ($_GET['submit'] == 'Delete') {
                $id = (int) $_GET['id'];
                $this->repository->remove(['id' => $id]);
            }

            return $this->indexAction();
        }

        return $this->twig->render('delete.html.twig', array('item_name' => 'universitie', 'id' => $_GET['id']));
    }

    /**
     * Show form for searching by full name and/or city or their parts and show search results.
     *
     * @return string
     */
    public function searchAction()
    {
        if (isset($_GET['search_name'])) {
            $universitiesData = $this->repository->findBy(
                [
                    'search_name' => $_GET['search_name'],
                    'search_city' => $_GET['search_city'],
                ]
            );

            return $this->twig->render('universities_search.html.twig',
                [
                    'universities' => $universitiesData,
                    'search_name' => $_GET['search_name'],
                    'search_city' => $_GET['search_city'],
                ]
            );
        }

        return $this->twig->render('universities_search.html.twig',
            [
                'universities' => [],
                'search_name' => '',
                'search_city' => '',
            ]
        );
    }
}
