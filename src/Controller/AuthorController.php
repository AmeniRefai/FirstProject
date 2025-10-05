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

    if ($form->isSubmitted() && $form->isValid()) { // Si le formulaire est soumis et valide
        //ajout en base de données
        $em = $doctrine->getManager(); // Récupération de l'EntityManager
        $em->persist($author); // Préparation de l'insertion (équivalent de INSERT INTO)
        $em->flush();// Exécution de l'insertion 

        // Redirection vers la liste après ajout
        return $this->redirectToRoute('list_authors');
    }
// Affichage du formulaire
    return $this->render('author/add.html.twig', [
        'form' => $form->createView(),
    ]);
}
// Modifier un auteur via un formulaire
#[Route('/authors/edit/{id}', name: 'modifier_auteur')]
public function editAuthor(ManagerRegistry $doctrine, Request $request, $id): Response
{
    $em = $doctrine->getManager();
    $author = $em->getRepository(Author::class)->find($id);
    // Vérification de l'existence de l'auteur
    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé'); // Gérer le cas où l'auteur n'existe pas
    }

    $form = $this->createForm(AuthorType::class, $author); // Création du formulaire avec les données de l'auteur existant
    $form->handleRequest($request); 

    if ($form->isSubmitted() && $form->isValid()) { // Si le formulaire est soumis et valide
        // Mise à jour en base de données
        $em->flush(); // Exécution de la mise à jour
        return $this->redirectToRoute('list_authors');
    }

    return $this->render('author/edit.html.twig', [
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
