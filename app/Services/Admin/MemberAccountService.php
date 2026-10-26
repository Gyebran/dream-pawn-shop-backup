<?php
namespace App\Services\Admin;
use App\Models\Member\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class MemberAccountService {
 public function paginate(array $f){return User::where('role','member')->when(isset($f['search']),fn($q)=>$q->where(fn($s)=>$s->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($f['search']).'%'])->orWhereRaw('LOWER(email) LIKE ?', ['%'.mb_strtolower($f['search']).'%'])))->when(isset($f['is_active']),fn($q)=>$q->where('is_active',$f['is_active']))->with('memberProfile')->orderBy('name')->paginate(20)->withQueryString();}
 public function create(array $data):User{return DB::transaction(function()use($data){$data['role']='member';$data['is_active']=$data['is_active']??true;$user=User::create($data);Member::create(['user_id'=>$user->id]);return $user->load('memberProfile');});}
 public function show(User $member):User{$this->assertMember($member);return $member->load('memberProfile');}
 public function update(User $member,array $data):User{$this->assertMember($member);$member->update($data);return $member->refresh()->load('memberProfile');}
 private function assertMember(User $u):void{if($u->role!=='member'||!$u->memberProfile()->exists())abort(404);}
}