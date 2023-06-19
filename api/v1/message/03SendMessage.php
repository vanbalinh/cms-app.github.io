<?php
namespace V1\Chat;

class SendMessage extends \V1\Chat\Message

{
    public function __construct()
    {
        parent::__construct();
    }

    public function apiSend($pathData, $getData, $bodyData)
    {
        if (is_null($this->authId)) {
            return (object) ["error" => 401];
        }
        if (!isset($bodyData->roomId) || !is_integer($bodyData->roomId) || !isset($bodyData->content) || !is_string($bodyData->content)) {
            return (object) ["error" => 400];
        }
        $this->initRoomAccount();
        $bodyData->accountId = $this->authId;
        if ($this->_exist($bodyData, ["roomId", "accountId"])->error === 0) {
            $roomId = $bodyData->roomId;
            $resAccount = $this->_fetch();
            $accounts = array();
            if ($resAccount->error === 0) {
                $accounts = $resAccount->data->data;
            }
            if (count($accounts) > 0) {
                $this->initMessage();
                $resMsg = $this->_create($bodyData);

                if ($resMsg->error === 0) {
                    $msg = $resMsg->data->data;
                    $this->initRoomMessageAccount();
                    $error = 0;
                    foreach ($accounts as $key => $account) {
                        # code...
                        $data = (object) [];
                        $data->messageId = $msg->id;
                        $data->accountId = (int) $account->id;
                        $data->isReceived = (int) $account->id === (int) $this->authId;
                        $data->isSeen = $account->id === $this->authId;
                        $data->roomId = $roomId;

                        if ($this->_create($data)->error !== 0) {
                            $error = -1;
                            goto end;
                        };
                    }
                    end:
                    if ($error === 0) {
                        $sql = "SELECT m.id as msg_id, m.content as content, rma.id as id, rma.deleted as deleted, rma.created_at as created_at, rma.created_by as created_by, rma.updated_at as updated_at, rma.updated_by as updated_by
                            FROM tbl_msg_message as m, tbl_msg_room_message_account as rma
                        WHERE
                            m.deleted = 0
                            AND rma.deleted = 0
                            AND rma.message_id = m.id
                            AND m.id = '{$msg->id}'
                            AND rma.account_id = '{$this->authId}'

                            LIMIT 0,1";
                        $stmt = $this->_query($sql);
                        $data = $stmt->fetch_assoc();
                        $message = $this->item($data);

                        $seenAccounts = $this->getSeenAccounts($bodyData->roomId);
                        $receivedAccounts = $this->getReceivedAccounts($bodyData->roomId);

                        if (isset($bodyData->uid)) {
                            $message->uid = $bodyData->uid;
                        }
                        return (object) [
                            "error" => 0,
                            "data" => (object) [
                                "data" => $message,
                                "roomId" => $roomId,
                                "seenAccounts" => $seenAccounts,
                                "receivedAccounts" => $receivedAccounts,
                            ],
                        ];
                    }
                }
            }
            return (object) ["error" => -1];
        } else {
            return (object) ["error" => 403];
        }
    }
}
