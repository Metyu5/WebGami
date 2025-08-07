const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle'); // Mobile toggle
const sidebarBackdrop = document.getElementById('sidebarBackdrop');
const desktopSidebarToggle = document.getElementById('desktopSidebarToggle'); // Desktop toggle
const mainContent = document.getElementById('mainContent');
const currentPageTitle = document.getElementById('currentPageTitle');


function getPhysicalBaseUrl() {
   
    return window.location.origin + '/WebGami/Walkes/';
}

// --- Global notification function (Notyf) ---
function showNotification(type, message) {
    if (typeof Notyf !== 'undefined') {
        const notyf = new Notyf({
            duration: 3000,
            position: { x: 'right', y: 'bottom' }
        });
        if (type === 'success') {
            notyf.success(message);
        } else {
            notyf.error(message);
        }
    } else {
        alert(message);
    }
}

// --- Fungsi Helper untuk setupEditButtons ---
function setupEditButtons() {
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Perlu menyesuaikan ini untuk walikelas juga
            const container = document.querySelector('#student-page-container') || document.querySelector('#walikelas-page-container');
            if (container && typeof Alpine !== 'undefined' && container.__alpine) {
                const alpineComponent = container.__alpine.$data;

                // Identifikasi apakah ini edit siswa atau walikelas
                const pageIdentifier = container.dataset.currentPage; // Asumsi Anda punya data-current-page di kontainer

                if (pageIdentifier === 'student') {
                    const siswaId = btn.getAttribute('data-siswa-id');
                    const nisn = btn.getAttribute('data-nisn');
                    const username = btn.getAttribute('data-username');
                    const kelas = btn.getAttribute('data-kelas');
                    const foto = btn.getAttribute('data-foto');

                    alpineComponent.currentStudent = { siswaId, nisn, username, kelas, foto };
                    alpineComponent.isEditMode = true;
                    alpineComponent.showModal = true;
                    console.log('Edit modal triggered for student:', username);
                } else if (pageIdentifier === 'walikelas') {
                    const walkesId = btn.getAttribute('data-walkes-id'); // Pastikan ini ada di HTML Anda
                    const nip = btn.getAttribute('data-nip');
                    const nama_lengkap = btn.getAttribute('data-nama-lengkap');
                    const kelas_diampu = btn.getAttribute('data-kelas-diampu');
                    const foto = btn.getAttribute('data-foto');

                    alpineComponent.currentWalikelas = { walkesId, nip, nama_lengkap, kelas_diampu, foto };
                    alpineComponent.isEditMode = true;
                    alpineComponent.showModal = true;
                    console.log('Edit modal triggered for walikelas:', nama_lengkap);
                }
            } else {
                console.warn('Alpine.js component or container not found for edit button.');
            }
        });
    });
}


// --- Mobile Sidebar Toggle Logic ---
sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    sidebarBackdrop.classList.toggle('hidden');
});

sidebarBackdrop.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarBackdrop.classList.add('hidden'); // Perbaiki typo di sini: sidebarBackrop -> sidebarBackdrop
});

// --- Desktop Sidebar Toggle Logic ---
let isSidebarOpen = true;
desktopSidebarToggle.addEventListener('click', () => {
    if (isSidebarOpen) {
        sidebar.classList.remove('lg:translate-x-0', 'lg:w-64');
        sidebar.classList.add('lg:sidebar-hidden', 'lg:w-0');
        isSidebarOpen = false;
    } else {
        sidebar.classList.remove('lg:sidebar-hidden', 'lg:w-0');
        sidebar.classList.add('lg:translate-x-0', 'lg:w-64');
        isSidebarOpen = true;
    }
});

// Set active menu
function setActiveMenuItem(clickedLink) {
    document.querySelectorAll('.spa-nav-link').forEach(link => {
        link.classList.remove('bg-primary', 'text-white', 'shadow-inner');
        link.classList.add('hover:bg-menuHoverLight', 'text-sidebarText', 'hover:text-dark');
    });

    clickedLink.classList.remove('hover:bg-menuHoverLight', 'text-sidebarText', 'hover:text-dark');
    clickedLink.classList.add('bg-primary', 'text-white', 'shadow-inner');

    const parentSubmenu = clickedLink.closest('#gamificationSubmenu, #reportsSubmenu');
    if (parentSubmenu) {
        parentSubmenu.classList.remove('submenu-closed');
        parentSubmenu.classList.add('submenu-open');
        
        const parentMenuId = parentSubmenu.id.replace('Submenu', 'Menu');
        const parentMenu = document.getElementById(parentMenuId);
        if (parentMenu) {
            const chevronIcon = parentMenu.querySelector('i.fa-chevron-down');
            if (chevronIcon) {
                chevronIcon.classList.add('rotate-180');
            }
        }
    }
}

// --- SPA/AJAX Content Loading Logic ---
async function loadContent(pageUrl, menuText = '', params = '') {

    if (!mainContent) {
        console.error("ERROR: mainContent element not found!");
        return;
    }

    mainContent.innerHTML = '<div class="flex justify-center items-center h-48"><i class="fas fa-spinner fa-spin text-4xl text-primary"></i></div>';
    if (currentPageTitle && menuText) currentPageTitle.textContent = menuText;

    try {
        let fullUrl;
        const physicalBase = getPhysicalBaseUrl();

        // Perbaikan 1: Handle parameter yang sudah ada di pageUrl
        let finalPath = pageUrl;
        let queryParams = '';
        
        // Pisahkan path dari query parameters jika ada
        if (finalPath.includes('&')) {
            const parts = finalPath.split('&');
            finalPath = parts[0]; // Ambil bagian pertama sebagai path
            queryParams = parts.slice(1).join('&'); // Sisanya sebagai query params
        }
        
        // Bersihkan path
        if (finalPath.startsWith('../')) {
            finalPath = finalPath.replace('../', '');
        }
        
        // Pastikan ada 'content/' di path jika belum ada
        if (!finalPath.startsWith('content/')) {
            finalPath = 'content/' + finalPath;
        }
        
        // Pastikan ada .php extension
        if (!finalPath.endsWith('.php')) {
            finalPath += '.php';
        }

        // Perbaikan 2: Gabungkan semua parameter dengan benar
        const allParams = new URLSearchParams();
        
        // Tambahkan parameter dari params
        if (params) {
            const paramsPairs = params.split('&');
            paramsPairs.forEach(pair => {
                const [key, value] = pair.split('=');
                if (key && value) {
                    allParams.set(key, decodeURIComponent(value));
                }
            });
        }
        
        // Tambahkan parameter dari queryParams
        if (queryParams) {
            const queryPairs = queryParams.split('&');
            queryPairs.forEach(pair => {
                const [key, value] = pair.split('=');
                if (key && value) {
                    allParams.set(key, decodeURIComponent(value));
                }
            });
        }

        // Bangun URL final
        fullUrl = `${physicalBase}${finalPath}`;
        if (allParams.toString()) {
            fullUrl += `?${allParams.toString()}`;
        }

        console.log("DEBUG: Attempting to fetch URL:", fullUrl);

        const response = await fetch(fullUrl);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Gagal memuat konten: ${response.status} ${response.statusText}. URL: ${fullUrl}. Server Response: ${errorText}`);
        }
        const html = await response.text();
        mainContent.innerHTML = html;

        // Perbaikan 3: Improved script handling
        // Hapus scripts yang sudah ada di head agar tidak duplikat
        document.querySelectorAll('head script[data-loaded-by-spa]').forEach(script => script.remove());

        const scripts = mainContent.querySelectorAll('script');
        let scriptsToLoad = scripts.length;
        let scriptsLoaded = 0;

        const checkAllScriptsLoaded = () => {
            scriptsLoaded++;
            if (scriptsLoaded >= scriptsToLoad) {
                // Semua script sudah dimuat, inisialisasi Alpine
                if (typeof Alpine !== 'undefined') {
                    setTimeout(() => {
                        Alpine.initTree(mainContent);
                        console.log("DEBUG: Alpine.js re-initialized for mainContent after all scripts loaded.");
                    }, 100);
                }
            }
        };

        if (scriptsToLoad === 0) {
            // Tidak ada script, langsung inisialisasi Alpine
            if (typeof Alpine !== 'undefined') {
                setTimeout(() => {
                    Alpine.initTree(mainContent);
                    console.log("DEBUG: Alpine.js re-initialized for mainContent (no scripts).");
                }, 50);
            }
        } else {
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                newScript.setAttribute('data-loaded-by-spa', 'true');
                
                if (script.src) {
                    newScript.src = script.src;
                    newScript.onload = checkAllScriptsLoaded;
                    newScript.onerror = checkAllScriptsLoaded; // Handle error juga
                } else {
                    newScript.innerHTML = script.innerHTML;
                    try {
                        eval(newScript.innerHTML);
                        checkAllScriptsLoaded();
                    } catch (error) {
                        console.error("Error evaluating inline script from loaded content:", error);
                        checkAllScriptsLoaded();
                    }
                }
                document.head.appendChild(newScript);
            });
        }
        
        // Perbaikan 4: Setup edit buttons dengan delay
        setTimeout(() => {
            if (typeof setupEditButtons === 'function') {
                setupEditButtons();
            }
        }, 150);

        // Perbaikan 5: Improved browser history handling
        const browserPageParam = finalPath.replace('content/', 'pages/').replace('.php', '');
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.set('page', browserPageParam);
        
        // Tambahkan parameter lain ke URL browser juga
        if (allParams.toString()) {
            allParams.forEach((value, key) => {
                if (key !== 'page') { // jangan duplikat page parameter
                    currentParams.set(key, value);
                }
            });
        }
        
        history.pushState(null, '', `?${currentParams.toString()}`);

        // Set active menu item
        const activeLink = document.querySelector(`.spa-nav-link[data-page="${pageUrl}"], .spa-nav-link[data-page="${finalPath}"]`);
        if (activeLink) {
            setActiveMenuItem(activeLink);
        }

    } catch (error) {
        console.error("Load content error:", error);
        mainContent.innerHTML = `<div class="text-center text-red-500 py-10">${error.message}</div>`;
        if (typeof showNotification === 'function') {
            showNotification('error', error.message);
        }
    }
}
// --- Form Submission ---
async function handleFormSubmission(form) {
    const formData = new FormData(form);
    try {
        const response = await fetch(form.action, { method: 'POST', body: formData });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}. Server Response: ${errorText}`);
        }

        const contentType = response.headers.get('content-type');
        const isJson = contentType && contentType.includes('application/json');

        let result;
        if (isJson) {
            result = await response.json();
            showNotification(result.status, result.message);
        } else {
            showNotification('success', 'Data berhasil diproses');
        }

        if (typeof Alpine !== 'undefined') {
            const modalContainer = document.getElementById('walikelas-page-container') || document.getElementById('student-page-container');
            if (modalContainer?.__alpine) modalContainer.__alpine.$data.showModal = false;
        }

        setTimeout(() => {
            const currentPageContainer = mainContent.querySelector('[data-current-page]');
            let pageToReload = '../content/walikelas_content.php'; // Default fallback (dalam format data-page)

            if (currentPageContainer) {
                const pageIdentifier = currentPageContainer.dataset.currentPage;
                if (pageIdentifier === 'student') {
                    pageToReload = '../content/siswa.php';
                } else if (pageIdentifier === 'walikelas') {
                    // *** PERBAIKAN: Pastikan ini selalu menunjuk ke path yang benar dari root admin ***
                    pageToReload = '../content/wali_kelas.php';
                }
            }

            const searchInput = document.getElementById('searchWalikelas') || document.getElementById('searchStudent');
            const search = searchInput ? searchInput.value : '';
            // Mencari nomor halaman aktif dari pagination yang baru dimuat
            const currentPage = document.querySelector('.pagination-btn.active')?.dataset.pageNumber || 1;

            loadContent(pageToReload, currentPageTitle.textContent, `page=${currentPage}&search=${encodeURIComponent(search)}`);
        }, 500);

    } catch (error) {
        console.error("Form submission error:", error);
        showNotification('error', 'Terjadi kesalahan saat memproses data.');
    }
}


// --- Delete Handler ---
async function handleDeleteStudent(idToDelete) {
    try {
        const formData = new FormData();
        formData.append('action', 'hapus');

        const currentPageContainer = mainContent.querySelector('[data-current-page]');
        let targetUrlForAjax = getPhysicalBaseUrl() + 'content/walikelas_content.php'; // Default target AJAX
        let pageToReloadAfterDelete = '../content/walikelas_content.php'; // Default pageUrl untuk loadContent

        if (currentPageContainer) {
            const pageIdentifier = currentPageContainer.dataset.currentPage;
            if (pageIdentifier === 'student') {
                targetUrlForAjax = getPhysicalBaseUrl() + 'content/siswa.php';
                pageToReloadAfterDelete = '../content/siswa.php';
                formData.append('siswaId', idToDelete);
            } else if (pageIdentifier === 'walikelas') {
                targetUrlForAjax = getPhysicalBaseUrl() + 'content/wali_kelas.php';
                // *** PERBAIKAN: Pastikan ini selalu menunjuk ke path yang benar dari root admin ***
                pageToReloadAfterDelete = '../content/wali_kelas.php';
                formData.append('walkesId', idToDelete);
            }
        }
        
        console.log("DEBUG: Deleting from URL:", targetUrlForAjax);
        const response = await fetch(targetUrlForAjax, { method: 'POST', body: formData });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Gagal menghapus data: ${response.status}. Server Response: ${errorText}`);
        }

        showNotification('success', 'Data berhasil dihapus');
        setTimeout(() => {
            const searchInput = document.getElementById('searchWalikelas') || document.getElementById('searchStudent');
            const search = searchInput ? searchInput.value : '';
            const currentPage = document.querySelector('.pagination-btn.active')?.dataset.pageNumber || 1;
            
            loadContent(pageToReloadAfterDelete, currentPageTitle.textContent, `page=${currentPage}&search=${encodeURIComponent(search)}`);
        }, 500);
    } catch (error) {
        console.error("Delete error:", error);
        showNotification('error', 'Terjadi kesalahan saat menghapus data.');
    }
}

// --- Event Listeners ---
document.addEventListener('click', e => {
    const targetLink = e.target.closest('.spa-nav-link');
    if (targetLink?.hasAttribute('data-page')) {
        e.preventDefault();
        const pageUrl = targetLink.dataset.page; // Contoh: '../content/student.php'
        const menuText = targetLink.textContent.trim();
        console.log("DEBUG: Link Sidebar clicked. pageUrl:", pageUrl, "menuText:", menuText);
        
        loadContent(pageUrl, menuText);
        
        if (window.innerWidth < 1024) { 
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
        }
    }
    
    if (e.target.closest('.delete-btn')) {
        e.preventDefault();
        // Perbaiki typo: siswaiId -> siswaId
        const idToDelete = e.target.closest('.delete-btn').dataset.siswaId || e.target.closest('.delete-btn').dataset.walkesId; 
        
        if (!idToDelete) {
            console.error("Error: ID for deletion not found on delete button.");
            showNotification('error', 'ID data tidak ditemukan untuk dihapus.');
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    handleDeleteStudent(idToDelete); 
                }
            });
        } else if (confirm('Apakah Anda yakin?')) {
            handleDeleteStudent(idToDelete); 
        }
    }
});

document.addEventListener('submit', e => {
    if (e.target.id === 'studentForm' || e.target.id === 'waliKelasForm') {
        e.preventDefault();
        handleFormSubmission(e.target);
    }
    if (e.target.id === 'searchForm') {
        e.preventDefault();
        const searchInput = document.getElementById('searchWalikelas') || document.getElementById('searchStudent');
        const search = searchInput ? searchInput.value : '';
        
        const currentPageContainer = mainContent.querySelector('[data-current-page]');
        let pageToLoad = '../content/walikelas_content.php'; // Default

        if (currentPageContainer) {
            const pageIdentifier = currentPageContainer.dataset.currentPage;
            if (pageIdentifier === 'student') {
                pageToLoad = '../content/siswa.php';
            } else if (pageIdentifier === 'walikelas') {
                // *** PERBAIKAN: Pastikan ini selalu menunjuk ke path yang benar dari root admin ***
                pageToLoad = '../content/wali_kelas.php';
            }
        }
        
        loadContent(pageToLoad, currentPageTitle.textContent, `page=1&search=${encodeURIComponent(search)}`);
    }
});


// --- Inisialisasi Submenu ---
function initializeSubmenus() {
    document.querySelectorAll('.sidebar-menu-item').forEach(menuItem => {
        const menuToggle = menuItem.querySelector('div');
        const submenu = menuItem.querySelector('div[id$="Submenu"]');
        if (menuToggle && submenu) {
            menuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                const isOpen = submenu.classList.contains('submenu-open');
                const chevronIcon = menuToggle.querySelector('i.fa-chevron-down');

                document.querySelectorAll('div[id$="Submenu"]').forEach(otherSubmenu => {
                    if (otherSubmenu !== submenu && otherSubmenu.classList.contains('submenu-open')) {
                        otherSubmenu.classList.remove('submenu-open');
                        otherSubmenu.classList.add('submenu-closed');
                        const otherMenuToggle = otherSubmenu.previousElementSibling;
                        const otherChevron = otherMenuToggle?.querySelector('i.fa-chevron-down');
                        if (otherChevron) otherChevron.classList.remove('rotate-180');
                    }
                });

                if (isOpen) {
                    submenu.classList.remove('submenu-open');
                    submenu.classList.add('submenu-closed');
                    if (chevronIcon) chevronIcon.classList.remove('rotate-180');
                } else {
                    submenu.classList.remove('submenu-closed');
                    submenu.classList.add('submenu-open');
                    if (chevronIcon) chevronIcon.classList.add('rotate-180');
                }
            });
        }
    });
}


// --- Initial load & Event binding on DOMContentLoaded ---
document.addEventListener('DOMContentLoaded', () => {
    initializeSubmenus();

    const urlParams = new URLSearchParams(window.location.search);
    let pageParamFromUrl = urlParams.get('page') || 'walikelas_content'; // Ubah default menjadi 'dashboard_content' karena itu nama filenya

    let initialPageUrlForContent = '../content/walikelas_content.php'; // Default format data-page

    // KARENA URL BROWSER ADALAH admin/pages/dashboard.php, maka pageParamFromUrl akan menjadi 'dashboard_content'
    // Jadi, kita harus mengubah ini menjadi 'pages/dashboard_content' saat pertama kali masuk
    // Atau jika pageParamFromUrl adalah 'pages/walikelas', kita akan mengubahnya menjadi '../content/wali_kelas.php'
    if (pageParamFromUrl.startsWith('pages/')) {
        const contentName = pageParamFromUrl.replace('pages/', ''); // Ambil hanya nama file: 'dashboard_content' atau 'walikelas'
        initialPageUrlForContent = `../content/${contentName}.php`; // Ubah ke format ../content/file.php
    } else {
        // Ini akan menangani kasus ketika pageParamFromUrl tidak memiliki 'pages/', misalnya hanya 'dashboard_content'
        // Yang terjadi saat pertama kali login tanpa parameter 'page' di URL
        initialPageUrlForContent = `../content/${pageParamFromUrl}.php`;
    }
    
    // Temukan link menu yang sesuai dengan halaman saat ini untuk mendapatkan text dan mengaktifkannya
    // Gunakan initialPageUrlForContent (format '../content/file.php') untuk mencari link
    const activeLinkOnLoad = document.querySelector(`.spa-nav-link[data-page="${initialPageUrlForContent}"]`);
    let menuTextForLoad = 'Dashboard'; // Default text

    if (activeLinkOnLoad) {
        menuTextForLoad = activeLinkOnLoad.textContent.trim();
        setActiveMenuItem(activeLinkOnLoad);
    } else {
        // Fallback ke dashboard link default jika parameter page tidak cocok
        const dashboardLink = document.querySelector('a[data-page="../content/walikelas_content.php"]');
        if (dashboardLink) {
            setActiveMenuItem(dashboardLink);
            menuTextForLoad = dashboardLink.textContent.trim();
            initialPageUrlForContent = dashboardLink.dataset.page; // Pastikan URL sesuai
        }
    }
    
    // Muat konten awal
    loadContent(initialPageUrlForContent, menuTextForLoad);
});