<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 24.03.2016
 * Time: 10:29
 */

namespace Bankiru\Api\JsonRpc\Serializer;

/**
 * Class ApiRelation
 *
 * @package Bankiru\Api\ApiClientBridge\Serializer
 * @Annotation
 * @Target("PROPERTY")
 */
class HandledType
{
    public $handler;

    /**
     * HandledType constructor.
     *
     * @param $handler
     */
    public function __construct($handler = 'Relation') { $this->handler = $handler; }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
