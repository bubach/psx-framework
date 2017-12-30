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

namespace PSX\Framework\App\Api\Proxy;

use PSX\Framework\App\ApiTestCase;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/proxy/soap', 'GET', ['SOAPAction' => '/population/popo/2#GET']);

        $body = (string) $response->getBody();
        $body = preg_replace('/<faultstring>(.*)<\/faultstring>/imsU', '<faultstring>[faultstring]</faultstring>', $body);
        $body = preg_replace('/<message type="string">(.*)<\/message>/imsU', '<message type="string">[message]</message>', $body);
        $body = preg_replace('/<trace type="string">(.*)<\/trace>/imsU', '<trace type="string">[trace]</trace>', $body);
        $body = preg_replace('/<context type="string">(.*)<\/context>/imsU', '<context type="string">[context]</context>', $body);

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <soap:Fault>
   <faultcode>soap:Server</faultcode>
   <faultstring>[faultstring]</faultstring>
   <detail>
    <error type="object" xmlns="http://phpsx.org/2014/data">
     <success type="boolean">false</success>
     <title type="string">PSX\Http\Exception\MethodNotAllowedException</title>
     <message type="string">[message]</message>
     <trace type="string">[trace]</trace>
     <context type="string">[context]</context>
    </error>
   </detail>
  </soap:Fault>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(405, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('/proxy/soap', 'POST', ['SOAPAction' => '/population/popo/2#GET']);

        $body   = (string) $response->getBody();
        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <population type="object" xmlns="http://phpsx.org/2014/data">
   <id type="integer">2</id>
   <place type="integer">2</place>
   <region type="string">United States</region>
   <population type="integer">307212123</population>
   <users type="integer">227719000</users>
   <worldUsers type="float">13.1</worldUsers>
   <datetime type="date-time">2009-11-29T15:22:40Z</datetime>
  </population>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }
}