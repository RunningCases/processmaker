<?php

namespace ProcessMaker\Model;

use Exception;
use G;
use ProcessMaker\Core\System;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\User;
use Illuminate\Database\Eloquent\Model;

class CaseList extends Model
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'CASE_LIST';

    /**
     * The primary key for the model.
     * @var string
     */
    protected $primaryKey = 'CAL_ID';

    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     * @var array
     */
    protected $guarded = [];

    /**
     * Represents the column aliases.
     * @var array
     */
    private static $columnAliases = [
        'CAL_ID' => 'id',
        'CAL_TYPE' => 'type',
        'CAL_NAME' => 'name',
        'CAL_DESCRIPTION' => 'description',
        'ADD_TAB_UID' => 'tableUid',
        'CAL_COLUMNS' => 'columns',
        'USR_ID' => 'userId',
        'CAL_ICON_LIST' => 'iconList',
        'CAL_ICON_COLOR' => 'iconColor',
        'CAL_ICON_COLOR_SCREEN' => 'iconColorScreen',
        'CAL_CREATE_DATE' => 'createDate',
        'CAL_UPDATE_DATE' => 'updateDate',
        'USR_USERNAME' => 'userName',
        'USR_FIRSTNAME' => 'userFirstname',
        'USR_LASTNAME' => 'userLastname',
        'USR_EMAIL' => 'userEmail',
        'ADD_TAB_NAME' => 'tableName',
        'PRO_TITLE' => 'process'
    ];

    /**
     * Get column name from alias.
     * @param array $array
     * @return array
     */
    public static function getColumnNameFromAlias(array $array): array
    {
        foreach (self::$columnAliases as $key => $value) {
            if (array_key_exists($value, $array)) {
                $array[$key] = $array[$value];
                unset($array[$value]);
            }
        }
        return $array;
    }

    /**
     * Get alias from column name.
     * @param array $array
     * @return array
     */
    public static function getAliasFromColumnName(array $array)
    {
        foreach (self::$columnAliases as $key => $value) {
            if (array_key_exists($key, $array)) {
                $array[$value] = $array[$key];
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Create and save this model from array values.
     * @param array $values
     * @param int $ownerId
     * @return object
     */
    public static function createSetting(array $values, int $ownerId)
    {
        $attributes = CaseList::getColumnNameFromAlias($values);

        $attributes['USR_ID'] = $ownerId;
        $attributes['CAL_CREATE_DATE'] = date("Y-m-d H:i:s");
        $attributes['CAL_UPDATE_DATE'] = date("Y-m-d H:i:s");
        if (empty($attributes['CAL_COLUMNS'])) {
            $attributes['CAL_COLUMNS'] = [];
        }
        $attributes['CAL_COLUMNS'] = json_encode($attributes['CAL_COLUMNS']);

        $model = CaseList::create($attributes);
        $model->CAL_COLUMNS = json_decode($model->CAL_COLUMNS);
        return $model;
    }

    /**
     * Update and save this model from array values.
     * @param int $id
     * @param array $values
     * @param int $ownerId
     * @return object
     */
    public static function updateSetting(int $id, array $values, int $ownerId)
    {
        $attributes = CaseList::getColumnNameFromAlias($values);

        $attributes['USR_ID'] = $ownerId;
        $attributes['CAL_UPDATE_DATE'] = date("Y-m-d H:i:s");
        if (empty($attributes['CAL_COLUMNS'])) {
            $attributes['CAL_COLUMNS'] = [];
        }
        $attributes['CAL_COLUMNS'] = json_encode($attributes['CAL_COLUMNS']);

        $caseList = CaseList::where('CAL_ID', '=', $id);
        $caseList->update($attributes);
        $model = $caseList->get()->first();
        if (!is_null($model)) {
            $model->CAL_COLUMNS = json_decode($model->CAL_COLUMNS);
        }
        return $model;
    }

    /**
     * Delete this model.
     * @param int $id
     * @return object
     */
    public static function deleteSetting(int $id)
    {
        $caseList = CaseList::where('CAL_ID', '=', $id);
        $model = $caseList->get()->first();
        if (!is_null($model)) {
            $caseList->delete();
            $model->CAL_COLUMNS = json_decode($model->CAL_COLUMNS);
        }
        return $model;
    }

    /**
     * Get the array of the elements of this model, this method supports the filter by: 
     * name, description, user name, first user name, second user name, user email. 
     * The result is returned based on the delimiters to allow pagination and the total 
     * of the existing models.
     * @param string $type
     * @param string $search
     * @param int $offset
     * @param int $limit
     * @param bool $paged
     * @return array
     */
    public static function getSetting(string $type, string $search, int $offset, int $limit, bool $paged = true): array
    {
        $order = 'asc';
        $model = CaseList::where('CAL_TYPE', '=', $type)
            ->leftJoin('USERS', 'USERS.USR_ID', '=', 'CASE_LIST.USR_ID')
            ->leftJoin('ADDITIONAL_TABLES', 'ADDITIONAL_TABLES.ADD_TAB_UID', '=', 'CASE_LIST.ADD_TAB_UID')
            ->leftJoin('PROCESS', 'PROCESS.PRO_UID', '=', 'ADDITIONAL_TABLES.PRO_UID')
            ->select([
                'CASE_LIST.*',
                'PROCESS.PRO_TITLE',
                'ADDITIONAL_TABLES.ADD_TAB_NAME',
                'USERS.USR_UID', 'USERS.USR_USERNAME', 'USERS.USR_FIRSTNAME', 'USERS.USR_LASTNAME', 'USERS.USR_EMAIL'
            ])
            ->where(function ($query) use ($search) {
                $query
                ->orWhere('CASE_LIST.CAL_NAME', 'like', '%' . $search . '%')
                ->orWhere('PROCESS.PRO_TITLE', 'like', '%' . $search . '%')
                ->orWhere('ADDITIONAL_TABLES.ADD_TAB_NAME', 'like', '%' . $search . '%');
            })
            ->orderBy('CASE_LIST.CAL_NAME', $order);

        $count = $model->count();

        if ($paged === true) {
            $model->offset($offset)->limit($limit);
        }
        $data = $model->get();

        $data->transform(function ($item, $key) {
            if (is_null($item->CAL_COLUMNS)) {
                $item->CAL_COLUMNS = '[]';
            }

            $result = CaseList::getAliasFromColumnName($item->toArray());

            $result['columns'] = json_decode($result['columns']);
            $result['userAvatar'] = System::getServerMainPath() . '/users/users_ViewPhotoGrid?pUID=' . $result['USR_UID'] . '&h=' . microtime(true);
            unset($result['USR_UID']);

            return $result;
        });

        return [
            'total' => $count,
            'data' => $data
        ];
    }

    /**
     * The export creates a temporary file with record data in json format.
     * @param int $id
     * @throws Exception
     */
    public static function export(int $id)
    {
        $model = CaseList::where('CAL_ID', '=', $id)
            ->leftJoin('USERS', 'USERS.USR_ID', '=', 'CASE_LIST.USR_ID')
            ->leftJoin('ADDITIONAL_TABLES', 'ADDITIONAL_TABLES.ADD_TAB_UID', '=', 'CASE_LIST.ADD_TAB_UID')
            ->select([
                'CASE_LIST.*'
            ])
            ->get()
            ->first();
        if (empty($model)) {
            throw new Exception(G::LoadTranslation('ID_DOES_NOT_EXIST'));
        }

        $result = CaseList::getAliasFromColumnName($model->toArray());
        $result['columns'] = json_decode($result['columns']);

        //clean invalid items
        unset($result['id']);
        unset($result['userId']);
        unset($result['createDate']);
        unset($result['updateDate']);

        //random name to distinguish the different sessions
        $filename = sys_get_temp_dir() . "/pm" . random_int(10000, 99999);
        file_put_contents($filename, json_encode($result));
        return [
            'filename' => $filename,
            'downloadFilename' => $result['name'] . ' ' . date('Y-m-d H:i:s') . '.json',
            'data' => $result
        ];
    }

    /**
     * The import requires a $ _FILES content in json format to create a record.
     * @param array $request_data
     * @param int $ownerId
     * @return array
     * @throws Exception
     */
    public static function import(array $request_data, int $ownerId)
    {
        if ($_FILES['file_content']['error'] !== UPLOAD_ERR_OK ||
            $_FILES['file_content']['tmp_name'] === '') {
            throw new Exception(G::LoadTranslation('ID_ERROR_UPLOADING_FILENAME'));
        }
        $content = file_get_contents($_FILES['file_content']['tmp_name']);
        try {
            $array = json_decode($content, true);
            $caseList = CaseList::createSetting($array, $ownerId);
            $result = CaseList::getAliasFromColumnName($caseList->toArray());
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
