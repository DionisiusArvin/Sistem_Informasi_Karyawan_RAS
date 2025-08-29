<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <!-- Tambahkan di head -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PT Reno Abirama Sakti</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap"
      rel="stylesheet"
    />

    <!-- AOS Animation -->
    <link
      href="https://unpkg.com/aos@2.3.1/dist/aos.css"
      rel="stylesheet"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans antialiased bg-gray-50">
    <nav
  id="navbar"
  class="animate-navbar bg-gradient-to-r from-blue-600 via-blue-500 to-blue-700 shadow-lg fixed w-full z-50 top-0 transition-all duration-300"
>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-20 items-center transition-all duration-300">
      <!-- Logo -->
      <div class="flex-shrink-0 flex items-center">
        <h1
          class="logo text-2xl font-bold text-white transition-transform duration-300 cursor-pointer"
        >
          PT <span class="text-yellow-300">Reno Abirama Sakti</span>
        </h1>
      </div>

      <!-- Menu -->
      <div class="flex items-center">
        @if (Route::has('login'))
        <div class="space-x-6 flex items-center">
          @auth
          <a
            href="{{ url('/dashboard') }}"
            class="nav-link text-white font-medium"
            >Dashboard</a
          >
          @else
          <a
            href="{{ route('login') }}"
            class="px-4 py-2 rounded-full bg-white text-blue-600 font-semibold hover:bg-yellow-300 hover:text-blue-900 shadow-md transition"
          >
            Log in
          </a>
          @if (Route::has('register'))
          <a
            href="{{ route('register') }}"
            class="px-4 py-2 rounded-full border border-white text-white font-semibold hover:bg-white hover:text-blue-700 shadow-md transition"
          >
            Register
          </a>
          @endif @endauth
        </div>
        @endif
      </div>
    </div>
  </div>
</nav>

<script>
  window.addEventListener("scroll", function () {
    const navbar = document.getElementById("navbar");
    if (window.scrollY > 50) {
      navbar.classList.add("bg-opacity-80", "backdrop-blur-md");
      navbar.classList.replace("h-20", "h-16");
    } else {
      navbar.classList.remove("bg-opacity-80", "backdrop-blur-md");
      navbar.classList.replace("h-16", "h-20");
    }
  });
</script>

<style>
  /* Animasi navbar masuk */
  @keyframes slideFadeDown {
    0% {
      opacity: 0;
      transform: translateY(-50px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }
  .animate-navbar {
    animation: slideFadeDown 0.8s ease-out forwards;
  }

  /* Efek underline animasi glowing gradien */
  .nav-link {
    position: relative;
    padding-bottom: 4px;
    transition: color 0.3s ease;
  }
  .nav-link::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #facc15, #2563eb);
    border-radius: 2px;
    transition: width 0.4s ease;
    box-shadow: 0 0 6px rgba(37, 99, 235, 0.6),
      0 0 12px rgba(250, 204, 21, 0.6);
  }
  .nav-link:hover::after {
    width: 100%;
    animation: glowing 1.5s infinite linear;
  }

  /* Efek glowing untuk logo */
  .logo:hover {
    text-shadow: 0 0 10px rgba(37, 99, 235, 0.8),
      0 0 20px rgba(250, 204, 21, 0.8),
      0 0 30px rgba(37, 99, 235, 0.6);
    animation: glowing 2s infinite linear;
    transform: scale(1.08);
  }

  @keyframes glowing {
    0% {
      filter: hue-rotate(0deg);
    }
    100% {
      filter: hue-rotate(360deg);
    }
  }
</style>


    <!-- Hero Section -->
    <header
      class="relative bg-cover bg-center h-[600px] flex items-center justify-center"
      style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=2070&auto=format&fit=crop');"
    >
      <div class="absolute inset-0 bg-black/60"></div>
      <div
        class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4"
        data-aos="fade-up"
      >
        <h2 class="text-5xl md:text-6xl font-extrabold drop-shadow-lg">
          PT. RENO ABIRAMA SAKTI
        </h2>
        <p
          class="mt-6 max-w-3xl text-lg md:text-xl text-gray-200 leading-relaxed"
        >
          kami bergerak dalam bidang jasa konsultasi, dengan spesifikasi pada
          bidang layanan jasa teknik. Perusahaan ini sejak berdiri sampai saat
          ini selalu aktif dalam penanganan pekerjaan swasta serta pada
          lingkungan Pemkab / Pemkot, menjadi mitra professional bagi pemerintah
          maupun pihak swasta yang menginginkan hasil kerja yang optimal,
          terkendali dan bertanggung jawab.
        </p>
        <a
          href="#services"
          class="mt-10 px-8 py-3 bg-blue-600 hover:bg-blue-700 rounded-full font-semibold shadow-lg transition transform hover:scale-105"
          >Lihat Layanan Kami</a
        >
      </div>
    </header>

    <!-- Layanan -->
    <section id="services" class="py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="text-center" data-aos="fade-down">
          <h3 class="text-3xl font-bold text-gray-800">Layanan Kami</h3>
          <p class="mt-2 text-gray-600">
            Kami menyediakan solusi konstruksi yang komprehensif.
          </p>
        </div>
        <div
          class="mt-14 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
        >
          <!-- Card -->
          <div
            class="bg-white p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mx-auto"
            >
              <svg
                class="h-8 w-8 text-blue-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9 12h6m-6 4h6M9 8h6m2-6H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2z"
                />
              </svg>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800">
              Perencanaan Umum
            </h4>
          </div>

          <div
            class="bg-white p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="100"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mx-auto"
            >
              <i class="fa-solid fa-hard-hat text-yellow-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800">
              Jasa Survey
            </h4>
          </div>

          <div
            class="bg-white p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="200"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mx-auto"
            >
              <i class="fa-solid fa-drafting-compass text-blue-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800">
              Perencanaan Teknik
            </h4>
          </div>

          <div
            class="bg-white p-8 rounded-xl shadow-md text-center hover:shadow-2xl transition transform hover:-translate-y-2"
            data-aos="zoom-in"
            data-aos-delay="300"
          >
            <div
              class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mx-auto"
            >
              <i class="fa-solid fa-binoculars text-green-600 text-3xl"></i>
            </div>
            <h4 class="mt-5 text-xl font-semibold text-gray-800">
              Pengawasan
            </h4>
          </div>
        </div>
      </div>
    </section>

    <!-- Rincian -->
<section class="py-24 bg-gradient-to-b from-gray-50 to-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12" data-aos="fade-up">
      <h3 class="text-3xl font-bold text-gray-800">Dengan Rincian Antara Lain</h3>
    </div>

    <div class="grid gap-10 md:grid-cols-2">
      <!-- Arsitektur dan Rekayasa -->
      <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition" data-aos="fade-right">
        <h4 class="text-2xl font-semibold text-gray-800 mb-4">Arsitektur dan Rekayasa</h4>

        <h3 class="mt-5 px-2 py-1 font-bold text-gray-800 text-left"> > Bidang Usaha Arsitektur (AR) :</h3>
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

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 text-left"> > Bidang Usaha Rekayasa (RK) :</h3>
        <ul class="space-y-2 mt-3">
          <li data-aos="fade-up" data-aos-delay="400" class="flex items-start">
            <i class="fa-solid fa-road text-green-600 mt-1 mr-2"></i>
            Jasa Rekayasa Pekerjaan Teknik Sipil Transportasi
          </li>
          <li data-aos="fade-up" data-aos-delay="500" class="flex items-start">
            <i class="fa-solid fa-water text-green-600 mt-1 mr-2"></i>
            Jasa Rekayasa Pekerjaan Teknik Sipil Sumber Daya Air
          </li>
        </ul>
      </div>

      <!-- Konsultan -->
      <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition" data-aos="fade-left">
        <h4 class="text-2xl font-semibold text-gray-800 mb-4">Konsultan</h4>

        <h3 class="mt-5 px-2 py-1 font-bold text-gray-800 text-left"> > Jasa Survey :</h3>
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

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 text-left"> > Jasa Studi, Penelitian, dan Bantuan Teknik :</h3>
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

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 text-left"> > Jasa Khusus :</h3>
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

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 text-left"> > Kepariwisataan :</h3>
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

        <h3 class="mt-6 px-2 py-1 font-bold text-gray-800 text-left"> > Pengembangan Pertanian dan Pedesaan :</h3>
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
        </ul>
      </div>
    </div>
  </div>
</section>



    <!-- Tombol WA -->
    <div class="text-center my-10" data-aos="zoom-in">
      <a
        href="https://wa.me/628123456789"
        target="_blank"
        class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-medium rounded-full shadow-lg hover:bg-green-600 transition transform hover:scale-105"
      >
        <i class="fa-brands fa-whatsapp text-2xl mr-2"></i>
        Chat via WhatsApp
      </a>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p>&copy; {{ date('Y') }} PT. Konstruksi Jaya. All Rights Reserved.</p>
      </div>
    </footer>

    <!-- Script AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800,
        once: true,
      });
    </script>
  </body>
</html>
