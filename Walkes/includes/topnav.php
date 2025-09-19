<div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white shadow-sm z-10">
        <div class="flex items-center justify-between px-6 py-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-600 focus:outline-none hover:text-dark transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <button id="desktopSidebarToggle" class="hidden lg:block text-gray-600 focus:outline-none hover:text-dark transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="hidden lg:flex items-center space-x-2 text-sm">
                <a href="../index.php" class="text-primary hover:underline font-medium">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600 font-medium" id="current-page-title">Dashboard</span> </div>

            <div class="flex items-center space-x-4">
                <!-- <button class="relative text-gray-600 hover:text-dark transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">5</span>
                </button> -->
                <div class="flex items-center space-x-2 cursor-pointer group">
                    <img src="../assets/images/logo-tutwuri-SD.png" alt="User" class="w-8 h-8 rounded-full border-2 border-red group-hover:border-secondary transition-colors">
                    <span class="hidden md:inline text-sm font-medium text-dark group-hover:text-primary transition-colors">Walikelas</span>
                </div>
            </div>
        </div>
    </header>