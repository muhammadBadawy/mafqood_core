<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stamps';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['print', 'image', 'bbox', 'report_id', 'suspect_id'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Suspect');
    }

    public function MyImage()
    {
        return asset('people/' . $this->image);
    }

}
