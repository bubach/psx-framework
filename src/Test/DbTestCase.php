<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Test;

/**
 * Base test class for database test cases. In the future we might implement our
 * own database test case which does not depend on the PHPUnit database
 * extension and is completely based on the doctrine DBAL connection. So in your
 * test case please implement only the getDataSet method and return a DataSet
 * instance
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class DbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected static $con;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @internal
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        if (!Environment::hasConnection()) {
            $this->markTestSkipped('Database connection not available');
        }

        if (self::$con === null) {
            self::$con = Environment::getService('connection');
        }

        if ($this->connection === null) {
            $this->connection = self::$con;
        }

        return $this->createDefaultDBConnection($this->connection->getWrappedConnection(), Environment::getService('config')->get('psx_sql_db'));
    }

    /**
     * @internal
     * @return \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return new \PHPUnit_Extensions_Database_Operation_Composite([
            new Operation\Truncate(),
            new Operation\Insert()
        ]);
    }
}
