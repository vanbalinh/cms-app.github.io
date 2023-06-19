<?php
namespace V1\Chat;

class GetMessage extends \V1\Chat\Message

{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  API GET MESSAGE
     */
    public function apiFetchAllMessageByRoom($pathData, $getData, $bodyData)
    {
        if (is_null($this->authId)) {
            return (object) ["error" => 401];
        }
        if (!isset($pathData->roomId) || !is_integer($pathData->roomId)) {
            return (object) ["error" => 400];
        }
        $this->initRoomAccount();
        $pathData->accountId = $this->authId;
        if ($this->_exist($pathData, ["roomId", "accountId"])->error === 0) {
            $sql = "SELECT
                    m.id as msg_id,
                    m.content as content,
                    rma.id as id,
                    rma.deleted as deleted,
                    rma.created_at as created_at,
                    rma.created_by as created_by,
                    rma.updated_at as updated_at,
                    rma.updated_by as updated_by
                FROM
                    tbl_msg_message as m,
                    tbl_msg_room_message_account as rma
                WHERE
                    m.deleted = 0
                    AND rma.deleted = 0
                    AND rma.message_id = m.id
                    AND m.room_id = {$pathData->roomId}
                    AND rma.account_id = {$this->authId}
                ORDER BY m.id desc
                LIMIT 0,20";

            $stmt = $this->_query($sql);
            $data = array();
            $this->account = new \V1\Account;
            while ($row = $stmt->fetch_assoc()) {
                array_push($data, $this->item($row));
            }
            $seenAccounts = $this->getSeenAccounts($pathData->roomId);
            $receivedAccounts = $this->getReceivedAccounts($pathData->roomId);

            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => $data,
                    "seenAccounts" => $seenAccounts,
                    "receivedAccounts" => $receivedAccounts,
                ],
            ];
        } else {
            return (object) ["error" => 403];
        }
    }

    /**
     *  API Lấy tin nhắn mới hơn
     */
    public function apiFetchAllMessageFrom($pathData, $getData, $bodyData)
    {
        if (is_null($this->authId)) {
            return (object) ["error" => 401];
        }
        if (!isset($pathData->roomId) || !is_integer($pathData->roomId)) {
            return (object) ["error" => 400];
        }
        if (!isset($pathData->messageId) || !is_integer($pathData->messageId)) {
            return (object) ["error" => 400];
        }
        $this->initRoomAccount();
        $pathData->accountId = $this->authId;
        if ($this->_exist($pathData, ["roomId", "accountId"])->error === 0) {
            $sql = "SELECT
                    m.id as msg_id,
                    m.content as content,
                    rma.id as id,
                    rma.deleted as deleted,
                    rma.created_at as created_at,
                    rma.created_by as created_by,
                    rma.updated_at as updated_at,
                    rma.updated_by as updated_by
                FROM tbl_msg_message as m, tbl_msg_room_message_account as rma
                WHERE
                    m.deleted = 0
                    AND rma.deleted = 0
                    AND rma.message_id = m.id
                    AND m.room_id = {$pathData->roomId}
                    AND rma.account_id = {$this->authId}
                    AND m.id > {$pathData->messageId}
                ORDER BY id asc
                LIMIT 0,20";

            $stmt = $this->_query($sql);
            $data = array();
            $this->account = new \V1\Account;
            while ($row = $stmt->fetch_assoc()) {
                array_push($data, $this->item($row));
            }
            $data = array_reverse($data);

            return (object) [
                "error" => 0,
                "data" => (object) ["data" => $data],
            ];
        } else {
            return (object) ["error" => 403];
        }
    }

    /**
     *  API lấy tin nhắn cũ hơn
     */
    public function apiFetchAllMessageTo($pathData, $getData, $bodyData)
    {
        if (is_null($this->authId)) {
            return (object) ["error" => 401];
        }
        if (!isset($pathData->roomId) || !is_integer($pathData->roomId)) {
            return (object) ["error" => 400];
        }
        if (!isset($pathData->messageId) || !is_integer($pathData->messageId)) {
            return (object) ["error" => 400];
        }
        $this->initRoomAccount();
        $pathData->accountId = $this->authId;
        if ($this->_exist($pathData, ["roomId", "accountId"])->error === 0) {
            $sql = "SELECT
                    m.id as msg_id,
                    m.content as content,
                    rma.id as id,
                    rma.deleted as deleted,
                    rma.created_at as created_at,
                    rma.created_by as created_by,
                    rma.updated_at as updated_at,
                    rma.updated_by as updated_by
                FROM tbl_msg_message as m, tbl_msg_room_message_account as rma
                WHERE
                    m.deleted = 0
                    AND rma.deleted = 0
                    AND rma.message_id = m.id
                    AND m.room_id = {$pathData->roomId}
                    AND rma.account_id = {$this->authId}
                    AND m.id < {$pathData->messageId}
                ORDER BY id desc
                LIMIT 0,20";

            $stmt = $this->_query($sql);
            $data = array();
            $this->account = new \V1\Account;
            while ($row = $stmt->fetch_assoc()) {
                array_push($data, $this->item($row));
            }

            return (object) [
                "error" => 0,
                "data" => (object) ["data" => $data],
            ];
        } else {
            return (object) ["error" => 403];
        }
    }
}
