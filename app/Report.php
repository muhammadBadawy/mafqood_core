<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Report extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reports';

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
    protected $fillable = ['name', 'phone', 'email', 'gender', 'birth', 'case_date', 'area_id', 'lat', 'lang', 'mental_condition', 'type', 'reporter_type'];

    public function stamps()
    {
        return $this->hasMany('App\Stamp');
    }

    public function images()
    {
        $images = $this->hasMany('App\Stamp')->distinct('image')->pluck('image');
        // foreach ($images as $value) {
        //   $value = 'people/' . $value;
        // }
        for ($i=0; $i < count($images); $i++) {
          $images[$i] = asset(Storage::url('people/' . $images[$i]));
        }
        return $images;
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    // public function images()
    // {
    //     $my_stamps = $this->stamps_info;
    //     return asset('people/' . $this->image);
    // }

}
