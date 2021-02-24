<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserAdminType;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/mon-espace-client")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request,  SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFichier */
            $imageFichier = $form->get('image')->getData();

            if ($imageFichier) {
                $originalFilename = pathinfo($imageFichier->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFichier->guessExtension();

                try {
                    $imageFichier->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }


                $user->setImageUser($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

/**
 * @Route("", name="user_home", methods={"GET","POST"})
 */
public function index(UserRepository $userRepository, Request $request): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser(); 

    // Créer un formulaire lié à ce utilisateur
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->getDoctrine()->getManager()->flush();

    }

    return $this->render('espace-client/layout.html.twig', [
        'form' => $form->createView()
    ]);
}

    /**
     * @Route("/admin/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFichier */
            $imageFichier = $form->get('image')->getData();

            if ($imageFichier) {
                $originalFilename = pathinfo($imageFichier->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFichier->guessExtension();

                try {
                    $imageFichier->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $user->setImageUser($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_compte');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/moncompte/{id}", name="user_compte", methods={"GET"})
     */
    public function compte(User $user): Response
    {
        return $this->render('user/compte.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/moncompte/{id}/edit", name="compte_edit", methods={"GET","POST"})
     */
    public function editCompte(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_compte',['id'=> $user->getId()]);
        }

        return $this->render('user/editcompte.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-souscriptions/{id}", name="user_souscriptions")
     */
    public function souscription(User $user) : Response
    {
        $user = $this->getUser();
        $user->getRelation();

        return $this->render('user/mysouscription.html.twig', [
            'user' => $user->getRelation(),
        ]);
    }


}
