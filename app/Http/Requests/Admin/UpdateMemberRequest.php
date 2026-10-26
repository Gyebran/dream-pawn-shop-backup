<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateMemberRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{$m=$this->route('member');return ['name'=>['sometimes','required','string','max:255'],'email'=>['sometimes','required','email','max:255',Rule::unique('users','email')->ignore($m?->id)],'is_active'=>['sometimes','required','boolean']];}
}