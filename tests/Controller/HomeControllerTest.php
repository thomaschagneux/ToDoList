<?php

namespace Tests\Controller;

class HomeControllerTest extends AbstractWebTestCase
{
    public function testHomepageLoadSuccessfully(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
