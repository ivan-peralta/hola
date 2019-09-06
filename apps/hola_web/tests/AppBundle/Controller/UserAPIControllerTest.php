<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Cookie;

class UserAPIControllerTest extends WebTestCase
{
        
    protected static $application;

    protected $client;
    
    protected $container;

    protected $entityManager;

    protected $tokenStorage;

    protected $session;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->tokenStorage = $this->container->get('security.token_storage');
        $this->session = $this->container->get('session');

        parent::setUp();
    }
    
    public function testIndex()
    {
        $client_noauth = static::createClient();
        $client_auth = static::createClient([], [
            'PHP_AUTH_USER' => 'hola',
            'PHP_AUTH_PW'   => 'hola',
        ]);
        
        // Check user 1 is Admin without basic auth returns Forbidden 403
        $crawler = $client_noauth->request('GET', '/api/1.0/retrieve/1');
        $this->assertEquals(403, $client_noauth->getResponse()->getStatusCode());

        // Check user 1 is Admin without basic auth returns 200
        $crawler = $client_auth->request('GET', '/api/1.0/retrieve/1');
        $this->assertEquals(200, $client_auth->getResponse()->getStatusCode());

        // Test POST, PUT, DELETE without basic authentication returns 403
        $crawler = $client_noauth->request('POST', '/api/1.0/create');
        $this->assertEquals(403, $client_noauth->getResponse()->getStatusCode());
        
        $crawler = $client_noauth->request('PUT', '/api/1.0/update/3');
        $this->assertEquals(403, $client_noauth->getResponse()->getStatusCode());

        // I've created new user with id 4, and then delete. If that id doesn't exist should return 404
        $crawler = $client_auth->request('delete', '/api/1.0/delete/4');
        $this->assertEquals(200, $client_auth->getResponse()->getStatusCode());
        $crawler = $client_auth->request('delete', '/api/1.0/delete/5');
        $this->assertEquals(404, $client_auth->getResponse()->getStatusCode());
    }
}
