<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Integration;

/**
 * Integration test for the Analytics API.
 *
 * @package Elastic\AppSearch\Client\Test\Integration
 */
class AnalyticsApiTest extends AbstractEngineTestCase
{
    /**
     * Test top clicks analytics request.
     *
     * @testWith array("brunch", 3)
     *           array("brunch", 20)
     */
    public function testTopClicks($searchTerm, $size)
    {
        $client = $this->getDefaultClient();
        $engine = $this->getDefaultEngineName();

        $topClicks = $client->getTopClicksAnalytics($engine, $searchTerm, $size);

        $this->assertLessThanOrEqual($size, $topClicks['meta']['page']['size']);
        $this->assertArrayHasKey('results', $topClicks);
    }

    /**
     * Test top queries analytics request.
     *
     * @testWith array(3, null, null)
     *           array(3, true, true)
     *           array(3, true, false)
     *           array(20, false, true)
     *           array(20, false, false)
     */
    public function testTopQueries($size, $withResults, $clicked)
    {
        $client = $this->getDefaultClient();
        $engine = $this->getDefaultEngineName();

        $filters = array();

        if (null != $withResults) {
            $filters['all'][] = array('results' => $withResults);
        }

        if (null != $clicked) {
            $filters['all'][] = array('clicks' => $clicked);
        }

        $queries = $client->getTopQueriesAnalytics($engine, $size, !empty($filters) ? $filters : null);

        $this->assertLessThanOrEqual($size, $queries['meta']['page']['size']);
        $this->assertArrayHasKey('results', $queries);
    }
}
