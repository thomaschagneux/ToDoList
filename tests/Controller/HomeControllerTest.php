<?php

namespace Tests\Controller;

class HomeControllerTest extends AbstractWebTestCase
{
    public function testHomepageLoadSuccessfully(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
