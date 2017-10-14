<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Tests\Schema;

use PSX\Framework\Schema\Passthru;
use PSX\Schema\Generator;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaManager;

/**
 * PassthruTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PassthruTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $generator = new Generator\JsonSchema();
        $manage = new SchemaManager();
        $schema = $manage->getSchema(Passthru::class);

        $this->assertInstanceOf(SchemaInterface::class, $schema);

        $actual = $generator->generate($schema);
        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:schema.phpsx.org#",
    "type": "object",
    "title": "passthru",
    "description": "No schema information available"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}
