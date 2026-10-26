<?php
namespace App\Services\Member;
use App\Models\Catalog\Category;
use App\Models\Catalog\Item;
class CatalogService {
 /** Archived units never appear in the Member catalog; reserved/loaned units appear with their status so they are not shown as ready to borrow (F-05 AC4). */
 public function paginateItems(array $f){
  return Item::with('categories:id,name')->where('status','!=',Item::ARCHIVED)
   ->when(isset($f['search']),fn($q)=>$q->whereRaw('LOWER(name) LIKE ?',['%'.mb_strtolower($f['search']).'%']))
   ->when(isset($f['category_id']),fn($q)=>$q->whereHas('categories',fn($c)=>$c->where('categories.id',$f['category_id'])))
   ->when(!empty($f['available']),fn($q)=>$q->where('status',Item::AVAILABLE))
   ->orderBy('name')->orderBy('unit_code')->paginate(20)->withQueryString()
   ->through(fn(Item $i)=>$this->decorate($i));
 }
 public function showItem(Item $item):Item{if($item->status===Item::ARCHIVED)abort(404);return $this->decorate($item->load('categories:id,name'));}
 public function categories(){return Category::where('is_archived',false)->orderBy('name')->get(['id','name','description']);}
 private function decorate(Item $i):Item{$i->setAttribute('is_available',$i->status===Item::AVAILABLE);return $i;}
}
