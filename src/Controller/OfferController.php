<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SouscriptionRepository;
use App\Entity\Souscription;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/offer")
 */
class OfferController extends AbstractController
{
   
    /**
     * @Route("/liste", name="offer_public", methods={"GET"})
     */
    public function indexPublic(OfferRepository $offerRepository): Response
    {
        return $this->render('offer/public.html.twig', [
            'offers' => $offerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="offer_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
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


                $offer->setImageOffre($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('offer_public');
        }

        return $this->render('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offer_show", methods={"GET"})
     */
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    /**
     * @Route("/aperçu/{id}", name="offer_showpublic", methods={"GET"})
     */
    public function showPublic(Offer $offer, OfferRepository $offerRepository): Response
    {
        return $this->render('offer/showpublic.html.twig', [
            'offer' => $offer,
            'offers' => $offerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="offer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offer $offer, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
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

                $offer->setImageOffre($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('offer_public');
        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Offer $offer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offer_index');
    }

    /**
     * @Route("souscription/{id}", name="subscribe_to_offer", methods={"GET","POST"})
     */
    public function subscribeToOffer(Offer $offer, SouscriptionRepository $souscriptionRepository)
    {

        $user = $this->getUser();

        $souscriptionListe = $souscriptionRepository->findAll();

        if ($user) {

            if (($user->getTelephone() && $user->getNumSecu() && $user->getVille() && $user->getCodePostal() && $user->getPays()) != null) {

                $verif = false;
                foreach ($souscriptionListe as $souscriptions) {
                    if (($user->getId() == $souscriptions->getRelationUsSo()->getId()) && ($offer->getId() == $souscriptions->getRelation()->getId())) {
                        $this->addFlash(
                            'success',
                            'Vous ne pouvez pas souscrire deux fois à la même offre !'
                        );

                        $verif = true;
                        return $this->redirectToRoute('offer_showpublic', ['id' => $offer->getId()]);
                        break;
                    }
                }

                if (!$verif) {
                    $souscrip = new Souscription($user, $offer);
                    $entityManager = $this->getDoctrine()->getManager();

                    $user->addRelation($souscrip);

                    $offer->addRelationSoOf($souscrip);


                    $entityManager->persist($user);
                    $entityManager->persist($offer);
                    $entityManager->persist($souscrip);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Votre demande de souscription a bien été prise en compte et sera traitée sous peu. Merci.'
                    );
                    return $this->redirectToRoute('offer_showpublic', ['id' => $offer->getId()]);
                }
            } else {
                $this->addFlash('success', 'Vous devez saisir les informations obligatoires : N° de téléphone - Code Postal - N° sécurité sociale');

                return $this->redirectToRoute('offer_showpublic', ['id' => $offer->getId()]);
            }
        } else {
            $this->addFlash(
                'success',
                'Vous devez vous connecter afin de souscrire à une offre'
            );

            return $this->redirectToRoute('app_login');
        }
    }
}
