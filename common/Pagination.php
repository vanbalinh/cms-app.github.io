<?php
namespace Common;

class Pagination
{
    public $page, $pageSize, $total, $totalPage = 0, $from = 0, $to = 0, $allowNext = false, $allowPrev = false;
    private $sql = "";
    private $check = false;
    public function __construct($page, $pageSize, $total)
    {
        if ($page != null && $pageSize != null) {
            $limit_from = ($page - 1) * $pageSize;
            $this->sql = " LIMIT " . $limit_from . "," . $pageSize;

            $totalPage = (int) ($total / $pageSize) + ($total % $pageSize === 0 ? 0 : 1);
            $this->page = $page;
            $this->pageSize = $pageSize;
            $this->total = $total;
            $this->totalPage = $totalPage;
            $this->from = ($page - 1) * $pageSize + 1;
            $this->from = $this->from > $total ? null : $this->from;
            $this->to = $page * $pageSize;
            $this->to = is_null($this->from) ? null : ($this->to < $total ? $this->to : $total);
            $this->allowPrev = $page <= 1 ? false : true;
            $this->allowNext = $page >= $totalPage ? false : true;
            $this->check = true;
        }
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function check()
    {
        return $this->check;
    }
}
