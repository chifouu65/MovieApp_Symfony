<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
class MoviesController extends AbstractController
{
    private MovieRepository $movieRepository;
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }
    #[Route('/movies', name: 'get-movies', methods: ['GET'])]
    public function index(): Response
    {
        // findAll() - SELECT * FROM movies ;
        // find() - SELECT * FROM movies WHERE id = ? ;
        // findBy() - SELECT * FROM movies WHERE title = ? ;
        // findOneBy() - SELECT * FROM movies WHERE title = ? LIMIT 1 ;

        $movies = $this->movieRepository->findAll();

        return $this->render('movies/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movies/create', name: 'create-movie')]
    public function create(\Symfony\Component\HttpFoundation\Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie,);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/',
                        $newFileName
                    );
                } catch (\Exception $e) {
                    return new Response($e->getMessage());
                }
                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('get-movies');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movies/{id}', name: 'get-movie', methods: ['GET'])]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);

        return $this->render('movies/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/movies/delete/{id}', name: 'delete-movie')]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);

        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirectToRoute('get-movies');
    }

    #[Route('/movies/edit/{id}', name: 'edit-movie')]
    public function edit(\Symfony\Component\HttpFoundation\Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie,);

        $form->handleRequest($request);

        return $this->render('movies/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
