<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class AppNotes extends Model
{
    // Set our table name
    protected $table = 'APP_NOTES';
    // No timestamps
    public $timestamps = false;
    // Primary key
    protected $primaryKey = 'NOTE_ID';
    // The IDs are auto-incrementing
    public $incrementing = true;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'NOTE_TYPE' => 'USER',
        'NOTE_ORIGIN_OBJ' => '',
        'NOTE_AFFECTED_OBJ1' => '',
        'NOTE_AFFECTED_OBJ2' => ''
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'APP_UID',
        'USR_UID',
        'NOTE_DATE',
        'NOTE_CONTENT',
        'NOTE_TYPE',
        'NOTE_AVAILABILITY',
        'NOTE_ORIGIN_OBJ',
        'NOTE_AFFECTED_OBJ1',
        'NOTE_AFFECTED_OBJ2',
        'NOTE_RECIPIENTS'
    ];
}
