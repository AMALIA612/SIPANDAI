<!-- ================= FLOATING CHATBOT WIDGET (SI PANDAI AI) ================= -->
<div id="sipandai-chatbot-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end no-print font-sans">

    <!-- JENDELA CHAT -->
    <div id="chatbot-window" 
         class="hidden flex-col w-[350px] sm:w-[390px] h-[520px] max-h-[80vh] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transition-all duration-300 transform origin-bottom-right mb-4">
        
        <!-- HEADER CHATBOT -->
        <div class="bg-gradient-to-r from-pln-dark to-pln-navy px-4 py-3.5 flex items-center justify-between text-white shadow-md">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-400 to-amber-300 flex items-center justify-center text-pln-dark shadow-inner">
                        <i class="fa-solid fa-robot text-lg"></i>
                    </div>
                    <!-- Indicator Online -->
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-400 border-2 border-pln-navy rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center space-x-1.5">
                        <h4 class="font-bold text-sm tracking-wide">Si Pandai AI</h4>
                    </div>
                    <p class="text-[11px] text-slate-300 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Online &bull; Sarpras PLN UPT Gandul
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Header -->
            <div class="flex items-center space-x-1">
                <button type="button" 
                        onclick="clearChatbotMessages()" 
                        title="Bersihkan Percakapan"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors text-xs">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button type="button" 
                        onclick="toggleChatbot()" 
                        title="Tutup Chat"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors text-xs">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- QUICK CHIPS / REKOMENDASI PERTANYAAN -->
        <div class="bg-slate-100 border-b border-slate-200 px-3 py-2 flex items-center gap-1.5 overflow-x-auto text-[11px] scrollbar-none">
            <button type="button" onclick="sendQuickPrompt('Status tiket saya')" 
                    class="shrink-0 bg-white border border-slate-300 hover:border-pln-blue hover:text-pln-blue px-2.5 py-1 rounded-full text-slate-700 transition font-medium">
                🎫 Tiket Saya
            </button>
            <button type="button" onclick="sendQuickPrompt('AC di ruangan tidak dingin / bocor')" 
                    class="shrink-0 bg-white border border-slate-300 hover:border-pln-blue hover:text-pln-blue px-2.5 py-1 rounded-full text-slate-700 transition font-medium">
                ❄️ Lapor AC
            </button>
            <button type="button" onclick="sendQuickPrompt('Lampu padam / stop kontak bermasalah')" 
                    class="shrink-0 bg-white border border-slate-300 hover:border-pln-blue hover:text-pln-blue px-2.5 py-1 rounded-full text-slate-700 transition font-medium">
                💡 Listrik & Lampu
            </button>
            <button type="button" onclick="sendQuickPrompt('Bagaimana alur dan SOP pengaduan sarana?')" 
                    class="shrink-0 bg-white border border-slate-300 hover:border-pln-blue hover:text-pln-blue px-2.5 py-1 rounded-full text-slate-700 transition font-medium">
                📋 SOP Lapor
            </button>
        </div>

        <!-- AREA PERCAKAPAN (MESSAGES) -->
        <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 text-[13px] leading-relaxed">
            <!-- Pesan Selamat Datang Awal -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-pln-navy text-amber-400 flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2.5 shadow-sm max-w-[85%]">
                    <p class="font-semibold text-pln-navy mb-1 text-[12px]">Halo, {{ Auth::user()->name }}! 👋</p>
                    <p class="text-slate-700">
                        Saya <strong>Si Pandai AI</strong>, siap membantu informasi sarana prasarana kantor PLN UPT Gandul.
                    </p>
                    <p class="text-slate-500 text-[11px] mt-1.5 border-t border-slate-100 pt-1.5">
                        Anda bisa tanyakan status tiket, panduan lapor kerusakan fasilitas, maupun SOP perbaikan.
                    </p>
                </div>
            </div>
        </div>

        <!-- TYPING INDICATOR (Tersembunyi by default) -->
        <div id="chatbot-typing" class="hidden px-4 py-1.5 bg-slate-50 items-center gap-2 text-xs text-slate-500">
            <div class="w-6 h-6 rounded-full bg-pln-navy/80 text-amber-300 flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="flex items-center space-x-1 bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm">
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                <span class="text-[11px] text-slate-400 ml-1.5 font-medium">Mengetik...</span>
            </div>
        </div>

        <!-- FORM INPUT CHAT -->
        <div class="bg-white border-t border-slate-200 p-3">
            <form id="chatbot-form" onsubmit="handleChatbotSubmit(event)" class="flex items-center gap-2">
                <input type="text" 
                       id="chatbot-input" 
                       placeholder="Ketik pertanyaan atau nomor tiket..." 
                       autocomplete="off"
                       maxlength="500"
                       class="flex-1 px-3.5 py-2 text-sm bg-slate-100 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pln-blue/50 focus:bg-white transition text-slate-800 placeholder-slate-400">
                <button type="submit" 
                        id="chatbot-submit-btn"
                        title="Kirim Pesan"
                        class="bg-pln-blue hover:bg-pln-hover active:scale-95 text-white w-9 h-9 rounded-xl flex items-center justify-center transition shadow-sm shrink-0">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
            <div class="flex items-center justify-between mt-1.5 px-1 text-[10px] text-slate-400">
            </div>
        </div>

    </div>

    <!-- TOMBOL PEMICU MENGAMBANG (FLOATING LAUNCHER BUTTON) -->
    <button type="button" 
            id="chatbot-launcher-btn"
            onclick="toggleChatbot()" 
            aria-label="Buka Asisten Virtual Si Pandai"
            class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-tr from-pln-dark via-pln-navy to-pln-blue text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300">
        
        <!-- Ikon Default (Robot) -->
        <span id="launcher-icon-open" class="text-xl group-hover:rotate-12 transition-transform duration-300">
            <i class="fa-solid fa-robot"></i>
        </span>
        
        <!-- Ikon Close (X) saat terbuka -->
        <span id="launcher-icon-close" class="hidden text-xl">
            <i class="fa-solid fa-xmark"></i>
        </span>

        <!-- Badge AI Kuning Berkilau -->
        <span class="absolute -top-1 -right-1 bg-amber-400 text-pln-dark text-[10px] font-extrabold px-1.5 py-0.2 rounded-full border-2 border-white shadow-sm flex items-center gap-0.5">
            <i class="fa-solid fa-sparkles text-[8px]"></i>AI
        </span>

        <!-- Tooltip Hover Halus -->
        <span class="absolute right-16 bg-slate-900/90 backdrop-blur-sm text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg font-medium">
            Tanya Si Pandai AI
        </span>
    </button>

</div>

<!-- JAVASCRIPT LOGIC CHATBOT -->
<script>
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotTyping = document.getElementById('chatbot-typing');
    const launcherIconOpen = document.getElementById('launcher-icon-open');
    const launcherIconClose = document.getElementById('launcher-icon-close');
    const chatbotSubmitBtn = document.getElementById('chatbot-submit-btn');

    let chatHistory = [];
    let isChatOpen = false;

    // Toggle Tampilkan / Sembunyikan Chatbot
    function toggleChatbot() {
        isChatOpen = !isChatOpen;
        if (isChatOpen) {
            chatbotWindow.classList.remove('hidden');
            chatbotWindow.classList.add('flex');
            launcherIconOpen.classList.add('hidden');
            launcherIconClose.classList.remove('hidden');
            chatbotInput.focus();
            scrollChatToBottom();
        } else {
            chatbotWindow.classList.add('hidden');
            chatbotWindow.classList.remove('flex');
            launcherIconOpen.classList.remove('hidden');
            launcherIconClose.classList.add('hidden');
        }
    }

    // Shortcut tombol Escape untuk menutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isChatOpen) {
            toggleChatbot();
        }
    });

    // Kirim Pertanyaan Cepat dari Chips
    function sendQuickPrompt(promptText) {
        chatbotInput.value = promptText;
        handleChatbotSubmit(new Event('submit'));
    }

    // Scroll otomatis ke dasar percakapan
    function scrollChatToBottom() {
        if (chatbotMessages) {
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }
    }

    // Helper: Konversi Markdown Sederhana (bold, list, link) ke HTML aman
    function parseSimpleMarkdown(text) {
        let escaped = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Format [Link Text](url)
        escaped = escaped.replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+|\/[^\)]+)\)/g, function(match, label, url) {
            return `<a href="${url}" class="text-pln-blue font-semibold underline hover:text-pln-hover" target="_blank" rel="noopener noreferrer">${label}</a>`;
        });

        // Format **Bold**
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

        // Format baris baru
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }

    // Tambahkan Bubble Pesan ke Layar
    function appendMessage(sender, text) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2.5';

        if (sender === 'user') {
            wrapper.className += ' justify-end';
            wrapper.innerHTML = `
                <div class="bg-pln-blue text-white rounded-2xl rounded-tr-none px-3.5 py-2.5 shadow-sm max-w-[85%] text-left">
                    <p class="text-slate-50">${parseSimpleMarkdown(text)}</p>
                </div>
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fa-solid fa-user"></i>
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-pln-navy text-amber-400 flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2.5 shadow-sm max-w-[85%]">
                    <div class="text-slate-700">${parseSimpleMarkdown(text)}</div>
                </div>
            `;
        }

        chatbotMessages.appendChild(wrapper);
        scrollChatToBottom();
    }

    // Handle Submit Pesan
    async function handleChatbotSubmit(event) {
        if (event) event.preventDefault();
        
        const message = chatbotInput.value.trim();
        if (!message) return;

        // Tampilkan pesan pengguna
        appendMessage('user', message);
        chatbotInput.value = '';
        chatbotInput.disabled = true;
        chatbotSubmitBtn.disabled = true;

        // Tampilkan indikator mengetik
        chatbotTyping.classList.remove('hidden');
        chatbotTyping.classList.add('flex');
        scrollChatToBottom();

        // Rekam ke history
        chatHistory.push({ role: 'user', content: message });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]') 
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                : '{{ csrf_token() }}';

            const response = await fetch('{{ route("chatbot.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory.slice(-4)
                })
            });

            const data = await response.json();

            // Sembunyikan indikator mengetik
            chatbotTyping.classList.add('hidden');
            chatbotTyping.classList.remove('flex');

            if (data.status === 'success' && data.reply) {
                appendMessage('bot', data.reply);
                chatHistory.push({ role: 'assistant', content: data.reply });
            } else {
                appendMessage('bot', 'Maaf, sistem sedang mengalami kendala sementara. Anda dapat menanyakan langsung nomor tiket Anda atau hubungi admin sarpras.');
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            chatbotTyping.classList.add('hidden');
            chatbotTyping.classList.remove('flex');
            appendMessage('bot', 'Maaf, terjadi gangguan koneksi ke server. Silakan coba kembali sesaat lagi.');
        } finally {
            chatbotInput.disabled = false;
            chatbotSubmitBtn.disabled = false;
            chatbotInput.focus();
            scrollChatToBottom();
        }
    }

    // Bersihkan Pesan
    function clearChatbotMessages() {
        chatHistory = [];
        chatbotMessages.innerHTML = `
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-pln-navy text-amber-400 flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2.5 shadow-sm max-w-[85%]">
                    <p class="font-semibold text-pln-navy mb-1 text-[12px]">Halo, {{ Auth::user()->name }}! 👋</p>
                    <p class="text-slate-700">Percakapan telah direset. Silakan tanyakan hal yang ingin Anda ketahui seputar sarana prasarana PLN UPT Gandul.</p>
                </div>
            </div>
        `;
        chatbotInput.value = '';
        chatbotInput.focus();
    }
</script>
<!-- ================= END FLOATING CHATBOT WIDGET ================= -->

