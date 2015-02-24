<?php
/**
 * Description of ActivityViews
 * 
 */
class ActivityConfigurationView
{
    public function retrieveView()
    {
        $view = array(
            'type' => 'form',
            'language' => 'en',
            'title' => 'Actions by email configuration',
            'layouts' => array(),
            'fields' => array(
                array(
                    'name' => 'IFORM',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'INDEX',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'ABE_UID',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'PRO_UID',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'PRJ_UID',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'TAS_UID',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'SYS_LANG',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'ABE_EMAIL_FIELD_VALUE',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'ABE_ACTION_FIELD_VALUE',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'ABE_TYPE',
                    'label' => 'Type',
                    'type' => 'dropdown',
                    'options' => array(
                        array(
                            'name' => '',
                            'value' => '',
                            'text' => '- None -',
                            'type' => 'default'
                        ),
                        array(
                            'name' => 'LINK',
                            'value' => 'LINK',
                            'text' => 'Link to fill a form',
                        ),
                        array(
                            'name' => 'FIELD',
                            'value' => 'FIELD',
                            'text' => 'Use a field to generate actions links',
                        )
                    )
                ),
                array(
                    'name' => 'ABE_TEMPLATE',
                    'label' => 'Template',
                    'type' => 'dropdown',
                    'data_source' => array(
                        'type' => 'REST',
                        'method' => 'GET',
                        'end_point' => '/ActionsByEmail/Templates',
                        'parameters' => array()
                    ),
                    'options' => array(
                        array(
                            'name' => '',
                            'value' => '',
                            'text' => '- Select a Template -',
                            'type' => 'default'
                        )
                    )
                ),
                array(
                    'name' => 'EDIT',
                    'type' => 'link',
                    'value' => 'Edit',
                    'action' => array(
                        'type' => 'view-render',
                        'data_source' => array(
                            'type' => 'REST',
                            'method' => 'GET',
                            'end_point' => '/ActionsByEmail/Templates/editTemplate',
                            'parameters' => array('ABE_TEMPLATE')
                        )
                    )
                ),
                array(
                    'name' => 'DYN_UID',
                    'label' => 'Dynaform',
                    'value' => 'DYN_UID',
                    'type' => 'dropdown',
                    'data_source' => array(
                        'type' => 'REST',
                        'method' => 'GET',
                        'end_point' => '/project/{prj_uid}/dynaforms',
                        'parameters' => array()
                    ),
                    'options' => array(
                        array(
                            'name' => '',
                            'value' => '',
                            'text' => '- Select a Dynaform -',
                            'type' => 'default'
                        )
                    ),
                    'events' => array(
                        'change' => array(
                            'listeners' => array('email-field', 'action-field')
                        )
                    ),
                ),
                array(
                    'name' => 'ABE_EMAIL_FIELD',
                    'value' => 'ABE_EMAIL_FIELD',
                    'label' => 'Field with the email',
                    'type' => 'dropdown',
                    'options' => array(
                        array(
                            'name' => '',
                            'value' => '',
                            'text' => '- Send to the email of the assigned user to the task -',
                            'type' => 'default'
                        )
                    ),
                    'listeners' => array(
                        array(
                            'name' => 'email-field',
                            'action' => array(
                                'type' => 'field-render',
                                'data_source' => array(
                                    'type' => 'REST',
                                    'method' => 'GET',
                                    'end_point' => '/project/{prj_uid}/dynaform/{dyn_uid}/fields',
                                    'parameters' => array()
                                )
                            )
                        )
                    )
                ),
                array(
                    'name' => 'ABE_ACTION_FIELD',
                    'value' => 'ABE_ACTION_FIELD',
                    'label' => 'Field to Send in the Email',
                    'type' => 'dropdown',
                    'options' => array(
                        array(
                            'name' => '',
                            'value' => '',
                            'text' => '- Select a Field -',
                            'type' => 'default'
                        )
                    ),
                    'listeners' => array(
                        'name' => 'action-field',
                        'action' => array(
                            'type' => 'field-render',
                            'data_source' => array(
                                'type' => 'REST',
                                'method' => 'GET',
                                'end_point' => '/project/:prj_uid/dynaform/:dyn_uid/fields',
                                'parameters' => array()
                            )
                        )
                    )
                ),
                array(
                    'name' => 'ABE_CASE_NOTE_IN_RESPONSE',
                    'value' => true,
                    'default' => false,
                    'label' => 'Register a Case Note when the recipient submits the Response',
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'APPLY_CHANGES',
                    'type' => 'button',
                    'label' => 'Apply Changes',
                    'value' => 'APPLY_CHANGES',
                    'action' => array(
                        'type' => 'view-close',
                        'data_source' => array(
                            'type' => 'REST',
                            'method' => 'POST',
                            'end_point' => '/ActionsByEmail/saveConfiguration',
                            'parameters' => array('_ALL')
                        )
                    )
                ),
                array(
                    'name' => 'REQUIRED_LABEL',
                    'type' => 'label',
                    'label' => 'Required Field',
                    'value' => 'REQUIRED_LABEL'
                )
            )
        );
        return $view;
    }

}
