<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
class UpdateItemRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{$i=$this->route('item');return ['name'=>['sometimes','required','string','max:255'],'description'=>['sometimes','nullable','string'],'image'=>['sometimes','nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],'category_ids'=>['sometimes','array'],'category_ids.*'=>['integer','distinct','exists:categories,id']];}
}
