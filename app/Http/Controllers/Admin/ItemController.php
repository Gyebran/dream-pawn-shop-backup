<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Models\Catalog\Category;
use App\Models\Catalog\Item;
use App\Services\Admin\Catalog\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller {
 public function index(Request $r,ItemService $s): JsonResponse|View {
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([Item::AVAILABLE,Item::RESERVED,Item::LOANED,Item::ARCHIVED])],'search'=>['sometimes','nullable','string','max:100']]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.items.index', [
   'items' => $data,
   'categories' => Category::where('is_archived', false)->get(),
   'filters' => $f
  ]);
 }
 public function store(StoreItemRequest $r,ItemService $s): JsonResponse|RedirectResponse {
  $data = $s->create($r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Barang dibuat.','data'=>$data],201);
  }
  return redirect()->route('admin.items.index')->with('success', 'Barang prop "'.$data->name.'" ('.$data->unit_code.') berhasil ditambahkan.');
 }
 public function show(Item $item,ItemService $s): JsonResponse|View {
  $data = $s->show($item);
  if (request()->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.items.show', ['item' => $data]);
 }
 public function update(UpdateItemRequest $r,Item $item,ItemService $s): JsonResponse|RedirectResponse {
  $data = $s->update($item,$r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Barang diperbarui.','data'=>$data]);
  }
  return redirect()->route('admin.items.index')->with('success', 'Barang prop "'.$data->name.'" berhasil diperbarui.');
 }
 public function archive(Item $item,ItemService $s): JsonResponse|RedirectResponse {
  $data = $s->archive($item);
  if (request()->wantsJson()) {
   return response()->json(['message'=>'Barang diarsipkan.','data'=>$data]);
  }
  return redirect()->route('admin.items.index')->with('success', 'Barang prop "'.$data->name.'" berhasil diarsipkan.');
 }
}