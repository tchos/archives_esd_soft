<?php

namespace App\Controller;


use App\Entity\Main\Utilisateur;
use App\Entity\Main\Historique;
use App\Form\RegistrationFormType;
use App\Repository\Main\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class RegistrationController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword))
                ->setEnableYN(true)
                ->isPasswordModified(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/listusers', name: 'user_list')]
    public function index(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render('registration/index.html.twig', [
            'users' => $utilisateurRepository->findAll(),
        ]);
    }

    // Réinitialisation du mot de passe utilisteur
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/{id}/resetpassword', name: 'user_resetpassword')]
    public function resetPassword(EntityManagerInterface $entityManager, Request $request, Utilisateur $utilisateur,
                                  UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // On capte le user connecté
        $user = $this->getUser();
        // pour l'historisation de l'action
        $history = new Historique();

        $plainPassword = 'aaaaabbbbb';
        $hashedPassword = $userPasswordHasher->hashPassword($utilisateur, $plainPassword);
        $utilisateur->setPassword($hashedPassword);

        $history->setTypeAction("RESET")
            ->setAuteur($user->getUsername())
            ->setNature("PASSWORD")
            ->setClef($utilisateur->getUsername())
            ->setDateAction(new \DateTimeImmutable())
        ;

        $entityManager->persist($utilisateur);
        $entityManager->persist($history);
        $entityManager->flush();

        // Alerte succès de la mise à jour des informations sur un organisme
        $this->addFlash("warning", "Le mot de passe a été réinitialisé avec succès !");

        return $this->redirectToRoute('user_list');
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(EntityManagerInterface $manager, Request $request, Utilisateur $user): Response
    {
        // pour l'historique de l'action
        $history = new Historique();
        $user->setEnableYN(false);

        $history->setTypeAction("DELETE")
            ->setAuteur($this->getUser()->getUsername())
            ->setNature("COMPTE_USER")
            ->setClef($form->get('username')->getData())
            ->setDateAction(new \DateTimeImmutable())
        ;
        // Persistence de l'entité Organismes
        $manager->persist($user);
        $manager->persist($history);
        $manager->flush();

        // Alerte succès de la mise à jour des informations sur un organisme
        $this->addFlash("danger", "Utilisateur supprimé avec succès !");

        return $this->redirectToRoute('user_list');
    }

    // Modification du profil
    #[IsGranted('ROLE_USER')]
    #[Route('/user/{id}/edit', name: 'user_edit')]
    public function edit(EntityManagerInterface $manager, Request $request, Utilisateur $user): Response
    {
        // pour l'historisation de l'action
        $history = new Historique();

        // constructeur de formulaire de saisie des actes de décès
        $form = $this->createForm(RegistrationFormType::class, $user);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        /**
         * Ayant extrait les infos saisies dans le formulaire,
         * on vérifie que le formulaire a été soumis et qu'il est valide
         */
        if($form->isSubmitted() && $form->isValid())
        {
            dd("Bonjour");
            $history->setTypeAction("UPDATE")
                ->setAuteur($this->getUser()->getUsername())
                ->setNature("COMPTE_USER")
                ->setClef($form->get('username')->getData())
                ->setDateAction(new \DateTimeImmutable())
            ;
            // Persistence de l'entité Organismes
            $manager->persist($user);
            $manager->persist($history);
            $manager->flush();

            // Alerte succès de la mise à jour des informations sur un organisme
            $this->addFlash("warning", "Utilisateur modifié avec succès !");

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('registration/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
