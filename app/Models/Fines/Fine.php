<?php
namespace App\Models\Fines;
use App\Models\Loans\LoanRequestItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Fine extends Model {
 public const UNPAID='unpaid',PAID='paid';
 /** Illustrative late fee per physical unit per full 24-hour period (BR-13, D-06). */
 public const RATE_PER_UNIT_PER_DAY=10000;
 protected $fillable=['loan_request_item_id','recorded_by','paid_confirmed_by','amount','status','assessed_at','paid_at'];
 protected function casts():array{return ['amount'=>'integer','assessed_at'=>'datetime','paid_at'=>'datetime'];}
 public function loanRequestItem():BelongsTo{return $this->belongsTo(LoanRequestItem::class);}
 public function recorder():BelongsTo{return $this->belongsTo(User::class,'recorded_by');}
 public function paymentConfirmer():BelongsTo{return $this->belongsTo(User::class,'paid_confirmed_by');}
}