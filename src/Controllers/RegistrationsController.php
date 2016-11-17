<?php

namespace Controllers;

use Repositories\RegistrationsRepository;
use Repositories\SubjectsRepository;
use Repositories\StudentsRepository;

class RegistrationsController
{
    private $repository;
    private $subjects_repository;
    private $students_repository;
    private $loader;
    private $twig;

    /**
     * RegistrationsController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new RegistrationsRepository($connector);
        $this->subjects_repository = new SubjectsRepository($connector);
        $this->students_repository = new StudentsRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all registrations in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $registrationsData = $this->repository->findAll();

        return $this->twig->render('Registrations/registrations.html.twig',
            [
                'title' => 'Registrations',
                'registrations' => $registrationsData,
            ]);
    }

    /**
     * Show form for students registrations and add the new registrations to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['subject_id'])) {
            $registrations = $_GET['registration'];
            foreach ($registrations as $registration) {
                $this->repository->insert(
                    [
                        'student_id' => $registration,
                        'subject_id' => $_GET['subject_id'],
                    ]);
            }

            return $this->indexAction();
        }

        $subjectData = $this->subjects_repository->find((int) $_GET['id']);
        $studentsData = $this->students_repository->findBy(['search_department_id' => $subjectData['department_id']]);

        return $this->twig->render('Registrations/registrations_form.html.twig',
            [
                'subject_id' => $_GET['id'],
                'subject_name' => $subjectData['name'],
                'department_name' => $subjectData['department_name'],
                'students' => $studentsData,
            ]
        );
    }

    /**
     * Remove a registration from the database.
     *
     * @return string
     */
    public function deleteAction()
    {
        if (isset($_GET['submit'])) {
            if ($_GET['submit'] == 'Remove') {
                $student_id = (int) $_GET['student_id'];
                $subject_id = (int) $_GET['subject_id'];
                $this->repository->remove(['student_id' => $student_id, 'subject_id' => $subject_id]);
            }

            return $this->indexAction();
        }

        return $this->twig->render('Registrations/registration_delete.html.twig', array('student_id' => $_GET['student_id'], 'subject_id' => $_GET['subject_id']));
    }

    /**
     * Show form for searching by subject and show search results.
     *
     * @return string
     */
    public function searchAction()
    {
        $subjectData = $this->subjects_repository->find($_GET['search_subject_id']);
        $registrationsData = $this->repository->findBy(['subject_id' => $_GET['search_subject_id']]);

        return $this->twig->render('Registrations/registrations.html.twig',
            [
                'title' => 'Registrations for '.$subjectData['name'].' (id '.$subjectData['id'].')',
                'registrations' => $registrationsData,
            ]
        );
    }
}
