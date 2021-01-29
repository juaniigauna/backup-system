<?php
namespace Modules;

class DataBase {
    private string $host, $user, $password, $dbName;
    private $connection;
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
        $listTables = $this->connection->query("SHOW TABLES");
        while ($table = mysqli_fetch_array($listTables)) {
            $tables[] = $table[0];
        }
        return $tables;
    }
    public function getRowsFromTable($tableName) {
        $rows = array();
        $rowList = $this->connection->query("SELECT * FROM $tableName");
        foreach ($rowList as $row) {
            $rows[] = $row;
        }
        return [
            "tableName" => $tableName,
            "rows" => $rows
        ];
    }
    public function getCollection() {
        $tables = $this->getTables();
        $collection = array();
        foreach ($tables as $table) {
            $collection[] = $this->getRowsFromTable($table);
        }
        return $collection;
    }
}
