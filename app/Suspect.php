<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suspect extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'suspects';

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
    protected $fillable = ['name', 'appearance_times'];

    public function stamps()
    {
        return $this->hasMany('App\Stamp');
    }

}
