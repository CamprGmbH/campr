<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

abstract class BaseController extends Controller
{
    protected function persistAndFlush(...$objects)
    {
        Assert::allObject($objects);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        call_user_func_array([$em, 'persist'], $objects);
        $em->flush();
    }

    protected function refreshEntity($obj)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $em->refresh($obj);
    }

    protected function assertClassExists(string $class)
    {
        if (empty($class) || !class_exists($class)) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                sprintf('Class "%s" does not exist.', $class)
            );
        }
    }

    /**
     * @param        $data
     * @param int    $statusCode
     * @param string $emptyData
     *
     * @return JsonResponse
     */
    protected function createApiResponse($data, $statusCode = Response::HTTP_OK, $emptyData = '')
    {
        if (empty($data)) {
            if ('' === $emptyData && is_array($data)) {
                $emptyData = [];
            }

            return new JsonResponse($emptyData, JsonResponse::HTTP_NO_CONTENT);
        }

        $data = $this
            ->get('app.serializer.normalizer.json')
            ->normalize($data);

        return new JsonResponse($data, $statusCode);
    }

    /**
     * @param string|null $message
     *
     * @return JsonResponse
     */
    protected function createNotFoundApiResponse(string $message = null): JsonResponse
    {
        if (empty($message)) {
            $message = $this->get('translator')->trans('not_found.general');
        }

        return new JsonResponse(['message' => $message], JsonResponse::HTTP_NOT_FOUND);
    }

    protected function createdTranslatedAccessDeniedException(string $message)
    {
        throw $this->createAccessDeniedException(
            $this
                ->get('translator')
                ->trans($message)
        );
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->get('event_dispatcher');
    }

    /**
     * @param string $name
     * @param Event  $event
     *
     * @return Event
     */
    protected function dispatchEvent(string $name, Event $event): Event
    {
        return $this->getEventDispatcher()->dispatch($name, $event);
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public function merge_multidimensional_arrays(array &$array1, array &$array2)
    {
        $data = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($data[$key]) && is_array($data[$key])) {
                $data[$key] = $this->merge_multidimensional_arrays($data[$key], $value);
            } elseif (is_numeric($key)) {
                if (!in_array($value, $data)) {
                    $data[] = $value;
                }
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
