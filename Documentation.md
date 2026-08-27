# Documentation Nusantara Project Creative (NPC)

Selamat datang di dokumentasi resmi **Nusantara Project Creative (NPC)**. Dokumen ini menyajikan gambaran komprehensif mengenai arsitektur sistem, struktur basis data, aliran autentikasi, sistem penerjemahan bilingual (ID/EN), modul CMS Portofolio Karya (Works), komponen UI/UX frontend, serta daftar tugas (To-Do List) pengembangan lebih lanjut.

---

## 📌 Ringkasan Proyek (Project Overview)

**Nusantara Project Creative (NPC)** adalah platform web modern kelas atas (*luxury creative agency portal*) yang dirancang menggunakan pendekatan **Decoupled Architecture (SPA + RESTful API)**.

* **Backend Framework**: Laravel 13 (PHP 8.3+)
* **Frontend Framework**: React 19 + Vite 8
* **Styling & Animation**: CSS Custom (Design System Gold & Midnight Navy), Tailwind CSS v4, Anime.js v4, Lenis Smooth Scroll
* **Localization / i18n**: Multi-Language Support (Bahasa Indonesia & English) dengan persitensi state `localStorage`
* **Autentikasi**: Laravel Sanctum (Bearer Token + CSRF Cookie Stateful Support)
* **Routing**: React Router v7

---

## 🏗️ Struktur & Arsitektur Sistem

Proyek terbagi menjadi dua bagian utama:
1. **Backend API (Laravel)**: `D:\NPC\NusantaraProjectCreative\NPC`
2. **Frontend SPA (React)**: `D:\NPC\NusantaraProjectCreative\NPC\NPCFE`

```
NusantaraProjectCreative/
├── NPC/                      # Backend Laravel 13 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # AuthController, UserController, WorkController
│   │   │   └── Middleware/   # AdminMiddleware
│   │   └── Models/           # User.php, Work.php
│   ├── database/
│   │   ├── migrations/       # Schema tabel users, roles, works, sanctum
│   │   └── seeders/          # DatabaseSeeder (Akun default & Data Works)
│   └── routes/
│       └── api.php           # RESTful API Endpoints
└── NPCFE/                    # Frontend React 19 + Vite SPA
    ├── src/
    │   ├── api/              # Axios Client + Interceptor
    │   ├── auth/             # AuthProvider, ProtectedRoute, RoleRoute, authService
    │   ├── components/       # UI, Navigation, Cards, Layout Sections (Hero, Services, SelectedWorks, Philosophy, CTA, Footer)
    │   │   └── ui/           # LanguageSwitcher, Modals, Buttons
    │   ├── i18n/             # translations.js (ID/EN Dictionary) & LanguageContext.jsx
    │   ├── layouts/          # MainLayout (Public) & AdminLayout (Dashboard)
    │   ├── pages/            # Home, About, Services, Works, Contact, Login, AdminDashboard, AdminUsers, AdminWorks, UserDashboard
    │   └── router.jsx        # Config Routing SPA
```

---

## 🌐 Fitur Bahasa Indonesia & Inggris (Bilingual Localization Architecture)

Aplikasi mendukung lokalisasi dua bahasa secara penuh (**Bahasa Indonesia - `id`** dan **English - `en`**) yang dapat diganti secara instan oleh pengguna di seluruh bagian aplikasi (Public Navigation Bar, Halaman Utama/Home, Footer, Contact Portal, Client Portal, Admin Panel, dan Halaman Login).

### Komponen Utama Sistem i18n:
1. **Kamus Terjemahan Centralized (`src/i18n/translations.js`)**:
   Berisi seluruh pasangan kunci-nilai (*key-value*) untuk UI public (`home`, `nav`, `servicesSection`, `contactPage`, `footer`), admin panel (`adminNav`, `adminDashboard`, `adminUsers`, `adminWorks`), dashboard user (`userDashboard`), serta autentikasi dalam dua bahasa.
2. **State Manager & Context (`src/i18n/LanguageContext.jsx`)**:
   * Mengelola state bahasa aktif (`language`: `'id'` | `'en'`).
   * Menyimpan pilihan bahasa pengguna secara persisten di `localStorage` dengan key `npc_lang`.
   * Menyediakan fungsi helper `t('path.to.key')` dengan fitur *fallback lookup* otomatis jika sebuah teks belum tersedia pada bahasa tertentu.
   * Mengatur atribut `lang` pada elemen `<html>` dokumen secara dinamis.
3. **Komponen Selector UI (`src/components/ui/LanguageSwitcher.jsx`)**:
   * Menampilkan tombol pilihan bahasa bergaya kapsul mewah (Gold & Midnight Navy) dengan penanda bendera (🇮🇩 ID / 🇬🇧 EN).
   * Terintegrasi pada Header Bar utama, Mobile Navigation Drawer, Header Admin Panel, User Client Portal, dan Halaman Login.

### Panduan Menggunakan Terjemahan di Komponen Baru:
```jsx
import { useLanguage } from '../i18n/LanguageContext'

export default function MyComponent() {
  const { t, language } = useLanguage()

  return (
    <div>
      <h1>{t('nav.about')}</h1>
      <p>{t('home.heroDesc')}</p>
    </div>
  )
}
```

---

## 🎨 CMS Manajemen Karya & Portofolio (Works Module)

Modul **Manajemen Karya (Works CMS)** memungkinkan administrator untuk menambah, memperbarui, memfilter, mencari, dan menghapus portofolio karya NPC melalui antarmuka admin yang responsif dan terintegrasi penuh ke REST API Laravel.

### Skema Basis Data (`works` table):
* `id` (BigInt, PK, Auto Increment)
* `num` (String, Nomor karya misal: "01", "02")
* `title` (String, Judul proyek)
* `category` (String, Kategori karya)
* `year` (String, Tahun pembuatan)
* `description` (Text, Deskripsi ringkas karya)
* `tags` (JSON, Array kata kunci/layanan terkait)
* `color` (String, Kode warna aksen HEX/RGB)
* `featured` (Boolean, Flag karya pilihan di halaman utama)
* `created_at` & `updated_at` (Timestamps)

---

## 🔐 System Autentikasi & Authorization

Sistem menggunakan **Laravel Sanctum** dengan 2 tingkatan hak akses (Role):

1. **`admin`**: Memiliki akses penuh ke Admin Dashboard (`/admin`), Manajemen User (`/admin/users`), dan Manajemen Karya (`/admin/works`).
2. **`user`**: Klien / Pengguna terdaftar yang memiliki akses ke Client Portal (`/user`).

### API Endpoints Summary (`routes/api.php`)

| Method | Endpoint | Access | Deskripsi |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/auth/login` | Public | Authenticate user & return Sanctum Bearer Token |
| `POST` | `/api/auth/register` | Public | Registrasi akun pengguna baru (Default Role: `user`) |
| `POST` | `/api/auth/logout` | Auth (`auth:sanctum`) | Revoke token aktif pengguna |
| `GET` | `/api/auth/me` | Auth (`auth:sanctum`) | Mengambil profil pengguna yang sedang login |
| `GET` | `/api/works` | Public | Mendapatkan daftar portofolio karya (Disertai filter search & category) |
| `GET` | `/api/works/{id}` | Public | Detail portofolio karya tertentu |
| `GET` | `/api/user/dashboard` | Auth (`auth:sanctum`) | Endpoint khusus Client Portal |
| `GET` | `/api/admin/dashboard` | Admin Only | Status & statistik ringkasan admin |
| `GET` | `/api/admin/users` | Admin Only | Mengambil seluruh daftar pengguna |
| `POST` | `/api/admin/users` | Admin Only | Membuat pengguna baru (Admin/User) |
| `GET` | `/api/admin/users/{id}` | Admin Only | Detail informasi pengguna tertentu |
| `PUT` | `/api/admin/users/{id}` | Admin Only | Mengedit data/role/password pengguna |
| `DELETE`| `/api/admin/users/{id}` | Admin Only | Menghapus pengguna (Disertai pencegahan self-delete) |
| `GET` | `/api/admin/works` | Admin Only | Menampilkan seluruh daftar karya untuk manajemen admin |
| `POST` | `/api/admin/works` | Admin Only | Membuat portofolio karya baru |
| `PUT` | `/api/admin/works/{id}` | Admin Only | Memperbarui data portofolio karya |
| `DELETE`| `/api/admin/works/{id}` | Admin Only | Menghapus data karya dari sistem |

---

## 🔑 Kredensial Default (Development)

Berikut akun bawaan yang telah dibuat melalui `DatabaseSeeder.php`:

* **Administrator**:
  * Email: `dimas@npc.com`
  * Password: `dimasdimas`
  * Role: `admin`

* **Regular User / Client**:
  * Email: `baji@npc.com`
  * Password: `bajibaji`
  * Role: `user`

---

## 💻 Cara Menjalankan Aplikasi (Setup & Run)

### 1. Menjalankan Backend (Laravel)
```bash
cd D:\NPC\NusantaraProjectCreative\NPC
php artisan migrate --seed   # Jalankan migrasi & seeder karya/user
php artisan serve            # Server backend akan berjalan di http://127.0.0.1:8000
```

### 2. Menjalankan Frontend (React + Vite)
```bash
cd D:\NPC\NusantaraProjectCreative\NPC\NPCFE
npm install                  # Install dependensi jika belum
npm run dev                  # Server frontend akan berjalan di http://localhost:5173
```

---

## 📋 TODO LIST & ANALISIS KEBUTUHAN WEBSITE (Feature Gaps & Roadmap)

Berikut adalah **Daftar Tugas (To-Do List)** serta analisis fitur yang telah diselesaikan maupun yang perlu dilengkapi pada website Nusantara Project Creative:

### 🟢 Fitur Selesai (Completed Features)
- [x] **Dokumentasi Sistem & Arsitektur API (`Documentation.md`)**
- [x] **Autentikasi & Authorization Sanctum (Login, Register, Role-based Routing)**
- [x] **Manajemen Pengguna Admin (CRUD User dengan Modal & Validation)**
- [x] **Fitur Multi-Bahasa (Bilingual ID/EN Localization & Persistensi State di seluruh Halaman Utama, Footer, dan Admin Panel)**
- [x] **Redesign Halaman Kontak Interaktif (Grifflan-Inspired Action Slider & Editorial UX)**
- [x] **Modul Admin CMS Works / Portofolio Karya (Migration, Model, WorkController, Route API, dan Halaman `AdminWorks.jsx` CRUD lengkap)**

---

### 🔴 1. Prioritas Tinggi (Essential Core Backend & API Integration)
- [ ] **CMS Dynamic Services (API & Database)**
  - Buat migration `services` (nama layanan, deskripsi, fitur-fitur, harga estimasi, urutan tampil).
  - Hubungkan halaman `Services.jsx` dengan data dinamis dari API backend.
- [ ] **Form Kontak & Pesan Masuk (Contact Inquiries API)**
  - Buat endpoint `POST /api/contact` untuk menerima kiriman pesan dari halaman `Contact.jsx`.
  - Simpan pesan di database (`contact_submissions`) dan tambahkan modul admin untuk membaca pesan masuk.
  - Tambahkan integrasi Notifikasi Email (SMTP Laravel Mail) ke email admin saat ada pesan baru.
- [ ] **Sistem Lupa Password & Reset Password**
  - Implementasikan endpoint `POST /api/auth/forgot-password` dan `POST /api/auth/reset-password` menggunakan fitur bawaan Laravel Password Broker.

---

### 🟡 2. Prioritas Sedang (Admin Dashboard CMS UI & Client Portal)
- [ ] **Modul Admin CMS Halaman Content (`AdminContent.jsx`)**
  - Kembangkan `/admin/content` dengan fitur manajemen testimoni dan hero banner.
- [ ] **Modul Admin Pengaturan (`AdminSettings.jsx`)**
  - Ganti placeholder route `/admin/settings` dengan halaman pengubahan profil admin, ubah password admin, dan konfigurasi umum website (kontak, sosial media, logo).
- [ ] **Pengembangan Fitur Client Portal (`UserDashboard.jsx`)**
  - Kembangkan dashboard klien agar pengguna terdaftar bisa:
    - Mengirimkan pengajuan proyek baru (*Project Request*).
    - Melihat status pengerjaan proyek mereka (*Progress Tracking*).
    - Mengunduh berkas / invoice proyek.
- [ ] **Upload File & Manajemen Asset Gambar**
  - Tambahkan integrasi unggah file (Laravel `Storage` disk / Public Uploads) untuk foto profil pengguna dan thumbnail portofolio proyek.

---

### 🟢 3. Prioritas Penyempurnaan (UI/UX, Security & SEO)
- [ ] **Notifikasi Feedback & Toast System (Frontend)**
  - Integrasikan library toast (seperti `sonner` atau `react-hot-toast`) agar pemberitahuan sukses/gagal pada form login, register, dan admin modal lebih konsisten dan menarik.
- [ ] **Optimasi SEO & Dynamic Head Tags**
  - Pasang `react-helmet-async` pada frontend React untuk mengatur Title, Meta Description, dan OpenGraph tag secara dinamis per halaman (`Home`, `About`, `Services`, `Works`, `Contact`).
- [ ] **Proteksi Security & Rate Limiting Backend**
  - Aktifkan middleware `throttle:6,1` pada route login untuk mencegah serangan *brute-force*.
  - Konfigurasi `CORS` dan `SANCTUM_STATEFUL_DOMAINS` secara tepat saat deployment production.
- [ ] **Persiapan Deployment (Build & Server Config)**
  - Setup `.env.production` untuk frontend dan backend.
  - Buat script automated build (`npm run build`) dan konfigurasi Nginx / Apache reverse proxy.

---

*Dokumentasi ini diperbarui secara berkala sebagai panduan arsitektur dan peta jalan (roadmap) pengembangan website Nusantara Project Creative.*
