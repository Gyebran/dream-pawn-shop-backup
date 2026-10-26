<?php
namespace App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Member extends Model {
 protected $table='members';protected $primaryKey='user_id';public $incrementing=false;public $timestamps=false;protected $guarded=[];
 public function user():BelongsTo{return $this->belongsTo(User::class,'user_id');}
}