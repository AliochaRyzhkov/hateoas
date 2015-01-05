<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\JsonApi\Request;

// HTTP.
use Symfony\Component\HttpFoundation\Request;
// Utils.
use GoIntegro\Hateoas\Util;

/**
 * @see http://jsonapi.org/format/#crud-creating-resources
 */
class CreateBodyParser implements BodyParserInterface
{
    // @todo http://jsonapi.org/format/#crud-creating-client-ids
    const ERROR_ID_NOT_SUPPORTED = "Providing an Id on creation is not supported magically yet.";

    /**
     * @param Request $request
     * @param Params $params
     * @param array $body
     * @return array
     */
    public function parse(Request $request, Params $params, array $body)
    {
        $rawData = isset($body[$params->primaryType]) ? $body[$params->primaryType] : $body['data'];
        $entityData = [];

        if (empty($rawData)) {
            throw new ParseException(BodyParser::ERROR_PRIMARY_TYPE_KEY);
        } elseif (
            Util\ArrayHelper::isAssociative($rawData)
        ) {
            if (isset($rawData['id'])) {
                throw new ParseException(static::ERROR_ID_NOT_SUPPORTED);
            } else {
                $entityData[] = $rawData;
            }
        } else {
            foreach ($rawData as $datum) {
                if (isset($datum['id'])) {
                    throw new ParseException(static::ERROR_ID_NOT_SUPPORTED);
                } else {
                    $entityData[] = $datum;
                }
            }
        }

        return $entityData;
    }
}
