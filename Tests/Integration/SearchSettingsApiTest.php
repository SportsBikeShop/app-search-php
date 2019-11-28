<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Integration;

/**
 * Integration test for the Search Settings API.
 *
 * @package Elastic\AppSearch\Client\Test\Integration
 */
class SearchSettingsApiTest extends AbstractEngineTestCase
{
    /**
     * @var bool
     */
    protected static $importSampleDocs = true;

    /**
     * Test getting default search settings.
     */
    public function testGetSettings()
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $searchSettings = $client->getSearchSettings($engineName);

        $this->assertArrayHasKey('search_fields', $searchSettings);
        $this->assertNotEmpty($searchSettings['search_fields']);
        $this->assertArrayHasKey('id', $searchSettings['search_fields']);
        $this->assertEquals(1, $searchSettings['search_fields']['id']['weight']);

        $this->assertArrayHasKey('boosts', $searchSettings);
        $this->assertEmpty($searchSettings['boosts']);
    }

    /**
     * Test update search weights.
     *
     * @param array $searchFields
     *
     * @testWith array({"title": {"weight": 2}})
     *           array({"title": {}})
     *           array({"title": {"weight": 2.4}})
     *           array({"title": {"weight": 2}, "text": {"weight": 2}})
     */
    public function testUpdateSearchWeights($searchFields)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $client->updateSearchSettings($engineName, array('search_fields' => $searchFields));

        $searchSettings = $client->getSearchSettings($engineName);
        $this->assertEquals($searchFields, $searchSettings['search_fields']);
    }

    /**
     * Test update search weights with invalid data.
     *
     * @param array $searchFields
     *
     * @expectedException \Elastic\OpenApi\Codegen\Exception\BadRequestException
     *
     * @testWith array({"not_a_valid_field": {"weight": 2}})
     *           array({"title": {"weight": "not-a-number"}})
     *           array({"number_field": {"weight": 2}})
     *           array({"date_field": {"weight": 2}})
     */
    public function testInvalidUpdateSearchWeights($searchFields)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $client->updateSchema($engineName, array('number_field' => 'number', 'date_field' => 'date'));
        $client->updateSearchSettings($engineName, array('search_fields' => $searchFields));
    }

    /**
     * Test reset the search settings.
     */
    public function testResetSearchSettings()
    {
        $searchSettings = $this->getDefaultClient()->resetSearchSettings($this->getDefaultEngineName());

        $this->assertArrayHasKey('search_fields', $searchSettings);
        $this->assertNotEmpty($searchSettings['search_fields']);
        $this->assertArrayHasKey('id', $searchSettings['search_fields']);
        $this->assertEquals(1, $searchSettings['search_fields']['id']['weight']);

        $this->assertArrayHasKey('boosts', $searchSettings);
        $this->assertEmpty($searchSettings['boosts']);
    }

    /**
     * Test update search boosts.
     *
     * @param array $boosts
     *
     * @testWith array({})
     *           array({"tags" : {"type": "value", "value": "Cat"}})
     *           array({"tags" : {"type": "value", "value": "Cat", "operation": "multiply"}})
     *           array({"tags" : {"type": "value", "value": "Cat", "factor": 3}})
     *           array({"tags" : {"type": "value", "value": ["Cat"]}})
     */
    public function testUpdateBoosts($boosts)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $searchSettings = $client->getSearchSettings($engineName);
        $searchSettings['boosts'] = $boosts;
        $client->updateSearchSettings($engineName, $searchSettings);

        $searchSettings = $client->getSearchSettings($engineName);
        $this->assertCount(count($boosts), $searchSettings['boosts']);
    }
}
