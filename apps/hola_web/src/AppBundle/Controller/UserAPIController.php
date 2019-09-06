<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class UserAPIController extends AbstractFOSRestController
{

    /**
     * @Rest\Post("/api/1.0/create")
     */
    public function postAction(Request $request)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $accesible = $this->checkBasicAuth($authorizationHeader);
        if (!$accesible) {
            return new View('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $data = new User;
        $name = $request->get('name');
        $username = $request->get('username');
        $password = $request->get('password');
        $roles = $request->get('roles');
        
        if (empty($name) || empty($username) || empty($password) || empty($password)) {
            return new View('NULL VALUES ARE NOT ALLOWED', Response::HTTP_NOT_ACCEPTABLE);
        }

        $data->setName($name);
        $data->setUsername($username);
        $data->setPassword($password);
        $data->setRoles($roles);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("User Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/1.0/retrieve/{id}")
     */
    public function retrieve(Request $request, $id)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $accesible = $this->checkBasicAuth($authorizationHeader);
        if (!$accesible) {
            return new View('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (!$user) {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

    /**
     * @Rest\Put("/api/1.0/update/{id}")
     */
    public function updateAction(Request $request, $id)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $accesible = $this->checkBasicAuth($authorizationHeader);
        if (!$accesible) {
            return new View('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $name = $request->get('name');
        $username = $request->get('username');
        $password = $request->get('password');
        $roles = $request->get('roles');
        $updated = 0;

        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        }
       
        if (!empty($name)) {
            $user->setName($name);
            $updated = 1;
        }
        if (!empty($username)) {
            $user->setUsername($username);
            $updated = 1;
        }
        if (!empty($password)) {
            $user->setPassword($password);
            $updated = 1;
        }
        if (!empty($roles)) {
            $user->setRoles($roles);
            $updated = 1;
        }

        if ($updated) {
            $sn->flush();
            return new View('Updated user', Response::HTTP_OK);
        } else {
            return new View('Empty data', Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
    * @Rest\Delete("/api/1.0/delete/{id}")
    */
    public function deleteAction(Request $request, $id)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $accesible = $this->checkBasicAuth($authorizationHeader);
        if (!$accesible) {
            return new View('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $data = new User;
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($user);
            $sn->flush();
        }
        return new View('User deleted', Response::HTTP_OK);
    }

    private function checkBasicAuth($externalAuth)
    {
        $internalAuth = 'Basic ' .base64_encode('hola:hola');
        return $internalAuth == $externalAuth ? true : false;
    }
}
