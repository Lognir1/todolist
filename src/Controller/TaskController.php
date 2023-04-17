<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/{todolist_id}', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository,TodoListRepository $todoListRepository,int $todolist_id): Response
    {
        $todolist = $todoListRepository->find($todolist_id);
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findByTodoList($todolist),
            'todolist' => $todolist
        ]);
    }

    #[Route('/new/{todolist_id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskRepository $taskRepository, TodoListRepository $todolistRepository ,int $todolist_id): Response
    {
        $task = new Task();

        $todolist = $todolistRepository->find($todolist_id);
        $task->setTodoList($todolist);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', ['todolist_id'=> $todolist_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'todolist' => $todolist
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', ['todolist_id'=> $task->getTodoList()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
            'todolist' =>  $task->getTodoList()
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
