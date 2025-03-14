<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class destination extends Model
{
    use HasFactory;

    protected $fillable =[ 'itinerary_id' , 'name'  , 'lodging'] ;

    public function itinerary(){
        return $this->belongsTo(itinerary::class );
    }
}
