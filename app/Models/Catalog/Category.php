<?php
namespace App\Models\Catalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Category extends Model {
 protected $fillable=['name','description','is_archived'];
 protected function casts():array{return ['is_archived'=>'boolean'];}
 public function items():BelongsToMany{return $this->belongsToMany(Item::class,'item_category');}
}