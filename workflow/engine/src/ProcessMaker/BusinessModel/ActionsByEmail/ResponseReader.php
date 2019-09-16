<?php

namespace ProcessMaker\BusinessModel\ActionsByEmail;

use AbeConfigurationPeer;
use AbeResponses;
use ActionsByEmailCoreClass;
use AppDelegation;
use AppNotes;
use Bootstrap;
use Cases;
use Criteria;
use EmailServerPeer;
use Exception;
use G;
use Illuminate\Support\Facades\Crypt;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use PMLicensedFeatures;
use ProcessMaker\BusinessModel\ActionsByEmail;
use ProcessMaker\BusinessModel\EmailServer;
use ProcessMaker\ChangeLog\ChangeLog;
use ResultSet;
use WsBase;

/**
 * Class ResponseReader
 * @package ProcessMaker\BusinessModel\ActionsByEmail
 */
class ResponseReader
{
    /*----------------------------------********---------------------------------*/
    private $channel = "ActionsByEmail";
    private $case = [];
    private $messageResponseError = null;

    /**
     * @return string
     */
    public function getMessageResponseError()
    {
        return $this->messageResponseError;
    }

    /**
     * @param string $messageResponseError
     */
    public function setMessageResponseError($messageResponseError)
    {
        $this->messageResponseError = $messageResponseError;
    }

    /**
     * Read the Action by Email listener inbox looking for new messages
     */
    public function actionsByEmailEmailResponse()
    {
        try {
            if (!extension_loaded('imap')) {
                G::outRes(G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", ['php_imap']) . "\n");
                exit;
            }
            if (PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
                $criteriaAbe = new Criteria();
                $criteriaAbe->add(AbeConfigurationPeer::ABE_TYPE, "RESPONSE");
                $resultAbe = AbeConfigurationPeer::doSelectRS($criteriaAbe);
                $resultAbe->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($resultAbe->next()) {
                    $dataAbe = $resultAbe->getRow();
                    $this->getAllEmails($dataAbe);
                }
            }
        } catch (Exception $e) {
            Bootstrap::registerMonolog(
                $this->channel,
                $e->getCode() != 0 ? $e->getCode() : 300,
                $e->getMessage(),
                $this->case,
                config("system.workspace"),
                'processmaker.log'
            );
        }
    }

    /**
     * Decrypt password of Email Server
     * @param array $emailSetup
     * @return mixed|string
     */
    private function decryptPassword(array $emailSetup)
    {
        $pass = isset($emailSetup['MESS_PASSWORD']) ? $emailSetup['MESS_PASSWORD'] : '';
        $passDec = G::decrypt($pass, 'EMAILENCRYPT');
        $auxPass = explode('hash:', $passDec);
        if (count($auxPass) > 1) {
            if (count($auxPass) == 2) {
                $pass = $auxPass[1];
            } else {
                array_shift($auxPass);
                $pass = implode('', $auxPass);
            }
        }
        return $pass;
    }

    /**
     * Get all Email of server listener
     * @param array $dataAbe
     */
    public function getAllEmails(array $dataAbe)
    {
        try {
            $emailServer = new EmailServer();
            $emailSetup = (!is_null(EmailServerPeer::retrieveByPK($dataAbe['ABE_EMAIL_SERVER_RECEIVER_UID']))) ?
                $emailServer->getEmailServer($dataAbe['ABE_EMAIL_SERVER_RECEIVER_UID'], true) :
                $emailServer->getEmailServerDefault();
            if (empty($emailSetup) || (empty($emailSetup['MESS_INCOMING_SERVER']) && $emailSetup['MESS_INCOMING_PORT'] == 0)) {
                throw (new Exception(G::LoadTranslation('ID_ABE_LOG_CANNOT_READ'), 500));
            }
            $mailbox = new Mailbox(
                '{'. $emailSetup['MESS_INCOMING_SERVER'] . ':' . $emailSetup['MESS_INCOMING_PORT'] . '/imap/ssl/novalidate-cert}INBOX',
                $emailSetup['MESS_ACCOUNT'],
                $this->decryptPassword($emailSetup)
            );

            // Read all messages into an array
            $mailsIds = $mailbox->searchMailbox('UNSEEN');
            if ($mailsIds) {
                // Get the first message and save its attachment(s) to disk:
                foreach ($mailsIds as $key => $mailId) {
                    /** @var IncomingMail $mail */
                    $mail = $mailbox->getMail($mailId, false);
                    if (!empty($mail->textPlain)) {
                        preg_match("/{(.*)}/", $mail->textPlain, $matches);
                        if ($matches) {
                            try {
                                $dataEmail = G::json_decode(Crypt::decryptString($matches[1]), true);
                            } catch (Exception $e) {
                                Bootstrap::registerMonolog(
                                    $this->channel,
                                    300,
                                    G::LoadTranslation('ID_ABE_RESPONSE_CANNOT_BE_IDENTIFIED'),
                                    [],
                                    config("system.workspace"),
                                    'processmaker.log'
                                );
                                $mailbox->markMailAsRead($mailId);
                                continue;
                            }
                            $dataAbeReq = loadAbeRequest($dataEmail['ABE_REQ_UID']);
                            if (config("system.workspace") === $dataEmail['workspace']
                                && (array_key_exists('ABE_UID', $dataAbeReq) && $dataAbeReq['ABE_UID'] == $dataAbe['ABE_UID'])) {
                                $this->case = $dataEmail;
                                try {
                                    $appDelegate = new AppDelegation();
                                    $alreadyRouted = $appDelegate->alreadyRouted($this->case["appUid"], $this->case["delIndex"]);
                                    //Verify if the current case is already routed.
                                    if ($alreadyRouted) {
                                        $this->setMessageResponseError(G::LoadTranslation('ID_ABE_RESPONSE_ALREADY_ROUTED'));
                                        throw (new Exception(G::LoadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED'), 400));
                                    }
                                    $this->processABE($this->case, $mail, $dataAbe);
                                    Bootstrap::registerMonolog(
                                        $this->channel,
                                        100, // DEBUG
                                        G::LoadTranslation('ID_ABE_LOG_PROCESSED_OK'),
                                        $this->case,
                                        config("system.workspace"),
                                        'processmaker.log'
                                    );
                                } catch (Exception $e) {
                                    $this->sendMessageError(
                                        $this->getMessageResponseError() ? $this->getMessageResponseError() : $e->getMessage(),
                                        $this->case,
                                        $mail,
                                        $emailSetup
                                    );
                                    Bootstrap::registerMonolog(
                                        $this->channel,
                                        $e->getCode() != 0 ? $e->getCode() : 400,
                                        $e->getMessage(),
                                        $this->case,
                                        config("system.workspace"),
                                        'processmaker.log'
                                    );
                                }
                                $mailbox->markMailAsRead($mailId);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Bootstrap::registerMonolog(
                $this->channel,
                $e->getCode() != 0 ? $e->getCode() : 500,
                $e->getMessage(),
                $this->case,
                config("system.workspace"),
                'processmaker.log'
            );
        }
    }

    /**
     * Derivation of the case with the mail information
     * @param array $caseInfo
     * @param IncomingMail $mail
     * @param array $dataAbe
     * @throws Exception
     */
    public function processABE(array $caseInfo, IncomingMail $mail, array $dataAbe = [])
    {
        try {
            $actionsByEmail = new ActionsByEmail();
            $actionsByEmail->verifyLogin($caseInfo['appUid'], $caseInfo['delIndex']);

            $case = new Cases();
            $caseFieldsABE = $case->loadCase($caseInfo['appUid'], $caseInfo['delIndex']);

            $actionsByEmailCore = new ActionsByEmailCoreClass();
            $actionField = str_replace(
                $actionsByEmailCore->getPrefix(),
                '',
                $dataAbe['ABE_ACTION_FIELD']
            );
            $dataField = [];
            $dataField[$actionField] = $caseInfo['fieldValue'];
            $actionBodyField = str_replace(
                $actionsByEmailCore->getPrefix(),
                '',
                $dataAbe['ABE_ACTION_BODY_FIELD']
            );
            $textPlain = $mail->textPlain;
            $textPlain = substr($textPlain, 0, strpos($textPlain, "/="));
            $dataField[$actionBodyField] = $textPlain;
            $caseFieldsABE['APP_DATA'] = array_merge($caseFieldsABE['APP_DATA'], $dataField);

            $dataResponses = [];
            $dataResponses['ABE_REQ_UID'] = $caseInfo['ABE_REQ_UID'];
            $dataResponses['ABE_RES_CLIENT_IP'] = 'localhost';
            $dataResponses['ABE_RES_DATA'] = serialize($dataField);
            $dataResponses['ABE_RES_STATUS'] = 'PENDING';
            $dataResponses['ABE_RES_MESSAGE'] = '';

            try {
                $abeAbeResponsesInstance = new AbeResponses();
                $dataResponses['ABE_RES_UID'] = $abeAbeResponsesInstance->createOrUpdate($dataResponses);
            } catch (Exception $e) {
                Bootstrap::registerMonolog(
                    $this->channel,
                    300,
                    $e->getMessage(),
                    $this->case,
                    config("system.workspace"),
                    'processmaker.log'
                );
            }

            ChangeLog::getChangeLog()
                ->getUsrIdByUsrUid($caseFieldsABE['CURRENT_USER_UID'], true)
                ->setSourceId(ChangeLog::FromABE);

            $caseFieldsABE['CURRENT_DYNAFORM'] = '';
            $caseFieldsABE['USER_UID'] = $caseFieldsABE['CURRENT_USER_UID'];
            $caseFieldsABE['OBJECT_TYPE'] = '';

            $case->updateCase($caseInfo['appUid'], $caseFieldsABE);

            try {
                $ws = new WsBase();
                $result = $ws->derivateCase(
                    $caseFieldsABE['CURRENT_USER_UID'],
                    $caseInfo['appUid'],
                    $caseInfo['delIndex'],
                    true
                );
                $code = (is_array($result)) ? $result['status_code'] : $result->status_code;
                if ($code != 0) {
                    throw new Exception(
                        "An error occurred while the application was being processed\n" .
                        "Error code: " . $result->status_code . "\nError message: " . $result->message
                    );
                }
            } catch (Exception $e) {
                $this->setMessageResponseError(G::LoadTranslation('ID_ABE_RESPONSE_ROUTING_FAILED'));
                throw (new Exception(G::LoadTranslation('ID_ABE_LOG_ROUTING_FAILED'), 400));
            }

            //Update AbeResponses
            $dataResponses['ABE_RES_STATUS'] = ($code == 0)? 'SENT' : 'ERROR';
            $dataResponses['ABE_RES_MESSAGE'] = ($code == 0)? '-' : $result->message;

            try {
                $abeAbeResponsesInstance = new AbeResponses();
                $abeAbeResponsesInstance->createOrUpdate($dataResponses);
            } catch (Exception $e) {
                Bootstrap::registerMonolog(
                    $this->channel,
                    300,
                    $e->getMessage(),
                    $this->case,
                    config("system.workspace"),
                    'processmaker.log'
                );
            }
            $dataAbeRequests = loadAbeRequest($caseInfo['ABE_REQ_UID']);
            //Save Cases Notes
            if ($dataAbe['ABE_CASE_NOTE_IN_RESPONSE'] == 1) {
                $customGrid = unserialize($dataAbe['ABE_CUSTOM_GRID']);
                $fieldLabel = null;
                foreach ($customGrid as $key => $value) {
                    if ($value['abe_custom_value'] == $caseInfo['fieldValue']) {
                        $fieldLabel = $value['abe_custom_label'];
                        break;
                    }
                }
                $appNotes = new AppNotes();
                $noteText = G::LoadTranslation('ID_ABE_CASE_NOTE_HEADER', ['emailAccount' => $mail->toString]) . "\n\n";
                $noteText .= G::LoadTranslation('ID_ABE_CASE_NOTE_ANSWER', ['optionLabel' => $fieldLabel ? $fieldLabel : $caseInfo['fieldValue']]) . "\n\n";
                $noteText .= G::LoadTranslation('ID_ABE_CASE_NOTE_COMMENT', ['emailBody' => $textPlain]);
                $noteContent = addslashes($noteText);
                $appNotes->postNewNote($caseInfo['appUid'], $caseFieldsABE['APP_DATA']['USER_LOGGED'], $noteContent, false);
            }
            $dataAbeRequests['ABE_REQ_ANSWERED'] = 1;
            $code == 0 ? uploadAbeRequest($dataAbeRequests) : '';
        } catch (Exception $e) {
            if ($e->getCode() == 400) {
                throw (new Exception($e->getMessage(), $e->getCode()));
            } else {
                $this->setMessageResponseError(G::LoadTranslation('ID_ABE_RESPONSE_CANNOT_BE_IDENTIFIED'));
                throw (new Exception(G::LoadTranslation('ID_ABE_LOG_CANNOT_BE_IDENTIFIED'), 300));
            }
        }
    }

    /**
     * Send an error message to the sender
     * @param string $msgError
     * @param array $caseInf
     * @param IncomingMail $mail
     * @param array $emailSetup
     * @return \ProcessMaker\Util\Response|string|\WsResponse
     */
    public function sendMessageError($msgError, array $caseInf, IncomingMail $mail, array $emailSetup)
    {
        $wsBase = new WsBase();
        $result = $wsBase->sendMessage(
            $caseInf['appUid'],
            $mail->toString,
            $mail->fromAddress,
            '',
            '',
            $mail->subject,
            'actionsByEmailErrorReply.html',
            ['ACTIONS_BY_EMAIL_ERROR_MESSAGE' => $msgError],
            null,
            true,
            $caseInf['delIndex'],
            $emailSetup,
            0,
            WsBase::MESSAGE_TYPE_ACTIONS_BY_EMAIL
        );
        return $result;
    }
    /*----------------------------------********---------------------------------*/
}
