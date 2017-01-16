<?php

namespace MainBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Entity\Team;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Storage\StorageInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * Class ImageSerializeListener
 * Creates image url for the serializer.
 */
class ImageSerializeListener
{
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ImageSerializeListener constructor.
     *
     * @param StorageInterface $storage
     * @param RequestStack     $requestStack
     */
    public function __construct(StorageInterface $storage, RequestStack $requestStack)
    {
        $this->storage = $storage;
        $this->requestStack = $requestStack;
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        switch (true) {
            case $object instanceof User:
                $visitor->addData('avatar', $this->getUri($object, 'avatarFile'));
                break;
            case $object instanceof Team:
                $visitor->addData('logo', $this->getUri($object, 'logoFile'));
                break;
        }
    }

    /**
     * Gets the full uri for an image.
     *
     * @param $object
     * @param $fieldName
     *
     * @return null|string
     */
    private function getUri($object, $fieldName)
    {
        $uri = $this->storage->resolveUri($object, $fieldName);
        $request = $this->requestStack->getCurrentRequest();

        return $uri ? $request->getUriForPath($uri) : null;
    }
}
