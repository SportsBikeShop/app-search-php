<?php
/**
 * This file is part of the Elastic App Search PHP Client package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Client\Tests\Integration;

/**
 * Integration test for the Schema API.
 *
 * @package Elastic\AppSearch\Client\Test\Integration
 */
class SchemaApiTest extends AbstractEngineTestCase
{
    /**
     * @var bool
     */
    protected static $importSampleDocs = true;

    /**
     * Test getting the schema.
     */
    public function testGetSchema()
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();

        $schema = $client->getSchema($engineName);
        $this->assertArrayHasKey('title', $schema);
        $this->assertEquals('text', $schema['title']);
    }

    /**
     * Test updating the schema.
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @testWith array("string_field", "text")
     *           array("date_field", "date")
     *           array("number_field", "number")
     *           array("geo_field", "geolocation")
     */
    public function testUpdateSchema($fieldName, $fieldType)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();
        $schema = $client->updateSchema($engineName, array($fieldName => $fieldType));

        $this->assertArrayHasKey($fieldName, $schema);
        $this->assertEquals($fieldType, $schema[$fieldName]);
    }

    /**
     * Test invalid schema updates.
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @expectedException \Elastic\OpenApi\Codegen\Exception\BadRequestException
     *
     * @testWith array("string_field", "not-a-valid-type")
     *           array("id", "number")
     *           array("12", "text")
     *           array("invalid field name", "text")
     *           array("_invalid_field_name", "text")
     *           array("invalid-field-name", "text")
     *           array("invalidFieldName", "text")
     *           array("invalid.field.name", "text")
     *           array("INVALID", "text")
     */
    public function testInvalidSchemaUpdate($fieldName, $fieldType)
    {
        $client = $this->getDefaultClient();
        $engineName = $this->getDefaultEngineName();
        $client->updateSchema($engineName, array($fieldName => $fieldType));
    }
}
