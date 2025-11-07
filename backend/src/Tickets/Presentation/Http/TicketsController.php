<?php

namespace App\Tickets\Presentation\Http;

use App\Shared\Domain\Exception\NotFound;
use App\Tickets\Application\CreateTicket\CreateTicket;
use App\Tickets\Application\CreateTicket\CreateTicketHandler;
use App\Tickets\Domain\TicketRepository;
use App\Tickets\Domain\ValueObject\TicketId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Tickets')]
final class TicketsController
{
    public function __construct(
        private readonly CreateTicketHandler $createTicket,
        private readonly TicketRepository $tickets
    ) {}

    #[Route(path: '/tickets', name: 'tickets_create', methods: ['POST'])]
    #[OA\Post(path: '/tickets', summary: 'Create a new ticket')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['eventId','organiserId','priceCents'], properties: [
        new OA\Property(property: 'ticketId', description: 'Optional client-provided UUID', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'eventId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'priceCents', type: 'integer')
    ], type: 'object'))]
    #[OA\Response(response: 201, description: 'Ticket created', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'eventId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'priceCents', type: 'integer'),
        new OA\Property(property: 'status', type: 'string'),
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
            $ticketId = isset($data['ticketId']) && is_string($data['ticketId']) && $data['ticketId'] !== ''
                ? $data['ticketId']
                : (string) TicketId::v4();

            if (!isset($data['eventId']) || !is_string($data['eventId']) || $data['eventId'] === '') {
                return new JsonResponse(['error' => 'eventId is required'], Response::HTTP_BAD_REQUEST);
            }
            if (!isset($data['organiserId']) || !is_string($data['organiserId']) || $data['organiserId'] === '') {
                return new JsonResponse(['error' => 'organiserId is required'], Response::HTTP_BAD_REQUEST);
            }
            if (!isset($data['priceCents']) || !is_int($data['priceCents'])) {
                return new JsonResponse(['error' => 'priceCents (int) is required'], Response::HTTP_BAD_REQUEST);
            }

            ($this->createTicket)(new CreateTicket(
                $ticketId,
                $data['eventId'],
                $data['organiserId'],
                $data['priceCents']
            ));

            $ticket = $this->tickets->get(TicketId::fromString($ticketId));

            return new JsonResponse([
                'id' => (string) $ticket->id(),
                'eventId' => (string) $ticket->eventId(),
                'organiserId' => (string) $ticket->organiserId(),
                'priceCents' => $ticket->priceCents(),
                'status' => $ticket->status()->value,
                'createdAt' => $ticket->createdAt()->format(DATE_ATOM),
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(path: '/tickets/{id}', name: 'tickets_get', methods: ['GET'])]
    #[OA\Get(path: '/tickets/{id}', summary: 'Get ticket by ID')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 200, description: 'Ticket found', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'eventId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'organiserId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'priceCents', type: 'integer'),
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\Response(response: 404, description: 'Not Found')]
    public function getOne(string $id): Response
    {
        try {
            $ticket = $this->tickets->get(TicketId::fromString($id));

            return new JsonResponse([
                'id' => (string) $ticket->id(),
                'eventId' => (string) $ticket->eventId(),
                'organiserId' => (string) $ticket->organiserId(),
                'priceCents' => $ticket->priceCents(),
                'status' => $ticket->status()->value,
                'createdAt' => $ticket->createdAt()->format(DATE_ATOM),
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
