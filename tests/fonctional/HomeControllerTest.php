<?php

namespace Tests\fonctional;

class HomeControllerTest extends AbstractWebTestCase
{
    public function testHomepageLoadSuccessfully(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
