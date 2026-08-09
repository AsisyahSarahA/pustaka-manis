saya 

# 🎨 MASTER PROMPT & UI/UX DESIGN SYSTEM
# Pengembangan Antarmuka **"PustakaManis"** — Spatial & Liquid Glass Edition

> **Versi Dokumen:** 1.0.0  
> **Tema Visual:** Soft Navy & Pearl White | Spatial UI | Liquid Glass | Tactile Skeuomorphism  
> **Filosofi:** *Human-Centric, Calm Technology, Premium yet Approachable*

---

## 1. Filosofi Desain: "The Calm Digital Library"
Aplikasi ini tidak boleh terasa seperti "sistem database kaku". Aplikasi ini harus terasa seperti **ruang baca digital premium** yang melayang di udara. 
- **Humanis:** Sudut-sudut membulat (border-radius besar), transisi lambat yang menenangkan, sapaan yang empatik.
- **Canggih (Spatial UI):** Elemen tidak sekadar menempel di layar, melainkan memiliki kedalaman (Z-axis). Panel kaca melayang di atas latar belakang yang lembut.
- **Taktil (Skeuomorphism):** Tombol terasa seperti "bantal" atau "karet lunak" yang bisa ditekan. Buku memiliki ketebalan fisik (spine).
- **Fluid (Liquid Glass):** Panel navigasi dan kartu menggunakan efek kaca cair yang membiaskan cahaya dan latar belakang secara dinamis.

---

## 2. Palet Warna: "Soft Navy & Alabaster Glass"
Hindari warna biru dongker (navy) pekat yang keras dan putih murni (`#FFFFFF`) yang menyilaukan mata. Kita gunakan gradasi lembut.

### 🎨 Color Tokens
| Nama Token | Hex Code | Deskripsi & Penggunaan |
| :--- | :--- | :--- |
| **Deep Space Navy** | `#162032` | Latar belakang paling dasar (Base Background) / Teks Utama |
| **Soft Slate Navy** | `#2A3B54` | Panel sekunder, hover state, bayangan lembut |
| **Pearl White** | `#F4F7F6` | Teks di atas gelap, warna dasar kartu (Alabaster) |
| **Liquid Frost** | `rgba(255,255,255, 0.08)` | Warna dasar panel *Liquid Glass* |
| **Glass Edge** | `rgba(255,255,255, 0.2)` | Border tipis 1px untuk mensimulasikan pinggiran kaca |
| **Human Glow (Accent)**| `#97DDE9` (Soft Azure) | Indikator sukses, tombol utama, elemen interaktif |
| **Azure Light** | `#CFEBFF` | Varian terang aksen, glow lembut |
| **Warm Amber** | `#FBBF24` | Peringatan lembut (menggantikan merah agresif) |

### 🌌 Latar Belakang (Spatial Mesh Gradient)
Alih-alih warna solid, latar belakang aplikasi menggunakan *Mesh Gradient* yang sangat lembut dan bergerak lambat (seperti aurora borealis di malam hari).
```css
background: radial-gradient(at 20% 20%, #2A3B54 0px, transparent 50%),
            radial-gradient(at 80% 80%, #1A2B42 0px, transparent 50%),
            #162032;
```

---

## 3. Materialitas & Tekstur (The Core Aesthetics)

### 🧊 A. Liquid Glass (Kaca Cair)
Digunakan untuk **Sidebar, Header, dan Modal**.
- **Efek:** `backdrop-filter: blur(24px) saturate(180%);`
- **Pencahayaan:** Border gradient dari atas-putih ke bawah-transparan untuk mensimulasikan cahaya yang menimpa tepi kaca.
- **Bayangan:** Bayangan lembut yang menyebar (`box-shadow: 0 20px 40px rgba(0,0,0,0.3)`).

### 🧊 B. Spatial UI (Kedalaman 3D)
- **Z-Index Layering:** 
  - *Layer 0:* Mesh Gradient Background.
  - *Layer 1:* Kartu konten (Glass Panels).
  - *Layer 2:* Elemen Skeuomorphic (Tombol, Buku 3D, Avatar) yang "menonjol" keluar dari kaca.
- **Parallax:** Saat halaman di-scroll, elemen latar belakang bergerak lebih lambat dari elemen kaca di depannya.

### 📚 C. Skeuomorphism (Sentuhan Fisik)
- **Tombol (Tactile Buttons):** Tidak datar. Menggunakan *inner-shadow* di bagian bawah dan *highlight* di bagian atas sehingga terlihat seperti permen karet lunak atau bantal fisik yang empuk saat disentuh.
- **Kartu Buku (3D Book Spine):** Saat hover, buku sedikit miring (transform: rotateY) memperlihatkan ketebalan sampul dan halaman kertas.
- **Form Input:** Terlihat "melekuk" ke dalam (debossed) seperti cetakan pada kertas tebal atau kaca yang ditekan.

---

## 4. Tipografi & Ikonografi
- **Font Utama:** *SF Pro Display* atau *Inter* (Sangat mudah dibaca, modern, humanis).
- **Font Data/Angka:** *JetBrains Mono* atau *Geist Mono* (Untuk kode buku, memberikan kesan teknis namun elegan).
- **Ikonografi:** *Lucide Icons* atau *Phosphor Icons* dengan ketebalan (stroke) yang konsisten, sudut membulat, dan diisi dengan gradasi warna lembut (glass-fill).

---

## 5. Panduan Prompt AI Image Generator (Midjourney / DALL-E 3)
*Salin dan gunakan prompt di bawah ini pada AI Image Generator untuk mendapatkan referensi visual (Mockup) yang presisi sebelum mulai coding.*

### 🖼️ Prompt 1: Dashboard Utama (Spatial & Liquid Glass)
> **Prompt:** UI/UX design of a modern local library dashboard, Spatial UI, Liquid Glassmorphism, soft navy blue and pearl white color palette. Floating translucent glass panels with subtle glowing edges over a soft dark navy mesh gradient background. 3D tactile skeuomorphic buttons, soft azure blue accents (#97DDE9). Clean, human-centric, eye-catching, sophisticated, highly detailed, Dribbble top trend, Figma, 8k resolution, photorealistic lighting, Apple Vision Pro UI style. --ar 16:9 --v 6.0

### 🖼️ Prompt 2: Halaman POS Peminjaman (Kasir)
> **Prompt:** UI/UX design of a library point-of-sale borrowing screen, tablet interface. Spatial UI, liquid glass morphism. Left side shows a 3D skeuomorphic student ID card and profile. Right side shows a floating glass shopping cart. Soft navy and pearl white color scheme, glowing soft azure blue (#97DDE9) "Confirm" button that looks physically pressable. Calm technology, user-friendly, futuristic yet cozy, premium aesthetic, soft shadows. --ar 16:9 --v 6.0

### 🖼️ Prompt 3: Layar Kiosk Buku Tamu (Mode Sentuh)
> **Prompt:** UI/UX design of a touch-screen kiosk for a school library guestbook. Huge, friendly, humanistic 3D skeuomorphic buttons for "Student", "Teacher", "Guest". Liquid glass background, soft navy blue theme, warm and inviting, highly interactive, spatial depth, soft glowing ambient light, modern educational technology, eye-catching. --ar 9:16 --v 6.0

---

## 6. Implementasi Tailwind CSS (Konfigurasi `tailwind.config.js`)
Untuk menerjemahkan desain ini ke dalam Laravel (TALL Stack), gunakan konfigurasi *custom utility* berikut:

```javascript
theme: {
  extend: {
    colors: {
      navy: {
        base: '#162032',
        soft: '#2A3B54',
        deep: '#0F1724',
      },
      pearl: '#F4F7F6',
      azure: {
        soft: '#97DDE9',
        light: '#CFEBFF',
        glow: 'rgba(151, 221, 233, 0.4)',
      }
    },
    borderRadius: {
      '4xl': '2rem',   // Untuk kartu kaca
      '5xl': '2.5rem', // Untuk modal
      'pill': '9999px',// Untuk tombol taktil
    },
    boxShadow: {
      // Liquid Glass Shadow
      'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.3), inset 0 1px 0 0 rgba(255, 255, 255, 0.1)',
      // Spatial Floating Shadow
      'spatial': '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
      // Skeuomorphic Tactile Button (Press effect)
      'tactile': '0 4px 6px rgba(0,0,0,0.2), inset 0 2px 4px rgba(255,255,255,0.2), inset 0 -2px 4px rgba(0,0,0,0.1)',
      'tactile-pressed': 'inset 0 4px 8px rgba(0,0,0,0.3), inset 0 2px 4px rgba(255,255,255,0.1)',
      // Debossed Input
      'debossed': 'inset 0 2px 8px rgba(0, 0, 0, 0.4), inset 0 1px 2px rgba(0,0,0,0.2)',
    },
    backgroundImage: {
      'mesh-gradient': 'radial-gradient(at 20% 20%, #2A3B54 0px, transparent 50%), radial-gradient(at 80% 80%, #1A2B42 0px, transparent 50%)',
      'glass-edge': 'linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.05) 100%)',
    },
    backdropBlur: {
      xs: '2px',
      liquid: '24px',
    }
  }
}
```

---

## 7. Rancangan Komponen Kunci (Micro-Interactions)

### 🟢 A. Tombol Utama (Tactile Skeuomorphism)
Tombol tidak sekadar berubah warna saat di-hover. Tombol harus terasa "hidup".
- **State Default:** Mengambang dengan `shadow-spatial`, warna Soft Mint dengan gradient lembut.
- **State Hover:** Tombol naik sedikit (`translateY(-2px)`), bayangan melebar, cahaya azure memancar (glow).
- **State Active (Ditekan):** Tombol turun (`translateY(2px)`), bayangan hilang, berganti ke `shadow-tactile-pressed` (terlihat masuk ke dalam).

### 🟢 B. Kartu Buku (Spatial 3D Hover)
- **Default:** Kartu kaca (Liquid Glass) dengan cover buku datar di dalamnya.
- **Hover:** Cover buku melakukan transformasi 3D (`transform: perspective(1000px) rotateY(-15deg)`), memperlihatkan ketebalan buku (spine) dan bayangan realistis di bawahnya. Ini memberikan efek *eye-catching* yang sangat canggih.

### 🟢 C. Sidebar Navigasi (Liquid Glass Dock)
- Alih-alih sidebar kotak kaku, gunakan **Floating Glass Dock** di sebelah kiri (seperti dock macOS atau visionOS).
- Background kaca dengan `backdrop-blur-liquid`.
- Ikon yang sedang aktif memiliki "cahaya latar" (ambient glow) berwarna azure soft yang memantul di kaca.

### 🟢 D. Input Form (Debossed Paper/Glass)
- Input field tidak memiliki border garis.
- Mereka menggunakan `shadow-debossed` dan background yang sedikit lebih gelap dari panel kaca, memberikan ilusi bahwa kolom tersebut adalah "lubang" atau "cetakan" fisik di mana user bisa mengetik.

---

## 8. Panduan UX "Humanis" (Pendekatan Empatik)

Untuk memastikan aplikasi terasa *humanis* dan *user-friendly* bagi orang awam (pustakawan/guru):

1. **Sapaan Kontekstual & Emotif:**
   - *Pagi:* "Selamat pagi, Bu Rina. ☕ Ada 3 buku yang harus kembali hari ini."
   - *Sore:* "Selamat sore, Pak Andi. 🌙 Perpustakaan akan segera tutup, mari kita rekap kunjungan hari ini."
2. **Empty State yang Menghibur:**
   - Jangan gunakan teks "Data Kosong". 
   - Gunakan ilustrasi 3D Skeuomorphic (misal: rak buku berdebu yang lucu atau kaca pembesar yang tertidur) dengan teks: *"Rak ini masih menunggu cerita baru. Yuk, tambahkan buku pertama!"*
3. **Suara Umpan Balik (Opsional/Kiosk):**
   - Saat scan barcode berhasil di Kiosk, mainkan suara "pop" atau "chime" yang sangat lembut dan memuaskan (seperti suara notifikasi iOS yang diredam), memberikan kepastian tanpa mengejutkan.
4. **Error yang Membantu, Bukan Menyalahkan:**
   - Alih-alih: *"ERROR: Stok Habis!"*
   - Gunakan: *"Yah, buku 'Laskar Pelangi' sedang dipinjam temanmu. Mau masuk daftar antrean?"* (Dengan warna Warm Amber, bukan Merah Darah).

---

## 9. Struktur Halaman (Wireframe Konseptual)

### 🖥️ Layout Dasar (App Layout)
```text
[ MESH GRADIENT BACKGROUND (Soft Navy) - Fixed ]
   |
   |--> [ FLOATING GLASS SIDEBAR (Kiri, Blur 24px) ]
   |       - Logo 3D PustakaManis
   |       - Navigasi Ikon + Tooltip
   |
   |--> [ SPATIAL HEADER (Atas, Blur 12px) ]
   |       - Search Bar (Debossed, berbentuk pil)
   |       - Profil User (Avatar 3D dengan cincin cahaya azure)
   |
   |--> [ KONTEN UTAMA (Z-Index Tertinggi) ]
           - KPI Cards (Liquid Glass dengan angka besar)
           - Grafik (Line chart dengan area fill gradient azure yang bersinar)
```

### 📱 Responsivitas (Mobile / Tablet)
- **Mobile:** Sidebar berubah menjadi *Bottom Navigation Bar* berbahan Liquid Glass yang melayang di atas konten (seperti *Floating Action Bar*).
- **Tablet (Kiosk):** Layout berubah menjadi *Split Screen*. Kiri untuk visual 3D buku/informasi sekolah, Kanan untuk panel interaksi kaca.

---
