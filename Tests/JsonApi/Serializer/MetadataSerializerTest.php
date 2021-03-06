<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\JsonApi\Serializer;

// Mocks.
use Codeception\Util\Stub;

class MetadataSerializerTest extends \PHPUnit_Framework_TestCase
{
    const RESOURCE_TYPE = 'resources';

    public function testSerializingPaginatedDocument()
    {
        /* Given... (Fixture) */
        $size = 3;
        $offset = 10;
        $resources = self::createResourcesMock($size, $offset);
        $pagination = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\DocumentPagination',
            [
                'total' => 1000,
                'size' => $size,
                'page' => 5,
                'offset' => $offset
            ]
        );
        $document = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\Document',
            [
                'wasCollection' => TRUE, // Key to this test.
                'resources' => $resources,
                'getResourceMeta' => function() { return []; },
                'pagination' => $pagination
            ]
        );
        $serializer = new MetadataSerializer(
            self::buildPaginationSerializer(),
            self::buildSearchResultSerializer(),
            self::buildTranslationsSerializer()
        );
        /* When... (Action) */
        $json = $serializer->serialize($document);
        /* Then... (Assertions) */
        $this->assertEquals(['resources' => ['pagination' => [
            'page' => 5,
            'size' => 3,
            'total' => 1000
        ]]], $json);
    }

    /**
     * @param integer $amount
     * @param integer $offset
     * @return \GoIntegro\Hateoas\JsonApi\ResourceCollection
     */
    private static function createResourcesMock($amount, $offset = 0)
    {
        $metadata = Stub::makeEmpty(
            'GoIntegro\Hateoas\Metadata\Resource\ResourceMetadata',
            [
                'type' => self::RESOURCE_TYPE,
                'subtype' => self::RESOURCE_TYPE,
                'fields' => []
            ]
        );

        $resources = [];
        for ($i = 0; $i < $amount; ++$i) {
            $resources[] = Stub::makeEmpty(
                'GoIntegro\Hateoas\JsonApi\EntityResource',
                [
                    'id' => (string) $offset,
                    'getMetadata' => function() use ($metadata) {
                        return $metadata;
                    }
                ]
            );
            ++$offset;
        }

        $collection = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\ResourceCollection',
            [
                'getMetadata' => function() use ($metadata) {
                    return $metadata;
                },
                'getIterator' => function() use ($resources) {
                    return new \ArrayIterator($resources);
                },
                'count' => function() use ($resources) {
                    return count($resources);
                }
            ]
        );

        return $collection;
    }

    /**
     * @return PaginationMetadataSerializer
     */
    public static function buildPaginationSerializer()
    {
        return Stub::makeEmpty(
            'GoIntegro\\Hateoas\\JsonApi\\Serializer\\PaginationMetadataSerializer',
            ['serialize' => [
                'page' => 5,
                'size' => 3,
                'total' => 1000
            ]]
        );
    }

    /**
     * @return SearchResultMetadataSerializer
     */
    public static function buildSearchResultSerializer()
    {
        return Stub::makeEmpty(
            'GoIntegro\\Hateoas\\JsonApi\\Serializer\\SearchResultMetadataSerializer'
        );
    }

    /**
     * @return TranslationsMetadataSerializer
     */
    public static function buildTranslationsSerializer()
    {
        return Stub::makeEmpty(
            'GoIntegro\\Hateoas\\JsonApi\\Serializer\\TranslationsMetadataSerializer'
        );
    }
}
