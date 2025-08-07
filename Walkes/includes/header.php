<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walikelas - Dashboard</title>
    <link rel="icon" type="image/png" href="../../image/logo-tutwuri-SD.png" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3498db',
                        secondary: '#2ecc71',
                        accent: '#e74c3c',
                        dark: '#2c3e50',
                        light: '#ecf0f1',
                        sidebarBg: '#f8f9fa',
                        sidebarText: '#34495e',
                        menuHoverLight: '#e0e7eb',
                    }
                }
            }
        }
    </script>
    <style>
        /* Apply Poppins to all elements */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Custom styles for smoother submenu transitions */
        .submenu-transition {
            transition: all 0.3s ease-out;
        }
        .submenu-open {
            max-height: 500px;
            opacity: 1;
            overflow: hidden;
        }
        .submenu-closed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .sidebar-menu-item {
            margin-bottom: 4px;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        .sidebar-visible {
            transform: translateX(0);
        }
        @media (min-width: 1024px) {
            .lg\:sidebar-hidden {
                width: 0;
                overflow: hidden;
                transform: translateX(-100%);
            }
            .lg\:sidebar-visible {
                width: 16rem;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        