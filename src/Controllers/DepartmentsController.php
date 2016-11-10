<?php

namespace Controllers;

use Repositories\DepartmentsRepository;
use Repositories\UniversitiesRepository;

class DepartmentsController
{
    private $repository;
    private $universities_repository;
    private $loader;
    private $twig;

    /**
     * DepartmentsController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new DepartmentsRepository($connector);
        $this->universities_repository = new UniversitiesRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all departments in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $departmentsData = $this->repository->findAll();

        return $this->twig->render('departments.html.twig', ['departments' => $departmentsData]);
    }

    /**
     * Show form for adding a new department and add the new department to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->insert(
                [
                    'name' => $_GET['name'],
                    'university_id' => $_GET['university_id'],
                ]
            );

            return $this->indexAction();
        }

        $universitiesData = $this->universities_repository->findAll();

        return $this->twig->render('departments_form.html.twig',
            [
                'id' => '',
                'name' => '',
                'university_id' => '',
                'action' => 'new',
                'button' => 'Create',
                'universities' => $universitiesData,
            ]
        );
    }

    /**
     * Show form for updating a department data and update the database.
     *
     * @return string
     */
    public function editAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->update(
                [
                    'name' => $_GET['name'],
                    'university_id' => $_GET['university_id'],
                    'id' => (int) $_GET['id'],
                ]
            );

            return $this->indexAction();
        }
        $departmentData = $this->repository->find((int) $_GET['id']);
        $universitiesData = $this->universities_repository->findAll();

        return $this->twig->render('departments_form.html.twig',
            [
                'id' => $_GET['id'],
                'name' => $departmentData['name'],
                'university_id' => $departmentData['university_id'],
                'action' => 'edit',
                'button' => 'Update',
                'universities' => $universitiesData,
            ]
        );
    }

    /**
     * Remove a department from the database.
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

        return $this->twig->render('departments_delete.html.twig', array('department_id' => $_GET['id']));
    }

    /**
     * Show form for searching by full name and/or university and/or city or their parts and show search results.
     *
     * @return string
     */
    public function searchAction()
    {
        if (isset($_GET['search_name'])) {
            $departmentsData = $this->repository->findBy(
                [
                    'search_name' => $_GET['search_name'],
                    'search_university' => $_GET['search_university'],
                ]
            );

            return $this->twig->render('departments_search.html.twig',
                [
                    'departments' => $departmentsData,
                    'search_name' => $_GET['search_name'],
                    'search_university' => $_GET['search_university'],
                ]
            );
        }

        return $this->twig->render('departments_search.html.twig',
            [
                'departments' => [],
                'search_name' => '',
                'search_university' => '',
            ]
        );
    }
}
