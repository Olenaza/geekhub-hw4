<?php

namespace Controllers;

use Repositories\TasksRepository;
use Repositories\SubjectsRepository;

class TasksController
{
    private $repository;
    private $subjects_repository;
    private $loader;
    private $twig;

    /**
     * TasksController constructor.
     *
     * @param $connector
     */
    public function __construct($connector)
    {
        $this->repository = new TasksRepository($connector);
        $this->subjects_repository = new SubjectsRepository($connector);
        $this->loader = new \Twig_Loader_Filesystem('src/Views/Templates/');
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => false,
        ));
    }

    /**
     * Show all tasks in the database.
     *
     * @return string
     */
    public function indexAction()
    {
        $tasksData = $this->repository->findAll();

        return $this->twig->render('Tasks/tasks.html.twig', ['tasks' => $tasksData]);
    }

    /**
     * Show form for adding a new task and add the new task to the database.
     *
     * @return string
     */
    public function newAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->insert(
                [
                    'name' => $_GET['name'],
                    'subject_id' => $_GET['subject_id'],
                ]
            );

            return $this->indexAction();
        }

        $subjectsData = $this->subjects_repository->findAll();

        return $this->twig->render('Tasks/tasks_form.html.twig',
            [
                'id' => '',
                'name' => '',
                'subject_id' => '',
                'action' => 'new',
                'button' => 'Create',
                'subjects' => $subjectsData,
            ]
        );
    }

    /**
     * Show form for updating a task data and update the database.
     *
     * @return string
     */
    public function editAction()
    {
        if (isset($_GET['name'])) {
            $this->repository->update(
                [
                    'name' => $_GET['name'],
                    'subject_id' => $_GET['subject_id'],
                    'id' => (int) $_GET['id'],
                ]
            );

            return $this->indexAction();
        }
        $taskData = $this->repository->find((int) $_GET['id']);
        $subjectsData = $this->subjects_repository->findAll();

        return $this->twig->render('Tasks/tasks_form.html.twig',
            [
                'id' => $_GET['id'],
                'name' => $taskData['name'],
                'subject_id' => $taskData['subject_id'],
                'action' => 'edit',
                'button' => 'Update',
                'subjects' => $subjectsData,
            ]
        );
    }

    /**
     * Remove a task from the database.
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

        return $this->twig->render('delete.html.twig', array('item_name' => 'task', 'id' => $_GET['id']));
    }

    /**
     * Show form for searching by full name or its parts and/or by subject
     * and show search results.
     *
     * @return string
     */
    public function searchAction()
    {
        $subjectsData = $this->subjects_repository->findAll();
        if (isset($_GET['search_name'])) {
            $tasksData = $this->repository->findBy(
                [
                    'search_name' => $_GET['search_name'],
                    'search_subject_id' => $_GET['search_subject_id'],
                ]
            );

            return $this->twig->render('Tasks/tasks_search.html.twig',
                [
                    'tasks' => $tasksData,
                    'subjects' => $subjectsData,
                    'search_name' => $_GET['search_name'],
                    'search_subject_id' => $_GET['search_subject_id'],
                ]
            );
        }

        return $this->twig->render('Tasks/tasks_search.html.twig',
            [
                'tasks' => [],
                'subjects' => $subjectsData,
                'search_name' => '',
                'search_subject_id' => '',
            ]
        );
    }
}
