<?php

namespace ProcessMaker\Model;

use Configurations;
use Illuminate\Database\Eloquent\Model;
use Exception;
use RBAC;

class User extends Model
{
    protected $table = "USERS";
    protected $primaryKey = 'USR_ID';
    // Our custom timestamp columns
    const CREATED_AT = 'USR_CREATE_DATE';
    const UPDATED_AT = 'USR_UPDATE_DATE';

    /**
     * Returns the delegations this user has (all of them)
     */
    public function delegations()
    {
        return $this->hasMany(Delegation::class, 'USR_ID', 'USR_ID');
    }

    /**
     * Return the user this belongs to
     */
    public function groups()
    {
        return $this->belongsTo(GroupUser::class, 'USR_UID', 'USR_UID');
    }

    /**
     * Return the groups from a user
     *
     * @param boolean $usrUid
     *
     * @return array
     */
    public static function getGroups($usrUid)
    {
        return User::find($usrUid)->groups()->get();
    }

    /**
     * Scope for the specified user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws Exception
     */
    public function scopeUserFilters($query, array $filters)
    {
        if (!empty($filters['USR_ID'])) {
            $query->where('USR_ID', $filters['USR_ID']);
        } elseif (!empty($filters['USR_UID'])) {
            $query->where('USR_UID', $filters['USR_UID']);
        } else {
            throw new Exception("There are no filter for loading a user model");
        }

        return $query;
    }

    /**
     * Scope a query to exclude the guest user
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutGuest($query)
    {
        $query->where('USR_UID', '<>', RBAC::GUEST_USER_UID);
    }

    /**
     * Scope a query to include only active users (ACTIVE, VACATION)
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('USERS.USR_STATUS', ['ACTIVE', 'VACATION']);
    }

    /**
     * Get all users, paged optionally, can be sent a text to filter results by user information (first name, last name, username)
     *
     * @param string $text
     * @param int $offset
     * @param int $limit
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getUsersForHome($text = null, $offset = null, $limit = null)
    {
        try {
            // Load configurations of the environment
            $configurations = new Configurations();

            // Field to order the results
            $orderBy = $configurations->userNameFormatGetFirstFieldByUsersTable();

            // Format of the user names
            $formatName = $configurations->getFormats()['format'];

            // Get users from the current workspace
            $query = User::query()->select(['USR_ID', 'USR_USERNAME', 'USR_FIRSTNAME', 'USR_LASTNAME']);

            // Set full name condition if is sent
            if (!empty($text)) {
                $query->where(function ($query) use ($text) {
                    $query->orWhere('USR_USERNAME', 'LIKE', "%{$text}%");
                    $query->orWhere('USR_FIRSTNAME', 'LIKE', "%{$text}%");
                    $query->orWhere('USR_LASTNAME', 'LIKE', "%{$text}%");
                });
            }

            // Exclude guest user
            $query->withoutGuest();

            // Only get active
            $query->active();

            // Order by full name
            $query->orderBy($orderBy);

            // Set pagination if offset and limit are sent
            if (!is_null($offset) && !is_null($limit)) {
                $query->offset($offset);
                $query->limit($limit);
            }

            // Get users
            $users = $query->get()->toArray();

            // Populate the field with the user names in format
            $users = array_map(function ($user) use ($configurations, $formatName) {
                // Format the user names
                $user['USR_FULLNAME'] = $configurations->usersNameFormatBySetParameters($formatName,
                    $user['USR_USERNAME'], $user['USR_FIRSTNAME'], $user['USR_LASTNAME']);

                // Return value with the new element
                return $user;

            }, $users);

            // Return users
            return $users;
        } catch (Exception $e) {
            throw new Exception("Error getting the users: {$e->getMessage()}.");
        }
    }
}
