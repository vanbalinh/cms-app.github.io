<?php
namespace V1\Chat;

class MarkSeenMessage extends \V1\Chat\Message

{
    public function __construct()
    {
        parent::__construct();
    }

    public function apiMarkSeen($pathData, $getData, $bodyData)
    {
        if (is_null($this->authId)) {
            return (object) ["error" => 401];
        }
        if (!isset($bodyData->roomId) || !is_integer($bodyData->roomId) || !isset($bodyData->messageId) || !is_integer($bodyData->messageId)) {
            return (object) ["error" => 400];
        }
        $this->initRoomAccount();
        $bodyData->accountId = $this->authId;
        if ($this->_exist($bodyData, ["roomId", "accountId"])->error === 0) {
            $roomId = $bodyData->roomId;
            // $bodyData->messageId
            $connect = new \Config\Connect;
            $conn = $connect->conn;
            // $query = $conn->prepare($sql);

            $sql = "UPDATE
                tbl_msg_room_message_account
                SET is_seen = '1', is_received = '1'
                WHERE
                    account_id = ?
                    AND message_id in (SELECT id
                                    FROM tbl_msg_message
                                    WHERE room_id = ?
                                        AND id <= ?
                )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $this->authId, $roomId, $bodyData->messageId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return (object) [
                    "data" => (object) [
                        "data" => (object) [
                            "roomId" => (int) $roomId,
                            "messageId" => (int) $bodyData->messageId,
                            "accountId" => (int) $this->authId,
                        ],
                    ],
                    "error" => 0,
                ];
            }
            // echo $sql;
            return (object) ["error" => -1];
        } else {
            return (object) ["error" => 403];
        }
    }
}
