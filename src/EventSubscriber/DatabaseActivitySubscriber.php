<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Symfony\Component\HttpKernel\KernelInterface;


class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    private $rootDir;

    public function __construct(KernelInterface $appKernel)
    {
        $this->rootDir = $appKernel->getProjectDir();
    }
    
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove
        ];
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->logActivity('remove', $args->getObject());
    }

    public function logActivity(string $action, mixed $entity): void
    {
        if (($entity instanceof Product) && $action === 'remove') {
            $filenames = $entity->getImageUrls();
            foreach($filenames as $filename) {
                $fileLink = $this->rootDir . '/public/assets/images/products/' . $filename;
                $this->deleteImage($fileLink);
            }
        }

        if (($entity instanceof Category) && $action === 'remove') {
            $filename = $entity->getImageUrl();
            $fileLink = $this->rootDir . '/public/assets/images/categories/' . $filename;
            $this->deleteImage($fileLink);
        }
    }

    public function deleteImage(string $imageUrl): void
    {
        try {
            unlink($imageUrl);
        } catch (\Throwable $th) {
            //message
        }
    }
}
