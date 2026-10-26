<?php
namespace App\Services\Admin\Catalog;
use App\Models\Catalog\Category;
use App\Models\Catalog\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ItemService {
 public function paginate(array $f){return Item::with('categories')->when(isset($f['status']),fn($q)=>$q->where('status',$f['status']))->when(isset($f['search']),fn($q)=>$q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($f['search']).'%']))->orderBy('name')->paginate(20)->withQueryString();}
 public function create(array $data):Item{$ids=$data['category_ids']??[];$image=$data['image']??null;unset($data['category_ids'],$data['image'],$data['unit_code']);$this->assertCategoriesActive($ids);if($image instanceof UploadedFile)$data['image_path']=$image->store('items','public');try{return DB::transaction(function()use($data,$ids){$item=Item::create($data+['unit_code'=>'TMP-'.Str::uuid(),'status'=>Item::AVAILABLE]);$item->update(['unit_code'=>$this->unitCode($item->name,(int)$item->id)]);$item->categories()->sync($ids);return $item->fresh()->load('categories');});}catch(\Throwable $e){if(!empty($data['image_path']))Storage::disk('public')->delete($data['image_path']);throw $e;}}
 public function show(Item $item):Item{return $item->load('categories');}
 public function update(Item $item,array $data):Item{$ids=$data['category_ids']??null;$image=$data['image']??null;unset($data['category_ids'],$data['image']);if($image instanceof UploadedFile)$data['image_path']=$image->store('items','public');$newPath=$data['image_path']??null;try{$updated=DB::transaction(function()use($item,$data,$ids){$locked=Item::whereKey($item->id)->lockForUpdate()->firstOrFail();$this->assertEditable($locked);if($ids!==null)$this->assertCategoriesActive($ids);$oldPath=$locked->image_path;$locked->update($data);if($ids!==null)$locked->categories()->sync($ids);return ['item'=>$locked->fresh()->load('categories'),'old_path'=>$oldPath];});if($newPath&&$updated['old_path'])Storage::disk('public')->delete($updated['old_path']);return $updated['item'];}catch(\Throwable $e){if($newPath)Storage::disk('public')->delete($newPath);throw $e;}}
 public function archive(Item $item):Item{return DB::transaction(function()use($item){$locked=Item::whereKey($item->id)->lockForUpdate()->firstOrFail();$this->assertEditable($locked);$locked->update(['status'=>Item::ARCHIVED]);return $locked->refresh();});}
 private function unitCode(string $name,int $id):string{$ascii=Str::upper(Str::ascii($name));preg_match_all('/[A-Z0-9]+/',$ascii,$matches);$prefix=collect($matches[0])->map(fn($word)=>substr($word,0,1))->implode('');return ($prefix!==''?$prefix:'IT').'-'.$id;}
 private function assertEditable(Item $item):void{if(in_array($item->status,[Item::RESERVED,Item::LOANED],true))throw ValidationException::withMessages(['item'=>'Barang yang sedang direservasi atau dipinjam tidak dapat diubah.']);if($item->status===Item::ARCHIVED)throw ValidationException::withMessages(['item'=>'Barang yang sudah diarsipkan tidak dapat diubah.']);}
 private function assertCategoriesActive(array $ids):void{if($ids===[])return;$count=Category::whereIn('id',$ids)->where('is_archived',false)->count();if($count!==count(array_unique($ids)))throw ValidationException::withMessages(['category_ids'=>'Kategori tidak ditemukan atau sudah diarsipkan.']);}
}
