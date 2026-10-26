<?php
namespace Tests\Feature;
use App\Models\Catalog\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
class AdminBackendTest extends TestCase {
 use RefreshDatabase;
 private function roleUser(string $role,bool $active=true):User{$now=now();$id=DB::table('users')->insertGetId(['name'=>'Test '.ucfirst($role),'email'=>$role.'-'.uniqid().'@example.test','password'=>Hash::make('secret-password'),'role'=>$role,'is_active'=>$active,'created_at'=>$now,'updated_at'=>$now]);DB::table($role==='admin'?'admins':'members')->insert(['user_id'=>$id]);return User::findOrFail($id);}
 private function item(string $status=Item::AVAILABLE):int{return DB::table('items')->insertGetId(['unit_code'=>'UNIT-'.uniqid(),'name'=>'The Yesterday Watch','description'=>'Test prop','status'=>$status,'created_at'=>now(),'updated_at'=>now()]);}
 private function loan(int $memberId,int $itemId,string $status='pending',?\DateTimeInterface $dueAt=null):array{$now=now();$id=DB::table('loan_requests')->insertGetId(['member_id'=>$memberId,'status'=>$status,'requested_at'=>$now->copy()->subDays(6),'handed_over_at'=>$status==='active'?$now->copy()->subDays(5):null,'due_at'=>$dueAt,'created_at'=>$now,'updated_at'=>$now]);$detail=DB::table('loan_request_items')->insertGetId(['loan_request_id'=>$id,'item_id'=>$itemId,'created_at'=>$now]);return ['id'=>$id,'detail'=>$detail];}
 public function test_admin_dashboard_requires_authentication():void{$this->getJson('/admin/dashboard')->assertUnauthorized();}
 public function test_member_cannot_access_admin_dashboard():void{$this->actingAs($this->roleUser('member'))->getJson('/admin/dashboard')->assertForbidden();}
 public function test_inactive_admin_cannot_access_admin_dashboard():void{$this->actingAs($this->roleUser('admin',false))->getJson('/admin/dashboard')->assertForbidden();}
 public function test_admin_login_rejects_member_account():void{$u=$this->roleUser('member');$this->postJson('/admin/login',['email'=>$u->email,'password'=>'secret-password'])->assertForbidden();}
 public function test_admin_login_succeeds_for_admin_account():void{$u=$this->roleUser('admin');$this->postJson('/admin/login',['email'=>$u->email,'password'=>'secret-password'])->assertOk()->assertJsonPath('data.role','admin');}
 public function test_admin_can_create_category_and_item():void{$this->actingAs($this->roleUser('admin'))->postJson('/admin/categories',['name'=>'Relics'])->assertCreated();$category=DB::table('categories')->where('name','Relics')->value('id');$this->postJson('/admin/items',['unit_code'=>'REL-001','name'=>'The Yesterday Watch','category_ids'=>[$category]])->assertCreated()->assertJsonPath('data.status','available');}
 public function test_admin_can_search_items_by_name():void{$this->item();$this->actingAs($this->roleUser('admin'))->getJson('/admin/items?search=Yesterday')->assertOk()->assertJsonFragment(['name'=>'The Yesterday Watch']);}
 public function test_admin_can_create_and_list_member_accounts():void{$this->actingAs($this->roleUser('admin'))->postJson('/admin/members',['name'=>'New Member','email'=>'new-member@example.test','password'=>'member-password-123','password_confirmation'=>'member-password-123'])->assertCreated()->assertJsonPath('data.role','member');$this->getJson('/admin/members')->assertOk()->assertJsonFragment(['email'=>'new-member@example.test']);}
 public function test_admin_can_view_loan_report_with_only_end_date_filter():void{$this->actingAs($this->roleUser('admin'))->getJson('/admin/reports/loans?to=2026-10-22')->assertOk();}
 public function test_admin_rejects_invalid_report_date_range():void{$this->actingAs($this->roleUser('admin'))->getJson('/admin/reports/loans?from=2026-10-23&to=2026-10-22')->assertUnprocessable();}
 public function test_approval_reserves_unit_and_handover_starts_five_day_period():void{$admin=$this->roleUser('admin');$member=$this->roleUser('member');$item=$this->item();$now=now()->startOfSecond();$this->travelTo($now);$loan=$this->loan($member->id,$item);$this->actingAs($admin)->postJson("/admin/loan-requests/{$loan['id']}/approve")->assertOk();$this->assertDatabaseHas('items',['id'=>$item,'status'=>'reserved']);$this->postJson("/admin/loan-requests/{$loan['id']}/handover")->assertOk();$this->assertDatabaseHas('items',['id'=>$item,'status'=>'loaned']);$this->assertDatabaseHas('loan_requests',['id'=>$loan['id'],'status'=>'active','due_at'=>$now->copy()->addDays(5)->format('Y-m-d H:i:s')]);}
 public function test_return_creates_fine_for_each_full_24_hour_period():void{$admin=$this->roleUser('admin');$member=$this->roleUser('member');$item=$this->item(Item::LOANED);$now=now()->startOfSecond();$this->travelTo($now);$loan=$this->loan($member->id,$item,'active',$now->copy()->subDay());$this->actingAs($admin)->postJson("/admin/loan-requests/{$loan['id']}/confirm-return")->assertOk();$this->assertDatabaseHas('fines',['loan_request_item_id'=>$loan['detail'],'amount'=>10000,'status'=>'unpaid']);$this->assertDatabaseHas('items',['id'=>$item,'status'=>Item::AVAILABLE]);$this->assertDatabaseHas('loan_requests',['id'=>$loan['id'],'status'=>'returned','return_confirmed_by'=>$admin->id]);}
 public function test_return_less_than_24_hours_late_does_not_create_fine():void{$admin=$this->roleUser('admin');$member=$this->roleUser('member');$item=$this->item(Item::LOANED);$now=now()->startOfSecond();$this->travelTo($now);$loan=$this->loan($member->id,$item,'active',$now->copy()->subHours(23)->subMinutes(59));$this->actingAs($admin)->postJson("/admin/loan-requests/{$loan['id']}/confirm-return")->assertOk();$this->assertDatabaseMissing('fines',['loan_request_item_id'=>$loan['detail']]);}
 public function test_member_with_unpaid_fine_cannot_have_new_request_approved():void{$admin=$this->roleUser('admin');$member=$this->roleUser('member');$oldItem=$this->item(Item::ARCHIVED);$old=$this->loan($member->id,$oldItem,'returned',now()->subDays(2));DB::table('fines')->insert(['loan_request_item_id'=>$old['detail'],'recorded_by'=>$admin->id,'amount'=>10000,'status'=>'unpaid','assessed_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);$newItem=$this->item();$pending=$this->loan($member->id,$newItem);$this->actingAs($admin)->postJson("/admin/loan-requests/{$pending['id']}/approve")->assertUnprocessable();$this->assertDatabaseHas('loan_requests',['id'=>$pending['id'],'status'=>'pending']);$this->assertDatabaseHas('items',['id'=>$newItem,'status'=>Item::AVAILABLE]);}
 public function test_member_cannot_exceed_two_allocated_units():void{$admin=$this->roleUser('admin');$member=$this->roleUser('member');$now=now();$existing=DB::table('loan_requests')->insertGetId(['member_id'=>$member->id,'status'=>'active','requested_at'=>$now->copy()->subDays(6),'handed_over_at'=>$now->copy()->subDays(5),'due_at'=>$now->copy()->addDay(),'created_at'=>$now,'updated_at'=>$now]);foreach([Item::LOANED,Item::LOANED] as $status)DB::table('loan_request_items')->insert(['loan_request_id'=>$existing,'item_id'=>$this->item($status),'created_at'=>$now]);$available=$this->item();$pending=$this->loan($member->id,$available);$this->actingAs($admin)->postJson("/admin/loan-requests/{$pending['id']}/approve")->assertUnprocessable();$this->assertDatabaseHas('items',['id'=>$available,'status'=>Item::AVAILABLE]);}
 public function test_expired_approved_request_is_cancelled_and_reserved_units_released():void{$member=$this->roleUser('member');$item=$this->item(Item::RESERVED);$now=now();$loan=DB::table('loan_requests')->insertGetId(['member_id'=>$member->id,'status'=>'approved','requested_at'=>$now->copy()->subDay(),'reviewed_at'=>$now->copy()->subDay(),'handover_deadline_at'=>$now->copy()->subSecond(),'created_at'=>$now,'updated_at'=>$now]);DB::table('loan_request_items')->insert(['loan_request_id'=>$loan,'item_id'=>$item,'created_at'=>$now]);Artisan::call('loans:cancel-expired-handover');$this->assertDatabaseHas('loan_requests',['id'=>$loan,'status'=>'cancelled']);$this->assertDatabaseHas('items',['id'=>$item,'status'=>Item::AVAILABLE]);}
 public function test_admin_can_upload_an_item_photo_to_public_storage():void{
  Storage::fake('public');
  $this->actingAs($this->roleUser('admin'))->post('/admin/items',[
   'unit_code'=>'IMG-001',
   'name'=>'The Illustrated Relic',
   'description'=>'Catalog image test',
   'image'=>UploadedFile::fake()->image('relic.jpg',640,480),
  ])->assertRedirect(route('admin.items.index'));
  $item=DB::table('items')->where('name','The Illustrated Relic')->first();
  $this->assertNotNull($item);
  $this->assertNotEmpty($item->image_path);
  Storage::disk('public')->assertExists($item->image_path);
 }
 public function test_admin_item_photo_rejects_non_image_uploads():void{
  Storage::fake('public');
  $this->actingAs($this->roleUser('admin'))->postJson('/admin/items',[
   'unit_code'=>'IMG-BAD-001',
   'name'=>'Invalid Photo',
   'image'=>UploadedFile::fake()->create('payload.txt',10,'text/plain'),
  ])->assertUnprocessable()->assertJsonValidationErrors('image');
  $this->assertDatabaseMissing('items',['unit_code'=>'IMG-BAD-001']);
 }

}
