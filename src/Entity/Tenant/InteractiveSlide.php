<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Repository\InteractiveSlideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: InteractiveSlideRepository::class)]
class InteractiveSlide extends AbstractTenantScopedEntity
{
    #[Ignore]
    #[ORM\Column(nullable: true)]
    private ?array $configuration = null;

    #[ORM\Column(length: 255)]
    private ?string $implementationClass = null;

    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    public function setConfiguration(?array $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getImplementationClass(): ?string
    {
        return $this->implementationClass;
    }

    public function setImplementationClass(string $implementationClass): static
    {
        $this->implementationClass = $implementationClass;

        return $this;
    }
}
