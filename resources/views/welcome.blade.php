<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"/>
  <head>
    <!-- Font & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PT Reno Abirama Sakti</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"/>
    
    <link rel="icon" type="image/png" href="image/RAS.png">

        <script>
      // On page load or when changing themes, best to add inline in `head` to avoid FOUC
      if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
      } else {
          document.documentElement.classList.remove('dark')
      }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pt-20">
    
    <!-- Navbar -->
    <nav id="navbar" class="animate-navbar bg-gradient-to-r from-blue-600 via-blue-500 to-blue-700 shadow-lg fixed w-full z-50 top-0 transition-all duration-300 h-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center transition-all duration-300">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <h1 class="logo text-xl sm:text-2xl font-bold text-white cursor-pointer transition-transform duration-300">
              PT <span class="text-yellow-300">Reno Abirama Sakti</span>
            </h1>
          </div>

          <!-- Menu -->
          <div class="flex items-center">
            @if (Route::has('login'))
            <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-3 sm:space-y-0 items-center">
              @auth
              <a href="{{ url('/dashboard') }}" class="nav-link text-white font-medium px-3 py-2">Dashboard</a>
              @else
              <a href="{{ route('login') }}" class="w-full sm:w-auto text-center px-6 py-2 rounded-full bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-bold shadow-md hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Log in
              </a>
              @if (Route::has('register'))
              <a href="{{ route('register') }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-full bg-transparent border-2 border-white/50 text-white font-bold hover:bg-white hover:text-blue-700 hover:border-transparent hover:-translate-y-0.5 transition-all duration-300">
                Register
              </a>
              @endif
              @endauth
              <button id="theme-toggle" type="button" class="text-white hover:text-yellow-300 focus:outline-none transition-colors border border-transparent p-2 rounded-full hover:bg-white/10 ml-0 sm:ml-4">
                <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-lg"></i>
                <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-lg"></i>
              </button>
            </div>
            @endif
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative bg-cover bg-center h-[500px] sm:h-[600px] flex items-center justify-center" style="background-image: url('{{ asset('image/wided.jpg') }}');">
      <div class="absolute inset-0 bg-black/60"></div>
      <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4" data-aos="fade-up">
        <h2 class="text-3xl sm:text-5xl md:text-6xl font-extrabold drop-shadow-lg">
          PT. RENO ABIRAMA SAKTI
        </h2>
        <p class="mt-6 max-w-3xl text-sm sm:text-lg md:text-xl text-gray-200 leading-relaxed px-2">
          kami bergerak dalam bidang jasa konsultasi, dengan spesifikasi pada bidang layanan jasa teknik. 
          Perusahaan ini sejak berdiri sampai saat ini selalu aktif dalam penanganan pekerjaan swasta 
          serta pada lingkungan Pemkab / Pemkot.
        </p>
        <a href="#services" class="mt-10 px-6 sm:px-8 py-3 bg-blue-600 hover:bg-blue-700 rounded-full font-semibold shadow-lg transition transform hover:scale-105">
          Lihat Layanan Kami
        </a>
      </div>
    </header>

    <!-- Layanan -->
    <section id="services" class="py-20 sm:py-24 bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="text-center" data-aos="fade-down">
          <h3 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100">Layanan Kami</h3>
          <p class="mt-2 text-gray-600 dark:text-gray-300 text-sm sm:text-base">Kami menyediakan solusi konstruksi yang komprehensif.</p>
        </div>
        <div class="mt-12 grid gap-6 sm:gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Card contoh -->
          <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="zoom-in">
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-gray-700 mx-auto">
              <svg class="h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6m2-6H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2z"/>
              </svg>
            </div>
            <h4 class="mt-5 text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100">Perencanaan Umum</h4>
            <img src="image/1.jpg">
          </div>
          <div
            class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="100"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-gray-700 mx-auto"
            >
              <i class="fa-solid fa-hard-hat text-yellow-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800 dark:text-gray-100">
              Jasa Survey
            </h4>
            <img src="image/8.jpg">
          </div>

          <div
            class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="200"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-gray-700 mx-auto"
            >
              <i class="fa-solid fa-drafting-compass text-blue-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800 dark:text-gray-100">
              Perencanaan Teknik
            </h4>
            <img src="image/2.jpg">
          </div>

          <div
            class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="300"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-gray-700 mx-auto"
            >
              <i class="fa-solid fa-binoculars text-green-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800 dark:text-gray-100">
              Pengawasan
            </h4>
            <img src="image/7.jpeg">
          </div>
        </div>
      </div>
    </section>

    <!-- Rincian -->
<section class="py-24 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12" data-aos="fade-up">
      <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Dengan Rincian Antara Lain</h3>
    </div>

    <div class="grid gap-10 md:grid-cols-2">
      <!-- Arsitektur dan Rekayasa -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition" data-aos="fade-right">
        <h4 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Arsitektur dan Rekayasa</h4>

        <h3 class="mt-5 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Bidang Usaha Arsitektur (AR) :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="100" class="flex items-start">
            <i class="fa-solid fa-building text-blue-600 mt-1 mr-2"></i>
            Jasa Arsitektural Bangunan Gedung Hunian dan Non Hunian
          </li>
          <li data-aos="fade-up" data-aos-delay="200" class="flex items-start">
            <i class="fa-solid fa-pencil-ruler text-blue-600 mt-1 mr-2"></i>
            Jasa Arsitektur Lainnya
          </li>
          <li data-aos="fade-up" data-aos-delay="300" class="flex items-start">
            <i class="fa-solid fa-couch text-blue-600 mt-1 mr-2"></i>
            Jasa Desain Interior Bangunan Gedung dan Bangunan Sipil
          </li>
        </ul>

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Bidang Usaha Rekayasa (RK) :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="400" class="flex items-start">
            <i class="fa-solid fa-road text-green-600 mt-1 mr-2"></i>
            Jasa Rekayasa Pekerjaan Teknik Sipil Transportasi
          </li>
          <li data-aos="fade-up" data-aos-delay="500" class="flex items-start">
            <i class="fa-solid fa-water text-green-600 mt-1 mr-2"></i>
            Jasa Rekayasa Pekerjaan Teknik Sipil Sumber Daya Air
          </li>
          <img src="image/4.jpeg">
          <img src="image/5.jpeg">
          <img src="image/6.jpeg">
        </ul>
      </div>

      <!-- Konsultan -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-xl transition" data-aos="fade-left">
        <h4 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Konsultan</h4>

        <h3 class="mt-5 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Jasa Survey :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="100" class="flex items-start">
            <i class="fa-solid fa-map-location-dot text-yellow-600 mt-1 mr-2"></i>
            Sistem Informasi Geografi
          </li>
          <li data-aos="fade-up" data-aos-delay="200" class="flex items-start">
            <i class="fa-solid fa-map text-yellow-600 mt-1 mr-2"></i>
            Survey Registrasi Kepemilikan Tanah / Kadastral
          </li>
          <li data-aos="fade-up" data-aos-delay="300" class="flex items-start">
            <i class="fa-solid fa-mountain text-yellow-600 mt-1 mr-2"></i>
            Survey Geologi
          </li>
          <li data-aos="fade-up" data-aos-delay="400" class="flex items-start">
            <i class="fa-solid fa-seedling text-yellow-600 mt-1 mr-2"></i>
            Survey Pertanian
          </li>
        </ul>

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Jasa Studi, Penelitian, dan Bantuan Teknik :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="500" class="flex items-start">
            <i class="fa-solid fa-microscope text-purple-600 mt-1 mr-2"></i>
            Studi Micro
          </li>
          <li data-aos="fade-up" data-aos-delay="600" class="flex items-start">
            <i class="fa-solid fa-chart-line text-purple-600 mt-1 mr-2"></i>
            Studi Kelayakan dan Studi Mikro Lainnya
          </li>
          <li data-aos="fade-up" data-aos-delay="700" class="flex items-start">
            <i class="fa-solid fa-drafting-compass text-purple-600 mt-1 mr-2"></i>
            Studi Perencanaan Umum
          </li>
          <li data-aos="fade-up" data-aos-delay="800" class="flex items-start">
            <i class="fa-solid fa-flask text-purple-600 mt-1 mr-2"></i>
            Jasa Penelitian
          </li>
          <li data-aos="fade-up" data-aos-delay="900" class="flex items-start">
            <i class="fa-solid fa-hands-helping text-purple-600 mt-1 mr-2"></i>
            Jasa Bantuan Teknik
          </li>
        </ul>

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Jasa Khusus :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="1000" class="flex items-start">
            <i class="fa-solid fa-scale-balanced text-red-600 mt-1 mr-2"></i>
            Jasa Penilai / Appraisal / Valuer
          </li>
          <li data-aos="fade-up" data-aos-delay="1100" class="flex items-start">
            <i class="fa-solid fa-user-check text-red-600 mt-1 mr-2"></i>
            Jasa Surveyor Independen
          </li>
          <li data-aos="fade-up" data-aos-delay="1200" class="flex items-start">
            <i class="fa-solid fa-wrench text-red-600 mt-1 mr-2"></i>
            Jasa Inspeksi Teknik
          </li>
        </ul>

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Kepariwisataan :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="1300" class="flex items-start">
            <i class="fa-solid fa-bus text-pink-600 mt-1 mr-2"></i>
            Permintaan, Aspek Transportasi dan Studi Dampak Pariwisata
          </li>
          <li data-aos="fade-up" data-aos-delay="1400" class="flex items-start">
            <i class="fa-solid fa-umbrella-beach text-pink-600 mt-1 mr-2"></i>
            Penyiapan dan Implementasi Proyek Wisata
          </li>
          <li data-aos="fade-up" data-aos-delay="1500" class="flex items-start">
            <i class="fa-solid fa-hotel text-pink-600 mt-1 mr-2"></i>
            Pengelolaan Fasilitas Wisata
          </li>
          <li data-aos="fade-up" data-aos-delay="1600" class="flex items-start">
            <i class="fa-solid fa-landmark text-pink-600 mt-1 mr-2"></i>
            Museum, Benda-benda Sejarah, Margasatwa, Kerajinan dan Lain-lain
          </li>
          <li data-aos="fade-up" data-aos-delay="1700" class="flex items-start">
            <i class="fa-solid fa-mountain-sun text-pink-600 mt-1 mr-2"></i>
            Sub-bidang Kepariwisataan Lainnya
          </li>
        </ul>

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 dark:text-gray-100 text-left"> > Pengembangan Pertanian dan Pedesaan :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="1800" class="flex items-start">
            <i class="fa-solid fa-users text-green-600 mt-1 mr-2"></i>
            Peranan Sosial dan Pengembangan / Partisipasi Masyarakat
          </li>
          <li data-aos="fade-up" data-aos-delay="1900" class="flex items-start">
            <i class="fa-solid fa-hand-holding-dollar text-green-600 mt-1 mr-2"></i>
            Kredit dan Kelembagaan Pertanian
          </li>
          <li data-aos="fade-up" data-aos-delay="2000" class="flex items-start">
            <i class="fa-solid fa-seedling text-green-600 mt-1 mr-2"></i>
            Pembibitan
          </li>
          <li data-aos="fade-up" data-aos-delay="2100" class="flex items-start">
            <i class="fa-solid fa-bug text-green-600 mt-1 mr-2"></i>
            Pengendalian Hama / Penyakit Tanaman
          </li>
          <li data-aos="fade-up" data-aos-delay="2200" class="flex items-start">
            <i class="fa-solid fa-cow text-green-600 mt-1 mr-2"></i>
            Peternakan
          </li>
          <li data-aos="fade-up" data-aos-delay="2300" class="flex items-start">
            <i class="fa-solid fa-tree text-green-600 mt-1 mr-2"></i>
            Kehutanan
          </li>
          <li data-aos="fade-up" data-aos-delay="2400" class="flex items-start">
            <i class="fa-solid fa-fish text-green-600 mt-1 mr-2"></i>
            Perikanan
          </li>
          <li data-aos="fade-up" data-aos-delay="2500" class="flex items-start">
            <i class="fa-solid fa-apple-whole text-green-600 mt-1 mr-2"></i>
            Pengolahan Keras dan Pangan, dan Produk Tanaman Lain
          </li>
          <li data-aos="fade-up" data-aos-delay="2600" class="flex items-start">
            <i class="fa-solid fa-leaf text-green-600 mt-1 mr-2"></i>
            Konservasi dan Penghijauan
          </li>
          <li data-aos="fade-up" data-aos-delay="2700" class="flex items-start">
            <i class="fa-solid fa-tractor text-green-600 mt-1 mr-2"></i>
            Sub-bidang Pengembangan Pertanian dan Pedesaan Lainnya
          </li>
          <img src="image/9.jpg">
        </ul>
      </div>
        </div>
      </div>
    </section>

    <!-- Tombol WA -->
    <div class="text-center my-10 px-4" data-aos="zoom-in">
      <a href="https://wa.me/0818518168" target="_blank" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-green-500 text-white font-medium rounded-full shadow-lg hover:bg-green-600 transition transform hover:scale-105">
        <i class="fa-brands fa-whatsapp text-2xl mr-2"></i>
        Chat via WhatsApp
      </a>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p>&copy; {{ date('Y') }} PT. Reno Abirama Sakti. All Rights Reserved.</p>
      </div>
    </footer>

    <!-- Chatbot Widget -->
    <div id="chat-widget" class="fixed bottom-6 right-6 z-50">
      <!-- Chat Button -->
      <button id="chat-btn" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-transform transform hover:scale-110">
        <i class="fa-solid fa-comments text-2xl"></i>
      </button>

      <!-- Chat Panel -->
      <div id="chat-panel" class="hidden absolute bottom-20 right-0 w-80 sm:w-80 w-[calc(100vw-3rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 flex-col overflow-hidden transition-all duration-300 transform origin-bottom-right scale-95 opacity-0">
        <!-- Header -->
        <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
          <div class="flex items-center space-x-2">
            <i class="fa-solid fa-robot text-xl"></i>
            <h3 class="font-bold text-sm">RAS Virtual Assistant</h3>
          </div>
          <button id="close-chat" class="text-white hover:text-gray-200 focus:outline-none">
            <i class="fa-solid fa-xmark text-lg"></i>
          </button>
        </div>

        <!-- Chat body -->
        <div id="chat-body" class="p-4 h-72 overflow-y-auto bg-gray-50 dark:bg-gray-900 flex flex-col space-y-3">
          <!-- Bot Message -->
          <div class="bg-blue-100 dark:bg-gray-700 text-blue-900 dark:text-blue-300 px-4 py-2 rounded-lg rounded-tl-none self-start max-w-[90%] text-sm shadow-sm">
            Halo! 👋 Saya asisten virtual PT Reno Abirama Sakti. Ada yang bisa saya bantu terkait estimasi harga jasa kami?
          </div>
        </div>

        <!-- Options / Input -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex flex-col">
          <div id="chat-options" class="flex gap-2 p-3 pb-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
            <button class="chat-option flex-shrink-0 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-full transition" data-reply="Harga untuk Perencanaan Umum dan Arsitektur sangat bervariasi bergantung pada skala bangunan, tingkat kesulitan, dan lokasi. Silakan konsultasikan detail proyek Anda kepada tim ahli kami.">
              Harga Perencanaan
            </button>
            <button class="chat-option flex-shrink-0 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-full transition" data-reply="Estimasi Harga Jasa Survey dan Pemetaan disesuaikan dengan luas lahan, topografi, dan jenis alat yang dibutuhkan (misalnya Total Station atau Drone/UAV). Hubungi kami untuk penawaran terbaik.">
              Harga Jasa Survey
            </button>
            <button class="chat-option flex-shrink-0 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-full transition" data-reply="Biaya Jasa Pengawasan (Supervisi) umumnya dihitung dari persentase Rencana Anggaran Biaya (RAB) fisik bangunan atau berdasarkan billing rate tenaga ahli (man-month).">
              Harga Pengawasan
            </button>
            <button class="chat-option flex-shrink-0 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-green-500 hover:text-white dark:hover:bg-green-500 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-full transition" data-reply="Anda dapat menghubungi admin kami melalui WhatsApp untuk mendapatkan estimasi harga yang lebih akurat sesuai kebutuhan pekerjaan Anda.">
              Tanya via WhatsApp
            </button>
          </div>
          <div class="px-3 pb-3 pt-1 flex items-center">
            <input type="text" id="chat-input" class="w-full bg-gray-100 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-200 rounded-full px-4 py-2 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik pesan disini...">
            <button id="send-chat" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full w-9 h-9 flex items-center justify-center flex-shrink-0 transition transform hover:scale-105">
              <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            if(themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
        } else {
            if(themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
            });
        }
      });
    </script>
    <!-- Script AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800, once: true });
    </script>

    <!-- Navbar Scroll -->
    <script>
      window.addEventListener("scroll", function () {
        const navbar = document.getElementById("navbar");
        if (window.scrollY > 50) {
          navbar.classList.remove("h-20", "bg-gradient-to-r", "from-blue-600", "via-blue-500", "to-blue-700");
          navbar.classList.add("bg-black/30", "backdrop-blur-md", "h-16");
        } else {
          navbar.classList.remove("bg-black/30", "backdrop-blur-md", "h-16");
          navbar.classList.add("h-20", "bg-gradient-to-r", "from-blue-600", "via-blue-500", "to-blue-700");
        }
      });
    </script>

    <style>
      /* Hide scrollbar */
      .scrollbar-hide::-webkit-scrollbar {
        display: none;
      }
      .scrollbar-hide {
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
      }
      /* Animasi navbar */
      @keyframes slideFadeDown {
        0% { opacity: 0; transform: translateY(-50px); }
        100% { opacity: 1; transform: translateY(0); }
      }
      .animate-navbar { animation: slideFadeDown 0.8s ease-out forwards; }
      /* Animasi underline menu */
      .nav-link { position: relative; padding-bottom: 4px; transition: color 0.3s ease; }
      .nav-link::after {
        content: "";
        position: absolute; left: 0; bottom: 0; width: 0%; height: 3px;
        background: linear-gradient(90deg, #2563eb, #facc15, #2563eb);
        border-radius: 2px; transition: width 0.4s ease;
        box-shadow: 0 0 6px rgba(37,99,235,0.6), 0 0 12px rgba(250,204,21,0.6);
      }
      .nav-link:hover::after { width: 100%; animation: glowing 1.5s infinite linear; }
      /* Efek glowing logo */
      .logo:hover {
        text-shadow: 0 0 10px rgba(37,99,235,0.8), 0 0 20px rgba(250,204,21,0.8), 0 0 30px rgba(37,99,235,0.6);
        animation: glowing 2s infinite linear; transform: scale(1.08);
      }
      @keyframes glowing { 0% { filter: hue-rotate(0deg); } 100% { filter: hue-rotate(360deg); } }
    </style>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const chatBtn = document.getElementById("chat-btn");
        const chatPanel = document.getElementById("chat-panel");
        const closeChat = document.getElementById("close-chat");
        const chatBody = document.getElementById("chat-body");
        const chatOptions = document.querySelectorAll(".chat-option");
        const chatInput = document.getElementById("chat-input");
        const sendChat = document.getElementById("send-chat");

        function toggleChat() {
          if (chatPanel.classList.contains("hidden")) {
            chatPanel.classList.remove("hidden");
            setTimeout(() => {
              chatPanel.classList.remove("scale-95", "opacity-0");
              chatPanel.classList.add("scale-100", "opacity-100");
              chatBody.scrollTo({top: chatBody.scrollHeight, behavior: 'smooth'});
            }, 10);
            chatInput.focus();
          } else {
            chatPanel.classList.remove("scale-100", "opacity-100");
            chatPanel.classList.add("scale-95", "opacity-0");
            setTimeout(() => {
              chatPanel.classList.add("hidden");
            }, 300);
          }
        }

        chatBtn.addEventListener("click", toggleChat);
        closeChat.addEventListener("click", toggleChat);

        function handleUserMessage(userText, customReply = null) {
            if(!userText.trim()) return;
            
            // Add user message with animation
            const userMsg = document.createElement("div");
            userMsg.className = "bg-blue-600 text-white px-4 py-2 rounded-lg rounded-tr-none self-end max-w-[90%] text-sm shadow-sm mt-3 transition-all duration-300 transform translate-y-4 opacity-0";
            userMsg.innerText = userText.trim();
            chatBody.appendChild(userMsg);
            
            setTimeout(() => {
               userMsg.classList.remove("translate-y-4", "opacity-0");
            }, 20);

            chatBody.scrollTo({top: chatBody.scrollHeight, behavior: 'smooth'});

            // Bot response logic
            let botReply = customReply;
            if(!botReply) {
                const lowerText = userText.toLowerCase();
                if (lowerText.includes("harga") && lowerText.includes("perencanaan")) {
                    botReply = "Harga untuk Perencanaan Umum dan Arsitektur sangat bervariasi bergantung pada skala bangunan, tingkat kesulitan, dan lokasi. Silakan konsultasikan detail proyek Anda kepada tim ahli kami.";
                } else if (lowerText.includes("survey") || lowerText.includes("survei")) {
                    botReply = "Estimasi Harga Jasa Survey dan Pemetaan disesuaikan dengan luas lahan, topografi, dan jenis alat yang dibutuhkan. Hubungi kami untuk penawaran terbaik.";
                } else if (lowerText.includes("pengawasan") || lowerText.includes("supervisi")) {
                    botReply = "Biaya Jasa Pengawasan (Supervisi) umumnya dihitung dari persentase Rencana Anggaran Biaya (RAB) fisik bangunan atau berdasarkan billing rate tenaga ahli (man-month).";
                } else if (lowerText.includes("whatsapp") || lowerText.includes("wa") || lowerText.includes("kontak") || lowerText.includes("hubungi")) {
                    botReply = "Silakan hubungi admin kami melalui tombol WhatsApp yang telah disediakan untuk informasi lebih lanjut.";
                } else if (lowerText.includes("harga") || lowerText.includes("biaya")) {
                    botReply = "Harga jasa kami bervariasi tergantung jenis layanan dan skala proyek. Anda bisa spesifikkan layanan yang dimaksud (misal: Harga Survey) atau hubungi kami via WhatsApp untuk detailnya.";
                } else if (lowerText.includes("halo") || lowerText.includes("hai") || lowerText.includes("hi")) {
                    botReply = "Halo! Silakan ketik pertanyaan Anda atau pilih opsi yang tersedia di bawah.";
                } else {
                    botReply = "Maaf, saya asisten virtual terbatas. Anda bisa memilih salah satu opsi layanan atau hubungi kami melalui WhatsApp untuk detail lebih lanjut.";
                }
            }

            // Simulate bot typing
            setTimeout(() => {
              const botMsg = document.createElement("div");
              botMsg.className = "bg-blue-100 dark:bg-gray-700 text-blue-900 dark:text-blue-300 px-4 py-2 rounded-lg rounded-tl-none self-start max-w-[90%] text-sm shadow-sm mt-3 transition-all duration-300 transform translate-y-4 opacity-0 flex items-center justify-center w-14 h-9";
              botMsg.innerHTML = '<span class="flex space-x-1"><span class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full animate-bounce"></span><span class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span><span class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span></span>';
              chatBody.appendChild(botMsg);
              
              setTimeout(() => {
                 botMsg.classList.remove("translate-y-4", "opacity-0");
              }, 20);

              chatBody.scrollTo({top: chatBody.scrollHeight, behavior: 'smooth'});

              setTimeout(() => {
                chatBody.removeChild(botMsg);

                const botReplyMsg = document.createElement("div");
                botReplyMsg.className = "bg-blue-100 dark:bg-gray-700 text-blue-900 dark:text-blue-300 px-4 py-2 rounded-lg rounded-tl-none self-start max-w-[90%] text-sm shadow-sm mt-3 transition-all duration-300 transform translate-y-4 opacity-0";
                botReplyMsg.innerText = botReply;
                chatBody.appendChild(botReplyMsg);

                setTimeout(() => {
                   botReplyMsg.classList.remove("translate-y-4", "opacity-0");
                }, 20);

                if (botReply.includes("WhatsApp") || botReply.includes("Hubungi") || botReply.includes("hubungi") || botReply.includes("whatsapp") || botReply.includes("wa") || botReply.includes("kontak")) {
                   const actionBtn = document.createElement("a");
                   actionBtn.href = "https://wa.me/0818518168";
                   actionBtn.target = "_blank";
                   actionBtn.className = "mt-3 inline-block bg-green-500 text-white text-xs px-3 py-1.5 rounded-full hover:bg-green-600 self-start shadow-md flex items-center transition transform hover:scale-105 duration-300 translate-y-4 opacity-0";
                   actionBtn.innerHTML = '<i class="fa-brands fa-whatsapp mr-1 text-sm"></i> Lanjutkan ke WhatsApp';
                   chatBody.appendChild(actionBtn);
                   
                   setTimeout(() => {
                      actionBtn.classList.remove("translate-y-4", "opacity-0");
                   }, 20);
                }

                chatBody.scrollTo({top: chatBody.scrollHeight, behavior: 'smooth'});
              }, 1200);
            }, 300);
        }

        chatOptions.forEach(option => {
          option.addEventListener("click", function() {
            const userText = this.innerText;
            const botReply = this.getAttribute("data-reply");
            handleUserMessage(userText, botReply);
          });
        });

        sendChat.addEventListener("click", function() {
           const text = chatInput.value;
           if(text.trim()) {
               handleUserMessage(text);
               chatInput.value = "";
           }
        });

        chatInput.addEventListener("keypress", function(e) {
           if(e.key === "Enter") {
               const text = chatInput.value;
               if(text.trim()) {
                   handleUserMessage(text);
                   chatInput.value = "";
               }
           }
        });
      });
    </script>
  </body>
</html>
