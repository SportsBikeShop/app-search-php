<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Integration;

/**
 * Integration test for the Log API.
 *
 * @package Elastic\AppSearch\Client\Test\Integration
 */
class LogApiTest extends AbstractEngineTestCase
{
    /**
     * Test the basic API log enpoint is returning results.
     *
     * @testWith array(null, null, null, null, null, null)
     *           array("search", null, null, null, null, null)
     *           array("search", 2, 2, null, null, null)
     *           array(null, null, null, 200, null, null)
     *           array(null, null, null, 400, null, null)
     *           array(null, null, null, null, "POST", null)
     *           array(null, null, null, null, null, null, "asc")
     *           array(null, null, null, null, null, null, "desc")
     *           array(null, null, null, null, null, null, "ASC")
     *           array(null, null, null, null, null, null, "DESC")
     *           array("search", 2, 2, 200, "GET", "desc")
     */
    public function testGetLogs($query, $currentPage, $pageSize, $status, $method, $sortDir)
    {
        $client = $this->getDefaultClient();
        $engine = $this->getDefaultEngineName();

        $fromDate = date('c', strtotime('yesterday'));
        $toDate = date('c');

        $logs = $client->getApiLogs(
            $engine,
            $fromDate,
            $toDate,
            $currentPage,
            $pageSize,
            $query,
            $status,
            $method,
            $sortDir
        );

        $this->assertArrayHasKey('results', $logs);

        if ($pageSize) {
            $this->assertEquals($logs['meta']['page']['current'], $currentPage ? $currentPage : 1);
            $this->assertEquals($logs['meta']['page']['size'], $pageSize);
        }

        if ($query) {
            $this->assertEquals($query, $logs['meta']['query']);
        }

        if ($status) {
            $this->assertEquals($status, $logs['meta']['filters']['status']);
        }

        if ($method) {
            $this->assertEquals($method, $logs['meta']['filters']['method']);
        }

        if ($sortDir) {
            $this->assertEquals($sortDir, $logs['meta']['sort_direction']);
        }
    }
}
