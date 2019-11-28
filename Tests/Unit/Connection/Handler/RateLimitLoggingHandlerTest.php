<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Unit\Connection\Handler;

use GuzzleHttp\Ring\Future\CompletedFutureArray;
use PHPUnit\Framework\TestCase;
use Elastic\AppSearch\Client\Connection\Handler\RateLimitLoggingHandler;
use Psr\Log\LoggerInterface;

/**
 * Rate limit logger tests.
 *
 * @package Elastic\AppSearch\Client\Test\Unit\Connection\Handler
 */
class RateLimitLoggingHandlerTest extends TestCase
{
    public $logArray = array();

    /**
     * Check rate limit warning are logged successfully.
     *
     * @testWith array(null, null)
     *           array(100, 50)
     *           array(100, 5)
     */
    public function testExceptionTypes($limit, $remaining)
    {
        $response = array('headers' => array_filter($this->getResponseHeaders($limit, $remaining)));
        $handler = $this->getHandler($response);

        $handler(array())->wait();

        if ($this->shouldLogWarning($limit, $remaining)) {
            $this->assertNotEmpty($this->logArray);
            $this->assertArrayHasKey('warning', $this->logArray);
        } else {
            $this->assertEmpty($this->logArray);
        }
    }

    /**
     * Return a the response handler used in test.
     *
     * @param array $response
     *
     * @return \Elastic\AppSearch\Client\Connection\Handler\RateLimitLoggingHandler
     */
    private function getHandler($response)
    {
        $responseCallback = function ($request) use ($response) {
            return new CompletedFutureArray($response);
        };

        return new RateLimitLoggingHandler($responseCallback, $this->getLoggerMock());
    }

    /**
     * Indicate if a warning should be logged or not.
     *
     * @param int|null $limit
     * @param int|null $remaining
     *
     * @return bool
     */
    private function shouldLogWarning($limit, $remaining)
    {
        return $limit && ($remaining / $limit) < RateLimitLoggingHandler::RATE_LIMIT_PERCENT_WARNING_TRESHOLD;
    }

    /**
     * Return response headers.
     *
     * @param int|null $limit
     * @param int|null $remaining
     *
     * @return array[]
     */
    private function getResponseHeaders($limit, $remaining)
    {
        return array(
            RateLimitLoggingHandler::RATE_LIMIT_LIMIT_HEADER_NAME => array($limit),
            RateLimitLoggingHandler::RATE_LIMIT_REMAINING_HEADER_NAME => array($remaining),
        );
    }

    /**
     * Create a mock for the logger interface.
     *
     * @return \Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        $this->logArray = array();

        $logger = $this->createMock("\Psr\Log\LoggerInterface");
        $that = $this;
        $logger->method('warning')->willReturnCallback(function ($message) use ($that) {
            $that->logArray['warning'][] = $message;
        });

        return $logger;
    }
}
