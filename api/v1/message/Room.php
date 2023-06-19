<?php
namespace V1\Chat;

class Room extends \Controller

{
    private $authId = null;
    private $account;
    private $defaultAvatarUrl = null;
    public function __construct()
    {
        parent::__construct();
        $auth = new \V1\Auth;
        if ($auth->check()) {
            $this->authId = $auth->jwt->user->id;
        }
        $systemConfig = $this->getSystemConfig("DEFAULT_AVATAR");
        if (isset($systemConfig->DEFAULT_AVATAR)) {
            $this->defaultAvatarUrl = $systemConfig->DEFAULT_AVATAR;
        }

    }

    private function item($room)
    {

        $format = $this->getSystemFormat();
        $sendAt = $format->DateTimeConvert($room["sent_at"]);
        $sendBy = (int) $room["sent_by"];
        $createdAt = $format->DateTimeConvert($room["created_at"]);
        $createdBy = (int) $room["created_by"];
        $updatedAt = $format->DateTimeConvert($room["updated_at"]);
        $updatedBy = (int) $room["updated_by"];
        $accounts = $this->getAccounts($room["id"]);
        $room["avatarUrl"] = $this->defaultAvatarUrl;
        if ($room["type"] === "INDIVIDUAL" && count($accounts) === 2) {
            if (is_null($room["title"])) {
                foreach ($accounts as $acc) {
                    if ($acc->id !== $this->authId) {
                        $room["title"] = is_null($acc->nickname) ? $acc->fullName : $acc->nickname;
                    }
                }
            }
            foreach ($accounts as $acc) {
                if ($acc->id !== $this->authId && $acc->avatarUrl !== "DEFAULT_AVATAR") {
                    $room["avatarUrl"] = $acc->avatarUrl;
                }
            }

        }

        return (object) [
            "id" => (int) $room["id"],
            "title" => $room["title"],
            "type" => $room["type"],
            "avatarUrl" => $room["avatarUrl"],
            "lastMessage" => (object) [
                "id" => (int) $room["message_id"],
                "content" => $room["message_content"],
                "sentAt" => $sendAt,
                "sentBy" => $sendBy,
            ],
            "accounts" => $accounts,
            "createdAt" => $createdAt,
            "createdBy" => $createdBy,
            "updatedAt" => $sendAt !== $updatedAt ? $updatedAt : null,
            "updatedBy" => $sendAt !== $updatedAt ? $updatedBy : null,
        ];
    }

    private function getAccounts($roomId)
    {
        $sql = "SELECT
                    a.id,
                    a.first_name,
                    a.last_name,
                    a.avatar_url,
                    r_a.nickname
                FROM
                    tbl_msg_room_account as r_a,
                    tbl_accounts as a
                WHERE
                    a.deleted = 0
                    AND a.id = r_a.account_id
                    AND r_a.room_id = '{$roomId}'
        ";
        // echo $sql;
        $stmt = $this->_query($sql);
        $data = array();
        while ($row = $stmt->fetch_assoc()) {
            $item = (object) [
                "id" => (int) $row["id"],
                "fullName" => $row["first_name"] . " " . $row["last_name"],
                "avatarUrl" => $row["avatar_url"] === "DEFAULT_AVATAR" ? $this->defaultAvatarUrl : $row["avatar_url"],
                "nickname" => $row["nickname"],
            ];
            array_push($data, $item);
        }
        return $data;
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        $sql = "SELECT
                r.id,
                r.title,
                r.type,
                m.id as message_id,
                rma.id as m_id,
                m.content as message_content,
                m.created_by as sent_by,
                m.created_at as sent_at,
                r.created_at,
                r.created_by,
                r.updated_at,
                r.updated_by
            FROM
                tbl_msg_room as r,
                tbl_msg_message as m,
                tbl_msg_room_account as ra,
                tbl_msg_room_message_account as rma

            WHERE
                r.deleted = 0
                AND ra.room_id = r.id
                AND rma.message_id = m.id
                AND rma.account_id = ra.account_id
                AND m.room_id = r.id
                AND r.id = m.room_id
                AND m.id = (SELECT max(m_m.id) from tbl_msg_message as m_m WHERE m_m.room_id = r.id )
                AND ra.account_id = '{$this->authId}'
            ORDER BY m.created_at desc
        ";
        $stmt = $this->_query($sql);
        $data = array();
        while ($row = $stmt->fetch_assoc()) {
            array_push($data, $this->item($row));
        }
        return (object) [
            "error" => 0,
            "data" => (object) [
                "data" => $data,
            ],
        ];

        return (object) [
            "error" => 1,
        ];

    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        $sql = "SELECT
                r.id,
                r.title,
                r.type,
                m.id as message_id,
                rma.id as m_id,
                m.content as message_content,
                m.created_by as sent_by,
                m.created_at as sent_at,
                r.created_at,
                r.created_by,
                r.updated_at,
                r.updated_by
            FROM
                tbl_msg_room as r,
                tbl_msg_message as m,
                tbl_msg_room_account as ra,
                tbl_msg_room_message_account as rma

            WHERE
                r.deleted = 0
                AND ra.room_id = r.id
                AND rma.message_id = m.id
                AND rma.account_id = ra.account_id
                AND m.room_id = r.id
                AND r.id = m.room_id
                AND m.id = (SELECT max(m_m.id) from tbl_msg_message as m_m)
                AND r.id = '{$pathData->id}'
                AND ra.account_id = '{$this->authId}'
        ";
        $stmt = $this->_query($sql);
        if ($stmt->num_rows === 1) {
            $data = $stmt->fetch_assoc();
            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => $this->item($data),
                ],
            ];
        }

        return (object) [
            "error" => 1,
        ];
    }
}
