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

namespace PSX\Framework\Oauth;

use PSX\Data\WriterInterface;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Oauth\Consumer;
use PSX\Oauth\Data\Credentials;
use PSX\Oauth\Data\Request;
use PSX\Oauth\Data\Response;

/**
 * AccessAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class AccessAbstract extends ApiAbstract
{
    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        if ($request->getMethod() != 'POST') {
            throw new StatusCode\MethodNotAllowedException('Only POST requests are allowed', ['POST']);
        }

        $extractor = new AuthorizationHeaderExtractor(array(
            'consumerKey',
            'token',
            'signatureMethod',
            'signature',
            'timestamp',
            'nonce',
            'verifier',
        ));

        $record   = $extractor->extract($request);
        $consumer = $this->getConsumer($record->getConsumerKey(), $record->getToken());

        if ($consumer instanceof Credentials) {
            $signature = Consumer::getSignature($record->getSignatureMethod());

            $method = $request->getMethod();
            $url    = $request->getUri();
            $params = array_merge($record->getProperties(), $request->getUri()->getParameters());

            $baseString = Consumer::buildBasestring($method, $url, $params);

            if ($signature->verify($baseString, $consumer->getConsumerSecret(), $consumer->getTokenSecret(), $record->getSignature()) !== false) {
                $resp = $this->getResponse($consumer, $record);

                if ($resp instanceof Response) {
                    $this->responseWriter->setBody($response, $resp, $this->getWriterOptions($request));
                } else {
                    throw new StatusCode\BadRequestException('Invalid response');
                }
            } else {
                throw new StatusCode\BadRequestException('Invalid signature');
            }
        } else {
            throw new StatusCode\BadRequestException('Invalid Consumer Key');
        }
    }

    protected function getSupportedWriter()
    {
        return [WriterInterface::FORM];
    }

    /**
     * Returns the consumer object with the $consumerKey and $token
     *
     * @param string $consumerKey
     * @param string $token
     * @return \PSX\Oauth\Data\Credentials
     */
    abstract protected function getConsumer($consumerKey, $token);

    /**
     * Returns the response depending on the $credentials and $request
     *
     * @param \PSX\Oauth\Data\Credentials $credentials
     * @param \PSX\Oauth\Data\Request $request
     * @return \PSX\Oauth\Data\Response
     */
    abstract protected function getResponse(Credentials $credentials, Request $request);
}
