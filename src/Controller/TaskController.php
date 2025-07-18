<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    #[Route('/tasks', name: 'task_list')]
    public function listAction(): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_LIST);

        /** @var User $user */
        $user = $this->getUser();
        $tasks = $this->taskRepository->getTasksByUser($user);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_CREATE);
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_EDIT, $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_EDIT, $task);

        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été modifié.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_DELETE, $task);

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
