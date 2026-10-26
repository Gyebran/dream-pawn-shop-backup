<?php
namespace App\Models;
use App\Models\Admin\Admin;
use App\Models\Member\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use HasFactory,Notifiable;
 protected $fillable=['name','email','password','role','is_active'];
 protected $hidden=['password','remember_token'];
 protected function casts():array{return ['password'=>'hashed','is_active'=>'boolean'];}
 public function adminProfile():HasOne{return $this->hasOne(Admin::class,'user_id');}
 public function memberProfile():HasOne{return $this->hasOne(Member::class,'user_id');}
 public function isAdmin():bool{return $this->role==='admin'&&$this->is_active;}
 public function isMember():bool{return $this->role==='member'&&$this->is_active;}
}