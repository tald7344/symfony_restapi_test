<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Movie;
use App\Form\MovieType;

/**
 * Movie Controller
 * @Route("/api", name="api_")
 */
class MovieController extends FOSRestController
{
    /**
     * Lists all Movies.
     * @Rest\Get("/movies")
     *
     * @return Response
     */
    public function getMovieAction()
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $repository->findall();
        return $this->handleView($this->view($movies));
    }

    /**
     * Create Movie
     * @Rest\Post("/movie")
     * @return Response
     */
    public function postMovieAction(Request $request)
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $data = json_decode($request->getContent(), true);
        $form->submit($data); // Submitting the Form
        // check iF Submitted And Validate 
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }
}