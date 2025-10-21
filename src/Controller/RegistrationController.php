<?php

namespace App\Controller;


use App\Entity\Main\Utilisateur;
use App\Entity\Main\Historique;
use App\Form\RegistrationFormType;
use App\Form\UpdatePasswordType;
use App\Form\UpdateProfilType;
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
    #[Route('/user/register', name: 'user_register')]
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
        // pour l'historisation de l'action
        $history = new Historique();

        $plainPassword = 'aaaaabbbbb';
        $hashedPassword = $userPasswordHasher->hashPassword($utilisateur, $plainPassword);
        $utilisateur->setPassword($hashedPassword);

        $history->setTypeAction("RESET")
            ->setAuteur($this->getUser()->getUsername())
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

        if($user->isEnableYN() === false){
            $user->setEnableYN(true);
            $history->setTypeAction("ACTIVER");
        } else {
            $user->setEnableYN(false);
            $history->setTypeAction("DESACTIVER");
        }

        $history->setAuteur($this->getUser()->getUsername())
            ->setNature("COMPTE_USER")
            ->setClef($user->getUsername())
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
        $form = $this->createForm(UpdateProfilType::class, $user);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        /**
         * Ayant extrait les infos saisies dans le formulaire,
         * on vérifie que le formulaire a été soumis et qu'il est valide
         */
        if($form->isSubmitted() && $form->isValid())
        {
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

            if ($this->getUser()->getUsername() !== $user->getUsername()) {
                return $this->redirectToRoute('user_list');
            }
            return $this->redirectToRoute('app_logout');
        }

        return $this->render('registration/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[IsGranted('ROLE_USER')]
    //Mise à jour du mot de passe
    #[Route('/user/changepassword', name: 'user_password_edit')]
    public function changePassword(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager,
                                   Request $request): Response
    {
        $user = $this->getUser();
        $history = new Historique();

        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();

            // Si l'ancien mot de passe est le bon
            if($userPasswordHasher->isPasswordValid($user, $old_password))
            {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                // On enregistre en BD l'action et celui qui l'a exécuté.
                $history->setTypeAction("UPDATE")
                    ->setAuteur($this->getUser()->getUsername())
                    ->setNature("PASSWORD")
                    ->setClef($form->get('username')->getData())
                    ->setDateAction(new \DateTimeImmutable())
                ;

                $manager->persist($user);
                $manager->persist($history);
                $manager->flush();

                // Notification du mot de passe modifié
                $this->addFlash("success", "Mot de passe modifié avec succès !!!");

                // Redirection vers la page de connexion
                return $this->redirectToRoute('app_logout');
            }else{
                // Notification du mot de passe modifié
                $this->addFlash("danger", "Votre ancien mot de passe n'est pas valide !!!");
            }
        }

        return $this->render('registration/change_pwd.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
