<?php

namespace App\Dto;

class Campaign
{
    public string $title = '';
    public string $description = '';
    public \DateTimeInterface $created;
    public \DateTimeInterface $modified;
    public string $modifiedBy = '';
    public string $createdBy = '';

    public string $layout = '';
    public string $inScreenGroups = '/v1/campaigns/{id}/groups';
    public array $regions = [];
}
