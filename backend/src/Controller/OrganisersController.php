<?php

namespace App\Controller;

use App\Organisers\Application\CreateOrganiser\CreateOrganiser;
use App\Organisers\Application\CreateOrganiser\CreateOrganiserHandler;
use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;
use App\Shared\Domain\Exception\NotFound;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Organisers')]
final class OrganisersController
{
    public function __construct(
        private readonly CreateOrganiserHandler $createOrganiser,
        private readonly OrganiserRepository $organisers,
    ) {}

    #[Route(path: '/organisers', name: 'organisers_list', methods: ['GET'])]
    #[OA\Get(path: '/organisers', summary: 'List all organisers')]
    #[OA\Response(response: 200, description: 'List of organisers', content: new OA\JsonContent(type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
    ], type: 'object')))]
    public function list(): Response
    {
        $all = $this->organisers->all();
        $data = array_map(static function ($o) {
            return [
                'id' => (string) $o->id(),
                'name' => $o->name(),
                'createdAt' => $o->createdAt()->format(DATE_ATOM),
            ];
        }, $all);

        return new JsonResponse($data);
    }

    #[Route(path: '/organisers', name: 'organisers_create', methods: ['POST'])]
    #[OA\Post(path: '/organisers', summary: 'Create a new organiser')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name'], properties: [
        new OA\Property(property: 'organiserId', description: 'Optional client-provided UUID', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'name', type: 'string')
    ], type: 'object'))]
    #[OA\Response(response: 201, description: 'Organiser created', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\Response(response: 404, description: 'Not Found')]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent() ?: '{}', true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $organiserId = isset($data['organiserId']) && is_string($data['organiserId']) && $data['organiserId'] !== ''
                ? $data['organiserId']
                : (string) OrganiserId::v4();

            if (!isset($data['name']) || !is_string($data['name']) || trim($data['name']) === '') {
                return new JsonResponse(['error' => 'name is required'], Response::HTTP_BAD_REQUEST);
            }

            ($this->createOrganiser)(new CreateOrganiser(
                $organiserId,
                $data['name'],
            ));

            $organiser = $this->organisers->get(OrganiserId::fromString($organiserId));

            return new JsonResponse([
                'id' => (string) $organiser->id(),
                'name' => $organiser->name(),
                'createdAt' => $organiser->createdAt()->format(DATE_ATOM),
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(path: '/organisers/{id}', name: 'organisers_get', methods: ['GET'])]
    #[OA\Get(path: '/organisers/{id}', summary: 'Get organiser by ID')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 200, description: 'Organiser found', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ], type: 'object'))]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\Response(response: 404, description: 'Not Found')]
    public function getOne(string $id): Response
    {
        try {
            $organiser = $this->organisers->get(OrganiserId::fromString($id));

            return new JsonResponse([
                'id' => (string) $organiser->id(),
                'name' => $organiser->name(),
                'createdAt' => $organiser->createdAt()->format(DATE_ATOM),
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (NotFound $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
