<?php

declare(strict_types=1);

namespace App\InteractiveSlide;

use App\Entity\Tenant\Slide;
use App\Exceptions\InteractiveSlideException;
use Symfony\Component\Security\Core\User\UserInterface;

interface InteractiveSlideInterface
{
    public function getConfigOptions(): array;

    /**
     * Perform the given InteractionRequest with the given Slide.
     *
     * @throws InteractiveSlideException
     */
    public function performAction(UserInterface $user, Slide $slide, InteractionSlideRequest $interactionRequest): array;
}
