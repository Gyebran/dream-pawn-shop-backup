<?php
namespace App\Services\Admin;
use App\Models\Catalog\Category;
use App\Models\Catalog\Item;
use App\Models\Fines\Fine;
use App\Models\Loans\LoanRequest;
use App\Models\User;
class DashboardService {
 public function summary():array{return [
 'items_total'=>Item::where('status','!=',Item::ARCHIVED)->count(),
 'items_available'=>Item::where('status',Item::AVAILABLE)->count(),
 'categories_active'=>Category::where('is_archived',false)->count(),
 'members_active'=>User::where('role','member')->where('is_active',true)->count(),
 'requests_pending'=>LoanRequest::where('status',LoanRequest::PENDING)->count(),
 'loans_active'=>LoanRequest::where('status',LoanRequest::ACTIVE)->count(),
 'unpaid_fines_amount'=>(int)Fine::where('status',Fine::UNPAID)->sum('amount'),
 'unpaid_fines_count'=>Fine::where('status',Fine::UNPAID)->count()
 ];}
}