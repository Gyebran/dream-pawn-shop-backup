<?php
namespace App\Models\Loans;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class LoanRequest extends Model {
 public const PENDING='pending',APPROVED='approved',REJECTED='rejected',CANCELLED='cancelled',ACTIVE='active',RETURNED='returned';
 protected $fillable=['member_id','reviewed_by','return_confirmed_by','status','requested_at','reviewed_at','handover_deadline_at','handed_over_at','due_at','returned_at'];
 protected function casts():array{return ['requested_at'=>'datetime','reviewed_at'=>'datetime','handover_deadline_at'=>'datetime','handed_over_at'=>'datetime','due_at'=>'datetime','returned_at'=>'datetime'];}
 public function member():BelongsTo{return $this->belongsTo(User::class,'member_id');}
 public function reviewer():BelongsTo{return $this->belongsTo(User::class,'reviewed_by');}
 public function returnConfirmer():BelongsTo{return $this->belongsTo(User::class,'return_confirmed_by');}
 public function details():HasMany{return $this->hasMany(LoanRequestItem::class);}
}