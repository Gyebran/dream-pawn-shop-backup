<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Models\Catalog\Item;
use App\Services\Member\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller {
 public function index(Request $r,CatalogService $s): JsonResponse|View {
  $f=$r->validate(['search'=>['sometimes','nullable','string','max:100'],'category_id'=>['sometimes','nullable','integer','exists:categories,id'],'available'=>['sometimes','nullable','boolean']]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginateItems($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.catalog.index', [
   'items' => $data,
   'categories' => $s->categories(),
   'filters' => $f
  ]);
 }
 public function show(Item $item,CatalogService $s): JsonResponse|View {
  $data = $s->showItem($item);
  if (request()->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.catalog.show', ['item' => $data]);
 }
 public function categories(CatalogService $s): JsonResponse|View {
  $data = $s->categories();
  if (request()->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.catalog.categories', ['categories' => $data]);
 }
}
