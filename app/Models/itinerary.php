<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itinerary extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','duration','image','category_id'  , 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->hasOne(category::class , 'id' , 'category_id');
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }
    public function favoriteByUser()
    {
        return $this->belongsToMany(User::class , 'favoris');
    }
}
