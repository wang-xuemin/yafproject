<?php

/**
 * Class DBMongo
 * @desc mongo操作类
 * @author wangxuemin
 */
class DBMongo
{

    private static $manager;

    /**
     * @link https://php.net/manual/en/mongodb-driver-manager.construct.php
     * @return MongoDB\Driver\Manager
     */
    public static function manager()
    {
        try {
            if (empty(self::$manager)) {
                $config = Yaf_Registry::get('config');
                $host = $config->mongo->host;
                $port = $config->mongo->port;
                $user = $config->mongo->user;
                $password = $config->mongo->password;
                $db = $config->mongo->db;
                $url = sprintf("mongodb://%s:%s@%s:%-5s/%s", $user, $password, $host, $port, $db);
                self::$manager = new MongoDB\Driver\Manager($url);
            }
        } catch (Exception $exception) {
            exit($exception->getMessage());
        }
        return self::$manager;
    }

    /**
     * @param string $collection
     * @return string
     */
    private static function getNamespace($collection = '')
    {
        $config = Yaf_Registry::get('config');
        return sprintf("%s.%s", $config->mongo->db, $collection);
    }

    /**
     * 添加文档
     * @link https://php.net/manual/en/mongodb-driver-bulkwrite.insert.php
     * @param string $collection
     * @param array $data
     * @return array
     */
    public static function insert($collection = '', $data = array())
    {
        $result = array('id' => '', 'insert_count' => 0);
        $bulk = new MongoDB\Driver\BulkWrite();
        $document = array_merge(array('_id' => new MongoDB\BSON\ObjectID()), $data);
        $_id = $bulk->insert($document);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $writeResult = self::manager()->executeBulkWrite(self::getNamespace($collection), $bulk, $writeConcern);
        if ($writeResult->getInsertedCount() > 0) {
            $result = array('_id' => $_id, 'insert_count' => $writeResult->getInsertedCount());
        }
        return $result;
    }

    /**
     * 批量添加文档
     * @link https://php.net/manual/en/mongodb-driver-bulkwrite.insert.php
     * @param string $collection
     * @param array $data
     * @return array
     */
    public static function inserts($collection = '', $data = array())
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        foreach ($data as $datum) {
            $bulk->insert($datum);
        }
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $writeResult = self::manager()->executeBulkWrite(self::getNamespace($collection), $bulk, $writeConcern);
        return array('insert_count' => $writeResult->getInsertedCount());
    }

    /**
     * 批量删除文档
     * @link https://php.net/manual/en/mongodb-driver-bulkwrite.delete.php
     * @param string $collection
     * @param array|object $filter
     * @param array $deleteOptions
     * @return array
     */
    public static function delete($collection = '', $filter, $deleteOptions = array('limit' => false))
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete($filter, $deleteOptions);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $writeResult = self::manager()->executeBulkWrite(self::getNamespace($collection), $bulk, $writeConcern);
        return array('delete_count' => $writeResult->getDeletedCount());
    }

    /**
     * 批量更新文档
     * @link https://php.net/manual/en/mongodb-driver-bulkwrite.update.php
     * @param string $collection
     * @param object|array $filter
     * @param object|array $newObj
     * @param array $updateOptions
     * bool $updateOptions['multi'] 为true时，更新一条。为false批到批量更新
     * bool $updateOptions['upsert'] 文档不存在时，为true则添加。为false不添加
     * @return array
     */
    public static function update($collection = '', $filter, $newObj, $updateOptions = array())
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update($filter, $newObj, $updateOptions);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $writeResult = self::manager()->executeBulkWrite(self::getNamespace($collection), $bulk, $writeConcern);
        return array(
            'upserted_count' => $writeResult->getUpsertedCount(),
            'update_count' => $writeResult->getMatchedCount()
        );
    }

    /**
     * 查询文档
     * @link https://php.net/manual/en/mongodb-driver-query.construct.php
     * @param string $collection
     * @param array|object $filter
     * @param array $queryOptions
     * @return MongoDB\Driver\Cursor
     * @throws MongoDB\Driver\Exception\Exception
     */
    public static function find($collection = '', $filter, $queryOptions = array())
    {
        $query = new MongoDB\Driver\Query($filter, $queryOptions);
        return self::manager()->executeQuery(self::getNamespace($collection), $query);
    }

}