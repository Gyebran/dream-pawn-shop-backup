<?php
namespace App\Models\Loans;
use App\Models\Catalog\Item;
use App\Models\Fines\Fine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class LoanRequestItem extends Model {
 public $timestamps=false;protected $fillable=['loan_request_id','item_id','created_at'];
 protected function casts():array{return ['created_at'=>'datetime'];}
 public function loanRequest():BelongsTo{return $this->belongsTo(LoanRequest::class);}
 public function item():BelongsTo{return $this->belongsTo(Item::class);}
 public function fine():HasOne{return $this->hasOne(Fine::class);}
}