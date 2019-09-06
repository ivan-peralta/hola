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

class PageControllerTest extends WebTestCase
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
        $client = static::createClient();

        // Anomymous users should be redirected to login page by 302 code
        $crawler = $client->request('GET', '/page/1');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Login user with ADMIN role programatically, and visit page/1 returns 200
        $username = 'admin';
        $password = 'adminpassword';

        $user = $this->entityManager->getRepository("AppBundle:User")->findOneBy(array('username' => $username));
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $this->session->set('_security_main', serialize($token));
        $this->session->save();

        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $crawler = $this->client->request('GET', '/page/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Visit page/3, doesn't exists returns 404
        $crawler = $this->client->request('GET', '/page/3');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
