<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;


/**
 * GmailIntegration Api Controller
 *
 *
 * @hybrid
 */
class GmailToken extends Api
{
    /**
     * Get token by usr_gmail
     *
     * @param array $request_data
     *
     *
     * @url POST /token
     * 
     */
    public function doPostAuthenticationbyEmail ($request_data){
    	try{
    		$Pmgmail = new \ProcessMaker\BusinessModel\Pmgmail();
    		$response = $Pmgmail->postTokenbyEmail($request_data);
    		return $response;
    	} catch (\Exception $e){
    		throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    	}
    }

}
