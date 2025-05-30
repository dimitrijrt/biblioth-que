<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BookRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/book')]
class BookController extends AbstractController

{
     #[IsGranted('IS_AUTHENTICATED')]
    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $repository): Response
    {

          $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('b')),
            $request->query->get('page', 1),
            20
        );
         
        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_book_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Book $book,Request $request, EntityManagerInterface $manager): Response
    {
        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($book);
            $manager->flush();

             return $this->redirectToRoute('app_admin_book_index');
            
        }

        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }


     #[Route('/{id}', name: 'app_admin_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render ('admin/book/show.html.twig', [
            'book' => $book,
        ]);

    }

    #[Route('/{id}', name: 'app_admin_book_delete', methods: ['POST'])]
    public function delete(Book $book, Request $request, EntityManagerInterface $manager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $manager->remove($book);
            $manager->flush();
        }

        return $this->redirectToRoute('app_admin_book_index');
    }
}