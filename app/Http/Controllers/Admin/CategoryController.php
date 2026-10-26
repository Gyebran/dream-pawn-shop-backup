<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Catalog\Category;
use App\Services\Admin\Catalog\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller {
 public function index(Request $r,CategoryService $s): JsonResponse|View {
  $f=$r->validate(['include_archived'=>['sometimes','boolean'],'search'=>['sometimes','string','max:100']]);
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.categories.index', ['categories' => $data, 'filters' => $f]);
 }
 public function store(StoreCategoryRequest $r,CategoryService $s): JsonResponse|RedirectResponse {
  $data = $s->create($r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Kategori dibuat.','data'=>$data],201);
  }
  return redirect()->route('admin.categories.index')->with('success', 'Kategori "'.$data->name.'" berhasil dibuat.');
 }
 public function show(Category $category,CategoryService $s): JsonResponse|View {
  $data = $s->show($category);
  if (request()->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.categories.show', ['category' => $data]);
 }
 public function update(UpdateCategoryRequest $r,Category $category,CategoryService $s): JsonResponse|RedirectResponse {
  $data = $s->update($category,$r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Kategori diperbarui.','data'=>$data]);
  }
  return redirect()->route('admin.categories.index')->with('success', 'Kategori "'.$data->name.'" berhasil diperbarui.');
 }
 public function archive(Category $category,CategoryService $s): JsonResponse|RedirectResponse {
  $data = $s->archive($category);
  if (request()->wantsJson()) {
   return response()->json(['message'=>'Kategori diarsipkan.','data'=>$data]);
  }
  return redirect()->route('admin.categories.index')->with('success', 'Kategori "'.$data->name.'" berhasil diarsipkan.');
 }
}