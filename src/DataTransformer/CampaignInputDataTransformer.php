<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Dto\CampaignInput;
use App\Entity\Campaign;

final class CampaignInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data, string $to, array $context = []): Campaign
    {
        $campaign = new Campaign();
        if (array_key_exists(AbstractItemNormalizer::OBJECT_TO_POPULATE, $context)) {
            $campaign = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];
        }

        /* @var CampaignInput $data */
        empty($data->title) ?: $campaign->setTitle($data->title);
        empty($data->description) ?: $campaign->setDescription($data->description);
        empty($data->createdBy) ?: $campaign->setCreatedBy($data->createdBy);
        empty($data->modifiedBy) ?: $campaign->setModifiedBy($data->modifiedBy);

        return $campaign;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Campaign) {
            return false;
        }

        return Campaign::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
