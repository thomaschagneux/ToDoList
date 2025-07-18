<?php

namespace Tests\fonctional;

use App\Entity\Task;
use App\Form\TaskType;

final class TaskControllerTest extends AbstractWebTestCase
{
    public function testListWithConnectedUser(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    public function testListWithDisconnectedUser(): void
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login');
    }

    public function testCreateTaskWithValidData(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de test',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
    }

    public function testCreateTaskWithInvalidData(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = self::getContainer()->get('form.factory')->create(TaskType::class);
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'titre exemple',
            'task[content]' => 'contenu exemple',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
    }

    public function testEditTask(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $task = $this->createTaskForUser($user);

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Titre modifié',
            'task[content]' => 'Contenu modifié',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
    }

    public function testToggleTask(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $task = $this->createTaskForUser($user, false);

        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $this->assertResponseRedirects('/tasks');
    }

    public function testDeleteTask(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $task = $this->createTaskForUser($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertResponseRedirects('/tasks');
    }

    private function createTaskForUser($user, bool $isDone = false): Task
    {
        $task = new Task();
        $task->setTitle('Tâche de test')
            ->setContent('Contenu')
            ->setIsDone($isDone)
            ->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }
}
