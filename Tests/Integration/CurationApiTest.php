<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Integration;

/**
 * Integration test for the Curation API.
 *
 * @package Elastic\AppSearch\Client\Test\Integration
 */
class CurationApiTest extends AbstractEngineTestCase
{
    /**
     * @var bool
     */
    protected static $importSampleDocs = true;

    /**
     * Test Curation API endpoints (create, get, list, update and delete).
     *
     * @param array $curationData
     *
     * @testWith array(array(""), array("INscMGmhmX4"), array("JNDFojsd02"))
     *           array(array("cat", "grumpy"), array("INscMGmhmX4"), null)
     *           array(array("lol"), null, array("INscMGmhmX4"))
     */
    public function testCurationApi($queries, $promotedIds, $hiddenIds)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $curation = $client->createCuration($engineName, $queries, $promotedIds, $hiddenIds);
        $this->assertArrayHasKey('id', $curation);

        $curation = $client->getCuration($engineName, $curation['id']);
        $this->assertEquals($queries, $curation['queries']);

        $curationListResponse = $client->listCurations($engineName);
        $this->assertEquals(1, $curationListResponse['meta']['page']['total_results']);
        $this->assertCount(1, $curationListResponse['results']);

        $updateResponse = $client->updateCuration($engineName, $curation['id'], $queries, $promotedIds, $hiddenIds);
        $this->assertArrayHasKey('id', $updateResponse);
        $this->assertEquals($curation['id'], $updateResponse['id']);

        $deleteOperationResponse = $client->deleteCuration($engineName, $curation['id']);
        $this->assertEquals(array('deleted' => true), $deleteOperationResponse);
    }
}
