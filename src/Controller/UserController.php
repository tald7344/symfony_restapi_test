<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use App\Form\UserType;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * User Controller
 * @Route("/api")
 */
class UserController extends FOSRestController
{
    /**
     * Retrieve A Collection Of User Resource
     * @Rest\Get("/admin/users/{filter}")
     * @return Response
     */
    public function getUsersAction($filter = null)
    {
        $entityRepo = $this->getDoctrine()->getRepository(User::class);
        $users = is_null($filter) ? $entityRepo->findAll() : $entityRepo->findOneBy($filter);
        if ($users === null) {
            return $this->handleView($this->view('There Are No Users Exists'), Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($this->view($users, Response::HTTP_OK));
    }

    /**
     * Create New User
     * @Rest\Post("/admin/users")
     * @param Request $request
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setPassword($request->get('password'));
        $user->setEmail($request->get('email'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->handleView($this->view('', Response::HTTP_CREATED));
    }

    /**
     * Get User By ID
     * @Rest\Get("/admin/users/{userid}")
     * @param int $userid
     */
    public function getUserAction(int $userid)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
        if ($user) {
            return $this->handleView($this->view($user, Response::HTTP_OK));
        }
        return $this->handleView($this->view('User Not Found', Response::HTTP_NOT_FOUND));
    }

    /**
     * Replace User Resource
     * @Rest\Put("/admin/users/{userid}")
     * @param Request $request
     * @param int $userid
     */
    public function putUserAction(Request $request, int $userid)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
        if ($user) {
            $user->setUsername($request->get('username'));
            $user->setPassword($request->get('password'));
            $user->setEmail($request->get('email'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->handleView($this->view($user, Response::HTTP_OK));
        }
        return $this->handleView($this->view('User Not Found', Response::HTTP_NOT_FOUND));
    }

    /**
     * Remove the User Resourse
     * @Rest\Delete("/admin/users/{userid}")
     * @param int $userid
     */
    public function deleteUserAction(int $userid)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->handleView($this->view('Successfully Deleted', Response::HTTP_OK));
        }
        return $this->handleView($this->view('User Not Found', Response::HTTP_NOT_FOUND));    }
}