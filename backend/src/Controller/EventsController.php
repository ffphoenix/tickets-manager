<?php

namespace App\Controller;

use App\Events\Application\CreateEvent\CreateEvent;
use App\Events\Application\CreateEvent\CreateEventHandler;
use App\Events\Domain\EventId;
use App\Events\Domain\EventRepository;
use App\Organisers\Domain\OrganiserId;
use App\Shared\Domain\Exception\NotFound;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Events')]
final class EventsController
{
    public function __construct(
        private readonly CreateEventHandler $createEvent,
        private readonly EventRepository $events,
    ) {}

    #[Route(path: '/events', name: 'events_list', methods: ['GET'])]
    #[OA\Get(path: '/events', summary: 'List all events')]
    #[OA\Response(response: 200, description: 'List of events', content: new OA\JsonContent(type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'startAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'endAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object')))]
    public function list(): Response
    {
        $events = $this->events->all();

        $data = array_map(static function ($event) {
            return [
                'id' => (string) $event->id(),
                'name' => $event->name(),
                'organiserId' => (string) $event->organiserId(),
                'startAt' => $event->startAt()->format(DATE_ATOM),
                'endAt' => $event->endAt()->format(DATE_ATOM),
                'createdAt' => $event->createdAt()->format(DATE_ATOM),
            ];
        }, $events);

        return new JsonResponse($data);
    }

    #[Route(path: '/events', name: 'events_create', methods: ['POST'])]
    #[OA\Post(path: '/events', summary: 'Create a new event')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['organiserId','name','startAt','endAt'], properties: [
        new OA\Property(property: 'eventId', description: 'Optional client-provided UUID', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'startAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'endAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 201, description: 'Event created', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'startAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'endAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\Response(response: 404, description: 'Not Found')]
    #[OA\Response(response: 422, description: 'Unprocessable Entity')]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent() ?: '{}', true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $eventId = isset($data['eventId']) && is_string($data['eventId']) && $data['eventId'] !== ''
                ? $data['eventId']
                : (string) EventId::v4();

            if (!isset($data['organiserId']) || !is_string($data['organiserId']) || $data['organiserId'] === '') {
                return new JsonResponse(['error' => 'organiserId is required'], Response::HTTP_BAD_REQUEST);
            }
            if (!isset($data['name']) || !is_string($data['name']) || trim($data['name']) === '') {
                return new JsonResponse(['error' => 'name is required'], Response::HTTP_BAD_REQUEST);
            }
            if (!isset($data['startAt']) || !is_string($data['startAt'])) {
                return new JsonResponse(['error' => 'startAt (ISO 8601 string) is required'], Response::HTTP_BAD_REQUEST);
            }
            if (!isset($data['endAt']) || !is_string($data['endAt'])) {
                return new JsonResponse(['error' => 'endAt (ISO 8601 string) is required'], Response::HTTP_BAD_REQUEST);
            }

            $startAt = new \DateTimeImmutable($data['startAt']);
            $endAt = new \DateTimeImmutable($data['endAt']);

            // Validate UUID formats early (will also be validated in value objects)
            OrganiserId::fromString($data['organiserId']);

            ($this->createEvent)(new CreateEvent(
                $eventId,
                $data['organiserId'],
                $data['name'],
                $startAt,
                $endAt,
            ));

            $event = $this->events->get(EventId::fromString($eventId));

            return new JsonResponse([
                'id' => (string) $event->id(),
                'name' => $event->name(),
                'organiserId' => (string) $event->organiserId(),
                'startAt' => $event->startAt()->format(DATE_ATOM),
                'endAt' => $event->endAt()->format(DATE_ATOM),
                'createdAt' => $event->createdAt()->format(DATE_ATOM),
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route(path: '/events/{id}', name: 'events_get', methods: ['GET'])]
    #[OA\Get(path: '/events/{id}', summary: 'Get event by ID')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 200, description: 'Event found', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'startAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'endAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\Response(response: 404, description: 'Not Found')]
    public function getOne(string $id): Response
    {
        try {
            $event = $this->events->get(EventId::fromString($id));

            return new JsonResponse([
                'id' => (string) $event->id(),
                'name' => $event->name(),
                'organiserId' => (string) $event->organiserId(),
                'startAt' => $event->startAt()->format(DATE_ATOM),
                'endAt' => $event->endAt()->format(DATE_ATOM),
                'createdAt' => $event->createdAt()->format(DATE_ATOM),
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
