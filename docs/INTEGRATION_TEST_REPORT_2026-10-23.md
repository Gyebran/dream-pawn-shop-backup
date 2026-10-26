# Dream Pawn Shop — Admin + Member Integration & Test Report

**Tanggal:** 23 Oktober 2026  
**Repository:** [Gyebran/Dream-Pawn-Shop](https://github.com/Gyebran/Dream-Pawn-Shop)  
**Target branch:** `main`  
**Pull request:** [#1 — Feat/member backend](https://github.com/Gyebran/Dream-Pawn-Shop/pull/1)  
**Merge commit:** `4aa85f30dced9080a4b4e38bf0aa9b89646a6b7f`  
**Hasil akhir:** **PASS — merge selesai dan feature test pada `main` lulus.**

## 1. Ringkasan eksekutif

Branch `feat/member-backend` direview terhadap `feat/admin-backend`. Setelah aturan batas dua unit dikonfirmasi, dokumentasi di branch diselaraskan, lalu pull request #1 berhasil di-merge ke `main`.

CI setelah merge menjalankan seluruh Laravel feature test pada `main` dan melaporkan:

- **52 tests passed**
- **183 assertions**
- **0 test failures**
- Runtime test suite sekitar **1.25 detik**
- Environment CI menggunakan **PHP 8.3** dan **SQLite in-memory**

CI yang sama kembali lulus pada commit dokumentasi terbaru di `main`. Ini membuktikan test suite yang dikonfigurasi lulus pada source code hasil integrasi. Ini **belum membuktikan kompatibilitas PostgreSQL/NeonDB**, karena workflow saat ini memakai SQLite.

## 2. Riwayat branch dan merge

| Tahap | Hasil |
|---|---|
| Review `feat/member-backend` dibanding `feat/admin-backend` | Member branch mencakup implementasi Admin dan menambahkan workflow Member |
| Konfirmasi aturan pending | Disetujui: pending ikut dihitung saat Member mengajukan request |
| Penyelarasan README di feature branch | Selesai |
| CI pada feature branch setelah perubahan dokumentasi | PASS |
| Merge PR #1 ke `main` | Berhasil |
| Test suite setelah merge pada `main` | PASS, 52 tests / 183 assertions |
| Test ulang setelah pembaruan dokumentasi handoff | PASS, 52 tests / 183 assertions |

- [Pull request #1](https://github.com/Gyebran/Dream-Pawn-Shop/pull/1)
- [CI pada merge commit `4aa85f3`](https://github.com/Gyebran/Dream-Pawn-Shop/actions/runs/39886735331)
- [CI pada `main` setelah pembaruan handoff](https://github.com/Gyebran/Dream-Pawn-Shop/actions/runs/39886845752)
- [Branch Admin](https://github.com/Gyebran/Dream-Pawn-Shop/tree/feat/admin-backend)
- [Branch Member](https://github.com/Gyebran/Dream-Pawn-Shop/tree/feat/member-backend)

## 3. Scope implementasi yang kini ada di main

### Admin backend

- Login/logout dan middleware otorisasi Admin.
- Dashboard Admin.
- Manajemen kategori dan item, termasuk archive.
- Pembuatan, daftar, detail, dan pembaruan akun Member oleh Admin.
- Daftar loan request, approve, reject, handover, dan confirm return.
- Daftar fine dan konfirmasi pembayaran.
- Laporan loan dengan filter dan ekspor CSV.
- Service layer, Form Request, model per domain, migration index/constraint, expiry command/schedule, dan feature tests.

### Member backend

- Login/logout Member dan middleware role.
- Dashboard eligibility, alokasi unit, status request, dan ringkasan denda.
- Lihat/update profil.
- Katalog kategori/item, pencarian, filter ketersediaan, dan detail item.
- Pengajuan loan request serta daftar/detail riwayat milik sendiri.
- Daftar denda milik sendiri.
- Validasi eligibility dan batas alokasi saat submission.
- Demo catalog seeder dan feature tests, termasuk alur end-to-end yang melalui aksi Admin.

### Integrasi lintas role

Feature test end-to-end memverifikasi alur: Member mengajukan request → Admin approve → Admin handover → Member melihat status loan → keterlambatan dihitung → Admin mengonfirmasi return → fine muncul → Admin mengonfirmasi pembayaran → eligibility Member diperbarui.

## 4. Keputusan bisnis yang dikonfirmasi

### Batas maksimum dua unit

Saat Member membuat pengajuan baru, hitung:

`pending units + approved reservations + active loan units + units in the new request <= 2`

Dengan demikian, unit pada request `pending` milik Member **ikut dihitung** saat submission. Test `pending units count toward cap at submission` lulus pada suite utama.

Penting: request `pending` **tidak mereservasi inventaris**. Status item baru berubah menjadi `reserved` saat Admin melakukan approval. Pada tahap approval, Admin tetap memvalidasi alokasi berdasarkan approved reservations dan active loans, bukan jumlah request pending lain. Dengan begitu, stok tidak dikunci sebelum approval dan request yang bersaing atas unit yang sama tetap harus divalidasi ulang oleh workflow Admin.

Aturan ini sudah dicatat di:
- `README_MEMBER_BACKEND.md`
- `README_ADMIN_BACKEND.md`
- `docs/TEAM_HANDOFF_ADMIN_AND_FRONTEND.md`

### Aturan penting lain yang tetap dipertahankan

- Member aktif dan tidak memiliki denda `unpaid` untuk dapat mengajukan request.
- Semua unit yang diajukan harus tersedia saat submission; pengajuan baru berstatus `pending`.
- Masa pinjam lima hari dimulai dari `handed_over_at`.
- Denda dikenakan per blok 24 jam penuh dan per unit fisik.
- Admin mengonfirmasi pengembalian dan pembayaran fine.
- Tidak ada endpoint `return_requested_at`, self-registration, atau pembatalan request oleh Member yang ditambahkan.

## 5. Rincian cakupan feature tests

### Admin

Test suite memeriksa:
- Authentication dan authorization Admin.
- Member/non-Admin ditolak dari dashboard Admin.
- Kategori dan item dapat dibuat; item dapat dicari.
- Akun Member dapat dibuat dan didaftarkan dalam daftar.
- Laporan loan dan validasi rentang tanggal.
- Approval mereservasi item dan handover memulai periode lima hari.
- Fine dibuat untuk blok keterlambatan 24 jam penuh; keterlambatan di bawah 24 jam tidak membuat fine satu hari.
- Unpaid fine memblokir request baru saat approval.
- Batas dua unit saat alokasi.
- Approval handover kedaluwarsa dibatalkan dan reservasi dilepas.

### Member

Test suite memeriksa:
- Authentication, role separation, dan akun nonaktif.
- Login/logout, perubahan profil, validasi password saat mengubah password, serta keunikan email.
- Visibilitas katalog, filter item tersedia, pencarian kategori, dan penyembunyian item archived.
- Submission untuk unit tersedia; penolakan unit duplikat, tidak dikenal, atau tidak tersedia.
- Batas dua unit termasuk pending units.
- Request berstatus returned/rejected tidak lagi dihitung pada batas.
- Pembatasan unpaid fine.
- Akses riwayat dan fine hanya milik Member terkait.
- Ringkasan dashboard dan estimasi keterlambatan.
- End-to-end workflow Member–Admin sampai fine dibayar.

## 6. Bukti CI

### Test pada merge commit

- Workflow: **Admin backend tests**
- Run: [#39886735331](https://github.com/Gyebran/Dream-Pawn-Shop/actions/runs/39886735331)
- Commit: `4aa85f30dced9080a4b4e38bf0aa9b89646a6b7f`
- Hasil: **success**
- Test suite: **52 passed (183 assertions)**

### Test ulang pada main setelah update handoff

- Run: [#39886845752](https://github.com/Gyebran/Dream-Pawn-Shop/actions/runs/39886845752)
- Commit: `e47d0caf277d9ca0f1f2a89e310a6600ba3ba01d`
- Hasil: **success**
- Test suite: **52 passed (183 assertions)**

Workflow menjalankan `composer install`, menyiapkan application key, lalu `php artisan test`. Database test adalah SQLite in-memory. Tidak ada kredensial NeonDB yang digunakan.

## 7. Batasan dan risiko yang belum diselesaikan

Status CI hijau tidak berarti seluruh sistem sudah siap production. Pekerjaan berikut belum terverifikasi dalam pengujian ini:

1. **NeonDB/PostgreSQL:** migrasi, foreign key, tipe timestamp, transaksi, row locking, dan query harus diuji terhadap PostgreSQL.
2. **Concurrent requests pada PostgreSQL:** SQLite feature tests tidak membuktikan race-condition behavior yang sama dengan database produksi.
3. **Scheduler production:** command pembatalan handover expired sudah tersedia, tetapi scheduler harus dikonfigurasi dan diverifikasi di environment deployment.
4. **UI/UX:** backend berupa endpoint JSON; UI Admin dan Member lengkap, integrasi Blade, dan UAT masih pekerjaan lanjutan.
5. **Deployment smoke test:** belum dijalankan pada environment hosting.
6. **Flowchart/ERD:** file diagram terpisah belum dibandingkan secara otomatis; implementasi mengacu pada aturan yang disepakati dan schema/migration yang ada.

## 8. Langkah berikutnya

1. Review seluruh migration dan cocokkan schema dengan NeonDB.
2. Gunakan database test/staging, lalu jalankan migration dari database kosong.
3. Jalankan smoke test pada PostgreSQL/NeonDB dan bereskan perbedaan jika ditemukan.
4. Pastikan scheduler berjalan di environment deployment.
5. Lanjutkan UI Admin dan Member sesuai `docs/TEAM_HANDOFF_ADMIN_AND_FRONTEND.md`.
6. Lakukan UAT dan deployment smoke test sebelum menyebut aplikasi siap production.

## Kesimpulan

**Integrasi Admin + Member berhasil di-merge ke `main`; seluruh 52 feature tests lulus dengan 183 assertions.** Aturan pending-unit cap telah dikonfirmasi dan didokumentasikan. Tahap teknis berikutnya adalah validasi database PostgreSQL/NeonDB, karena CI saat ini masih menggunakan SQLite in-memory.
