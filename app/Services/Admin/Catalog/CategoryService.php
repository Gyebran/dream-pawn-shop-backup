<?php
namespace App\Services\Admin\Catalog;
use App\Models\Catalog\Category;
use Illuminate\Validation\ValidationException;
class CategoryService {
 public function paginate(){return Category::withCount('items')->orderBy('name')->paginate(20);}
 public function create(array $data):Category{return Category::create($data+['is_archived'=>false]);}
 public function show(Category $category):Category{return $category->load('items');}
 public function update(Category $category,array $data):Category{
  if($category->is_archived)throw ValidationException::withMessages(['category'=>'Kategori yang diarsipkan tidak dapat diubah.']);
  $category->update($data);return $category->refresh();
 }
 public function archive(Category $category):Category{$category->update(['is_archived'=>true]);return $category->refresh();}
}