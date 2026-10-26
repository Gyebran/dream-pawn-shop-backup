# Dream Pawn Shop — Team Handoff, Admin Progress & Frontend Contract

**Updated:** 23 October 2026  
**Purpose:** satu pegangan tim untuk status Admin, aturan coding, daftar halaman FE, serta kamus database.

> Nama kolom dan relasi di bawah merangkum informasi yang telah diverifikasi dari implementasi dan keputusan proyek. Untuk tipe data, nullable, default, dan seluruh atribut fisik, migration Laravel adalah sumber kebenaran. Jangan mengarang field jika belum ditemukan di migration.

## 1. Ringkasan proyek

Dream Pawn Shop adalah rental house untuk collectible props fisik dengan konsep fantasi/surreal — bukan toko gadai sungguhan.

- Stack: Laravel/PHP, PostgreSQL melalui NeonDB, Blade + CSS/Bootstrap.
- Identitas autentikasi bersama disimpan di `users`; profil Admin dan Member berada pada tabel masing-masing.
- Controller harus tipis; business logic di service, validasi HTTP di Form Request, schema melalui migration.
- Jika BRD, flowchart, ERD, migration, atau implementasi tampak bertentangan, catat dan minta keputusan. Jangan diam-diam mengubah behavior.

## 2. Progress Admin

Kode Admin saat ini berada di branch `feat/admin-backend`, bukan `main`.

- [Branch Admin backend](https://github.com/Gyebran/Dream-Pawn-Shop/tree/feat/admin-backend)
- [Commit terakhir](https://github.com/Gyebran/Dream-Pawn-Shop/commit/56ef8e86b0c16a33a4c4f89ff4b719d4d493148e)
- [CI run](https://github.com/Gyebran/Dream-Pawn-Shop/actions/runs/39817056255)

### Sudah dikerjakan

- Autentikasi Admin dan middleware otorisasi.
- Dashboard Admin backend.
- Kategori: daftar, buat, detail, update, archive.
- Item: daftar, buat, detail, update, archive.
- Member: daftar, buat, detail, update.
- Loan requests: daftar, approve, reject, handover, confirm return.
- Fines: daftar dan konfirmasi pembayaran.
- Loan report dengan filter dan export CSV.
- Pemisahan Controllers, Form Requests, Services, dan Models per domain.
- Backend Member end-to-end: login/logout, dashboard eligibility, profil, katalog, pengajuan/riwayat loan, dan daftar denda.
- Shared `LoanEligibilityService` untuk unpaid fines dan batas alokasi dua unit.
- Command/schedule untuk membatalkan approval yang melewati deadline handover.
- Migration constraint/index tambahan.
- Feature tests untuk auth, authorization, katalog, Member, report, workflow loan, fine boundary, unpaid fine, batas dua unit, dan expiry.
- CI test memakai SQLite in-memory. Setelah integrasi Admin + Member ke `main`, test suite terakhir berhasil: **52 tests passed, 183 assertions**.
- Aturan Member: unit pada request `pending` milik Member ikut dihitung saat pengajuan baru dibuat, bersama approved reservations dan active loans. Pending tetap tidak mereservasi stok sampai Admin melakukan approval.

### Belum selesai / belum diverifikasi

- NeonDB belum diuji; CI SQLite bukan bukti kompatibilitas PostgreSQL.
- UI/UX lengkap dan integrasi semua halaman Blade belum dianggap selesai.
- Scheduler production perlu benar-benar dikonfigurasi di environment deployment.
- UAT, dokumentasi pengguna, dan final handover masih lanjutan.
- File flowchart/ERD belum tersedia sebagai file terpisah yang dapat dibandingkan langsung di repo saat penulisan dokumen ini.

### Endpoint Admin yang tersedia

| Fitur | Endpoint |
|---|---|
| Auth | `POST /admin/login`, `POST /admin/logout` |
| Dashboard | `GET /admin/dashboard` |
| Categories | `GET/POST /admin/categories`, `GET/PATCH /admin/categories/{category}`, `PATCH /admin/categories/{category}/archive` |
| Items | `GET/POST /admin/items`, `GET/PATCH /admin/items/{item}`, `PATCH /admin/items/{item}/archive` |
| Members | `GET/POST /admin/members`, `GET/PATCH /admin/members/{member}` |
| Loan requests | `GET /admin/loan-requests`; `POST /admin/loan-requests/{loan}/approve`, `reject`, `handover`, `confirm-return` |
| Fines | `GET /admin/fines`, `POST /admin/fines/{fine}/confirm-payment` |
| Reports | `GET /admin/reports/loans`, `GET /admin/reports/loans/export` |

Verifikasi nama route, method, input, dan redirect aktual pada `routes/web.php` serta controller/Form Request sebelum integrasi FE.

### Endpoint Member yang tersedia

| Fitur | Endpoint |
|---|---|
| Auth | `POST /member/login`, `POST /member/logout` |
| Dashboard | `GET /member/dashboard` |
| Profile | `GET /member/profile`, `PATCH /member/profile` |
| Catalog | `GET /member/catalog/categories`, `GET /member/catalog/items`, `GET /member/catalog/items/{item}` |
| Loan requests | `GET /member/loan-requests`, `POST /member/loan-requests`, `GET /member/loan-requests/{loan}` |
| Fines | `GET /member/fines` |

Semua route Member selain login/logout dilindungi autentikasi dan middleware Member. Periksa controller/Form Request untuk detail payload dan response.

## 3. Business rules yang wajib dipertahankan

1. Rental gratis; hanya ada denda keterlambatan.
2. Durasi rental 5 hari kalender sejak waktu serah-terima aktual (`handed_over_at`), bukan sejak request/approval.
3. `due_at = handed_over_at + 5 hari`, pada waktu yang sama.
4. Denda: `floor(overdue_seconds / 86400) × Rp10.000 × jumlah unit fisik`.
5. Keterlambatan kurang dari 24 jam belum menghasilkan denda satu hari penuh.
6. Approval mereservasi seluruh unit secara atomik.
7. Handover harus selesai dalam 24 jam setelah approval. Jika deadline lewat, request dibatalkan dan semua unit reservasi dilepas.
8. Seluruh unit satu request diserahkan dan dikembalikan bersama.
9. Member menghubungi Admin untuk return; Admin memeriksa dan mengonfirmasi pengembalian. Jangan menambahkan `return_requested_at` tanpa revisi ERD/keputusan eksplisit.
10. Member dengan denda `unpaid` tidak boleh membuat request baru.
11. Maksimum 2 unit teralokasi per Member; approved reservation dan active loan sama-sama dihitung.
12. Item/kategori/akun yang memiliki riwayat tidak dihapus permanen; archive/deactivate sesuai domain.
13. Konfirmasi pembayaran hanya oleh Admin dan harus idempotent.
14. Perubahan stok/status sensitif menggunakan transaksi dan row locking.
15. Status item, loan request, dan fine adalah domain terpisah.

Status yang digunakan:
- `items.status`: `available`, `reserved`, `loaned`, `archived`.
- `loan_requests.status`: `pending`, `approved`, `rejected`, `cancelled`, `active`, `returned`.
- `fines.status`: `unpaid`, `paid`.

Jangan menambahkan status seperti `overdue` tanpa keputusan domain; keterlambatan dapat dihitung dari `due_at` dan waktu sekarang.

**Batas dua unit pada pengajuan Member:** hitung unit dari request `pending` milik Member tersebut + approved reservations + active loans + unit pada request baru. Ini aturan saat pengajuan oleh Member. Saat Admin memutuskan approval, service Admin tetap memvalidasi approved reservations dan active loans; pending tidak mereservasi unit fisik.

## 4. Aturan coding dan kolaborasi

### Arsitektur

- `app/Http/Controllers/Admin/`: HTTP orchestration, bukan business logic panjang.
- `app/Http/Requests/Admin/`: validasi dan authorization request.
- `app/Services/Admin/`: use case Admin.
- `app/Services/Loans/LoanEligibilityService.php`: aturan eligibility/alokasi reusable.
- `app/Models/User.php`: identitas login bersama.
- `app/Models/Admin/`, `Member/`, `Catalog/`, `Loans/`, `Fines/`: model per domain.
- `routes/web.php`: route/middleware.
- `database/migrations/`: seluruh perubahan schema.
- `tests/Feature/`: regression tests behavior penting.

**Jangan:** fat controller, duplikasi query/business rule, validasi hanya di JavaScript, otorisasi hanya di UI, angka bisnis hard-coded tersebar, hard delete riwayat, atau ubah database langsung tanpa migration.

### Naming convention

- Database: `snake_case`, tabel plural (`loan_requests`).
- Model: singular PascalCase (`LoanRequest`).
- Method/variable PHP: `camelCase`.
- Controller berakhiran `Controller`; Form Request berakhiran `Request`; service bernama berdasarkan use case.
- Foreign key biasanya `<entity>_id`, kecuali profil Admin/Member yang memakai `user_id` sebagai PK/FK.
- Timestamp event memakai `*_at`, misalnya `handed_over_at`.
- Boolean mengikuti schema yang ada; umumnya `is_`/ `has_`.
- Jangan mengubah nama atribut tanpa memeriksa migration, model, request, service, dan view.

### Git

- `main` adalah branch stabil. Dokumentasi ini diletakkan di `main` atas permintaan eksplisit owner; kode aplikasi tetap dikerjakan pada feature branch.
- Gunakan nama branch seperti `feat/admin-items-ui`, `feat/member-loans`, `fix/loan-due-date`.
- Jangan merge/push kode ke `main`, force-push, hapus branch, atau ubah remote tanpa izin eksplisit.
- PR fokus pada satu fitur dan memuat tujuan, file penting, langkah test, screenshot UI bila relevan, migration, dan dampak business rule.
- Jangan commit `.env`, password, token, Neon connection string, data pribadi, atau kredensial.
- Jangan edit migration lama yang mungkin sudah dijalankan di environment bersama; buat migration baru.
- Perubahan schema destruktif/rename/drop wajib direview dan punya rencana data migration.
- Koordinasikan file/schema/API yang disentuh agar tidak menimpa kerja anggota lain.

### Definition of Done

- [ ] Business rule dan transisi status benar.
- [ ] Authorization server-side dan validasi server aktif.
- [ ] Success, validation error, business error, empty, loading, dan unauthorized state ditangani.
- [ ] Test jalur sukses dan edge case ditambahkan.
- [ ] Query/transaction aman dan tidak ada N+1 yang jelas.
- [ ] Tidak ada secret atau stack trace pada response.
- [ ] UI responsif dan dapat digunakan dengan keyboard.
- [ ] Dokumentasi kontrak diperbarui bila route/schema berubah.
- [ ] CI lolos dan target database sudah diuji sebelum klaim siap.

## 5. Frontend Admin: navigasi dan kontrak per halaman

Navigasi yang disarankan: Dashboard, Categories, Items, Members, Loan Requests, Fines, Reports, Logout. Gunakan layout Blade konsisten dan reusable partial/component untuk sidebar, topbar, breadcrumb, form fields, flash message, status badge, pagination, empty state, dan confirmation dialog.

### Aturan FE umum

- Server adalah sumber kebenaran untuk izin, status, stok, due date, denda, dan eligibility.
- Gunakan route helper Laravel, CSRF, method spoofing PATCH bila dibutuhkan, serta Form Request backend.
- Jangan mengirim actor IDs seperti `reviewed_by`, `recorded_by`, atau `paid_confirmed_by` dari browser sebagai identitas terpercaya. Server mengambil Admin dari sesi login.
- Jangan tampilkan aksi yang tidak valid untuk status saat ini. UI boleh menyembunyikan aksi demi UX, tetapi server tetap harus memvalidasi.
- Jangan gunakan dummy data sebagai hasil final ketika endpoint tersedia.
- Semua tabel perlu pagination, filter yang jelas, dan empty state.
- Form harus punya label, error per field, required state, serta mempertahankan input valid saat validasi gagal.
- Mutasi penting perlu confirmation dialog, loading/disabled state, dan feedback server.
- Jangan pakai browser `alert()` untuk seluruh feedback; gunakan komponen konsisten.
- Jangan mengubah badge/status di client sebelum server mengonfirmasi sukses.
- Jangan menambah fitur yang tidak disepakati seperti payment gateway, rental fee, atau pawn transaction.

### 5.1 Login — `/admin/login`

**Harus ada:** field kredensial sesuai backend, label, tombol login, loading state, pesan error generik, validation feedback, layout responsif, redirect aman setelah berhasil.

**Tidak boleh:** registrasi Admin publik, password hard-coded/logged, pesan yang membocorkan apakah akun tertentu terdaftar, atau menganggap menyembunyikan link cukup untuk keamanan.

### 5.2 Dashboard — `/admin/dashboard`

**Harus ada:** statistik dari backend untuk katalog/status item, request menunggu review, active/late loans berdasarkan due date, unpaid fines bila tersedia; shortcut ke daftar yang butuh tindakan; loading/error/empty state.

**Tidak boleh:** angka hard-coded, grafik dengan data fiktif, atau menganggap `overdue` sebagai status database baru.

### 5.3 Categories — `/admin/categories`

**Harus ada:** daftar, pagination, search/filter bila didukung, create/edit form, validation errors, archive dengan konfirmasi, success/error feedback.

**Tidak boleh:** hard delete kategori bersejarah, field input yang tidak cocok dengan Form Request, atau mengubah relasi kategori tanpa menyimpan pivot dengan benar.

### 5.4 Items — `/admin/items`

**Harus ada:** daftar item, identitas/nama/status/kategori dan atribut yang benar-benar ada di schema, search/filter, pagination, detail/edit, create form, archive confirmation, penjelasan bila item sedang reserved/loaned.

**Tidak boleh:** hard delete, mengubah status `reserved`/`loaned` dari form katalog, mengirim status arbitrary, atau menganggap stok browser sebagai keputusan final approval.

### 5.5 Members — `/admin/members`

**Harus ada:** daftar, pencarian/pagination, status aktif, form create/edit sesuai backend, detail dan ringkasan loan/fine hanya jika data tersedia.

**Tidak boleh:** menampilkan password/hash, mengubah role Admin melalui form Member, hard delete akun bersejarah, atau hanya mengandalkan FE untuk memblokir unpaid fine.

### 5.6 Loan Requests — `/admin/loan-requests`

**Harus ada:** daftar request, Member, waktu request, seluruh unit, status, filter; detail dan aksi sesuai status:
- `pending`: approve/reject dengan konfirmasi.
- `approved`: tampilkan `handover_deadline_at` dan aksi handover yang masih valid.
- `active`: tampilkan `handed_over_at`, `due_at`, item/unit dan indikator terlambat; aksi confirm return.
- `returned`, `rejected`, `cancelled`: riwayat read-only.
Tampilkan error ramah jika stok/status berubah dan jelaskan confirm return berlaku untuk seluruh unit sekaligus.

**Tidak boleh:** mengubah status langsung dari FE, membuat keputusan eligibility sendiri, confirm sebelum server merespons, handover/return per unit jika rule mengharuskan sekaligus, atau menambahkan `return_requested_at` tanpa revisi ERD.

### 5.7 Fines — `/admin/fines`

**Harus ada:** daftar denda, Member/loan terkait, nominal, status, waktu dan dasar perhitungan jika tersedia; filter/pagination; confirm payment hanya untuk unpaid; konfirmasi dan feedback.

**Tidak boleh:** mengubah nominal hasil sistem lewat input bebas tanpa requirement, confirm dua kali untuk fine paid, mengklaim ada payment gateway, atau menambah rental fee.

### 5.8 Reports — `/admin/reports/loans`

**Harus ada:** filter rentang tanggal yang didukung, apply/reset, tabel sesuai filter, empty/error state, export CSV dengan filter sama.

**Tidak boleh:** klaim PDF/Excel jika hanya CSV tersedia, hitung laporan berbeda dari backend dengan data halaman aktif saja, atau kehilangan filter saat export.

### Komponen global

Buat reusable status badge, validation summary, field error, flash message, confirmation modal, pagination, loading/disabled button, empty state, dan error page tanpa stack trace. Gunakan label manusiawi di UI tetapi jangan mengubah nilai status internal.

## 6. Database dictionary

### Relasi inti

- `users` adalah parent identitas.
- `admins.user_id` dan `members.user_id` masing-masing PK sekaligus FK ke `users.id`. Jangan mengasumsikan `admins.id`/`members.id` sebagai PK terpisah.
- `loan_requests.member_id` → `members.user_id`.
- `loan_requests.reviewed_by` dan `loan_requests.return_confirmed_by` → `admins.user_id`.
- `fines.recorded_by` dan `fines.paid_confirmed_by` → `admins.user_id`.
- `item_category` adalah pivot item-category.
- `loan_request_items` menyimpan unit item per request.

### Tabel dan field penting yang diketahui

| Tabel | Fungsi | Kolom/relasi yang diketahui |
|---|---|---|
| `users` | Identitas login | `id`, `role`, `is_active`; kolom kredensial sesuai migration |
| `admins` | Profil Admin | `user_id` PK/FK ke `users.id` |
| `members` | Profil Member | `user_id` PK/FK ke `users.id` |
| `categories` | Kategori katalog | PK dan atribut sesuai migration |
| `items` | Unit fisik katalog | PK, `status`, atribut katalog sesuai migration |
| `item_category` | Pivot kategori | FK item dan kategori sesuai migration |
| `loan_requests` | Header loan | `member_id`, `status`, `requested_at`, `reviewed_at`, `handover_deadline_at`, `handed_over_at`, `due_at`, `returned_at`, `reviewed_by`, `return_confirmed_by` |
| `loan_request_items` | Detail unit request | FK request dan item; pasangan `loan_request_id + item_id` unik |
| `fines` | Denda | `status`, `recorded_by`, `paid_confirmed_by`; field nominal/waktu sesuai migration |

Tabel ini bukan pengganti schema fisik lengkap. Sebelum menulis query/form, buka migration dan model untuk memastikan nama field, tipe, nullable, default, dan relasi.

### Timestamp loan: arti yang tepat

| Kolom | Makna |
|---|---|
| `requested_at` | Waktu Member mengajukan request |
| `reviewed_at` | Waktu Admin approve/reject |
| `handover_deadline_at` | Tenggat handover setelah approval |
| `handed_over_at` | Waktu fisik unit diserahkan |
| `due_at` | Tenggat kembali, lima hari sejak handover |
| `returned_at` | Waktu Admin mengonfirmasi semua unit kembali |

Jangan menyamakan `requested_at` dengan `created_at`, `reviewed_at` dengan handover, atau `handover_deadline_at` dengan `due_at`. ERD yang disepakati tidak memiliki `return_requested_at`.

### Cara memilih atribut di kode

1. Cek migration untuk nama kolom, tipe, nullability, dan default.
2. Cek model untuk casts, relationships, fillable/guarded.
3. Cek Form Request untuk input yang tervalidasi.
4. Gunakan nama field yang sama di Request → Service → Model kecuali ada DTO/kontrak eksplisit.
5. Blade memakai variabel yang disediakan controller; jangan mengarang nama relasi Eloquent.
6. Actor/audit IDs harus diisi server dari Admin yang login, bukan hidden input.
7. Perubahan nama kolom harus melalui migration baru dan penyesuaian model, request, service, view, serta test.
8. Jangan mengirim atribut yang tidak ada di schema ke `create()` atau `update()`.

### Index/constraint yang ditambahkan oleh migration backend

- Unique `loan_request_id + item_id` pada `loan_request_items`.
- Index `loan_request_items.item_id`.
- Index gabungan `loan_requests(member_id, status)`.
- Index gabungan `loan_requests(status, requested_at)`.
- Index gabungan `items(status, name)`.
- Index `fines(status)`.
- Index `item_category(category_id)`.

Jangan hapus index/constraint tanpa memahami query dan menyiapkan migration baru.

## 7. Testing dan NeonDB

1. Rahasiakan `.env`, password, token, dan connection string Neon.
2. Jalankan seluruh migration dari database test kosong sebelum memakai data penting.
3. Uji pada PostgreSQL/Neon: foreign keys, unique constraints, timestamp/timezone, transactions/row locks, reports, dan alokasi bersamaan. SQLite tidak sepenuhnya setara.
4. Uji command expiry dan pastikan scheduler benar-benar aktif di production.
5. Jalankan feature tests dan smoke tests sebagai Admin serta non-Admin.
6. Siapkan backup/rollback plan untuk migration berisiko.
7. Jangan memasukkan kredensial database ke dokumen atau commit.

## 8. Urutan kerja berikutnya

1. Review migration/schema dan cocokkan dengan NeonDB.
2. Jalankan migration pada database test dan selesaikan perbedaan PostgreSQL.
3. Finalisasi kontrak input/response/redirect.
4. Implementasi UI Admin berdasarkan spesifikasi halaman di atas.
5. Integrasikan tiap halaman ke endpoint dan tambahkan test edge case.
6. Implementasi dan integrasi UI Member berdasarkan kontrak workflow yang sudah diimplementasikan.
7. UAT, dokumentasi pengguna, review PR, lalu merge setelah approval.

## 9. Checklist anggota tim

- [ ] Baca business rules.
- [ ] Buat feature branch dan koordinasikan scope file.
- [ ] Periksa migration/model sebelum memilih atribut.
- [ ] Periksa route/Form Request/service sebelum mengintegrasikan FE.
- [ ] Jangan duplikasi business logic atau hard delete riwayat.
- [ ] Tambahkan tests untuk fitur dan edge cases.
- [ ] Jalankan test, review diff, dan pastikan secret tidak ikut.
- [ ] Sertakan langkah test dan screenshot UI pada PR.
- [ ] Minta keputusan bila requirement, ERD, dan schema bertentangan.

**Prinsip akhir:** kalau field, status, route, atau behavior belum pasti, periksa source code/migration atau tanyakan. Jangan mengarang.
