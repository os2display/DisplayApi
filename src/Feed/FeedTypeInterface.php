<?php

namespace App\Feed;

use App\Entity\Tenant\Feed;
use App\Entity\Tenant\FeedSource;
use App\Exceptions\MissingFeedConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Interface that feed types must implement.
 */
interface FeedTypeInterface
{
    /**
     * Get admin form options that will be exposed in the admin.
     *
     * @param FeedSource $feedSource the feed source
     *
     * @return array
     *   Array of admin options
     */
    public function getAdminFormOptions(FeedSource $feedSource): array;

    /**
     * Get feed data for the given feed.
     *
     * @param Feed $feed the feed
     *
     * @return array
     *   Array of data
     *
     * @throws ClientExceptionInterface
     * @throws MissingFeedConfigurationException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function getData(Feed $feed): array;

    /**
     * Get config options for $name from $feedSource.
     *
     * @param Request $request
     * @param FeedSource $feedSource
     * @param string $name
     *
     * @return array|null
     */
    public function getConfigOptions(Request $request, FeedSource $feedSource, string $name): ?array;

    /**
     * Get list of required secrets.
     *
     * @return array
     */
    public function getRequiredSecrets(): array;

    /**
     * Get list of required configuration.
     *
     * @return array
     */
    public function getRequiredConfiguration(): array;

    /**
     * Get name of the type of feed it supports.
     *
     * @return string
     */
    public function getSupportedFeedOutputType(): string;
}
