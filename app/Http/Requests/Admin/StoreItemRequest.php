<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreItemRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{return ['unit_code'=>['sometimes','nullable','string','max:255'],'name'=>['required','string','max:255'],'description'=>['nullable','string'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],'category_ids'=>['sometimes','array'],'category_ids.*'=>['integer','distinct','exists:categories,id']];}
}
