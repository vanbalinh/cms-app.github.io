<?php
namespace Controller;

class ControllerTree extends ControllerFetch
{
    public function __construct()
    {
        parent::__construct();

    }

    private function recursive($data = [], $parentId = null)
    {
        $parentKey = $this->config->selfKey;
        $result = array();
        foreach ($data as $item) {
            if (
                (is_null($item->$parentKey) && is_null($parentId))
                ||
                (!is_null($item->$parentKey) && !is_null($parentId) && (int) $item->$parentKey->id === (int) $parentId)
            ) {
                $item->children = $this->recursive($data, (int) $item->id);
                array_push($result, $item);
            }
        }
        return $result;
    }

    public function tree($data = [])
    {
        $this->config->hiddenItemInfo = true;
        $res = $this->fetch((object) $data);
        if ($res->error === 0) {
            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => isset($this->config->selfKey) ? $this->recursive($res->data->data) : $res->data->data,
                ],
            ];
        } else {
            return $res;
        }
    }
}
