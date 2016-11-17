<?php

namespace Controllers;

use Repositories\ResultsRepository;
use Repositories\TasksRepository;
use Repositories\RegistrationsRepository;

class ResultsController
{
    private $repository;
    private $tasks_repository;
    private $registrations_repository;
    private $loader;
    private $twig;

    /**
     * ResultsController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new ResultsRepository($connector);
        $this->tasks_repository = new TasksRepository($connector);
        $this->registrations_repository = new RegistrationsRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show form for students results input and add the new results to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['subject_id'])) {
            $registrations = $this->registrations_repository->findBy(['subject_id' => $_GET['subject_id']]);
            foreach ($registrations as $registration) {
                $submission = isset($_GET[$registration['student_id']]) ? 1 : 0;

                $this->repository->insert(
                    [
                        'student_id' => $registration['student_id'],
                        'task_id' => $_GET['task_id'],
                        'submission' => $submission,
                    ]);
            }

            return $this->searchAction($_GET['task_id']);
        }

        $taskData = $this->tasks_repository->find((int) $_GET['id']);
        $subjectId = $taskData['subject_id'];
        $registrationsData = $this->registrations_repository->findBy(['subject_id' => $subjectId]);

        return $this->twig->render('Results/results_form.html.twig',
            [
                'task_id' => $_GET['id'],
                'task_name' => $taskData['name'],
                'subject_id' => $subjectId,
                'subject_name' => $taskData['subject_name'],
                'registrations' => $registrationsData,
            ]
        );
    }

    /**
     * Show form for updating results in the database.
     *
     * @return string
     */
    public function editAction()
    {
        $submission = abs($_GET['submission'] - 1);

        $this->repository->update(
            [
                'student_id' => $_GET['student_id'],
                'task_id' => $_GET['task_id'],
                'submission' => $submission,
            ]
        );

        return $this->searchAction($_GET['task_id']);
    }

    /**
     * Search students results by task id and show search results.
     *
     * @return string
     */
    public function searchAction($taskId)
    {
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }

        $taskData = $this->tasks_repository->find((int) $taskId);
        $submissionsData = $this->repository->findBy($taskId);

        return $this->twig->render('Results/results.html.twig',
            [
                'task_id' => $taskId,
                'task_name' => $taskData['name'],
                'results' => $submissionsData,
            ]
        );
    }
}
