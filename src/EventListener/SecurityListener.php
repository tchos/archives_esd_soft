<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\Main\Utilisateur;

class SecurityListener implements EventSubscriberInterface
{
    private $em;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        // Si l'utilisateur est bien de type User
        if ($user instanceof Utilisateur) {
            $user->setDateDerniereConnexion(new \DateTime());

            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
