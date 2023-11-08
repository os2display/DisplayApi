<?php

/**
 * @file
 * Normalizer build base on CollectionNormalizer from API-platform. It fixes PlaylistSlide entity issues with generating
 * IRI in collection gets.
 */

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\DataProvider\PaginatorInterface;
use ApiPlatform\Core\DataProvider\PartialPaginatorInterface;
use ApiPlatform\Core\Serializer\ContextTrait;
use App\Entity\Tenant\PlaylistScreenRegion;
use App\Entity\Tenant\PlaylistSlide;
use App\Entity\Tenant\ScreenCampaign;
use App\Entity\Tenant\ScreenGroupCampaign;
use App\Utils\PathUtils;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RelationNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use ContextTrait;
    use NormalizerAwareTrait;

    public const FORMAT = 'jsonld';
    public const IRI_ONLY = 'iri_only';

    private array $defaultContext = [
        self::IRI_ONLY => false,
    ];

    public function __construct(
        private readonly PathUtils $utils,
        private readonly ResourceClassResolverInterface $resourceClassResolver,
        private readonly IriConverterInterface $iriConverter,
        array $defaultContext = []
    ) {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!isset($context['resource_class']) || isset($context['api_sub_level'])) {
            return $this->normalizeRawCollection($object, $format, $context);
        }

        $resourceClass = $this->resourceClassResolver->getResourceClass($object, $context['resource_class']);
        $context = $this->initContext($resourceClass, $context);
        $data = ['@context' => '/contexts/'.$context['output']['name']];

        if (isset($context['operation_type']) && OperationType::SUBRESOURCE === $context['operation_type']) {
            // FIXME: When do we end up here? `getSubresourceIriFromResourceClass` is not defined anymore.
            $data['@id'] = $this->iriConverter->getSubresourceIriFromResourceClass($resourceClass, $context);
        } else {
            $path = $this->utils->getApiPlatformPathPrefix().$context['output']['name'].'s';
            $data['@id'] = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '-$1', $path));
        }

        $data['@type'] = 'hydra:Collection';
        $data['hydra:member'] = [];
        $iriOnly = $context[self::IRI_ONLY] ?? $this->defaultContext[self::IRI_ONLY];
        foreach ($object as $obj) {
            $data['hydra:member'][] = $iriOnly ? $this->iriConverter->getIriFromResource($obj) : $this->normalizer->normalize($obj, $format, $context);
        }

        if ($object instanceof PaginatorInterface) {
            $data['hydra:totalItems'] = $object->getTotalItems();
        }
        if (\is_array($object) || ($object instanceof \Countable && !$object instanceof PartialPaginatorInterface)) {
            $data['hydra:totalItems'] = \count($object);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format)
    {
        // https://symfony.com/doc/current/serializer/custom_normalizer.html#improving-performance-of-normalizers-denormalizers
        return [
            // We support these classes and the result is cacheable (cf. self::supportsNormalization()).
            ScreenCampaign::class => true,
            ScreenGroupCampaign::class => true,
            PlaylistSlide::class => true,
            PlaylistScreenRegion::class => true,
            // Other stuff …
            'object' => false,
            '*' => null,
        ];
    }

    /**
     * Normalizes a raw collection (not API resources).
     */
    private function normalizeRawCollection(iterable $object, ?string $format, array $context): array
    {
        $data = [];
        foreach ($object as $index => $obj) {
            $data[$index] = $this->normalizer->normalize($obj, $format, $context);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (empty($context) || empty($context['resource_class'])) {
            return false;
        }

        $types = [
            ScreenCampaign::class,
            ScreenGroupCampaign::class,
            PlaylistSlide::class,
            PlaylistScreenRegion::class,
        ];

        return in_array($context['resource_class'], $types)
            && 'collection' === $context['operation_type']
            && $data instanceof Paginator;
    }
}
