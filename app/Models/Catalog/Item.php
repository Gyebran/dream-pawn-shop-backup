<?php
namespace App\Models\Catalog;
use App\Models\Loans\LoanRequestItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Item extends Model {
 public const AVAILABLE='available',RESERVED='reserved',LOANED='loaned',ARCHIVED='archived';
 protected $fillable=['unit_code','name','description','image_path','status'];
 public function categories():BelongsToMany{return $this->belongsToMany(Category::class,'item_category');}
 public function loanRequestItems():HasMany{return $this->hasMany(LoanRequestItem::class);}
}
