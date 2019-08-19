<?php
  
namespace App;
  
use Illuminate\Database\Eloquent\Model;
   
class Pi extends Model
{
    protected $fillable = [
        'pi_id', 'name', 'mac_address', 'description'
    ];
}