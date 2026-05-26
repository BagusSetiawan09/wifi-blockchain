<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otorisasi Jaringan - Akses Aman Web3</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff', 
                        secondary: '#2563eb', 
                        secondaryHover: '#1d4ed8',
                        textDark: '#1e293b',
                        textMuted: '#64748b',
                        bgLight: '#f8fafc',
                    }
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/5.7.2/ethers.umd.min.js"></script>

    <style>
        .loader {
            border-top-color: #2563eb;
            animation: spinner 1.5s linear infinite;
        }
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-bgLight min-h-screen flex items-center justify-center p-4 font-sans antialiased text-textDark relative">

    <div class="bg-primary w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100 z-10">
        
        <div class="px-8 pt-8 pb-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-textDark mb-2">Autentikasi Jaringan</h1>
            <p class="text-textMuted text-sm">Gunakan dompet kripto Anda yang terdaftar untuk mengakses layanan Wi-Fi.</p>
        </div>

        <div class="px-8 py-4 bg-gray-50 border-y border-gray-100">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-textMuted uppercase tracking-wider">IP Address</span>
                <span class="text-sm font-mono text-textDark">{{ $ip }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-textMuted uppercase tracking-wider">MAC Address</span>
                <span class="text-sm font-mono text-textDark" id="mac-address">{{ $mac }}</span>
            </div>
        </div>

        <div class="p-8">
            <div id="alert-box" class="hidden mb-4 p-4 rounded-lg text-sm font-medium text-center"></div>

            <div id="mobile-instruction" class="hidden mb-4 p-5 bg-orange-50 rounded-xl border border-orange-100 text-center shadow-inner">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 mb-3">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="mb-2 text-sm font-bold text-textDark">
                    Buka Aplikasi Dompet Anda
                </p>
                <p class="mb-4 text-xs text-textMuted leading-relaxed">
                    Sistem mendeteksi Anda menggunakan browser bawaan Wi-Fi. Klik tombol di bawah untuk membuka halaman ini langsung di aplikasi MetaMask Anda.
                </p>
                <button onclick="openMetaMask()" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl transition duration-200 text-sm shadow-md flex justify-center items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Buka Langsung di MetaMask</span>
                </button>
            </div>

            <button id="connect-btn" onclick="authenticateWallet()" class="w-full bg-secondary hover:bg-secondaryHover text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                <span>Hubungkan & Verifikasi Akses</span>
            </button>

            <div id="loading-spinner" class="hidden justify-center mt-4">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-8 w-8"></div>
            </div>
        </div>
        
        <div class="pb-6 text-center">
            <p class="text-xs text-textMuted">Powered by Blockchain Security</p>
        </div>
    </div>

    <div id="success-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full mx-4 transform transition-all p-8 text-center border border-gray-100">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Koneksi Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                Identitas dompet Web3 Anda telah diverifikasi. Perangkat ini sekarang memiliki akses penuh ke jaringan internet.
            </p>
            
            <button onclick="window.location.href='https://www.google.com'" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 shadow-lg shadow-green-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2">
                Mulai Berselancar
            </button>
        </div>
    </div>

    <script>
        const macAddress = "{{ $mac }}";
        const ipAddress = "{{ $ip }}";
        const backendApiUrl = "/api/verify-login"; 

        const btn = document.getElementById('connect-btn');
        const spinner = document.getElementById('loading-spinner');
        const alertBox = document.getElementById('alert-box');
        const successModal = document.getElementById('success-modal');
        const mobileInstruction = document.getElementById('mobile-instruction');

        // DETEKSI OTOMATIS: Apakah dibuka di Webview HP atau Browser Biasa?
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.ethereum === 'undefined') {
                btn.classList.add('hidden'); // Sembunyikan tombol verifikasi biasa
                mobileInstruction.classList.remove('hidden'); // Tampilkan tombol Buka MetaMask
            }
        });

        // FUNGSI AJAIB: DEEP LINK KE METAMASK
        function openMetaMask() {
            // Mengambil URL lengkap saat ini (misal: http://192.168.88.254:8000/wifi/login?mac=...&ip=...)
            const rawUrl = window.location.href;
            
            // Menghapus tulisan "http://" atau "https://" karena Deep Link tidak membutuhkannya
            const cleanUrl = rawUrl.replace(/(^\w+:|^)\/\//, '');
            
            // Menggabungkan dengan protokol Deep Link resmi MetaMask
            const metamaskDeepLink = `https://metamask.app.link/dapp/${cleanUrl}`;
            
            // Mengarahkan HP secara paksa untuk membuka aplikasi MetaMask
            window.location.href = metamaskDeepLink;
        }

        function showAlert(message, type) {
            alertBox.classList.remove('hidden', 'bg-red-100', 'text-red-700');
            if (type === 'error') {
                alertBox.classList.add('bg-red-100', 'text-red-700');
                alertBox.innerText = message;
            }
        }

        async function authenticateWallet() {
            alertBox.classList.add('hidden');
            btn.classList.add('hidden');
            spinner.classList.remove('hidden');
            spinner.classList.add('flex');

            try {
                if (typeof window.ethereum === 'undefined') {
                    throw new Error("Dompet kripto tidak terdeteksi. Harap install MetaMask di browser Anda.");
                }

                const provider = new ethers.providers.Web3Provider(window.ethereum);
                await provider.send("eth_requestAccounts", []);
                const signer = provider.getSigner();
                const walletAddress = await signer.getAddress();

                const message = `Login ke Jaringan WiFi\nMAC Address: ${macAddress}\nTimestamp: ${Date.now()}`;
                const signature = await signer.signMessage(message);

                const response = await fetch(backendApiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        wallet_address: walletAddress,
                        signature: signature,
                        message: message,
                        mac_address: macAddress,
                        ip_address: ipAddress
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || "Gagal memverifikasi identitas di server.");
                }

                spinner.classList.add('hidden');
                spinner.classList.remove('flex');
                successModal.classList.remove('hidden');

            } catch (error) {
                console.error(error);
                showAlert(error.message || "Terjadi kesalahan saat memproses permintaan.", 'error');
                spinner.classList.add('hidden');
                spinner.classList.remove('flex');
                btn.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>