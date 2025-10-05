<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AuthorType;
use Symfony\Component\HttpFoundation\Request;



class AuthorController extends AbstractController
{
    #[Route('/authors', name: 'list_authors')]
    public function listAuthors(ManagerRegistry $doctrine): Response
    {
        $authors = $doctrine->getRepository(Author::class)->findAll();

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }


// Ajouter un auteur via un formulaire
#[Route('/authors/add_author_form', name: 'ajouter_auteur_formulaire')]
public function addAuthorForm(Request $request, ManagerRegistry $doctrine): Response
{
    $author = new Author();
    $form = $this->createForm(AuthorType::class, $author);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();

        // Redirection vers la liste après ajout
        return $this->redirectToRoute('list_authors');
    }

    return $this->render('author/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

//  les détails d'un auteur

    #[Route('/author/{id}', name: 'author_details')]
    public function authorDetails(int $id): Response
    {
        $authors = array(
            1 => array(
                'id' => 1,
                'picture' => '/images/Victor-Hugo.jpg',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100
            ),
            2 => array(
                'id' => 2,
                'picture' => '/images/william-shakespeare.jpg',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200
            ),
            3 => array(
                'id' => 3,
                'picture' => '/images/Taha_Hussein.jpg',
                'username' => 'Taha Hussein',
                'email' => 'taha.hussein@gmail.com',
                'nb_books' => 300
            ),
        );

        $author = $authors[$id] ?? null;

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author
        ]);
    }
}
