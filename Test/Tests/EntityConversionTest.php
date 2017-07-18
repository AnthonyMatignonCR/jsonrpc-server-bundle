<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;

class EntityConversionTest extends JsonRpcTestCase
{
    public function testEntitySerialization()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/',
            [
                'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                'method'  => 'entity',
                'id'      => 'test',
                'params'  => [
                    'payload' => 'my-payload',
                ],
            ]
        );

        self::assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent());
        self::assertInstanceOf(\stdClass::class, $content);

        $jsonResponse = new SyncResponse($content);
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content, JSON_PRETTY_PRINT));
        self::assertTrue(isset($jsonResponse->getBody()->{'sample_payload'}), json_encode($content, JSON_PRETTY_PRINT));
        self::assertEquals('my-payload', $jsonResponse->getBody()->{'sample_payload'});

        self::assertFalse(
            isset($jsonResponse->getBody()->{'private_payload'}),
            json_encode($content, JSON_PRETTY_PRINT)
        );
    }

    public function testPrivateEntityFields()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/private/',
            [
                'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                'method'  => 'entity/private',
                'id'      => 'test',
                'params'  => [
                    'payload' => 'my-payload',
                ],
            ]
        );

        self::assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent());
        self::assertInstanceOf(\stdClass::class, $content);

        $jsonResponse = new SyncResponse($content);
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content, JSON_PRETTY_PRINT));
        self::assertTrue(isset($jsonResponse->getBody()->{'sample_payload'}));
        self::assertEquals('my-payload', $jsonResponse->getBody()->{'sample_payload'});

        if (static::$kernel->getContainer()->has('jms_serializer')) {
            self::assertTrue(
                isset($jsonResponse->getBody()->{'private_payload'}),
                json_encode($content, JSON_PRETTY_PRINT)
            );
            self::assertEquals('secret-payload', $jsonResponse->getBody()->{'private_payload'});
        }
    }

    public function testNestedContext()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/private/',
            [
                'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                'method'  => 'entity/nested',
                'id'      => 'test',
                'params'  => [
                    'payload' => 'my-payload',
                ],
            ]
        );

        self::assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent());
        self::assertInstanceOf(\stdClass::class, $content);

        $jsonResponse = new SyncResponse($content);
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content, JSON_PRETTY_PRINT));
        self::assertTrue(isset($jsonResponse->getBody()->{'sample_payload'}));
        self::assertEquals('my-payload', $jsonResponse->getBody()->{'sample_payload'});

        if (static::$kernel->getContainer()->has('jms_serializer')) {
            self::assertTrue(
                isset($jsonResponse->getBody()->{'private_payload'}),
                json_encode($content, JSON_PRETTY_PRINT)
            );
            self::assertEquals('secret-payload', $jsonResponse->getBody()->{'private_payload'});
        }
    }
}
