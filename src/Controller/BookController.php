<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    // ✅ Liste des livres
    #[Route('/', name: 'book_index')]
    public function index(BookRepository $repo): Response
    {
        $books = $repo->findBy(['enabled' => true]);

        $countPublished = $repo->count(['enabled' => true]);
        $countUnpublished = $repo->count(['enabled' => false]);

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'countPublished' => $countPublished,
            'countUnpublished' => $countUnpublished,
        ]);
    }

    // ✅ Ajouter un nouveau livre
    #[Route('/new', name: 'book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setEnabled(true); // par défaut publié

            // Incrémenter nb_books de l’auteur
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }

            $em->persist($book);
            $em->flush();

            // ✅ redirection vers la page du livre créé
            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ✅ Modifier un livre
    #[Route('/{id}/edit', name: 'book_edit')]
    public function edit(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // ✅ redirection vers la liste après modification
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    // ✅ Supprimer un livre
    #[Route('/{id}/delete', name: 'book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();

        $em->remove($book);
        $em->flush();

        // Décrémenter le nb_books
        if ($author) {
            $author->setNbBooks($author->getNbBooks() - 1);
            $em->flush();

            // Supprimer l’auteur si nb_books = 0
            if ($author->getNbBooks() === 0) {
                $em->remove($author);
                $em->flush();
            }
        }

        // ✅ redirection vers la liste
        return $this->redirectToRoute('book_index');
    }

    // ✅ Afficher un livre
    #[Route('/{id}', name: 'book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
