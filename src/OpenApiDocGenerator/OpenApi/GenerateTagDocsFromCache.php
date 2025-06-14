<?php

namespace Perry\OpenApiDocGenerator\OpenApi;

use Perry\Attributes\Tag\Tag;
use Perry\Files\Storage;

class GenerateTagDocsFromCache
{
    public function execute(): array
    {
        $tags = Storage::getTagsOrEmpty();
        if(empty($tags)) {
            return [];
        }

        return $this->mapTags($tags);
    }

    /**
     * @param Tag[] $tags
     */
    private function mapTags(array $tags): array
    {
        $output = [];
        foreach ($tags as $tag) {
            $tagArray = [
                'name' => $tag->name,
                'description' => $tag->description
            ];
            if($tag->externalDocs) {
                $tagArray['externalDocs'] = [
                    'description' => $tag->externalDocs->description,
                    'url' => $tag->externalDocs->url
                ];
            }

            $output[] = $tagArray;
        }
        return $output;
    }
}
