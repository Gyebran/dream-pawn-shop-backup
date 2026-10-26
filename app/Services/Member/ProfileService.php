<?php
namespace App\Services\Member;
use App\Models\User;
class ProfileService {
 public function show(User $member):array{return $this->payload($member);}
 public function update(User $member,array $data):array{
  unset($data['current_password'],$data['password_confirmation']);
  $member->update($data);
  return $this->payload($member->refresh());
 }
 private function payload(User $u):array{return ['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role,'is_active'=>$u->is_active,'created_at'=>$u->created_at,'updated_at'=>$u->updated_at];}
}
