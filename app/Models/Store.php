<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table ='stores';

    protected $fillable =[
        'name',
        'slug',
        'description',
        'owner_id',
        'ownerName',
        'img',
        'country',
        'city',
        'address_latitude',
        'address_longitude',
        'active',
        'populer',
        'pay_pal_key',
        'razpory_key',
        'myfatoora_key',
    ];


    protected $hidden = [
        'pay_pal_key',
        'razpory_key',
        'myfatoora_key',
    ];


    public function owner( )
    {
       return $this->belongsTo(User::class,'owner_id','id');
    }

    public function categories( )
    {
       return $this->hasMany(Category::class,'store_id','id');
    }


}


