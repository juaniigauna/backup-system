<?php
namespace Modules;

class DataBase {
    private string $host, $user, $password, $dbName;
    public $connection;
    public function __construct(string $host = '', $user = '', $password = '', $dbName = '') {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbName = $dbName;
        $this->connection = $this->connect();
    }
    private function connect() {            
        $connection = new \Mysqli($this->host, $this->user, $this->password, $this->dbName);
        return $connection;
    }
    public function getTables() {
        $tables = array();
        $tableList = $this->connection->query("SHOW TABLES");
        while ($table = mysqli_fetch_array($tableList)) {
            $tables[] = $table[0];
        }
        return $tables;
    }
    public function getTableRows($tableName) {
        $rows = array();
        $rowList = $this->connection->query("SELECT * FROM $tableName");
        foreach ($rowList as $row) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getTableStructure($tableName) {
        $fields = array();
        $fieldList = $this->connection->query("SHOW COLUMNS FROM $tableName");
        foreach ($fieldList as $field) {
            $fields[] = $field;
        }
        return $fields;
    }
    public function getCollection() {
        $tables = $this->getTables();
        $collection = array();
        foreach ($tables as $tableName) {
            $collection[] = [
                "tableName" => $tableName,
                "structure" => $this->getTableStructure($tableName),
                "rows" => $this->getTableRows($tableName)
            ];
        }
        return $collection;
    }
}
