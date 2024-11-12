<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\FeedSourceInput;
use App\Entity\Tenant\FeedSource;
use App\Repository\FeedSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FeedSourceProcessor extends AbstractProcessor
{
    private const string PATTERN_WITHOUT_PROTOCOL = '^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\\.)+[A-Za-z]{2,6}$';
    private const string PATTERN_WITH_PROTOCOL = 'https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        ProcessorInterface $persistProcessor,
        private readonly ProcessorInterface $removeProcessor,
        private readonly FeedSourceRepository $feedSourceRepository,
    ) {
        parent::__construct($entityManager, $persistProcessor, $removeProcessor);
    }


    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof DeleteOperationInterface) {
            $queryBuilder = $this->feedSourceRepository->getFeedSourceSlideRelationsFromFeedSourceId($uriVariables['id']);
            $hasSlides = $queryBuilder->getQuery()->getResult();
            if ($hasSlides) {
                throw new ConflictHttpException('This feed source is used by one or more slides and cannot be deleted.');
            }
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }
        parent::process($data, $operation, $uriVariables, $context);
    }

    protected function fromInput(mixed $object, Operation $operation, array $uriVariables, array $context): FeedSource
    {
        // FIXME Do we really have to do (something like) this to load an existing object into the entity manager?
        $feedSource = $this->loadPrevious(new FeedSource(), $context);

        /* @var FeedSourceInput $object */
        empty($object->title) ?: $feedSource->setTitle($object->title);
        empty($object->description) ?: $feedSource->setDescription($object->description);
        empty($object->createdBy) ?: $feedSource->setCreatedBy($object->createdBy);
        empty($object->modifiedBy) ?: $feedSource->setModifiedBy($object->modifiedBy);
        empty($object->secrets) ?: $feedSource->setSecrets($object->secrets);
        empty($object->feedType) ?: $feedSource->setFeedType($object->feedType);
        empty($object->supportedFeedOutputType) ?: $feedSource->setSupportedFeedOutputType($object->supportedFeedOutputType);

        $this->validateFeedSource($object);

        return $feedSource;
    }

    private function validateFeedSource(object $object): void
    {
        $title = $object->title;

        // Check title isset
        if (empty($title) || !is_string($title)) {
            throw new InvalidArgumentException('A feed source must have a title');
        }

        $description = $object->description;

        // Check description isset
        if (empty($description) || !is_string($description)) {
            throw new InvalidArgumentException('A feed source must have a description');
        }

        $supportedFeedOutputType = $object->supportedFeedOutputType;

        // Check description isset
        if (empty($supportedFeedOutputType) || !is_string($supportedFeedOutputType)) {
            throw new InvalidArgumentException('A feed source must have a supported feed output type');
        }

        $feedType = $object->feedType;

        // Check feedType isset
        if (empty($feedType) || !is_string($feedType)) {
            throw new InvalidArgumentException('A feed source must have a type');
        }

        switch ($object->feedType) {
            case 'App\\Feed\\EventDatabaseApiFeedType':
                $host = $object->secrets[0]['host'];

                // Check host isset
                if (empty($host) || !is_string($host)) {
                    throw new InvalidArgumentException('This feed source type must have a host defined');
                }

                // Check host valid url
                if (!preg_match('`'.self::PATTERN_WITH_PROTOCOL.'`', $host)) {
                    if (!preg_match('`'.self::PATTERN_WITHOUT_PROTOCOL.'`', $host)) {
                        throw new InvalidArgumentException('The host must be a valid URL');
                    } else {
                        throw new InvalidArgumentException('The host must be a valid URL including http or https');
                    }
                }
                break;
            case "App\Feed\NotifiedFeedType":
                $token = $object->secrets[0]['token'];

                // Check token isset
                if (!isset($token) || !is_string($token)) {
                    throw new InvalidArgumentException('This feed source type must have a token defined');
                }
                break;
        }
    }
}
