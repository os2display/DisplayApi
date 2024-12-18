<?php

namespace App\Feed\OutputModel;

class ConfigOption {
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $value
    ) {}
}
