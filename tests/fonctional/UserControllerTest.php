<?php

namespace Tests\fonctional;

use App\Form\UserType;

final class UserControllerTest extends AbstractWebTestCase
{
    public function testIndexWithUserConnected(): void
    {
        $user = $this->createUserWithRole(['ROLE_ADMIN']);
        $this->client->loginUser($user);
        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
    }

    public function testIndexWithUserDisonnected(): void
    {
        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(302);
    }

    public function testCreateUserSubmitValidForm(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'nouveau_user',
            'user[password][first]' => 'password123',
            'user[password][second]' => 'password123',
            'user[email]' => 'test-create@example.com',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/users');
    }

    public function testCreateUserSubmitInvalidForm(): void
    {
        $user = $this->createUserWithRole(['ROLE_USER']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/users/create');
        $form = self::getContainer()->get('form.factory')->create(UserType::class);
        $form->submit([
            'user[username]' => 'nouveau_user',
            'user[password][first]' => 'password123',
            'user[password][second]' => 'password123',
            'user[email]' => 'test-create@example.com',
        ]);

        $this->assertFalse($form->isValid());
    }
}
