<?php

namespace Controllers;

use Repositories\StudentsRepository;
use Repositories\DepartmentsRepository;
use Repositories\UniversitiesRepository;

class StudentsController
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
        $this->repository = new StudentsRepository($connector);
        $this->departments_repository = new DepartmentsRepository($connector);
        $this->universities_repository = new UniversitiesRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all students in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $studentsData = $this->repository->findAll();

        return $this->twig->render('students.html.twig', ['students' => $studentsData]);
    }

    /**
     * Show form for adding a new student and add the new student to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['first_name'])) {
            $this->repository->insert(
                [
                    'first_name' => $_GET['first_name'],
                    'last_name' => $_GET['last_name'],
                    'email' => $_GET['email'],
                    'tel' => $_GET['tel'],
                    'department_id' => $_GET['department_id'],
                ]
            );
            echo $_GET['first_name'], $_GET['last_name'], $_GET['department_id'];

            return $this->indexAction();
        }

        $departmentsData = $this->departments_repository->findAll();

        return $this->twig->render('students_form.html.twig',
            [
                'id' => '',
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'tel' => '',
                'department_id' => '',
                'action' => 'new',
                'button' => 'Create',
                'departments' => $departmentsData,
            ]
        );
    }

    /**
     * Show form for updating a student data and update the database.
     *
     * @return string
     */
    public function editAction()
    {
        if (isset($_GET['first_name'])) {
            $this->repository->update(
                [
                    'first_name' => $_GET['first_name'],
                    'last_name' => $_GET['last_name'],
                    'email' => $_GET['email'],
                    'tel' => $_GET['tel'],
                    'department_id' => $_GET['department_id'],
                    'id' => (int) $_GET['id'],
                ]
            );

            return $this->indexAction();
        }
        $studentData = $this->repository->find((int) $_GET['id']);
        $departmentsData = $this->departments_repository->findAll();

        return $this->twig->render('students_form.html.twig',
            [
                'id' => $_GET['id'],
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'email' => $studentData['email'],
                'tel' => $studentData['tel'],
                'department_id' => $studentData['department_id'],
                'action' => 'edit',
                'button' => 'Update',
                'departments' => $departmentsData,
            ]
        );
    }

    /**
     * Remove a student from the database.
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

        return $this->twig->render('delete.html.twig', array('item_name' => 'student', 'id' => $_GET['id']));
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
        if (isset($_GET['search_first_name'])) {
            $studentsData = $this->repository->findBy(
                [
                    'search_first_name' => $_GET['search_first_name'],
                    'search_last_name' => $_GET['search_last_name'],
                    'search_department_id' => $_GET['search_department_id'],
                    'search_university_id' => $_GET['search_university_id'],
                ]
            );

            return $this->twig->render('students_search.html.twig',
                [
                    'students' => $studentsData,
                    'departments' => $departmentsData,
                    'universities' => $universitiesData,
                    'search_first_name' => $_GET['search_first_name'],
                    'search_last_name' => $_GET['search_last_name'],
                    'search_department_id' => $_GET['search_department_id'],
                    'search_university_id' => $_GET['search_university_id'],
                ]
            );
        }

        return $this->twig->render('students_search.html.twig',
            [
                'students' => [],
                'departments' => $departmentsData,
                'universities' => $universitiesData,
                'search_first_name' => '',
                'search_last_name' => '',
                'search_department_id' => '',
                'search_university_id' => '',
            ]
        );
    }
}
