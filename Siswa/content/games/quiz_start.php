<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racing Math Adventure - Game Matematika Mobil</title>
    
    <meta name="theme-color" content="#FF6B35">
    <meta name="description" content="Game Matematika Racing yang seru untuk belajar sambil balapan">
    <link rel="manifest" href="data:application/json;base64,ewogICJuYW1lIjogIlJhY2luZyBNYXRoIEFkdmVudHVyZSAtIEdhbWUgTWF0ZW1hdGlrYSBNb2JpbCIsCiAgInNob3J0X25hbWUiOiAiUmFjaW5nIE1hdGgiLAogICJkZXNjcmlwdGlvbiI6ICJHYW1lIE1hdGVtYXRpa2EgUmFjaW5nIHlhbmcgc2VydSB1bnR1ayBiZWxhamFyIHNhbWJpbCBiYWxhcGFuIiwKICAic3RhcnRfdXJsIjogIi4vIiwKICAiZGlzcGxheSI6ICJzdGFuZGFsb25lIiwKICAiYmFja2dyb3VuZF9jb2xvciI6ICIjRkZGRkZGIiwKICAidGhlbWVfY29sb3IiOiAiI0ZGNkIzNSIsCiAgImljb25zIjogWwogICAgewogICAgICAic3JjIjogImRhdGE6aW1hZ2Uvc3ZnK3htbDtiYXNlNjQsUEhOMlp5QjNhV1IwYUQwaU1UUTRMQ0JvWldsbmFIUTlJakUwT0NJZ2RtbGxkMEp2ZUQwaU1DQXdJREUwT0NBek5EZ2lJR1pwYkd3OUlpTkdSalpDTXpVaUlIaHRiRzV6UFNKb2RIUndPaTh2ZDNkM0xuY3pMbTl5Wnk4eU1EQXdMM04yWnlJK1BISmxZM1FnZUQwaU1qQWlJSGs5SWpJd0lpQjNhV1IwYUQwaU1UQTRJaUJvWldsbmFIUTlJakUwT0NJZ2NuZzlJakZqSWlCbWFXeHNQU0lqUWpjNFEwRWlMejQ4WlhOc2FYQnpaU0JqZUQwaU5UUWlJR041UFNJMU5DSWdWVUk5SWpJNElpQmhjajFpTlNJdlBqeDFjMlVnZUVocGJtdGlaRDBpUVVOMWMzUnZiU05EYjJ4dmNpTnViMjVsSWo0OGNHRjBhQ0JrUFNKTk1URWdOREJzTXpZaUx6NDhMM1Z6WlQ0OEwybHRZV2RsUGp3dmMzWm5QZz09IiwKICAgICAgInNpemVzIjogIjE0NHgxNDQiLAogICAgICAidHlwZSI6ICJpbWFnZS9zdmcreG1sIgogICAgfQogIF0sCiAgIm9yaWVudGF0aW9uIjogInBvcnRyYWl0IiwKICAiY2F0ZWdvcmllcyI6IFsiZ2FtZXMiLCAiZWR1Y2F0aW9uIl0KfQ==">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTQ4IiBoZWlnaHQ9IjE0OCIgdmlld0JveD0iMCAwIDE0OCAzNDgiIGZpbGw9IiNGRjZCMzUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3QgeD0iMjAiIHk9IjIwIiB3aWR0aD0iMTA4IiBoZWlnaHQ9IjE0OCIgcng9IjFjIiBmaWxsPSIjQjc4Q0EiLz48ZWxsaXBzZSBjeD0iNTQiIGN5PSI1NCIgVUI9IjI4IiBhcj1iNSIvPjx1c2UgeEhpbmtiZD0iQUN1c3RvbSNDb2xvciNub25lIj4KPHBhdGggZD0iTTExIDQwbDM2Ii8+PC91c2U+PC9pbWFnZT48L3N2Zz4=">
    
    <link href="../../../src/output.css" rel="stylesheet" onerror="console.log('Local CSS failed')">
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.70.0/dist/phaser.min.js"></script>
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
        }
        
        .racing-font {
            font-family: 'Orbitron', monospace;
        }
        
        .game-container {
            max-width: 100vw;
            max-height: 100vh;
            overflow: hidden;
        }
        
        .engine-animation {
            animation: engineVibrate 0.1s ease-in-out infinite alternate;
        }
        
        @keyframes engineVibrate {
            0% { transform: translateX(0px) translateY(0px); }
            100% { transform: translateX(1px) translateY(1px); }
        }
        
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
        
        .road-lines {
            background-image: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 20px,
                white 20px,
                white 40px
            );
            animation: roadMove 0.5s linear infinite;
        }
        
        @keyframes roadMove {
            0% { background-position: 0px 0px; }
            100% { background-position: 40px 0px; }
        }
        
        .speedometer {
            background: radial-gradient(circle, #1a1a1a 0%, #333 70%, #555 100%);
            border: 4px solid #FF6B35;
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
            <p class="text-xl text-orange-200 mb-6">ADVENTURE</p>
            <div class="flex justify-center space-x-2">
                <div class="w-3 h-3 bg-orange-400 rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-red-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-yellow-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
    <div id="app" x-data="racingGameApp()" x-show="!loading" class="min-h-screen">
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
                <h1 class="text-6xl md:text-7xl font-black text-white mb-2 drop-shadow-lg racing-font" x-text="getText('title')"></h1>
                <p class="text-2xl text-white/90 mb-8 drop-shadow racing-font" x-text="getText('subtitle')"></p>
                <div class="space-y-4">
                    <button @click="startRace()" 
                            class="w-full race-button text-white font-bold py-5 px-8 rounded-2xl text-2xl racing-font engine-animation">
                        <span class="mr-3">🏁</span>
                        <span x-text="getText('startRace')"></span>
                    </button>
                    <button @click="showSettings()" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-8 rounded-2xl text-lg shadow-lg transform transition hover:scale-105 racing-font">
                        <span class="mr-2">⚙️</span>
                        <span x-text="getText('settings')"></span>
                    </button>
                    <button @click="showGarage()" 
                            class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-4 px-8 rounded-2xl text-lg shadow-lg transform transition hover:scale-105 racing-font">
                        <span class="mr-2">🏆</span>
                        <span x-text="getText('garage')"></span>
                    </button>
                </div>
                <div class="mt-8 flex justify-center">
                    <button @click="toggleLanguage()" 
                            class="bg-white/20 hover:bg-white/30 text-white font-medium py-3 px-6 rounded-xl backdrop-blur-sm transition-colors racing-font">
                        <span class="mr-2">🌐</span>
                        <span x-text="language === 'id' ? 'English' : 'Bahasa Indonesia'"></span>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="screen === 'settings'" class="min-h-screen flex flex-col items-center justify-center p-4 fade-in-race">
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 max-w-md w-full zoom-in border-4 border-orange-400">
                <h2 class="text-4xl font-bold text-gray-800 mb-6 text-center racing-font" x-text="getText('settings')"></h2>
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl">
                        <span class="text-lg font-medium text-gray-700 racing-font" x-text="getText('engineSound')"></span>
                        <button @click="toggleSound()" 
                                class="w-16 h-8 rounded-full transition-colors duration-200"
                                :class="soundEnabled ? 'bg-green-500' : 'bg-gray-300'">
                            <div class="w-6 h-6 bg-white rounded-full shadow transform transition-transform duration-200"
                                 :class="soundEnabled ? 'translate-x-8' : 'translate-x-1'"></div>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                        <span class="text-lg font-medium text-gray-700 racing-font" x-text="getText('raceMusic')"></span>
                        <button @click="toggleMusic()" 
                                class="w-16 h-8 rounded-full transition-colors duration-200"
                                :class="musicEnabled ? 'bg-green-500' : 'bg-gray-300'">
                            <div class="w-6 h-6 bg-white rounded-full shadow transform transition-transform duration-200"
                                 :class="musicEnabled ? 'translate-x-8' : 'translate-x-1'"></div>
                        </button>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <label class="block text-lg font-medium text-gray-700 mb-3 racing-font" x-text="getText('trackDifficulty')"></label>
                        <select x-model="difficulty" 
                                class="w-full p-3 border-2 border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent racing-font">
                            <option value="rookie" x-text="getText('rookie')"></option>
                            <option value="pro" x-text="getText('pro')"></option>
                            <option value="champion" x-text="getText('champion')"></option>
                        </select>
                    </div>
                    <button @click="resetProgress()" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-6 rounded-xl transition-colors racing-font">
                        <span class="mr-2">🔄</span>
                        <span x-text="getText('resetProgress')"></span>
                    </button>
                </div>
                <button @click="screen = 'menu'" 
                        class="w-full mt-8 bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl transition-colors racing-font">
                    <span class="mr-2">←</span>
                    <span x-text="getText('backToGarage')"></span>
                </button>
            </div>
        </div>
        <div x-show="screen === 'garage'" class="min-h-screen flex flex-col items-center justify-center p-4 fade-in-race">
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 max-w-md w-full zoom-in border-4 border-purple-400">
                <h2 class="text-4xl font-bold text-gray-800 mb-6 text-center racing-font" x-text="getText('garage')"></h2>
                
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-100 to-blue-200 p-4 rounded-xl border-2 border-blue-300">
                        <div class="text-sm text-blue-600 font-medium racing-font" x-text="getText('currentTrack')"></div>
                        <div class="text-3xl font-bold text-blue-800 racing-font" x-text="currentLevel"></div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-100 to-green-200 p-4 rounded-xl border-2 border-green-300">
                        <div class="text-sm text-green-600 font-medium racing-font" x-text="getText('totalPoints')"></div>
                        <div class="text-3xl font-bold text-green-800 racing-font" x-text="totalScore.toLocaleString()"></div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-100 to-purple-200 p-4 rounded-xl border-2 border-purple-300">
                        <div class="text-sm text-purple-600 font-medium racing-font" x-text="getText('winRate')"></div>
                        <div class="text-3xl font-bold text-purple-800 racing-font" x-text="Math.round(accuracy) + '%'"></div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 p-4 rounded-xl border-2 border-yellow-500">
                        <div class="text-sm text-yellow-600 font-medium racing-font" x-text="getText('racesCompleted')"></div>
                        <div class="text-3xl font-bold text-yellow-800 racing-font" x-text="gamesPlayed"></div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-orange-100 to-red-200 p-4 rounded-xl border-2 border-orange-400">
                        <div class="text-sm text-orange-600 font-medium racing-font" x-text="getText('topSpeed')"></div>
                        <div class="text-3xl font-bold text-red-800 racing-font" x-text="topSpeed + ' km/h'"></div>
                    </div>
                </div>
                
                <button @click="screen = 'menu'" 
                        class="w-full mt-8 bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl transition-colors racing-font">
                    <span class="mr-2">←</span>
                    <span x-text="getText('backToGarage')"></span>
                </button>
            </div>
        </div>

        <div x-show="screen === 'race'" class="min-h-screen track-bg">
            <div class="bg-gray-900/90 backdrop-blur-sm shadow-lg p-4 border-b-4 border-yellow-400">
                <div class="flex justify-between items-center max-w-4xl mx-auto">
                    <button @click="pauseRace()" 
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg racing-font">
                        ⏸️ <span x-text="getText('pit')"></span>
                    </button>
                    <div class="text-center">
                        <div class="text-sm text-yellow-400 racing-font" x-text="getText('track')"></div>
                        <div class="text-xl font-bold text-white racing-font" x-text="currentLevel"></div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm text-green-400 racing-font" x-text="getText('points')"></div>
                        <div class="text-xl font-bold text-green-400 racing-font" x-text="score.toLocaleString()"></div>
                    </div>
                    
                    <div class="speedometer w-16 h-16 rounded-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-xs text-orange-400 racing-font">SPEED</div>
                            <div class="text-lg font-bold text-white racing-font" x-text="currentSpeed"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="phaser-game" class="game-container"></div>
        </div>

        <div x-show="showPauseModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 rounded-3xl shadow-2xl p-8 max-w-sm w-full zoom-in border-4 border-orange-400">
                <h3 class="text-3xl font-bold text-white mb-6 text-center racing-font" x-text="getText('pitStop')"></h3>
                
                <div class="space-y-4">
                    <button @click="resumeRace()" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-xl racing-font">
                        🏁 <span x-text="getText('backToRace')"></span>
                    </button>
                    
                    <button @click="restartRace()" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-6 rounded-xl racing-font">
                        🔄 <span x-text="getText('newRace')"></span>
                    </button>
                    
                    <button @click="exitRace()" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl racing-font">
                        🏠 <span x-text="getText('backToGarage')"></span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="showGameOverModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
            <div class="bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900 rounded-3xl shadow-2xl p-8 max-w-sm w-full zoom-in border-4 border-gold">
                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br rounded-full flex items-center justify-center"
                         :class="raceWon ? 'from-yellow-400 to-orange-500' : 'from-red-500 to-red-700'">
                        <span class="text-5xl" x-text="raceWon ? '🏆' : '💥'"></span>
                    </div>
                    
                    <h3 class="text-4xl font-bold text-white mb-4 racing-font" 
                        x-text="raceWon ? getText('raceWon') : getText('raceOver')"></h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-sm text-gray-300 racing-font" x-text="getText('points')"></div>
                            <div class="text-2xl font-bold text-yellow-400 racing-font" x-text="score.toLocaleString()"></div>
                        </div>
                        
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-sm text-gray-300 racing-font" x-text="getText('winRate')"></div>
                            <div class="text-2xl font-bold text-green-400 racing-font" 
                                 x-text="Math.round((correctAnswers / totalQuestions) * 100) + '%'"></div>
                        </div>
                        
                        <div class="bg-white/10 rounded-lg p-3">
                            <div class="text-sm text-gray-300 racing-font" x-text="getText('topSpeed')"></div>
                            <div class="text-2xl font-bold text-orange-400 racing-font" x-text="currentSpeed + ' km/h'"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <button x-show="raceWon" @click="nextLevel()" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-xl racing-font transform transition hover:scale-105">
                            🏁 <span x-text="getText('nextTrack')"></span>
                        </button>
                        
                        <button @click="restartRace()" 
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl racing-font transform transition hover:scale-105">
                            🔄 <span x-text="getText('tryAgain')"></span>
                        </button>
                        
                        <button @click="exitToMenu()" 
                                class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold py-4 px-6 rounded-xl racing-font transform transition hover:scale-105">
                            🏠 <span x-text="getText('backToGarage')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Language System
        const translations = {
            id: {
                title: 'RACING MATH',
                subtitle: 'ADVENTURE',
                startRace: 'MULAI BALAPAN',
                settings: 'PENGATURAN',
                garage: 'GARASI',
                engineSound: 'Suara Mesin',
                raceMusic: 'Musik Balapan',
                trackDifficulty: 'Tingkat Sirkuit',
                rookie: 'Pemula',
                pro: 'Profesional', 
                champion: 'Juara',
                resetProgress: 'Reset Progress',
                backToGarage: 'Kembali ke Garasi',
                currentTrack: 'Sirkuit Saat Ini',
                totalPoints: 'Total Poin',
                winRate: 'Tingkat Kemenangan',
                racesCompleted: 'Balapan Selesai',
                topSpeed: 'Kecepatan Tertinggi',
                track: 'Sirkuit',
                points: 'Poin',
                pit: 'Pit',
                pitStop: 'Pit Stop',
                backToRace: 'Kembali Balapan',
                newRace: 'Balapan Baru',
                correct: 'BENAR! +50 km/h',
                wrong: 'SALAH! Kehilangan Kecepatan',
                timeUp: 'WAKTU HABIS!',
                raceWon: 'MENANG! Sirkuit Selesai!',
                raceOver: 'BALAPAN SELESAI!',
                nextTrack: 'Sirkuit Berikutnya',
                tryAgain: 'Coba Lagi',
                fuel: 'Bahan Bakar',
                speed: 'Kecepatan',
                position: 'Posisi',
                loadingQuestions: 'Memuat Soal...',
                errorLoadingQuestions: 'Error memuat soal, menggunakan soal default'
            },
            en: {
                title: 'RACING MATH',
                subtitle: 'ADVENTURE',
                startRace: 'START RACE',
                settings: 'SETTINGS',
                garage: 'GARAGE',
                engineSound: 'Engine Sound',
                raceMusic: 'Race Music',
                trackDifficulty: 'Track Difficulty',
                rookie: 'Rookie',
                pro: 'Professional',
                champion: 'Champion',
                resetProgress: 'Reset Progress',
                backToGarage: 'Back to Garage',
                currentTrack: 'Current Track',
                totalPoints: 'Total Points',
                winRate: 'Win Rate',
                racesCompleted: 'Races Completed',
                topSpeed: 'Top Speed',
                track: 'Track',
                points: 'Points',
                pit: 'Pit',
                pitStop: 'Pit Stop',
                backToRace: 'Back to Race',
                newRace: 'New Race',
                correct: 'CORRECT! +50 km/h',
                wrong: 'WRONG! Speed Lost',
                timeUp: 'TIME UP!',
                raceWon: 'VICTORY! Track Complete!',
                raceOver: 'RACE OVER!',
                nextTrack: 'Next Track',
                tryAgain: 'Try Again',
                fuel: 'Fuel',
                speed: 'Speed',
                position: 'Position',
                loadingQuestions: 'Loading Questions...',
                errorLoadingQuestions: 'Error loading questions, using default questions'
            }
        };

        // Get soal_id from URL parameters
        function getSoalIdFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('soal_id') || '1'; // Default to 1 if not provided
        }

        // Main Alpine.js App
        function racingGameApp() {
            return {
                loading: true,
                screen: 'menu',
                language: localStorage.getItem('racingGameLanguage') || 'id',
                soundEnabled: localStorage.getItem('racingGameSound') !== 'false',
                musicEnabled: localStorage.getItem('racingGameMusic') !== 'false',
                difficulty: localStorage.getItem('racingGameDifficulty') || 'rookie',
                currentLevel: parseInt(localStorage.getItem('racingGameLevel')) || 1,
                score: 0,
                totalScore: parseInt(localStorage.getItem('racingGameTotalScore')) || 0,
                accuracy: parseFloat(localStorage.getItem('racingGameAccuracy')) || 100,
                gamesPlayed: parseInt(localStorage.getItem('racingGamesPlayed')) || 0,
                topSpeed: parseInt(localStorage.getItem('racingGameTopSpeed')) || 0,
                currentSpeed: 0,
                showPauseModal: false,
                showGameOverModal: false,
                raceWon: false,
                phaserGame: null,
                currentQuestion: null,
                timeLeft: 60,
                correctAnswers: 0,
                totalQuestions: 0,
                carPosition: 1,
                soalId: getSoalIdFromURL(),
                databaseQuestions: [],
                currentQuestionIndex: 0,

                init() {
                    // Hide loading screen after 2.5 seconds
                    setTimeout(() => {
                        this.loading = false;
                    }, 2500);

                    // Load saved stats
                    this.loadStats();
                    
                    // Load questions from database
                    this.loadQuestionsFromDatabase();
                },

                getText(key) {
                    return translations[this.language][key] || key;
                },

                toggleLanguage() {
                    this.language = this.language === 'id' ? 'en' : 'id';
                    localStorage.setItem('racingGameLanguage', this.language);
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    localStorage.setItem('racingGameSound', this.soundEnabled);
                },

                toggleMusic() {
                    this.musicEnabled = !this.musicEnabled;
                    localStorage.setItem('racingGameMusic', this.musicEnabled);
                },

                showSettings() {
                    this.screen = 'settings';
                },

                showGarage() {
                    this.screen = 'garage';
                },

                async loadQuestionsFromDatabase() {
                    try {
                        console.log("Soal ID yang akan difetch:", this.soalId);
                        const response = await fetch(`fetch_questions.php?soal_id=${this.soalId}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        console.log('Questions loaded:', data);
                        
                        if (Array.isArray(data) && data.length > 0) {
                            this.databaseQuestions = data;
                            console.log(`Loaded ${data.length} questions from database`);
                        } else {
                            throw new Error('No questions found in database');
                        }
                    } catch (error) {
                        console.error('Error loading questions:', error);
                        // Fallback to dummy questions if database fails
                        this.generateFallbackQuestions();
                    }
                },

                generateFallbackQuestions() {
                    console.log('Using fallback questions');
                    const fallbackQuestions = [
                        {bilangan1: 5, bilangan2: 3, operator: '+', jawaban_benar: 8},
                        {bilangan1: 7, bilangan2: 4, operator: '+', jawaban_benar: 11},
                        {bilangan1: 9, bilangan2: 6, operator: '+', jawaban_benar: 15},
                        {bilangan1: 12, bilangan2: 8, operator: '+', jawaban_benar: 20},
                        {bilangan1: 15, bilangan2: 5, operator: '+', jawaban_benar: 20},
                        {bilangan1: 10, bilangan2: 3, operator: '-', jawaban_benar: 7},
                        {bilangan1: 15, bilangan2: 7, operator: '-', jawaban_benar: 8},
                        {bilangan1: 20, bilangan2: 12, operator: '-', jawaban_benar: 8}
                    ];
                    this.databaseQuestions = fallbackQuestions;
                },

                startRace() {
                    this.screen = 'race';
                    this.score = 0;
                    this.currentSpeed = 60;
                    this.carPosition = 3;
                    this.currentQuestionIndex = 0;
                    this.correctAnswers = 0;
                    this.totalQuestions = 0;
                    this.showGameOverModal = false;
                    this.gamesPlayed++;
                    this.saveStats();
                    
                    // Shuffle questions for variety
                    this.shuffleQuestions();
                    
                    this.initPhaserRaceGame();
                },

                shuffleQuestions() {
                    if (this.databaseQuestions.length > 0) {
                        // Fisher-Yates shuffle algorithm
                        for (let i = this.databaseQuestions.length - 1; i > 0; i--) {
                            const j = Math.floor(Math.random() * (i + 1));
                            [this.databaseQuestions[i], this.databaseQuestions[j]] = 
                            [this.databaseQuestions[j], this.databaseQuestions[i]];
                        }
                    }
                },

                pauseRace() {
                    this.showPauseModal = true;
                    if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                        this.phaserGame.scene.scenes[0].scene.pause();
                    }
                },

                resumeRace() {
                    this.showPauseModal = false;
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
                    if (this.phaserGame) {
                        this.phaserGame.destroy(true);
                        this.phaserGame = null;
                    }
                },

                exitToMenu() {
                    this.showGameOverModal = false;
                    this.screen = 'menu';
                    if (this.phaserGame) {
                        this.phaserGame.destroy(true);
                        this.phaserGame = null;
                    }
                },

                nextLevel() {
                    this.currentLevel++;
                    this.showGameOverModal = false;
                    this.saveStats();
                    this.startRace();
                },

                endRace(won) {
                    this.raceWon = won;
                    this.showGameOverModal = true;
                    if (won) {
                        this.playRacingSound('victory');
                        this.updateScore(500); // Bonus for completing race
                    }
                    if (this.phaserGame && this.phaserGame.scene.scenes[0]) {
                        this.phaserGame.scene.scenes[0].scene.pause();
                    }
                },

                resetProgress() {
                    if (confirm(this.getText('resetProgress') + '?')) {
                        localStorage.removeItem('racingGameLevel');
                        localStorage.removeItem('racingGameTotalScore');
                        localStorage.removeItem('racingGameAccuracy');
                        localStorage.removeItem('racingGamesPlayed');
                        localStorage.removeItem('racingGameTopSpeed');
                        this.currentLevel = 1;
                        this.totalScore = 0;
                        this.accuracy = 100;
                        this.gamesPlayed = 0;
                        this.topSpeed = 0;
                    }
                },

                loadStats() {
                    this.currentLevel = parseInt(localStorage.getItem('racingGameLevel')) || 1;
                    this.totalScore = parseInt(localStorage.getItem('racingGameTotalScore')) || 0;
                    this.accuracy = parseFloat(localStorage.getItem('racingGameAccuracy')) || 100;
                    this.gamesPlayed = parseInt(localStorage.getItem('racingGamesPlayed')) || 0;
                    this.topSpeed = parseInt(localStorage.getItem('racingGameTopSpeed')) || 0;
                },

                saveStats() {
                    localStorage.setItem('racingGameLevel', this.currentLevel);
                    localStorage.setItem('racingGameTotalScore', this.totalScore);
                    localStorage.setItem('racingGameAccuracy', this.accuracy);
                    localStorage.setItem('racingGamesPlayed', this.gamesPlayed);
                    localStorage.setItem('racingGameDifficulty', this.difficulty);
                    localStorage.setItem('racingGameTopSpeed', this.topSpeed);
                },

                updateScore(points) {
                    this.score += points;
                    this.totalScore += points;
                    this.saveStats();
                },

                updateSpeed(speedChange) {
                    this.currentSpeed = Math.max(0, Math.min(300, this.currentSpeed + speedChange));
                    if (this.currentSpeed > this.topSpeed) {
                        this.topSpeed = this.currentSpeed;
                        this.saveStats();
                    }
                },

                updateAccuracy() {
                    if (this.totalQuestions > 0) {
                        this.accuracy = (this.correctAnswers / this.totalQuestions) * 100;
                        this.saveStats();
                    }
                },

                generateRacingQuestion() {
                    // Use database questions if available
                    if (this.databaseQuestions.length > 0) {
                        const dbQuestion = this.databaseQuestions[this.currentQuestionIndex % this.databaseQuestions.length];
                        this.currentQuestionIndex++;
                        
                        // Generate wrong answers based on correct answer
                        const correctAnswer = dbQuestion.jawaban_benar;
                        const wrongAnswers = [];
                        
                        // Generate 2 wrong answers
                        while (wrongAnswers.length < 2) {
                            let wrong;
                            if (dbQuestion.operator === '×' || dbQuestion.operator === '*') {
                                wrong = correctAnswer + (Math.floor(Math.random() * 20) - 10);
                            } else {
                                wrong = correctAnswer + (Math.floor(Math.random() * 10) - 5);
                            }
                            if (wrong !== correctAnswer && wrong >= 0 && !wrongAnswers.includes(wrong)) {
                                wrongAnswers.push(wrong);
                            }
                        }
                        
                        // Shuffle answers
                        const answers = [correctAnswer, ...wrongAnswers].sort(() => Math.random() - 0.5);
                        
                        this.currentQuestion = {
                            num1: dbQuestion.bilangan1,
                            num2: dbQuestion.bilangan2,
                            operation: dbQuestion.operator,
                            correctAnswer: correctAnswer,
                            answers: answers,
                            correctIndex: answers.indexOf(correctAnswer)
                        };
                        
                        return this.currentQuestion;
                    }
                    
                    // Fallback to generated questions if no database questions
                    return this.generateFallbackQuestion();
                },

                generateFallbackQuestion() {
                    let maxNum1, maxNum2, operation;
                    
                    switch (this.difficulty) {
                        case 'rookie':
                            maxNum1 = Math.min(20, 5 + this.currentLevel * 2);
                            maxNum2 = Math.min(20, 5 + this.currentLevel * 2);
                            operation = '+';
                            break;
                        case 'pro':
                            maxNum1 = Math.min(50, 10 + this.currentLevel * 3);
                            maxNum2 = Math.min(50, 10 + this.currentLevel * 3);
                            operation = Math.random() > 0.5 ? '+' : '-';
                            break;
                        case 'champion':
                            maxNum1 = Math.min(100, 15 + this.currentLevel * 4);
                            maxNum2 = Math.min(100, 15 + this.currentLevel * 4);
                            const ops = ['+', '-', '*'];
                            operation = ops[Math.floor(Math.random() * ops.length)];
                            break;
                    }

                    let num1 = Math.floor(Math.random() * maxNum1) + 1;
                    let num2 = Math.floor(Math.random() * maxNum2) + 1;
                    let correctAnswer;

                    // Ensure subtraction doesn't result in negative numbers
                    if (operation === '-' && num2 > num1) {
                        [num1, num2] = [num2, num1];
                    }

                    switch (operation) {
                        case '+':
                            correctAnswer = num1 + num2;
                            break;
                        case '-':
                            correctAnswer = num1 - num2;
                            break;
                        case '*':
                            correctAnswer = num1 * num2;
                            break;
                    }

                    // Generate wrong answers
                    const wrongAnswers = [];
                    while (wrongAnswers.length < 2) {
                        let wrong;
                        if (operation === '*') {
                            wrong = correctAnswer + (Math.floor(Math.random() * 20) - 10);
                        } else {
                            wrong = correctAnswer + (Math.floor(Math.random() * 10) - 5);
                        }
                        if (wrong !== correctAnswer && wrong >= 0 && !wrongAnswers.includes(wrong)) {
                            wrongAnswers.push(wrong);
                        }
                    }

                    // Shuffle answers
                    const answers = [correctAnswer, ...wrongAnswers].sort(() => Math.random() - 0.5);

                    this.currentQuestion = {
                        num1,
                        num2,
                        operation,
                        correctAnswer,
                        answers,
                        correctIndex: answers.indexOf(correctAnswer)
                    };

                    return this.currentQuestion;
                },

                checkRacingAnswer(selectedAnswer) {
                    this.totalQuestions++;
                    const isCorrect = selectedAnswer === this.currentQuestion.correctAnswer;
                    
                    if (isCorrect) {
                        this.correctAnswers++;
                        this.updateScore(100 * this.currentLevel);
                        this.updateSpeed(50);
                        this.playRacingSound('boost');
                        
                        // Improve position
                        if (this.carPosition > 1) {
                            this.carPosition--;
                        }
                    } else {
                        this.updateSpeed(-30);
                        this.playRacingSound('crash');
                        
                        // Worsen position
                        if (this.carPosition < 5) {
                            this.carPosition++;
                        }
                    }
                    
                    this.updateAccuracy();
                    return isCorrect;
                },

                playRacingSound(type) {
                    if (!this.soundEnabled) return;
                    
                    try {
                        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        if (type === 'boost') {
                            // Engine acceleration sound
                            oscillator.frequency.setValueAtTime(150, audioContext.currentTime);
                            oscillator.frequency.exponentialRampToValueAtTime(400, audioContext.currentTime + 0.3);
                            gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        } else if (type === 'crash') {
                            // Brake/crash sound
                            oscillator.frequency.setValueAtTime(200, audioContext.currentTime);
                            oscillator.frequency.exponentialRampToValueAtTime(50, audioContext.currentTime + 0.4);
                            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
                        } else if (type === 'victory') {
                            // Victory fanfare
                            oscillator.frequency.setValueAtTime(523, audioContext.currentTime);
                            oscillator.frequency.setValueAtTime(659, audioContext.currentTime + 0.2);
                            oscillator.frequency.setValueAtTime(784, audioContext.currentTime + 0.4);
                            gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.6);
                        }
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.6);
                    } catch (e) {
                        console.log('Audio not supported');
                    }
                },

                initPhaserRaceGame() {
                    // Destroy existing game
                    if (this.phaserGame) {
                        this.phaserGame.destroy(true);
                    }

                    const app = this;
                    
                    // Phaser Game Configuration
                    const config = {
                        type: Phaser.AUTO,
                        width: window.innerWidth > 768 ? 800 : window.innerWidth,
                        height: window.innerHeight > 600 ? 500 : window.innerHeight - 150,
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
                        }
                    };

                    // Game variables
                    let questionText, answerButtons, timerText;
                    let timeLeft = 60;
                    let raceTimer;
                    let roadLines = [];
                    let playerCar, opponentCars = [];
                    let currentQuestion;
                    let questionCount = 0;
                    let questionsPerRace = Math.min(8, app.databaseQuestions.length || 8);
                    let background, trees = [];
                    let speedLines = [];

                    function preload() {
                        // Create car sprites
                        this.add.graphics()
                            .fillStyle(0xFF3333)
                            .fillRoundedRect(0, 0, 40, 80, 8)
                            .fillStyle(0x000000)
                            .fillCircle(8, 15, 4)
                            .fillCircle(32, 15, 4)
                            .fillCircle(8, 65, 4)
                            .fillCircle(32, 65, 4)
                            .generateTexture('player-car', 40, 80);
                            
                        this.add.graphics()
                            .fillStyle(0x3333FF)
                            .fillRoundedRect(0, 0, 40, 80, 8)
                            .fillStyle(0x000000)
                            .fillCircle(8, 15, 4)
                            .fillCircle(32, 15, 4)
                            .fillCircle(8, 65, 4)
                            .fillCircle(32, 65, 4)
                            .generateTexture('opponent-car', 40, 80);

                        // Create button textures
                        this.add.graphics()
                            .fillStyle(0xFF6B35)
                            .fillRoundedRect(0, 0, 180, 50, 12)
                            .generateTexture('answer-button', 180, 50);

                        // Create road line texture
                        this.add.graphics()
                            .fillStyle(0xFFFFFF)
                            .fillRect(0, 0, 8, 30)
                            .generateTexture('road-line', 8, 30);

                        // Create tree texture
                        this.add.graphics()
                            .fillStyle(0x8B5A3C)
                            .fillRect(0, 25, 8, 15)
                            .fillStyle(0x228B22)
                            .fillCircle(4, 20, 15)
                            .generateTexture('tree', 8, 40);
                    }

                    function create() {
                        const scene = this; // 'this' refers to the scene context here
                        
                        // Create scrolling road background
                        background = this.add.graphics();
                        background.fillStyle(0x404040);
                        background.fillRect(0, 0, config.width, config.height);
                        
                        // Add grass borders
                        background.fillStyle(0x228B22);
                        background.fillRect(0, 0, config.width * 0.2, config.height);
                        background.fillRect(config.width * 0.8, 0, config.width * 0.2, config.height);

                        // Create road lines
                        for (let i = 0; i < 10; i++) {
                            const line = this.add.image(config.width / 2, i * 60, 'road-line');
                            roadLines.push(line);
                        }

                        // Create trees
                        for (let i = 0; i < 8; i++) {
                            const leftTree = this.add.image(config.width * 0.1, i * 70, 'tree');
                            const rightTree = this.add.image(config.width * 0.9, i * 70, 'tree');
                            trees.push(leftTree, rightTree);
                        }

                        // Create speed lines
                        for (let i = 0; i < 20; i++) {
                            const speedLine = this.add.rectangle(
                                Math.random() < 0.5 ? config.width * 0.25 : config.width * 0.75,
                                Math.random() * config.height,
                                2, 20, 0xFFFFFF, 0.3
                            );
                            speedLines.push(speedLine);
                        }

                        // Create player car
                        playerCar = this.add.image(config.width / 2, config.height * 0.8, 'player-car');

                        // Create opponent cars
                        const carPositions = [config.width * 0.35, config.width * 0.5, config.width * 0.65];
                        for (let i = 0; i < 3; i++) {
                            const car = this.add.image(carPositions[i], config.height * 0.3 - i * 50, 'opponent-car');
                            car.setTint(Math.random() * 0xFFFFFF);
                            opponentCars.push(car);
                        }

                        // UI Elements
                        timerText = this.add.text(config.width / 2, 30, `⏱️ ${timeLeft}s`, {
                            fontSize: '24px',
                            fill: '#FFD600',
                            fontFamily: 'Orbitron',
                            stroke: '#000',
                            strokeThickness: 2
                        }).setOrigin(0.5, 0);

                        // Question area with racing theme
                        const questionBg = this.add.graphics();
                        questionBg.fillStyle(0x000000, 0.8);
                        questionBg.fillRoundedRect(config.width * 0.1, 60, config.width * 0.8, 100, 15);
                        questionBg.lineStyle(3, 0xFFD600);
                        questionBg.strokeRoundedRect(config.width * 0.1, 60, config.width * 0.8, 100, 15);

                        questionText = this.add.text(config.width / 2, 110, '', {
                            fontSize: window.innerWidth > 768 ? '36px' : '28px',
                            fill: '#FFFFFF',
                            fontFamily: 'Orbitron',
                            align: 'center'
                        }).setOrigin(0.5);

                        // Answer buttons with racing style
                        answerButtons = [];
                        const buttonY = config.height - 80;
                        const buttonSpacing = window.innerWidth > 768 ? 200 : config.width / 3 - 10;
                        
                        for (let i = 0; i < 3; i++) {
                            const buttonX = config.width / 2 - buttonSpacing + (i * buttonSpacing);
                            
                            const button = this.add.image(buttonX, buttonY, 'answer-button')
                                .setInteractive()
                                .setScale(window.innerWidth > 768 ? 1 : 0.8);
                                
                            const buttonText = this.add.text(buttonX, buttonY, '', {
                                fontSize: window.innerWidth > 768 ? '24px' : '18px',
                                fill: '#FFF',
                                fontFamily: 'Orbitron'
                            }).setOrigin(0.5);

                            // ============= PERUBAHAN DI SINI =============
                            button.on('pointerdown', () => selectAnswer(scene, i));
                            // ===========================================

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

                        // Start race
                        generateNewQuestion();
                        startRaceTimer();
                    }

                    function update() {
                        // Animate road
                        const roadSpeed = Math.max(2, app.currentSpeed / 30);
                        
                        roadLines.forEach(line => {
                            line.y += roadSpeed;
                            if (line.y > config.height + 30) {
                                line.y = -30;
                            }
                        });

                        // Animate trees
                        trees.forEach(tree => {
                            tree.y += roadSpeed * 0.7;
                            if (tree.y > config.height + 40) {
                                tree.y = -40;
                            }
                        });

                        // Animate speed lines
                        speedLines.forEach(line => {
                            line.y += roadSpeed * 1.5;
                            if (line.y > config.height + 20) {
                                line.y = -20;
                                line.x = Math.random() < 0.5 ? config.width * 0.25 : config.width * 0.75;
                            }
                        });

                        // Animate opponent cars
                        opponentCars.forEach((car, index) => {
                            car.y += roadSpeed * (0.8 + index * 0.1);
                            if (car.y > config.height + 40) {
                                car.y = -40 - Math.random() * 100;
                            }
                        });

                        // Update player car position based on performance
                        const targetX = config.width * (0.3 + (5 - app.carPosition) * 0.1);
                        playerCar.x += (targetX - playerCar.x) * 0.05;
                    }

                    function generateNewQuestion() {
                        currentQuestion = app.generateRacingQuestion();
                        if (!currentQuestion) return; // Guard against no questions
                        const operatorSymbol = currentQuestion.operation;
                        questionText.setText(`${currentQuestion.num1} ${operatorSymbol} ${currentQuestion.num2} = ?`);
                        
                        // Update answer buttons
                        currentQuestion.answers.forEach((answer, index) => {
                            answerButtons[index].text.setText(answer.toString());
                        });
                    }

                    // ============= PERUBAHAN DI SINI =============
                    function selectAnswer(scene, buttonIndex) {
                        if (!currentQuestion) return; // Guard clause
                        const selectedAnswer = currentQuestion.answers[buttonIndex];
                        const isCorrect = app.checkRacingAnswer(selectedAnswer);
                        
                        // Visual feedback
                        const button = answerButtons[buttonIndex].button;
                        
                        if (isCorrect) {
                            button.setTint(0x00FF00);
                            
                            // Boost effect
                            scene.tweens.add({ // Menggunakan 'scene' dari parameter
                                targets: playerCar,
                                scaleX: 1.2,
                                scaleY: 1.2,
                                duration: 200,
                                yoyo: true
                            });
                            
                            // Show boost message
                            const boostText = scene.add.text(config.width / 2, config.height / 2, app.getText('correct'), {
                                fontSize: '28px',
                                fill: '#00FF00',
                                fontFamily: 'Orbitron',
                                stroke: '#000',
                                strokeThickness: 2
                            }).setOrigin(0.5);
                            
                            scene.tweens.add({ // Menggunakan 'scene' dari parameter
                                targets: boostText,
                                alpha: 0,
                                y: boostText.y - 50,
                                duration: 1500,
                                onComplete: () => boostText.destroy()
                            });
                        } else {
                            button.setTint(0xFF0000);
                            
                            // Show correct answer
                            const correctIndex = currentQuestion.correctIndex;
                            answerButtons[correctIndex].button.setTint(0x00FF00);
                            
                            // Crash effect
                            scene.tweens.add({ // Menggunakan 'scene' dari parameter
                                targets: playerCar,
                                angle: -10,
                                duration: 100,
                                yoyo: true,
                                repeat: 2,
                                onComplete: () => { playerCar.angle = 0; }
                            });
                            
                            // Show wrong message
                            const wrongText = scene.add.text(config.width / 2, config.height / 2, app.getText('wrong'), {
                                fontSize: '28px',
                                fill: '#FF3333',
                                fontFamily: 'Orbitron',
                                stroke: '#000',
                                strokeThickness: 2
                            }).setOrigin(0.5);
                            
                            scene.tweens.add({ // Menggunakan 'scene' dari parameter
                                targets: wrongText,
                                alpha: 0,
                                y: wrongText.y - 50,
                                duration: 1500,
                                onComplete: () => wrongText.destroy()
                            });
                        }
                        
                        // Disable buttons to prevent multiple clicks
                        answerButtons.forEach(btn => btn.button.disableInteractive());
                        
                        // Generate next question after delay
                        scene.time.delayedCall(2000, () => {
                             // Re-enable buttons
                            answerButtons.forEach(btn => btn.button.setInteractive().clearTint());
                            
                            questionCount++;
                            if (questionCount >= questionsPerRace) {
                                // End race based on performance
                                const winRate = app.totalQuestions > 0 ? (app.correctAnswers / app.totalQuestions) : 0;
                                const speedRequirement = app.currentSpeed >= 100;
                                const raceWon = winRate >= 0.6 && speedRequirement;
                                app.endRace(raceWon);
                            } else {
                                generateNewQuestion();
                            }
                        });
                    }
                    // ===========================================

                    function startRaceTimer() {
                        timeLeft = 60; // Reset timer
                        raceTimer = setInterval(() => {
                            timeLeft--;
                            timerText.setText(`⏱️ ${timeLeft}s`);
                            
                            if (timeLeft <= 0) {
                                clearInterval(raceTimer);
                                raceTimer = null;
                                // Time's up - end race based on current performance
                                const winRate = app.totalQuestions > 0 ? (app.correctAnswers / app.totalQuestions) : 0;
                                const raceWon = winRate >= 0.5 && app.currentSpeed >= 80;
                                app.endRace(raceWon);
                            }
                        }, 1000);
                    }

                    // Initialize the game
                    this.phaserGame = new Phaser.Game(config);
                }
            }
        }

        // Initialize app when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen
            setTimeout(() => {
                const loadingScreen = document.getElementById('loading');
                if (loadingScreen) {
                    loadingScreen.style.display = 'none';
                }
            }, 2500);
        });

        // Service Worker Registration for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('data:text/javascript;base64,c2VsZi5hZGRFdmVudExpc3RlbmVyKCdpbnN0YWxsJywgZnVuY3Rpb24oZXZlbnQpIHsKICBjb25zb2xlLmxvZygnU2VydmljZSBXb3JrZXIgaW5zdGFsbGVkJyk7Cn0pOwoKc2VsZi5hZGRFdmVudExpc3RlbmVyKCdmZXRjaCcsIGZ1bmN0aW9uKGV2ZW50KSB7CiAgZXZlbnQucmVzcG9uZFdpdGgoCiAgICBjYWNoZXMubWF0Y2goZXZlbnQucmVxdWVzdCkKICAgICAgLnRoZW4oZnVuY3Rpb24ocmVzcG9uc2UpIHsKICAgICAgICByZXR1cm4gcmVzcG9uc2UgfHwgZmV0Y2goZXZlbnQucmVxdWVzdCk7CiAgICAgIH0pCiAgKTsKfSk7')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed');
                });
            });
        }

        // Prevent zoom on iOS
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        // Prevent context menu on long press
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Handle window resize for responsive game
        window.addEventListener('resize', function() {
            if (window.racingGameApp && window.racingGameApp.phaserGame) {
                window.racingGameApp.phaserGame.scale.resize(
                    window.innerWidth > 768 ? 800 : window.innerWidth,
                    window.innerHeight > 600 ? 500 : window.innerHeight - 150
                );
            }
        });
    </script>
</body>
</html>