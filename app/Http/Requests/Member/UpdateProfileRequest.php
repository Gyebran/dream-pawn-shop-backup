<?php
namespace App\Http\Requests\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateProfileRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{
  return [
   'name'=>['sometimes','required','string','max:255'],
   'email'=>['sometimes','required','email','max:255',Rule::unique('users','email')->ignore($this->user()->id)],
   'password'=>['sometimes','required','string','min:8','confirmed'],
   'current_password'=>['required_with:password','current_password'],
  ];
 }
 public function messages():array{
  return [
   'current_password.required_with'=>'Masukkan password saat ini untuk mengganti password.',
   'current_password.current_password'=>'Password saat ini tidak sesuai.',
   'email.unique'=>'Email sudah digunakan akun lain.',
  ];
 }
}
