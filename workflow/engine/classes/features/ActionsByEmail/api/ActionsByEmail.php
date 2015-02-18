<?php
namespace Features\ActionsByEmail;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;

/**
 * Class Project
 *
 * @package Features\ActionsByEmail
 * @author gustavo cruz <gustavo.cruz@colosa.com>
 * @protected
 */
class ActionsByEmail extends Api
{
    /**
     * @url GET
     */
    public function getABEList()
    {
        try {
            $projects = array('status' => 200, 'message' => 'Hello');
            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

