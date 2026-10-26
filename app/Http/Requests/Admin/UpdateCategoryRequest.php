<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateCategoryRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{$c=$this->route('category');return ['name'=>['sometimes','required','string','max:255',Rule::unique('categories','name')->ignore($c?->id)],'description'=>['sometimes','nullable','string']];}
}