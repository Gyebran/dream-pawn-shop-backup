<?php
namespace App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Admin extends Model {
 protected $table='admins';protected $primaryKey='user_id';public $incrementing=false;public $timestamps=false;protected $guarded=[];
 public function user():BelongsTo{return $this->belongsTo(User::class,'user_id');}
}