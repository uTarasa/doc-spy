<?php

namespace App\Listener;

use App\Entity\User;
use App\Event\SourceParsingErrorEvent;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SourceParsingErrorListener
{
    private const NOTIFY_COUNTS = [5, 10];

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var RegistryInterface
     */
    private $entityManager;

    public function __construct(NotificationService $notificationService, RegistryInterface $entityManager)
    {
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
    }

    public function onSourceParsingError(SourceParsingErrorEvent $event){

        $source = $event->getSource();

        if(!in_array($source->getErrorCount(), self::NOTIFY_COUNTS)){
            return;
        }

        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        $subscribers = $repository->findAdmins();
        foreach($subscribers as $subscriber){
            $this->notificationService->send(
                $subscriber,
                'Source parsing error!',
                'mail/source_error.html.twig',
                [
                    'source' => $source,
                    'message' => $event->getMessage(),
                ]
            );
        }
    }
}