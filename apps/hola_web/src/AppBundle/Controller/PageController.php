<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{

    private function getName()
    {
        return $this->getUser()->getName();
    }

    /**
     * @Route("/page/1", methods={"GET"})
     */
    public function page1(Request $request)
    {
        return $this->render('custom_page.html.twig', ['number' => 1, 'name' => $this->getName()]);
    }

    /**
     * @Route("/page/2", methods={"GET"})
     */
    public function page2(Request $request)
    {
        return $this->render('custom_page.html.twig', ['number' => 2, 'name' => $this->getName()]);
    }
}
