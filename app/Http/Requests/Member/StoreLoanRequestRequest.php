<?php
namespace App\Http\Requests\Member;
use App\Services\Loans\LoanEligibilityService;
use Illuminate\Foundation\Http\FormRequest;
class StoreLoanRequestRequest extends FormRequest {
 public function authorize():bool{return true;}
 public function rules():array{
  return [
   'item_ids'=>['required','array','min:1','max:'.LoanEligibilityService::MAX_ALLOCATED_UNITS],
   'item_ids.*'=>['required','integer','distinct','exists:items,id'],
  ];
 }
 public function messages():array{
  return [
   'item_ids.required'=>'Pilih minimal satu unit.',
   'item_ids.max'=>'Maksimal '.LoanEligibilityService::MAX_ALLOCATED_UNITS.' unit per pengajuan.',
   'item_ids.*.distinct'=>'Unit yang sama tidak boleh dipilih dua kali.',
   'item_ids.*.exists'=>'Unit tidak ditemukan.',
  ];
 }
}
