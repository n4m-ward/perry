<?php

namespace Perry\Attributes;

#[\Attribute]
readonly class Info
{
    public function __construct(
        public string $version,
        public string $title,
        public string $description,
        public ?string $contactEmail = null,
        public ?string $termsOfService = null, // set null to don't break the old versions
        public ?ExternalDocs $externalDocs = null,
    ) {
    }
}
