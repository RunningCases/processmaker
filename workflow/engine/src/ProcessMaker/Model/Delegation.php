<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;
use G;

class Delegation extends Model
{
    protected $table = "APP_DELEGATION";

    // We don't have our standard timestamp columns
    public $timestamps = false;

    /**
     * Returns the application this delegation belongs to
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'APP_UID', 'APP_UID');
    }

    /**
     * Returns the user this delegation belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID', 'USR_ID');
    }

    /**
     * Return the process task this belongs to
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'TAS_ID', 'TAS_ID');
    }

    /**
     * Return the process this delegation belongs to
     */
    public function process()
    {
        return $this->belongsTo(Process::class, 'PRO_ID', 'PRO_ID');
    }

    /**
     * Searches for delegations which match certain criteria
     *
     * The query is related to advanced search with different filters
     * We can search by process, status of case, category of process, users, delegate date from and to
     *
     * @param string $userUid
     * @param integer $start for the pagination
     * @param integer $limit for the pagination
     * @param string $search
     * @param integer $process the pro_id
     * @param integer $status of the case
     * @param string $dir if the order is DESC or ASC
     * @param string $sort name of column by sort, can be:
     *        [APP_NUMBER, APP_TITLE, APP_PRO_TITLE, APP_TAS_TITLE, APP_CURRENT_USER, APP_UPDATE_DATE, DEL_DELEGATE_DATE, DEL_TASK_DUE_DATE, APP_STATUS_LABEL]
     * @param string $category uid for the process
     * @param date $dateFrom
     * @param date $dateTo
     * @param string $filterBy name of column for a specific search, can be: [APP_NUMBER, APP_TITLE, TAS_TITLE]
     * @return array $result result of the query
     */

    public static function search(
        $userUid,
        $start = null,
        $limit = null,
        $search = null,
        $process = null,
        $status = null,
        $dir = null,
        $sort = null,
        $category = null,
        $dateFrom = null,
        $dateTo = null,
        $filterBy = 'APP_TITLE'
    )
    {
        // Default pagination values
        $start = $start ? $start : 0;
        $limit = $limit ? $limit : 25;

        // Start the query builder
        $query = self::query();

        // Add pagination to the query
        $query = $query->offset($start)
            ->limit($limit);

        // Fetch results and transform to a laravel collection
        $results = collect($query->get());

        // Transform with additional data
        $priorities = ['1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH'];
        $results->transform(function($item, $key) use($priorities) {
            // Grab related records
            $application = Application::where('APP_UID', $item['APP_UID'])->first();
            if(!$application) {
                // Application wasn't found, return null
                return null;
            }
            $task = Task::where('TAS_ID', $item['TAS_ID'])->first();
            if(!$task) {
                // Task not found, return null
                return null;
            }
            $user = User::where('USR_ID', $item['USR_ID'])->first();
            if(!$user) {
                // User not found, return null
                return null;
            }
            $process = Process::where('PRO_ID', $item['PRO_ID'])->first();;
            if(!$process) {
                // Process not found, return null
                return null;
            }
            
            // Rewrite priority string
            if($item['DEL_PRIORITY']) {
                $item['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$priorities[$item['DEL_PRIORITY']]}" );
            }

            // Merge in desired application data
            $item['APP_STATUS'] = $application->APP_STATUS;
            if($item['APP_STATUS']) {
                $item['APP_STATUS_LABEL'] = G::LoadTranslation( "ID_${item['APP_STATUS']}");
            } else {
                $item['APP_STATUS_LABEL'] = $application->APP_STATUS;
            }
            $item['APP_CREATE_DATE'] = $application->APP_CREATE_DATE;
            $item['APP_FINISH_DATE'] = $application->APP_FINISH_DATE;
            $item['APP_UPDATE_DATE'] = $application->APP_UPDATE_DATE;
            $item['APP_TITLE'] = $application->APP_TITLE;

            // Merge in desired process data
            $item['APP_PRO_TITLE'] = $process->PRO_TITLE;

            // Merge in desired task data
            $item['APP_TAS_TITLE'] = $task->TAS_TITLE;
            $item['APP_TAS_TYPE'] = $task->TAS_TYPE;
            
            // Merge in desired user data
            $item['USR_LASTNAME'] = $user->USR_LASTNAME;
            $item['USR_FIRSTNAME'] = $user->USR_FIRSTNAME;
            $item['USR_USERNAME'] = $user->USR_USERNAME;

            //@todo: this section needs to use 'User Name Display Format', currently in the extJs is defined this
            $item["APP_CURRENT_USER"] = $item["USR_LASTNAME"].' '.$item["USR_FIRSTNAME"];

            $item["APPDELCR_APP_TAS_TITLE"] = '';

            $item["USRCR_USR_UID"] = $item["USR_UID"];
            $item["USRCR_USR_FIRSTNAME"] = $item["USR_FIRSTNAME"];
            $item["USRCR_USR_LASTNAME"] = $item["USR_LASTNAME"];
            $item["USRCR_USR_USERNAME"] = $item["USR_USERNAME"];
            $item["APP_OVERDUE_PERCENTAGE"] = '';

            return $item;
        });

        // Remove any empty erroenous data
        $results = $results->filter();

        // Bundle into response array
        $response = [
            // Fake totalCount to show pagination
            'totalCount' => $start + $limit + 1,
            'data' => $results->toArray()
        ];

        return $response;
    }

}
