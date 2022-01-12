<?php

namespace App\Controller;

use App\Repository\ScreenGroupRepository;
use App\Utils\ValidationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ScreenGroupsCampaignsDeleteController extends AbstractController
{
    public function __construct(
        private ScreenGroupRepository $screenGroupRepository,
        private ValidationUtils $validationUtils
    ) {
    }

    public function __invoke(string $id, string $screenGroupId): JsonResponse
    {
        $ulid = $this->validationUtils->validateUlid($id);
        $screenGroupUlid = $this->validationUtils->validateUlid($screenGroupId);

        $this->screenGroupRepository->deleteCampaignRelations($ulid, $screenGroupUlid);

        return new JsonResponse(null, 204);
    }
}
