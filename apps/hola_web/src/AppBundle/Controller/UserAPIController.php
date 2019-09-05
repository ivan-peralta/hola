<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserAPIController extends Controller
{
    /**
     * @Route("/api/1.0/create", methods={"POST"})
     */
    public function create(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/api/1.0/retrieve", methods={"GET"})
     */
    public function retrieve(Request $request, $id)
    {
        return new Response(
            '<html><body>Lucky number: '. $id . '</body></html>'
        );
    }

}
