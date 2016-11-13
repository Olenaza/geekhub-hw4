<?php

namespace Controllers;

use Repositories\SubjectsRepository;
use Repositories\DepartmentsRepository;
use Repositories\UniversitiesRepository;

class SubjectsController
{
    private $repository;
    private $departments_repository;
    private $universities_repository;
    private $loader;
    private $twig;

    /**
     * StudentsController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new SubjectsRepository($connector);
        $this->departments_repository = new DepartmentsRepository($connector);
        $this->universities_repository = new UniversitiesRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all subjects in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $subjectsData = $this->repository->findAll();

        return $this->twig->render('subjects.html.twig', ['subjects' => $subjectsData]);
    }

    /**
     * Show form for adding a new subject and add the new subject to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->insert(
                [
                    'name' => $_GET['name'],
                    'department_id' => $_GET['department_id'],
                ]
            );

            return $this->indexAction();
        }

        $departmentsData = $this->departments_repository->findAll();

        return $this->twig->render('subjects_form.html.twig',
            [
                'id' => '',
                'name' => '',
                'department_id' => '',
                'action' => 'new',
                'button' => 'Create',
                'departments' => $departmentsData,
            ]
        );
    }

    /**
     * Show form for updating a subject data and update the database.
     *
     * @return string
     */
    public function editAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->update(
                [
                    'name' => $_GET['name'],
                    'department_id' => $_GET['department_id'],
                    'id' => (int) $_GET['id'],
                ]
            );

            return $this->indexAction();
        }
        $subjectData = $this->repository->find((int) $_GET['id']);
        $departmentsData = $this->departments_repository->findAll();

        return $this->twig->render('subjects_form.html.twig',
            [
                'id' => $_GET['id'],
                'name' => $subjectData['name'],
                'department_id' => $subjectData['department_id'],
                'action' => 'edit',
                'button' => 'Update',
                'departments' => $departmentsData,
            ]
        );
    }

    /**
     * Remove a subject from the database.
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

        return $this->twig->render('delete.html.twig', array('item_name' => 'subject', 'id' => $_GET['id']));
    }

    /**
     * Show form for searching by full name or its parts and/or by department and/or by university
     * and show search results.
     *
     * @return string
     */
    public function searchAction()
    {
        $departmentsData = $this->departments_repository->findAll();
        $universitiesData = $this->universities_repository->findAll();
        if (isset($_GET['search_name'])) {
            $subjectsData = $this->repository->findBy(
                [
                    'search_name' => $_GET['search_name'],
                    'search_department_id' => $_GET['search_department_id'],
                    'search_university_id' => $_GET['search_university_id'],
                ]
            );

            return $this->twig->render('subjects_search.html.twig',
                [
                    'subjects' => $subjectsData,
                    'departments' => $departmentsData,
                    'universities' => $universitiesData,
                    'search_name' => $_GET['search_name'],
                    'search_department_id' => $_GET['search_department_id'],
                    'search_university_id' => $_GET['search_university_id'],
                ]
            );
        }

        return $this->twig->render('subjects_search.html.twig',
            [
                'subjects' => [],
                'departments' => $departmentsData,
                'universities' => $universitiesData,
                'search_name' => '',
                'search_department_id' => '',
                'search_university_id' => '',
            ]
        );
    }
}
