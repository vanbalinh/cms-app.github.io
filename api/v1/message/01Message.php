<?php
namespace V1\Chat;

class Message extends \Controller

{
    public $authId = null;
    private $systemConfig;
    public function __construct()
    {
        parent::__construct();
        $auth = new \V1\Auth;
        if ($auth->check()) {
            $this->authId = $auth->jwt->user->id;
        }
        $this->systemConfig = $this->getSystemConfig("DATE_TIME_FORMAT");
    }

    public function initRoomAccount()
    {
        $this->init(
            "tbl_msg_room_account",
            (object) [
                "room_id" => (object) [
                    "name" => "roomId",
                    "type" => "number",
                    "required" => true,
                ],
                "account_id" => (object) [
                    "name" => "accountId",
                    "type" => "number",
                    "required" => true,
                ],
            ]
        );
    }
    public function initMessage()
    {
        $this->init(
            "tbl_msg_message",
            (object) [
                "rool_id" => (object) [
                    "name" => "roomId",
                    "type" => "int",
                    "required" => true,
                ],
                "room_id" => (object) [
                    "name" => "roomId",
                    "type" => "int",
                    "required" => true,
                ],
                "content" => (object) [
                    "required" => true,
                ],
            ]
        );
    }

    public function initRoomMessageAccount()
    {
        $this->init(
            "tbl_msg_room_message_account",
            (object) [
                "message_id" => (object) [
                    "name" => "messageId",
                    "type" => "int",
                    "required" => true,
                ],
                "account_id" => (object) [
                    "name" => "accountId",
                    "type" => "int",
                    "required" => true,
                ],
                "is_received" => (object) [
                    "name" => "isReceived",
                    "type" => "boolean",
                ],
                "is_seen" => (object) [
                    "name" => "isSeen",
                    "type" => "boolean",
                ],
            ]
        );
    }

    public function item($msg)
    {
        $format = $this->getSystemFormat();
        $sendAt = $format->DateTimeConvert($msg["created_at"]);
        $sendBy = (int) $msg["created_by"];
        $updatedAt = $format->DateTimeConvert($msg["updated_at"]);
        $updatedBy = (int) $msg["updated_by"];
        return (object) [
            "id" => (int) $msg["msg_id"],
            "content" => $msg["content"],
            "sentAt" => $sendAt,
            "sentBy" => $sendBy,
            "updatedAt" => $sendAt !== $updatedAt ? $updatedAt : null,
            "updatedBy" => $sendAt !== $updatedAt ? $updatedBy : null,
        ];
    }

    public function getSeenAccounts($roomId)
    {
        $sql = "SELECT
            (SELECT
                max(id)
            FROM tbl_msg_room_message_account as rma1
            WHERE
                rma1.account_id = rma.account_id
                AND rma1.is_seen = '1'
            ) as id,
            max(m.id) as msg_id,
            ra.account_id as account_id
        FROM
            tbl_msg_message as m,
            tbl_msg_room_message_account as rma,
            tbl_msg_room_account as ra
        WHERE
            m.deleted = 0
            AND rma.is_seen = '1'
            AND rma.account_id = ra.account_id
            AND rma.message_id = m.id
            AND ra.room_id = '{$roomId}'
        GROUP BY ra.account_id";
        $stmt = $this->_query($sql);
        $data = array();
        while ($row = $stmt->fetch_assoc()) {
            array_push($data, (object) [
                "id" => (int) $row["msg_id"],
                "accountId" => (int) $row["account_id"],
            ]);
        }
        return $data;
    }

    public function getReceivedAccounts($roomId)
    {
        $sql = "SELECT
            (SELECT
                max(id)
            FROM tbl_msg_room_message_account as rma1
            WHERE
                rma1.account_id = rma.account_id
                AND rma1.is_received = '1'
            ) as id,
            max(m.id) as msg_id,
            ra.account_id as account_id
        FROM
            tbl_msg_message as m,
            tbl_msg_room_message_account as rma,
            tbl_msg_room_account as ra
        WHERE
            m.deleted = 0
            AND rma.is_received = '1'
            AND rma.account_id = ra.account_id
            AND rma.message_id = m.id
            AND ra.room_id = '{$roomId}'
        GROUP BY account_id";
        $stmt = $this->_query($sql);
        $data = array();
        while ($row = $stmt->fetch_assoc()) {
            array_push($data, (object) [
                "id" => (int) $row["msg_id"],
                "accountId" => (int) $row["account_id"],
            ]);
        }
        return $data;
    }

}
