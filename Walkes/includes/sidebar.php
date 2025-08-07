<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 h-screen bg-sidebarBg text-sidebarText transition-all duration-300 ease-in-out transform -translate-x-full lg:translate-x-0 shadow-2xl lg:shadow-md rounded-r-xl lg:rounded-none">
    <div class="flex flex-col h-full">
        <div class="p-5 text-center border-b border-gray-200">
            <img src="../assets/images/logo-tutwuri-SD.png" alt="School Logo" class="w-16 h-16 rounded-full border-4 border-yellow-600/70 mx-auto transform hover:scale-105 transition-transform duration-200">
            <h3 class="mt-3 text-2xl font-extrabold text-primary tracking-wide">SDN 07 PAGUAT</h3>
            <p class="text-sm font-semibold text-gray-600 mt-1">WALI KELAS</p>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4">
            <div class="mb-6">
                <h4 class="px-4 py-2 text-xs uppercase text-gray-500 tracking-widest font-semibold">Main</h4>
                <a href="#" data-page="../content/walikelas_content.php" class="flex items-center px-4 py-3 text-sm rounded-xl spa-nav-link active-link">
                    <i class="fas fa-home mr-3 text-lg "></i> Dashboard
                </a>
            </div>

            <div class="mb-6">
                <h4 class="px-4 py-2 text-xs uppercase text-gray-500 tracking-widest font-semibold">Student Management</h4>
                <a href="#" data-page="siswa" class="flex items-center px-4 py-3 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                    <i class="fas fa-users mr-3 text-lg"></i> Data Siswa
                </a>
            </div>

            <div class="mb-6">
                <h4 class="px-4 py-2 text-xs uppercase text-gray-500 tracking-widest font-semibold">Gamification</h4>
                <div id="gamificationMenu" class="cursor-pointer sidebar-menu-item">
                    <div class="flex items-center justify-between px-4 py-3 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-trophy mr-3 text-lg"></i> Soal Game
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-220"></i>
                    </div>
                    <div id="gamificationSubmenu" class="pl-6 mt-1 submenu-closed submenu-transition">
                        <a href="#" data-page="../content/matematika.php" class="block px-4 py-2 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                            <i class="fas fa-medal mr-2 text-base"></i> Matematika
                        </a>
                        <!-- <a href="#" data-page="../content/points.php" class="block px-4 py-2 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                            <i class="fas fa-star mr-2 text-base"></i> Points System
                        </a> -->
                        <a href="#" data-page="../content/leaderboard.php" class="block px-4 py-2 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                            <i class="fas fa-chart-line mr-2 text-base"></i> Leaderboard
                        </a>
                        <!-- <a href="#" data-page="../content/challenges.php" class="block px-4 py-2 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                            <i class="fas fa-flag mr-2 text-base"></i> Challenges
                        </a> -->
                    </div>
                </div>
            </div>


            <div class="mb-6">
                <div id="reportsMenu" class="cursor-pointer sidebar-menu-item">
                    <div class="flex items-center justify-between px-4 py-3 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie mr-3 text-lg"></i> Laporan
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </div>
                    <div id="reportsSubmenu" class="pl-6 mt-1 submenu-closed submenu-transition">
                        <a href="#" data-page="../content/game_history.php" class="block px-4 py-2 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                            <i class="fas fa-user-graduate mr-2 text-base"></i> Riwayat Game
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="px-4 py-2 text-xs uppercase text-gray-500 tracking-widest font-semibold">Settings</h4>
                <a href="#" data-page="../content/profile.php" class="flex items-center px-4 py-3 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200 spa-nav-link">
                    <i class="fas fa-user-shield mr-3 text-lg"></i> Profile Walikelas
                </a>
            </div>
        </nav>
        <div class="p-4 border-t border-gray-200">
            <a href="../../logout.php" class="flex items-center px-4 py-3 text-sm rounded-xl hover:bg-menuHoverLight text-sidebarText hover:text-dark transition-all duration-200">
                <i class="fas fa-sign-out-alt mr-3 text-lg"></i> Logout
            </a>
        </div>
    </div>
</aside>