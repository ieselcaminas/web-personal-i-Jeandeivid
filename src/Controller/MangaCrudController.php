<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\MangaType;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/manga')]
class MangaCrudController extends AbstractController
{
    #[Route('/', name: 'app_manga_index', methods: ['GET'])]
    public function index(MangaRepository $mangaRepository): Response
    {
        return $this->render('manga_crud/index.html.twig', [
            'mangas' => $mangaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_manga_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $manga = new Manga();
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($manga);
            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index');
        }

        return $this->render('manga_crud/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_manga_show', methods: ['GET'])]
    public function show(Manga $manga): Response
    {
        return $this->render('manga_crud/show.html.twig', [
            'manga' => $manga,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_manga_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_manga_index');
        }

        return $this->render('manga_crud/edit.html.twig', [
            'form' => $form,
            'manga' => $manga,
        ]);
    }

    #[Route('/{id}', name: 'app_manga_delete', methods: ['POST'])]
    public function delete(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->request->get('_token'))) {
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_manga_index');
    }
}
