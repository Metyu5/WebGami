<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racing Math Adventure - Simplified Edition</title>
    <meta name="theme-color" content="#FF6B35">
    <meta name="description" content="Game Matematika Racing yang seru dan sederhana">
    <link href="../../../src/output.css" rel="stylesheet" onerror="console.log('Local CSS failed')">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Notyf -->
    <link rel="stylesheet" href="../../../assets/vendor/notyf.min.css">
    <script defer src="../../../assets/vendor/notyf.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="../../../assets/vendor/alpine.min.js"></script>
    <!-- Phaser -->
    <script defer src="../../../assets/vendor/phaser.min.js"></script>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Fredoka', sans-serif; 
            overflow: hidden;
            touch-action: manipulation;
        }
        
        .racing-font { font-family: 'Orbitron', monospace; }

        .racing-gradient {
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 25%, #FFD600 50%, #7CB342 75%, #004D8F 100%);
            background-size: 400% 400%;
            animation: racingGradient 3s ease infinite;
        }
        
        @keyframes racingGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .car-bounce {
            animation: carBounce 2s ease-in-out infinite;
        }
        
        @keyframes carBounce {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }
        
        .fade-in-race {
            animation: fadeInRace 0.5s ease-in;
        }
        
        @keyframes fadeInRace {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .zoom-in {
            animation: zoomIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        @keyframes zoomIn {
            from { transform: scale(0.5) rotate(-5deg); opacity: 0; }
            to { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        
        .race-button {
            background: linear-gradient(45deg, #FF6B35, #F7931E);
            border: 3px solid #fff;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            transition: all 0.3s ease;
        }
        
        .race-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.6);
        }
        
        .track-bg {
            background: 
                linear-gradient(90deg, #333 0%, #555 50%, #333 100%),
                repeating-linear-gradient(
                    0deg,
                    transparent 0px,
                    transparent 48px,
                    #FFD600 48px,
                    #FFD600 52px,
                    transparent 52px,
                    transparent 100px
                );
        }
        
        .timer-critical {
            animation: timerPulse 0.5s ease-in-out infinite;
        }
        
        @keyframes timerPulse {
            0%, 100% { transform: scale(1); background-color: #FF0000; }
            50% { transform: scale(1.05); background-color: #FF6666; }
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FF6B35;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .race-button {
                padding: 12px 24px !important;
                font-size: 16px !important;
            }
            
            .car-bounce .w-40 {
                width: 120px !important;
                height: 120px !important;
            }
            
            .text-6xl {
                font-size: 2.5rem !important;
            }
            
            .text-7xl {
                font-size: 3rem !important;
            }
            
            .max-w-md {
                max-width: 90% !important;
            }
            
            .p-8 {
                padding: 1.5rem !important;
            }
            
            .space-y-4 > * + * {
                margin-top: 1rem !important;
            }
        }

        @media (max-width: 480px) {
            .text-6xl, .text-7xl {
                font-size: 2rem !important;
            }
            
            .text-2xl {
                font-size: 1.25rem !important;
            }
            
            .py-5 {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            .px-8 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

        /* Game UI Responsive */
        #phaser-game {
            width: 100%;
            height: calc(100vh - 120px);
            min-height: 300px;
        }

        @media (max-width: 768px) {
            #phaser-game {
                height: calc(100vh - 140px);
            }
        }
        .icon-emoji {
            transform: translateX(2%);
        }
    </style>
</head>

<body class="racing-gradient min-h-screen">
    <div id="loading" class="fixed inset-0 bg-gradient-to-br from-gray-900 via-red-900 to-orange-900 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="car-bounce mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto shadow-2xl relative">
                    <span class="text-6xl">🏎️</span>
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                        <div class="road-lines w-16 h-2 bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 racing-font">RACING MATH</h1>
            <p class="text-xl text-orange-200 mb-6">SIMPLIFIED EDITION</p>
            <div class="flex justify-center space-x-2">
                <div class="w-3 h-3 bg-orange-400 rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-red-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-yellow-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <div id="question-loading" class="loading-overlay" style="display: none;">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="text-white mt-4 racing-font">Loading Question...</p>
        </div>
    </div>

    <div id="app" x-data="simplifiedRacingGame()" x-show="!loading" class="min-h-screen">
        <div x-show="screen === 'menu'" class="min-h-screen flex flex-col items-center justify-center p-4 fade-in-race">
            <div class="text-center max-w-md w-full">
               <div class="car-bounce mb-8">
                    <div class="w-40 h-40 bg-gradient-to-br from-red-500 via-orange-500 to-yellow-500 rounded-full flex items-center justify-center mx-auto shadow-2xl relative border-4 border-white">
                        <span class="text-8xl">🏎️</span>
                        <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2">
                            <div class="road-lines w-24 h-3 bg-gray-800 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <h1 class="text-6xl md:text-7xl font-black text-white mb-2 drop-shadow-lg racing-font">RACING MATH</h1>
                <p class="text-2xl text-white/90 mb-8 drop-shadow racing-font">SIMPLIFIED EDITION</p>
                
                <div class="space-y-4">
                    <button @click="startRace()" 
                            class="w-full race-button text-white font-bold py-5 px-8 rounded-2xl text-2xl racing-font">
                        <span class="mr-3">🏁</span>
                        <span>START RACE</span>
                    </button>
                    
                    <button @click="showSettings()" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-5 px-8 rounded-2xl text-lg shadow-lg transform transition hover:scale-105 racing-font">
                        <span class="mr-2">⚙️</span>
                        <span>Settings</span>
                    </button>
                    
                    <button @click="showHistory()" 
                            class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-5 px-8 rounded-2xl text-lg shadow-lg transform transition hover:scale-105 racing-font">
                        <span class="mr-2">📊</span>
                        <span>History</span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="screen === 'settings'" class="min-h-screen flex flex-col items-center justify-center p-4 fade-in-race">
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 max-w-md w-full zoom-in border-4 border-orange-400">
                <h2 class="text-4xl font-bold text-gray-800 mb-6 text-center racing-font">SETTINGS</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl">
                        <span class="text-lg font-medium text-gray-700 racing-font">Engine Sound</span>
                        <button @click="toggleSound()" 
                                class="w-16 h-8 rounded-full transition-colors duration-200"
                                :class="soundEnabled ? 'bg-green-500' : 'bg-gray-300'">
                            <div class="w-6 h-6 bg-white rounded-full shadow transform transition-transform duration-200"
                                 :class="soundEnabled ? 'translate-x-8' : 'translate-x-1'"></div>
                        </button>
                    </div>
                    
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <label class="block text-lg font-medium text-gray-700 mb-3 racing-font">Difficulty Level</label>
                        <select x-model="difficulty" 
                                class="w-full p-3 border-2 border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent racing-font">
                            <option value="easy">🟢 EASY</option>
                            <option value="medium">🟡 MEDIUM</option>
                            <option value="hard">🔴 HARD</option>
                        </select>
                    </div>
                    
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <h3 class="text-lg font-bold text-gray-700 mb-2 racing-font">🎮 GAME RULES</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>⏱️ 10 detik per pertanyaan</li>
                            <li>💀 Game over jika waktu habis</li>
                            <li>🚗 Hindari tabrakan dengan mobil lain</li>
                            <li>🏁 Selesaikan 10 pertanyaan untuk menang</li>
                            <li>📊 Butuh 70% akurasi untuk menang</li>
                        </ul>
                    </div>
                </div>
                
                <button @click="screen = 'menu'" 
                        class="w-full mt-8 bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl transition-colors racing-font">
                    <span class="mr-2">←</span>
                    <span>BACK TO MENU</span>
                </button>
            </div>
        </div>
       <div x-show="screen === 'history'" class="min-h-screen flex flex-col items-center p-4 fade-in-history">
         <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 max-w-2xl w-full zoom-in border-4 border-orange-400">
        <h2 class="text-4xl font-bold text-gray-800 mb-6 text-center racing-font">🏁 RIWAYAT BALAPAN</h2>
        
        <div x-show="historyLoading" class="flex justify-center items-center h-48">
            <p class="text-lg text-gray-600 racing-font">Loading...</p>
        </div>

        <div x-show="!historyLoading">
            <template x-if="history.length > 0">
                <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Tanggal</th>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Skor</th>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Benar</th>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Total</th>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Tingkat</th>
                                <th scope="col" class="px-4 py-2 text-left text-sm font-bold text-gray-700 racing-font">Menang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="item in history" :key="item.id">
                                <tr class="hover:bg-gray-50">
                                    <td x-text="item.tanggal" class="whitespace-nowrap px-4 py-2 text-sm text-gray-500"></td>
                                    <td x-text="item.skor" class="whitespace-nowrap px-4 py-2 text-sm font-medium text-gray-900"></td>
                                    <td x-text="item.jawaban_benar" class="whitespace-nowrap px-4 py-2 text-sm text-gray-500"></td>
                                    <td x-text="item.total_pertanyaan" class="whitespace-nowrap px-4 py-2 text-sm text-gray-500"></td>
                                    <td x-text="item.tingkat_kesulitan" class="whitespace-nowrap px-4 py-2 text-sm text-gray-500"></td>
                                    <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-500">
                                        <span x-show="item.menang === 'Menang'" class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-600">
                                            Menang
                                        </span>
                                        <span x-show="item.menang === 'Kalah'" class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600">
                                            Kalah
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <template x-if="history.length === 0">
                <div class="text-center p-8 text-gray-500">
                    <p class="text-2xl font-bold mb-2">Belum ada riwayat permainan.</p>
                    <p>Mulai balapan pertama Anda sekarang!</p>
                </div>
            </template>
        </div>
        
        <button @click="screen = 'menu'" class="w-full mt-8 bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl transition-colors racing-font">
            <span class="mr-2">←</span>
            <span>KEMBALI KE MENU</span>
        </button>
         </div>
</div>

        <div x-show="screen === 'race'" class="min-h-screen track-bg relative">
            <div class="bg-gray-900/90 backdrop-blur-sm shadow-lg p-2 md:p-4 border-b-4 border-yellow-400">
                <div class="flex justify-between items-center max-w-4xl mx-auto text-xs md:text-base">
                    <button @click="pauseRace()" 
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 md:py-2 md:px-4 rounded-lg racing-font text-xs md:text-sm">
                        ⏸️ <span class="hidden sm:inline">PAUSE</span>
                    </button>
                    
                    <div class="text-center">
                        <div class="text-xs text-yellow-400 racing-font">QUESTION</div>
                        <div class="text-sm md:text-xl font-bold text-white racing-font" x-text="`${currentQuestionNumber}/${questionCount}`"></div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-xs text-green-400 racing-font">SCORE</div>
                        <div class="text-sm md:text-xl font-bold text-green-400 racing-font" x-text="(score || 0).toLocaleString()"></div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-xs text-blue-400 racing-font">TIMER</div>
                        <div class="text-sm md:text-xl font-bold racing-font" 
                             :class="timeLeft <= 3 ? 'text-red-400' : timeLeft <= 5 ? 'text-yellow-400' : 'text-green-400'"
                             x-text="timeLeft + 's'"></div>
                    </div>
                </div>
            </div>
            
            <div class="w-full h-2 bg-gray-800">
                <div class="h-full transition-all duration-1000 ease-linear"
                     :class="timeLeft <= 3 ? 'bg-red-500 timer-critical' : timeLeft <= 5 ? 'bg-yellow-500' : 'bg-green-500'"
                     :style="`width: ${(timeLeft / 10) * 100}%`"></div>
            </div>
            
            <div id="phaser-game" class="game-container w-full"></div>
        </div>

        <div x-show="showPauseModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 rounded-3xl shadow-2xl p-6 md:p-8 max-w-sm w-full zoom-in border-4 border-orange-400">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-6 text-center racing-font">🛑 PIT STOP</h3>
                
                <div class="space-y-4">
                    <button @click="resumeRace()" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 md:py-4 px-4 md:px-6 rounded-xl racing-font text-sm md:text-base">
                        🏁 RESUME RACE
                    </button>
                    
                    <button @click="restartRace()" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 md:py-4 px-4 md:px-6 rounded-xl racing-font text-sm md:text-base">
                        🔄 RESTART RACE
                    </button>
                    
                    <button @click="exitRace()" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 md:py-4 px-4 md:px-6 rounded-xl racing-font text-sm md:text-base">
                        🏠 EXIT TO MENU
                    </button>
                </div>
            </div>
        </div>

        <div x-show="showGameOverModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
            <div class="bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900 rounded-3xl shadow-2xl p-6 md:p-8 max-w-sm w-full zoom-in border-4"
                 :class="raceWon ? 'border-yellow-400' : 'border-red-500'">
                <div class="text-center">
                    <div class="w-20 h-20 md:w-24 md:h-24 mx-auto mb-6 bg-gradient-to-br rounded-full flex items-center justify-center"
                         :class="raceWon ? 'from-yellow-400 to-orange-500' : 'from-red-500 to-red-700'">
                        <span class="text-4xl md:text-5xl" x-text="raceWon ? '🏆' : '💥'"></span>
                    </div>
                    
                    <h3 class="text-3xl md:text-4xl font-bold text-white mb-4 racing-font" 
                        x-text="raceWon ? '🏁 VICTORY!' : gameOverReason || '💀 GAME OVER!'"></h3>
                    
                    <div class="space-y-5 mb-6">
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-xs md:text-sm text-gray-300 racing-font">FINAL SCORE</div>
                            <div class="text-xl md:text-2xl font-bold text-yellow-400 racing-font" x-text="(score || 0).toLocaleString()"></div>
                        </div>
                        
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-xs md:text-sm text-gray-300 racing-font">AKURASI</div>
                            <div class="text-xl md:text-2xl font-bold text-green-400 racing-font" 
                                 x-text="totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) + '%' : '0%'"></div>
                        </div>
                        
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-xs md:text-sm text-gray-300 racing-font">JAWABAN SELESAI</div>
                            <div class="text-xl md:text-2xl font-bold text-blue-400 racing-font" x-text="`${Math.max(0, currentQuestionNumber - 1)}/${questionCount}`"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-5">
                        <button @click="restartRace()" 
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-5 md:py-4 px-4 md:px-6 rounded-xl racing-font transform transition hover:scale-105 text-sm md:text-base">
                            🔄 MULAI LAGI
                        </button>
                         <button @click="saveResults()" 
                            :disabled="isSaving"
                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-5 md:py-4 px-4 md:px-6 rounded-xl racing-font transform transition hover:scale-105 text-sm md:text-base">
                        💾 SIMPAN DATA <span x-text="isSaving ? 'Menyimpan...' : 'SIMPAN'"></span>
                        </button>
                        
                        <button @click="exitToMenu()" 
                                class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold py-5 md:py-4 px-4 md:px-6 rounded-xl racing-font transform transition hover:scale-105 text-sm md:text-base">
                            🏠 KEMBALI KE MENU AWAL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
       function simplifiedRacingGame() {
    return {
        loading: true,
        screen: 'menu',
        soundEnabled: localStorage.getItem('racingGameSound') !== 'false',
        difficulty: localStorage.getItem('racingGameDifficulty') || 'easy',

        score: 0,
        currentQuestionNumber: 1,
        correctAnswers: 0,
        totalQuestions: 0,
        timeLeft: 10, 

        showPauseModal: false,
        showGameOverModal: false,
        raceWon: false,
        gameOverReason: '',

        phaserGame: null,
        gameTimer: null,
        currentQuestion: null,
        isAnswering: false,
        questionQueue: [],
        
        soalId: null,
        siswaId: null,
        questionCount: 0,
        isSaving: false, 
        notyf: null, 
         history: [],
            historyLoading: false,
            

        init() {
            setTimeout(() => {
                this.loading = false;
            }, 2500);
            this.notyf = new Notyf({
                duration: 4000,
                position: { x: 'right', y: 'top' }
            });
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('racingGameSound', this.soundEnabled);
        },

        showSettings() {
            this.screen = 'settings';
        },

        showHistory() {
            this.screen = 'history';
        },
        async fetchHistory() {
                this.historyLoading = true;
                this.history = [];
                try {
                    const response = await fetch('../API/get_history.php');
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        this.history = data.history.map(item => {
                            // Hitung kemenangan di sisi client
                            const akurasi = item.jawaban_benar / item.total_pertanyaan;
                            const menang = akurasi >= 0.7;

                            return {
                                ...item,
                                menang: menang ? 'Menang' : 'Kalah',
                                tanggal: new Date(item.tanggal_main).toLocaleDateString('id-ID', {
                                    year: 'numeric', month: 'short', day: 'numeric'
                                })
                            };
                        });
                    } else {
                        throw new Error(data.message || 'Gagal mengambil riwayat.');
                    }
                } catch (error) {
                    console.error('Error fetching history:', error);
                    this.notyf.error(`Terjadi kesalahan: ${error.message}`);
                } finally {
                    this.historyLoading = false;
                }
            },
            
            showHistory() {
                this.screen = 'history';
                this.fetchHistory();
            },

        async loadQuestions() {
            const loadingOverlay = document.getElementById('question-loading');
            loadingOverlay.style.display = 'flex';
            
            try {
                const response = await fetch(`fetch_questions.php?soal_id=${this.soalId}`);
                const data = await response.json();

                if (response.ok && data.questions && data.questions.length > 0) {
                    this.questionQueue = data.questions;
                    this.questionCount = data.count;
                } else {
                    throw new Error('Failed to fetch questions or no questions found');
                }
            } catch (error) {
                console.warn('Using fallback questions:', error);
                this.questionQueue = this.generateFallbackQuestions();
                this.questionCount = this.questionQueue.length;
            } finally {
                loadingOverlay.style.display = 'none';
            }
        },

        generateFallbackQuestions() {
            const questions = [];
            for (let i = 0; i < 10; i++) { 
                questions.push(this.generateSingleQuestion());
            }
            return questions;
        },

        generateSingleQuestion() {
            const configs = {
                'easy': { maxNum1: 20, maxNum2: 20, operations: ['+', '-'] },
                'medium': { maxNum1: 50, maxNum2: 50, operations: ['+', '-', '*'] },
                'hard': { maxNum1: 100, maxNum2: 12, operations: ['+', '-', '*', '/'] }
            };

            const config = configs[this.difficulty];
            const operation = config.operations[Math.floor(Math.random() * config.operations.length)];
            let num1 = Math.floor(Math.random() * config.maxNum1) + 1;
            let num2 = Math.floor(Math.random() * config.maxNum2) + 1;
            let correctAnswer;

            switch (operation) {
                case '+':
                    correctAnswer = num1 + num2;
                    break;
                case '-':
                    if (num2 > num1) [num1, num2] = [num2, num1];
                    correctAnswer = num1 - num2;
                    break;
                case '*':
                    correctAnswer = num1 * num2;
                    break;
                case '/':
                    correctAnswer = num1;
                    num1 = num1 * num2;
                    break;
            }

            const wrongAnswers = [];
            while (wrongAnswers.length < 2) {
                let wrong = correctAnswer + (Math.floor(Math.random() * 20) - 10);
                if (wrong !== correctAnswer && wrong >= 0 && !wrongAnswers.includes(wrong)) {
                    wrongAnswers.push(wrong);
                }
            }

            const answers = [correctAnswer, ...wrongAnswers].sort(() => Math.random() - 0.5);

            return {
                question: `${num1} ${operation} ${num2} = ?`,
                num1,
                num2,
                operation,
                correctAnswer,
                answers,
                correctIndex: answers.indexOf(correctAnswer)
            };
        },

        async startRace() {
            this.screen = 'race';
            this.score = 0; // Initialize score as number
            this.currentQuestionNumber = 1;
            this.correctAnswers = 0;
            this.totalQuestions = 0;
            this.timeLeft = 10; // 10 seconds timer
            this.showGameOverModal = false;
            this.isAnswering = false;
            this.gameOverReason = '';

            const urlParams = new URLSearchParams(window.location.search);
            const soalId = urlParams.get('soal_id');
            this.soalId = soalId || '9';
            
            await this.loadQuestions();
            this.initSimplifiedPhaserGame();
        },

        pauseRace() {
            this.showPauseModal = true;
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                this.phaserGame.scene.scenes[0].scene.pause();
            }
        },

        resumeRace() {
            this.showPauseModal = false;
            this.startQuestionTimer();
            if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                this.phaserGame.scene.scenes[0].scene.resume();
            }
        },

        restartRace() {
            this.showPauseModal = false;
            this.showGameOverModal = false;
            this.startRace();
        },

        exitRace() {
            this.showPauseModal = false;
            this.screen = 'menu';
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            if (this.phaserGame) {
                this.phaserGame.destroy(true);
                this.phaserGame = null;
            }
        },

        exitToMenu() {
            this.showGameOverModal = false;
            this.screen = 'menu';
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            if (this.phaserGame) {
                this.phaserGame.destroy(true);
                this.phaserGame = null;
            }
        },

        startQuestionTimer() {
            this.timeLeft = 10; 
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            
            this.gameTimer = setInterval(() => {
                this.timeLeft--;
                
                if (this.timeLeft <= 3 && this.timeLeft > 0) {
                    this.playSound('tick');
                }
                
                if (this.timeLeft <= 0) {
                    clearInterval(this.gameTimer);
                    if (!this.isAnswering) {
                        this.handleTimeout();
                    }
                }
            }, 1000);
        },

        handleTimeout() {
            this.isAnswering = true;
            this.totalQuestions++;
            this.playSound('gameOver');
            this.gameOverReason = '⏰ WAKTU HABIS!';
            this.triggerGameOver(this.gameOverReason);
            this.endRace(false); // Game over immediately
        },

        handleCarCollision() {
            if (this.isAnswering) return;
            
            this.isAnswering = true;
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            
            this.playSound('gameOver');
            this.gameOverReason = '🚗 MOBIL TERTABRAK!';
            this.triggerGameOver(this.gameOverReason);
            this.endRace(false); 
        },

        getNextQuestion() {
            if (this.questionQueue.length > 0) {
                return this.questionQueue.shift();
            }
            return this.generateSingleQuestion();
        },

        checkAnswer(selectedAnswer) {
            if (this.isAnswering || !this.currentQuestion) return;
            
            this.isAnswering = true;
            clearInterval(this.gameTimer);
            
            this.totalQuestions++;
            const isCorrect = selectedAnswer === this.currentQuestion.correctAnswer;
            
            if (isCorrect) {
                this.correctAnswers++;
                
                // Calculate score with maximum 100 points system
                const basePointsPerQuestion = 10; // Each question worth 10 points
                const timeBonus = Math.floor(this.timeLeft * 0.5); // Time bonus up to 5 points (10s * 0.5)
                const scoreToAdd = basePointsPerQuestion + timeBonus;
                
                // Add score but cap at maximum 100
                this.score = Math.min(100, this.score + scoreToAdd);
                
                console.log('Score calculation:', { 
                    question: this.currentQuestionNumber,
                    basePointsPerQuestion, 
                    timeLeft: this.timeLeft,
                    timeBonus, 
                    scoreToAdd, 
                    totalScore: this.score,
                    maxPossible: 100
                });
                
                this.playSound('success');
                this.triggerSuccessEffect(); 
                
                setTimeout(() => {
                    this.nextQuestion();
                }, 1500);
            } else {
                this.playSound('gameOver');
                this.gameOverReason = '❌ JAWABAN SALAH!';
                this.triggerGameOver(this.gameOverReason);
                this.endRace(false); // Game over immediately
            }
        },

        nextQuestion() {
            if (this.questionQueue.length > 0) {
                this.currentQuestionNumber++;
                this.isAnswering = false;
                this.startQuestionTimer();
                
                if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                    this.phaserGame.scene.scenes[0].generateNewQuestion();
                }
            } else {
                const accuracy = (this.correctAnswers / this.totalQuestions) * 100;
                this.endRace(accuracy >= 70);
            }
        },

        endRace(won) {
            this.raceWon = won;
            this.showGameOverModal = true;
            
            if (this.gameTimer) {
                clearInterval(this.gameTimer);
            }
            
            this.playSound(won ? 'victory' : 'gameOver');
            
            if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                this.phaserGame.scene.scenes[0].scene.pause();
            }
        },

        async saveResults() {
            if (this.isSaving) return;
            this.isSaving = true;
            
            try {
                const response = await fetch('../API/save_game.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        score: this.score,
                        correctAnswers: this.correctAnswers,
                        totalQuestions: this.totalQuestions,
                        difficulty: this.difficulty,
                        soalId: this.soalId
                    })
                });

                const result = await response.json();
                
                if (response.ok) {
                    this.notyf.success('🏁 Data balapan berhasil disimpan!');
                } else {
                    throw new Error(result.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Error saving data:', error);
                this.notyf.error(`Terjadi kesalahan: ${error.message}`);
            } finally {
                this.isSaving = false;
            }
        },

        triggerGameOver(message) {
            document.body.classList.add('shake');
            setTimeout(() => {
                document.body.classList.remove('shake');
            }, 500);
            
            if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                this.phaserGame.scene.scenes[0].triggerGameOverEffect(message);
            }
        },

        triggerSuccessEffect() {
            if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                this.phaserGame.scene.scenes[0].triggerSuccessEffect();
            }
        },

        playSound(type) {
            if (!this.soundEnabled) return;
            
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                const soundConfigs = {
                    success: { frequencies: [523, 659, 784], durations: [0.1, 0.1, 0.1], gain: 0.3 },
                    gameOver: { frequencies: [200], durations: [1.0], gain: 0.4, ramp: 50 },
                    tick: { frequencies: [800], durations: [0.1], gain: 0.1 },
                    victory: { frequencies: [523, 659, 784, 1047], durations: [0.2, 0.2, 0.2, 0.2], gain: 0.3 }
                };

                const config = soundConfigs[type];
                if (!config) return;

                if (config.frequencies.length === 1) {
                    oscillator.frequency.setValueAtTime(config.frequencies[0], audioContext.currentTime);
                    if (config.ramp) {
                        oscillator.frequency.exponentialRampToValueAtTime(config.ramp, audioContext.currentTime + config.durations[0]);
                    }
                } else {
                    let currentTime = audioContext.currentTime;
                    config.frequencies.forEach((freq, i) => {
                        oscillator.frequency.setValueAtTime(freq, currentTime);
                        currentTime += config.durations[i] || 0.1;
                    });
                }
                
                gainNode.gain.setValueAtTime(config.gain, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + (config.durations.reduce((a, b) => a + b, 0) || 1));
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 1);
            } catch (e) {
                console.log('Audio not supported');
            }
        },

        initSimplifiedPhaserGame() {
            if (this.phaserGame) {
                this.phaserGame.destroy(true);
            }

            const app = this;
            const gameContainer = document.getElementById('phaser-game');
            
            const config = {
                type: Phaser.AUTO,
                width: Math.min(800, window.innerWidth),
                height: Math.min(500, window.innerHeight - 150),
                parent: 'phaser-game',
                backgroundColor: '#2C5530',
                physics: {
                    default: 'arcade',
                    arcade: {
                        gravity: { y: 0 },
                        debug: false
                    }
                },
                scene: {
                    preload: preload,
                    create: create,
                    update: update
                },
                scale: {
                    mode: Phaser.Scale.FIT,
                    autoCenter: Phaser.Scale.CENTER_BOTH
                }
            };

            // Game variables - SIMPLIFIED
            let questionText, answerButtons, background;
            let playerCar, opponentCars = [], roadLines = [], trees = [];
            let particles, explosionEmitter;
            let screenShake = { x: 0, y: 0, intensity: 0 };
            let gameSpeed = 4; // Slower game speed
            let carSpeed = 8;
            let playerCarX = config.width / 2;
            let collisionCooldown = 0;
            let carSpawnTimer = 0; // Timer for spawning cars

            function preload() {
                // Create player car sprite
                const playerCarGraphics = this.add.graphics();
                
                // Car shadow
                playerCarGraphics.fillStyle(0x000000, 0.3);
                playerCarGraphics.fillEllipse(21, 41, 36, 72);
                
                // Main car body
                playerCarGraphics.fillStyle(0xDD2222);
                playerCarGraphics.fillEllipse(20, 40, 36, 72);
                
                // Car body highlight
                playerCarGraphics.fillStyle(0xFF4444);
                playerCarGraphics.fillEllipse(20, 38, 30, 65);
                
                // Hood area
                playerCarGraphics.fillStyle(0xFF3333);
                playerCarGraphics.fillEllipse(20, 20, 32, 25);
                
                // Windshield
                playerCarGraphics.fillStyle(0x1a1a2e);
                playerCarGraphics.fillEllipse(20, 30, 24, 20);
                
                // Rear window
                playerCarGraphics.fillStyle(0x1a1a2e);
                playerCarGraphics.fillEllipse(20, 50, 22, 15);
                
                // Headlights
                playerCarGraphics.fillStyle(0xFFFFDD);
                playerCarGraphics.fillEllipse(14, 10, 4, 6);
                playerCarGraphics.fillEllipse(26, 10, 4, 6);
                
                // Taillights
                playerCarGraphics.fillStyle(0xFF0000);
                playerCarGraphics.fillEllipse(14, 70, 3, 4);
                playerCarGraphics.fillEllipse(26, 70, 3, 4);
                
                // Wheels
                playerCarGraphics.fillStyle(0x000000);
                playerCarGraphics.fillEllipse(6, 22, 8, 12);
                playerCarGraphics.fillEllipse(34, 22, 8, 12);
                playerCarGraphics.fillEllipse(6, 58, 8, 12);
                playerCarGraphics.fillEllipse(34, 58, 8, 12);
                
                // Wheel rims
                playerCarGraphics.fillStyle(0xCCCCCC);
                playerCarGraphics.fillEllipse(6, 22, 5, 8);
                playerCarGraphics.fillEllipse(34, 22, 5, 8);
                playerCarGraphics.fillEllipse(6, 58, 5, 8);
                playerCarGraphics.fillEllipse(34, 58, 5, 8);
                
                // Racing stripes
                playerCarGraphics.fillStyle(0xFFFFFF, 0.7);
                playerCarGraphics.fillRect(18, 15, 4, 50);
                
                playerCarGraphics.generateTexture('player-car', 40, 80);
                playerCarGraphics.destroy();

                // Create fewer opponent car colors
                const opponentColors = [0x3333FF, 0x33FF33, 0xFFFF33];
                opponentColors.forEach((color, index) => {
                    const carGraphics = this.add.graphics();
                    carGraphics.fillStyle(color);
                    carGraphics.fillEllipse(20, 40, 36, 72);
                    carGraphics.fillStyle(0x1a1a2e);
                    carGraphics.fillEllipse(20, 30, 24, 20);
                    carGraphics.fillStyle(0x000000);
                    carGraphics.fillEllipse(6, 22, 8, 12);
                    carGraphics.fillEllipse(34, 22, 8, 12);
                    carGraphics.fillEllipse(6, 58, 8, 12);
                    carGraphics.fillEllipse(34, 58, 8, 12);
                    carGraphics.generateTexture(`opponent-car-${index}`, 40, 80);
                    carGraphics.destroy();
                });

                // Create UI elements
                this.add.graphics()
                    .fillStyle(0xFF6B35)
                    .fillRoundedRect(0, 0, 200, 60, 15)
                    .generateTexture('answer-button', 200, 60);

                // Create particle textures
                this.add.graphics()
                    .fillStyle(0xFF6600)
                    .fillCircle(4, 4, 4)
                    .generateTexture('fire-particle', 8, 8);

                this.add.graphics()
                    .fillStyle(0xFFD700)
                    .fillCircle(3, 3, 3)
                    .generateTexture('spark-particle', 6, 6);

                // Road elements
                this.add.graphics()
                    .fillStyle(0xFFFFFF)
                    .fillRect(0, 0, 8, 30)
                    .generateTexture('road-line', 8, 30);

                this.add.graphics()
                    .fillStyle(0x8B5A3C)
                    .fillRect(0, 25, 8, 15)
                    .fillStyle(0x228B22)
                    .fillCircle(4, 20, 15)
                    .generateTexture('tree', 8, 40);
            }

            function create() {
                const scene = this;
                
                // Create scrolling road background
                background = this.add.graphics();
                background.fillStyle(0x404040);
                background.fillRect(0, 0, config.width, config.height);
                
                // Grass borders
                background.fillStyle(0x228B22);
                background.fillRect(0, 0, config.width * 0.2, config.height);
                background.fillRect(config.width * 0.8, 0, config.width * 0.2, config.height);

                // Create road lanes
                const laneWidth = config.width * 0.6 / 3;
                for (let i = 1; i < 3; i++) {
                    const x = config.width * 0.2 + (i * laneWidth);
                    const laneMarker = this.add.graphics();
                    laneMarker.lineStyle(2, 0xFFFFFF, 0.7);
                    laneMarker.lineBetween(x, 0, x, config.height);
                }

                // Create road lines
                for (let i = 0; i < 12; i++) {
                    const line = this.add.image(config.width / 2, i * 50, 'road-line');
                    roadLines.push(line);
                }

                // Create trees
                for (let i = 0; i < 8; i++) {
                    const leftTree = this.add.image(config.width * 0.1, i * 80, 'tree');
                    const rightTree = this.add.image(config.width * 0.9, i * 80, 'tree');
                    trees.push(leftTree, rightTree);
                }

                // Create player car with physics
                playerCar = this.physics.add.image(config.width / 2, config.height * 0.8, 'player-car');
                playerCar.setCollideWorldBounds(true);
                playerCar.body.setSize(30, 60, 5, 10);

                // Initialize empty opponent cars array
                opponentCars = [];

                // Input controls
                const cursors = this.input.keyboard.createCursorKeys();
                const wasd = this.input.keyboard.addKeys('W,S,A,D');
                
                // Touch/mouse controls
                this.input.on('pointerdown', (pointer) => {
                    const targetX = pointer.x;
                    const minX = config.width * 0.25;
                    const maxX = config.width * 0.75;
                    playerCarX = Phaser.Math.Clamp(targetX, minX, maxX);
                });

                // Update function for car movement
                this.updateCarMovement = function() {
                    // Keyboard controls
                    if (cursors.left.isDown || wasd.A.isDown) {
                        playerCarX -= carSpeed;
                    } else if (cursors.right.isDown || wasd.D.isDown) {
                        playerCarX += carSpeed;
                    }

                    // Constrain player car to road
                    const minX = config.width * 0.25;
                    const maxX = config.width * 0.75;
                    playerCarX = Phaser.Math.Clamp(playerCarX, minX, maxX);
                    
                    // Smooth car movement
                    const targetDiff = playerCarX - playerCar.x;
                    playerCar.x += targetDiff * 0.1;
                };

                // Function to spawn new opponent car
                this.spawnOpponentCar = function() {
                    // Only spawn if less than 2 cars on screen
                    if (opponentCars.length < 2) {
                        const lanes = [config.width * 0.3, config.width * 0.5, config.width * 0.7];
                        const availableLanes = lanes.filter(lane => {
                            // Check if lane is occupied by existing cars
                            return !opponentCars.some(car => Math.abs(car.x - lane) < 50 && car.y > -200);
                        });
                        
                        if (availableLanes.length > 0) {
                            const lane = availableLanes[Math.floor(Math.random() * availableLanes.length)];
                            const carIndex = Math.floor(Math.random() * 3);
                            const car = scene.physics.add.image(lane, -100, `opponent-car-${carIndex}`);
                            car.body.setSize(30, 60, 5, 10);
                            car.setData('speed', 1.5 + Math.random() * 0.5); // Slower speed: 0.8-1.2
                            opponentCars.push(car);
                            
                            // Set up collision detection for this car
                            scene.physics.add.overlap(playerCar, car, (player, opponent) => {
                                if (collisionCooldown <= 0) {
                                    collisionCooldown = 120;
                                    app.handleCarCollision();
                                }
                            });
                        }
                    }
                };

                // Create particle systems
                particles = this.add.particles(0, 0, 'fire-particle', {
                    speed: { min: 50, max: 150 },
                    scale: { start: 1, end: 0 },
                    lifespan: 600,
                    emitting: false
                });

                explosionEmitter = this.add.particles(0, 0, 'spark-particle', {
                    speed: { min: 100, max: 200 },
                    scale: { start: 0.8, end: 0 },
                    lifespan: 800,
                    emitting: false,
                    quantity: 10
                });

                // Question UI
                const questionBg = this.add.graphics();
                questionBg.fillStyle(0x000000, 0.85);
                questionBg.fillRoundedRect(config.width * 0.05, 20, config.width * 0.9, 80, 15);
                questionBg.lineStyle(3, 0xFFD600);
                questionBg.strokeRoundedRect(config.width * 0.05, 20, config.width * 0.9, 80, 15);

                questionText = this.add.text(config.width / 2, 60, '', {
                    fontSize: Math.min(32, config.width / 25) + 'px',
                    fill: '#FFFFFF',
                    fontFamily: 'Orbitron',
                    align: 'center'
                }).setOrigin(0.5);

                // Answer buttons (responsive)
                answerButtons = [];
                const buttonY = config.height - 40;
                const buttonWidth = Math.min(180, config.width / 4);
                const buttonSpacing = config.width / 3;
                
                for (let i = 0; i < 3; i++) {
                    const buttonX = buttonSpacing * 0.5 + (i * buttonSpacing);
                    
                    const button = this.add.image(buttonX, buttonY, 'answer-button')
                        .setInteractive()
                        .setScale(buttonWidth / 200);
                        
                    const buttonText = this.add.text(buttonX, buttonY, '', {
                        fontSize: Math.min(20, config.width / 40) + 'px',
                        fill: '#FFF',
                        fontFamily: 'Orbitron'
                    }).setOrigin(0.5);

                    button.on('pointerdown', () => {
                        if (!app.currentQuestion || app.isAnswering) return;
                        const selectedAnswer = app.currentQuestion.answers[i];
                        app.checkAnswer(selectedAnswer);
                    });

                    button.on('pointerover', () => {
                        button.setTint(0xFFFFAA);
                        this.tweens.add({
                            targets: button,
                            scaleX: button.scaleX * 1.1,
                            scaleY: button.scaleY * 1.1,
                            duration: 100
                        });
                    });

                    button.on('pointerout', () => {
                        button.clearTint();
                        this.tweens.add({
                            targets: button,
                            scaleX: button.scaleX / 1.1,
                            scaleY: button.scaleY / 1.1,
                            duration: 100
                        });
                    });

                    answerButtons.push({ button, text: buttonText });
                }

                // Scene methods
                this.generateNewQuestion = function() {
                    app.currentQuestion = app.getNextQuestion();
                    if (!app.currentQuestion) return;
                    
                    const questionDisplay = app.currentQuestion.question || 
                        `${app.currentQuestion.num1} ${app.currentQuestion.operation} ${app.currentQuestion.num2} = ?`;
                    questionText.setText(questionDisplay);
                    
                    app.currentQuestion.answers.forEach((answer, index) => {
                        if (answerButtons[index]) {
                            answerButtons[index].text.setText(answer.toString());
                        }
                    });
                };

                this.triggerGameOverEffect = function(message) {
                    // Car crash animation
                    scene.tweens.add({
                        targets: playerCar,
                        angle: -45,
                        x: playerCar.x - 50,
                        y: playerCar.y + 30,
                        duration: 500,
                        ease: 'Power2'
                    });

                    // Explosion particles
                    particles.setPosition(playerCar.x, playerCar.y);
                    explosionEmitter.setPosition(playerCar.x, playerCar.y);
                    
                    particles.start();
                    explosionEmitter.start();
                    
                    setTimeout(() => {
                        particles.stop();
                        explosionEmitter.stop();
                    }, 1000);

                    // Screen shake
                    screenShake.intensity = 15;
                    scene.cameras.main.shake(1000, 0.03);

                    // Show game over message
                    const gameOverText = scene.add.text(config.width / 2, config.height / 2, message, {
                        fontSize: Math.min(32, config.width / 25) + 'px',
                        fill: '#FF3333',
                        fontFamily: 'Orbitron',
                        stroke: '#000',
                        strokeThickness: 4
                    }).setOrigin(0.5);

                    scene.tweens.add({
                        targets: gameOverText,
                        alpha: 0.3,
                        y: gameOverText.y - 50,
                        scale: 1.2,
                        duration: 3000,
                        onComplete: () => {
                            gameOverText.destroy();
                        }
                    });
                };

                this.triggerSuccessEffect = function() {
                    // Success sparkle effect
                    const sparkles = scene.add.particles(playerCar.x, playerCar.y, 'spark-particle', {
                        speed: { min: 80, max: 120 },
                        scale: { start: 0.5, end: 0 },
                        lifespan: 500,
                        quantity: 15,
                        emitting: false
                    });

                    sparkles.start();
                    setTimeout(() => sparkles.stop(), 300);

                    // Car boost animation
                    scene.tweens.add({
                        targets: playerCar,
                        scaleX: 1.3,
                        scaleY: 1.3,
                        duration: 200,
                        yoyo: true
                    });

                    // Success message
                    const successText = scene.add.text(config.width / 2, config.height / 2, '✨ BENAR! ✨', {
                        fontSize: Math.min(32, config.width / 25) + 'px',
                        fill: '#00FF00',
                        fontFamily: 'Orbitron',
                        stroke: '#000',
                        strokeThickness: 3
                    }).setOrigin(0.5);

                    scene.tweens.add({
                        targets: successText,
                        alpha: 0,
                        y: successText.y - 60,
                        scale: 1.2,
                        duration: 1500,
                        onComplete: () => successText.destroy()
                    });
                };

                this.generateNewQuestion();
                app.startQuestionTimer();
            }

            function update() {
                if (collisionCooldown > 0) {
                    collisionCooldown--;
                }

                this.updateCarMovement();

                carSpawnTimer++;
                if (carSpawnTimer >= 180 + Math.random() * 240) { // 5-10 seconds
                    this.spawnOpponentCar();
                    carSpawnTimer = 0;
                }

                // Animate road elements
                roadLines.forEach(line => {
                    line.y += gameSpeed;
                    if (line.y > config.height + 30) {
                        line.y = -30;
                    }
                });

                // Animate trees
                trees.forEach(tree => {
                    tree.y += gameSpeed * 0.8;
                    if (tree.y > config.height + 40) {
                        tree.y = -40;
                    }
                });

                // Animate opponent cars (SIMPLIFIED - slower movement)
                opponentCars.forEach((car, index) => {
                    const speed = car.getData('speed') * gameSpeed;
                    car.y += speed;
                    
                    // Remove car when off screen
                    if (car.y > config.height + 40) {
                        car.destroy();
                        opponentCars.splice(index, 1);
                    }
                });

                // Screen shake effect
                if (screenShake.intensity > 0) {
                    screenShake.x = (Math.random() - 0.5) * screenShake.intensity;
                    screenShake.y = (Math.random() - 0.5) * screenShake.intensity;
                    screenShake.intensity *= 0.9;
                    
                    this.cameras.main.setScroll(screenShake.x, screenShake.y);
                    
                    if (screenShake.intensity < 0.1) {
                        screenShake.intensity = 0;
                        this.cameras.main.setScroll(0, 0);
                    }
                }
            }

            this.phaserGame = new Phaser.Game(config);
        }
    }
}   
        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const loadingScreen = document.getElementById('loading');
                if (loadingScreen) {
                    loadingScreen.style.display = 'none';
                }
            }, 2500);
        });

        // Prevent zoom and context menu on mobile
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.game && window.game.scale) {
                window.game.scale.refresh();
            }
        });

        // Prevent scrolling on mobile
        document.addEventListener('touchmove', function(e) {
            e.preventDefault();
        }, { passive: false });
    </script>
</body>
</html>