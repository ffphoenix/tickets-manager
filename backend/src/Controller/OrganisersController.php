<?php

namespace App\Controller;

use App\Organisers\Application\CreateOrganiser\CreateOrganiser;
use App\Organisers\Application\CreateOrganiser\CreateOrganiserHandler;
use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;
use App\Shared\Domain\Exception\NotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrganisersController
{
    public function __construct(
        private readonly CreateOrganiserHandler $createOrganiser,
        private readonly OrganiserRepository $organisers,
    ) {}

    #[Route(path: '/organisers', name: 'organisers_create', methods: ['POST'])]
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
