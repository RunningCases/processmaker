<?php

namespace ProcessMaker\Cases;

use AppDelegation;
use AppDelegationPeer;
use AppDocumentDrive;
use BasePeer;
use Cases;
use Derivation;
use Event;
use G;
use PMLicensedFeatures;
use ProcessMaker\BusinessModel\Pmgmail;
use Users;
use WebDriver\Exception;

trait CasesTrait
{

    /**
     * This initiates the routing of the case given the application and the form 
     * data in the web application interface.
     * @param string $processUid
     * @param string $application
     * @param array $postForm
     * @param string $status
     * @param boolean $flagGmail
     * @param string $tasUid
     * @param integer $index
     * @param string $userLogged
     */
    public function routeCase($processUid, $application, $postForm, $status, $flagGmail, $tasUid, $index, $userLogged)
    {
        //warning: we are not using the result value of function thisIsTheCurrentUser, so I'm commenting to optimize speed.
        $appFields = $this->loadCase($application);
        $appFields['APP_DATA'] = array_merge($appFields['APP_DATA'], G::getSystemConstants());

        $triggerDebug = [];
        $triggers = $this->loadTriggers($tasUid, 'ASSIGN_TASK', -2, 'BEFORE');

        //if there are some triggers to execute
        if (sizeof($triggers) > 0) {
            //Execute triggers before derivation
            $appFields['APP_DATA'] = $this->executeTriggers($tasUid, 'ASSIGN_TASK', -2, 'BEFORE', $appFields['APP_DATA']);

            //save trigger variables for debugger
            $triggerDebug[] = [
                'NUM_TRIGGERS' => sizeof($triggers),
                'TIME' => G::toUpper(G::loadTranslation('ID_BEFORE')),
                'TRIGGERS_NAMES' => array_column($triggers, 'TRI_TITLE'),
                'TRIGGERS_VALUES' => $triggers,
                'TRIGGERS_EXECUTION_TIME' => $this->arrayTriggerExecutionTime
            ];
        }

        unset($appFields['APP_STATUS']);
        unset($appFields['APP_PROC_STATUS']);
        unset($appFields['APP_PROC_CODE']);
        unset($appFields['APP_PIN']);

        $appFields["DEL_INDEX"] = $index;
        $appFields["TAS_UID"] = $tasUid;
        $appFields["USER_UID"] = $userLogged;
        $appFields["CURRENT_DYNAFORM"] = "-2";
        $appFields["OBJECT_TYPE"] = "ASSIGN_TASK";

        //save data
        $this->updateCase($application, $appFields);

        //prepare information for the derivation
        $derivation = new Derivation();
        $currentDerivation = [
            'APP_UID' => $application,
            'DEL_INDEX' => $index,
            'APP_STATUS' => $status,
            'TAS_UID' => $tasUid,
            'ROU_TYPE' => $postForm['ROU_TYPE']
        ];
        $dataForPrepareInfo = [
            'USER_UID' => $userLogged,
            'APP_UID' => $application,
            'DEL_INDEX' => $index
        ];

        //we define some parameters in the before the derivation
        //then this function will be route the case
        $arrayDerivationResult = $derivation->beforeDerivate(
                $dataForPrepareInfo,
                $postForm['TASKS'],
                $postForm['ROU_TYPE'],
                $currentDerivation
        );

        if (!empty($arrayDerivationResult)) {
            foreach ($postForm['TASKS'] as $key => $value) {
                if (isset($value['TAS_UID'])) {
                    foreach ($arrayDerivationResult as $value2) {
                        if ($value2['TAS_UID'] == $value['TAS_UID']) {
                            $postForm['TASKS'][$key]['DEL_INDEX'] = $value2['DEL_INDEX'];
                            break;
                        }
                    }
                }
            }
        }

        $appFields = $this->loadCase($application); //refresh appFields, because in derivations should change some values
        $triggers = $this->loadTriggers($tasUid, 'ASSIGN_TASK', -2, 'AFTER'); //load the triggers after derivation
        if (sizeof($triggers) > 0) {
            $appFields['APP_DATA'] = $this->ExecuteTriggers($tasUid, 'ASSIGN_TASK', -2, 'AFTER', $appFields['APP_DATA']); //Execute triggers after derivation

            $triggerDebug[] = [
                'NUM_TRIGGERS' => sizeof($triggers),
                'TIME' => G::toUpper(G::loadTranslation('ID_AFTER')),
                'TRIGGERS_NAMES' => array_column($triggers, 'TRI_TITLE'),
                'TRIGGERS_VALUES' => $triggers,
                'TRIGGERS_EXECUTION_TIME' => $this->arrayTriggerExecutionTime
            ];
        }
        unset($appFields['APP_STATUS']);
        unset($appFields['APP_PROC_STATUS']);
        unset($appFields['APP_PROC_CODE']);
        unset($appFields['APP_PIN']);

        $appFields["DEL_INDEX"] = $index;
        $appFields["TAS_UID"] = $tasUid;
        $appFields["USER_UID"] = $userLogged;
        $appFields["CURRENT_DYNAFORM"] = "-2";
        $appFields["OBJECT_TYPE"] = "ASSIGN_TASK";

        $this->updateCase($application, $appFields);

        // Send notifications - Start
        $oUser = new Users();
        $aUser = $oUser->load($userLogged);
        $fromName = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];

        $sFromData = $fromName . ($aUser['USR_EMAIL'] != '' ? ' <' . $aUser['USR_EMAIL'] . '>' : '');

        if ($flagGmail === true) {
            $appDel = new AppDelegation();
            $actualThread = $appDel->Load($application, $index);

            $appDelPrev = $appDel->LoadParallel($application);
            $Pmgmail = new Pmgmail();
            foreach ($appDelPrev as $app) {
                if (($app['DEL_INDEX'] != $index) && ($app['DEL_PREVIOUS'] != $actualThread['DEL_PREVIOUS'])) {
                    $Pmgmail->gmailsIfSelfServiceValueBased($application, $app['DEL_INDEX'], $postForm['TASKS'], $appFields['APP_DATA']);
                }
            }
        }

        try {
            $this->sendNotifications($tasUid, $postForm['TASKS'], $appFields['APP_DATA'], $application, $index, $sFromData);
        } catch (Exception $e) {
            G::SendTemporalMessage(G::loadTranslation('ID_NOTIFICATION_ERROR') . ' - ' . $e->getMessage(), 'warning', 'string', null, '100%');
        }
        // Send notifications - End
        // Events - Start
        $event = new Event();

        $event->closeAppEvents($processUid, $application, $index, $tasUid);
        $currentAppDel = AppDelegationPeer::retrieveByPk($application, $index + 1);
        $multipleDelegation = false;
        // check if there are multiple derivations
        if (count($postForm['TASKS']) > 1) {
            $multipleDelegation = true;
        }
        // If the case has been delegated
        if (isset($currentAppDel)) {
            // if there is just a single derivation the TASK_UID can be set by the delegation data
            if (!$multipleDelegation) {
                $arrayResult = $currentAppDel->toArray(BasePeer::TYPE_FIELDNAME);
                $event->createAppEvents($arrayResult['PRO_UID'], $arrayResult['APP_UID'], $arrayResult['DEL_INDEX'], $arrayResult['TAS_UID']);
            } else {
                // else we need to check every task and create the events if it have any
                foreach ($postForm['TASKS'] as $taskDelegated) {
                    $arrayResult = $currentAppDel->toArray(BasePeer::TYPE_FIELDNAME);
                    $event->createAppEvents($arrayResult['PRO_UID'], $arrayResult['APP_UID'], $arrayResult['DEL_INDEX'], $taskDelegated['TAS_UID']);
                }
            }
        }
        //Events - End

        /*----------------------------------********---------------------------------*/
        // Set users drive - start
        $licensedFeatures = PMLicensedFeatures::getSingleton();
        if ($licensedFeatures->verifyfeature('AhKNjBEVXZlWUFpWE8wVTREQ0FObmo0aTdhVzhvalFic1M=')) {
            $drive = new AppDocumentDrive();
            if ($drive->getStatusDrive()) {
                //add users email next task
                $drive->addUsersDocumentDrive($appFields['APP_UID']);
            }
        }
        // Set users drive - End
        /*----------------------------------********---------------------------------*/
        
        $result = [
            'appFields' => $appFields,
            'triggerDebug' => $triggerDebug
        ];
        return (object) $result;
    }
}
