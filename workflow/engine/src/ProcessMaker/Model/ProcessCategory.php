<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessCategory
 * @package ProcessMaker\Model
 *
 * Represents a process category object in the system.
 */
class ProcessCategory extends Model
{
    // Set our table name
    protected $table = 'PROCESS_CATEGORY';

    public $timestamps = false;

    /**
     * Get the categories
     *
     * @param string $dir
     *
     * @return array
     *
     * @see ProcessProxy::categoriesList()
     * @link https://wiki.processmaker.com/3.0/Process_Categories
     */
    public static function getCategories( $dir = 'ASC')
    {
        $query = ProcessCategory::query()
            ->select([
                'CATEGORY_UID',
                'CATEGORY_NAME'
            ])
            ->orderBy('CATEGORY_NAME', $dir);

        return $query->get()->values()->toArray();
    }
}
