    <!-- FOOTER SECTION (NAVY DARK THEME MATCHING MOCKUP) -->
    <footer class="bg-brand-dark text-slate-300 pt-16 pb-12 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-slate-800">
                
                <!-- BRAND INFO -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-white font-bold">
                            <i class="fa-solid fa-school text-lg"></i>
                        </div>
                        <span class="font-extrabold text-xl text-white tracking-tight">SMPN CIMAHI</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-sm">
                        Sekolah Menengah Pertama unggulan Kota Cimahi yang mencetak generasi bertakwa, cerdas, kreatif, serta siap bersaing di era global.
                    </p>
                    <div class="flex items-center gap-3 text-slate-400 pt-2">
                        <a href="https://www.facebook.com" target="_blank" aria-label="Facebook" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-brand-accent hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank" aria-label="Instagram" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-brand-accent hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        <a href="https://www.youtube.com" target="_blank" aria-label="YouTube" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-brand-accent hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- TAUTAN UTAMA -->
                <div class="md:col-span-3 space-y-3">
                    <p class="text-xs font-bold text-white uppercase tracking-wider">Tautan Utama</p>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <li><a href="index.php#beranda" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="index.php#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="index.php#ekstra" class="hover:text-white transition-colors">Ekstrakulikuler</a></li>
                        <li><a href="pendaftaran.php" class="hover:text-white transition-colors">Pendaftaran PPDB</a></li>
                    </ul>
                </div>

                <!-- KONTAK & ALAMAT -->
                <div class="md:col-span-4 space-y-3">
                    <p class="text-xs font-bold text-white uppercase tracking-wider">Kontak & Alamat</p>
                    <div class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <p class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot mt-1 text-slate-500"></i>
                            <span>Jl. Mahar Martanegara, Utama, Kec. Cimahi Selatan, Kota Cimahi, Jawa Barat 40533</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-500"></i>
                            <span>(022) 6654321</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-slate-500"></i>
                            <span>info@smpncimahi.sch.id</span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- COPYRIGHT FOOTER -->
            <div class="pt-8 text-center text-xs text-slate-500">
                <p>© <?= date('Y') ?> SMPN Cimahi. Terakreditasi A • All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- MODAL: KONTAK KAMI -->
    <div id="contact-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 my-8">
            <button onclick="closeContactModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
            
            <div class="space-y-2 mb-6">
                <h3 class="text-2xl font-extrabold text-slate-900">Hubungi SMPN Cimahi</h3>
                <p class="text-slate-500 text-xs sm:text-sm">Silakan kirimkan pertanyaan atau permohonan informasi kepada layanan tata usaha sekolah.</p>
            </div>

            <form onsubmit="submitContactForm(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" required placeholder="Nama Anda" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-blue focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Email / No. HP</label>
                    <input type="text" required placeholder="email@domain.com atau 08xx" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-blue focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pesan / Pertanyaan</label>
                    <textarea required rows="3" placeholder="Tuliskan pesan Anda..." class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-blue focus:outline-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-navy hover:bg-brand-blue text-white font-bold rounded-xl shadow transition-colors">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: NEWS PREVIEW DETAIL -->
    <div id="news-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 my-8">
            <button onclick="closeNewsModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center z-10">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
            
            <div id="news-modal-content" class="space-y-4">
                <!-- Populated via JavaScript -->
            </div>
        </div>
    </div>

    <!-- GLOBAL JAVASCRIPT -->
    <script>
        // MOBILE MENU TOGGLE
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');
            if (menu) {
                if (menu.classList.contains('translate-x-full')) {
                    // Show menu
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        // Small delay to allow display:block to apply before changing opacity
                        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                    }
                    menu.classList.remove('translate-x-full');
                    document.body.classList.add('overflow-hidden');
                } else {
                    // Hide menu
                    if (overlay) {
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                    menu.classList.add('translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                }
            }
        }

        // LOGIN MODAL
        function openLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) modal.classList.add('hidden');
        }

        function togglePasswordVisibility() {
            const passInput = document.getElementById('loginPassword');
            const icon = document.getElementById('togglePasswordIcon');
            if (passInput && icon) {
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                    icon.className = 'fa-solid fa-eye';
                } else {
                    passInput.type = 'password';
                    icon.className = 'fa-solid fa-eye-slash';
                }
            }
        }

        // CONTACT MODAL
        function openContactModal() {
            const modal = document.getElementById('contact-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeContactModal() {
            const modal = document.getElementById('contact-modal');
            if (modal) modal.classList.add('hidden');
        }

        function submitContactForm(e) {
            e.preventDefault();
            alert('Pesan Anda berhasil terkirim. Terima kasih telah menghubungi SMPN Cimahi!');
            closeContactModal();
            e.target.reset();
        }

        // NEWS MODAL
        function openNewsModal(title, category, date, content, imgUrl) {
            const modal = document.getElementById('news-modal');
            const modalContent = document.getElementById('news-modal-content');
            if (modal && modalContent) {
                modalContent.innerHTML = `
                    <div class="relative h-64 sm:h-72 rounded-xl overflow-hidden mb-4 bg-slate-100">
                        <img src="${imgUrl}" class="w-full h-full object-cover" alt="${title}">
                        <span class="absolute top-3 left-3 bg-brand-navy text-white text-xs font-semibold px-3 py-1 rounded">${category}</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">${date}</p>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">${title}</h3>
                    <div class="text-slate-600 text-sm leading-relaxed space-y-2">${content}</div>
                    <div class="pt-4 flex justify-end">
                        <button onclick="closeNewsModal()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">Tutup</button>
                    </div>
                `;
                modal.classList.remove('hidden');
            }
        }

        function closeNewsModal() {
            const modal = document.getElementById('news-modal');
            if (modal) modal.classList.add('hidden');
        }

        // ANIMATED COUNTER ON SCROLL
        const counters = document.querySelectorAll('.counter');
        let counterAnimated = false;

        const animateCounters = () => {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                const duration = 1500;
                const increment = target / (duration / 16);
                let current = 0;

                const updateCount = () => {
                    current += increment;
                    if (current < target) {
                        counter.innerText = Math.ceil(current);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        };

        window.addEventListener('scroll', () => {
            const statsSection = document.querySelector('.counter');
            if (statsSection && !counterAnimated) {
                const position = statsSection.getBoundingClientRect();
                if (position.top < window.innerHeight && position.bottom >= 0) {
                    animateCounters();
                    counterAnimated = true;
                }
            }
        });
    </script>
</body>
</html>