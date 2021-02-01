<?php
namespace Modules\Backup;

class Export {
    public array $data;
    public function __construct(array $data) {
        $this->data = $data;
    }
    public function convertDataToJson() {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS);
    }
    public function createJsonFile() {
        $path = "backups/backup_".date("Y-m-d_h:m:s").'.json';
        $file = fopen($path, "w+");
        if (!$file) throw new \Exception("Has been occurred an error while make the json file.");
        return [ "file" => $file, "path" => $path ];
    }
    public function start() {
        try {
            $jsonFile = $this->createJsonFile();
            $data = $this->convertDataToJson();
            fwrite($jsonFile['file'], $data);
        } catch (\Exception $error) {
            return false;
        }
        return $jsonFile['path'];
    }
}
