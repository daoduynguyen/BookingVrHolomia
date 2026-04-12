{{-- ============================================================
     Holomia VR — Chat bubble widget (global)
     Include trong layouts/app.blade.php trước thẻ đóng </body>

     @include('partials.chatbot-widget')
     ============================================================ --}}

<style>
.holo-bubble {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #534AB7;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9999;
    transition: transform 0.15s, background 0.15s;
    box-shadow: 0 4px 16px rgba(83,74,183,0.35);
}
.holo-bubble:hover { background: #3C3489; transform: scale(1.07); }
.holo-bubble svg { width: 26px; height: 26px; }

.holo-unread {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #E24B4A;
    border: 2px solid #fff;
    font-size: 10px;
    font-weight: 600;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    display: none;
}

.holo-panel {
    position: fixed;
    bottom: 96px;
    right: 28px;
    width: 340px;
    max-width: calc(100vw - 40px);
    background: #fff;
    border-radius: 18px;
    border: 0.5px solid rgba(0,0,0,0.1);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    transition: opacity 0.2s, transform 0.2s;
}
.holo-panel.holo-hidden {
    opacity: 0;
    pointer-events: none;
    transform: translateY(14px);
}
@media (max-width: 480px) {
    .holo-panel {
        bottom: 0; right: 0;
        width: 100vw; max-width: 100vw;
        border-radius: 18px 18px 0 0;
    }
    .holo-bubble { bottom: 16px; right: 16px; }
}

.holo-header {
    background: #534AB7;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.holo-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: #AFA9EC;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 15px; color: #26215C;
    flex-shrink: 0;
}
.holo-header-name { font-size: 14px; font-weight: 600; color: #fff; }
.holo-header-sub  { font-size: 11px; color: #CECBF6; display: flex; align-items: center; gap: 4px; }
.holo-online-dot  { width: 7px; height: 7px; border-radius: 50%; background: #5DCAA5; display: inline-block; }
.holo-close-btn {
    margin-left: auto;
    color: #CECBF6;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
    padding: 0 4px;
    background: none;
    border: none;
    transition: color 0.1s;
}
.holo-close-btn:hover { color: #fff; }

.holo-messages {
    padding: 14px 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    height: 280px;
    overflow-y: auto;
    scroll-behavior: smooth;
    flex-shrink: 0;
}
.holo-messages::-webkit-scrollbar { width: 4px; }
.holo-messages::-webkit-scrollbar-thumb { background: #D3D1C7; border-radius: 4px; }

.holo-msg {
    max-width: 85%;
    font-size: 13px;
    line-height: 1.55;
    padding: 9px 13px;
    border-radius: 14px;
    word-wrap: break-word;
}
.holo-msg.bot {
    background: #F1EFE8;
    color: #2C2C2A;
    border-bottom-left-radius: 4px;
    align-self: flex-start;
}
.holo-msg.user {
    background: #534AB7;
    color: #fff;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
}
.holo-msg.typing {
    color: #888780;
    font-style: italic;
    background: #F1EFE8;
    align-self: flex-start;
}

.holo-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 0 12px 10px;
    flex-shrink: 0;
}
.holo-sug-btn {
    font-size: 11.5px;
    padding: 5px 11px;
    border-radius: 20px;
    border: 0.5px solid #D3D1C7;
    background: #fff;
    color: #5F5E5A;
    cursor: pointer;
    transition: background 0.1s, border-color 0.1s;
    white-space: nowrap;
}
.holo-sug-btn:hover { background: #F1EFE8; border-color: #7F77DD; color: #534AB7; }

.holo-input-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-top: 0.5px solid #E8E6DF;
    flex-shrink: 0;
}
.holo-input {
    flex: 1;
    font-size: 13px;
    border: 0.5px solid #D3D1C7;
    border-radius: 20px;
    padding: 8px 14px;
    background: #F8F7F4;
    color: #2C2C2A;
    outline: none;
    transition: border-color 0.15s;
}
.holo-input:focus { border-color: #7F77DD; background: #fff; }
.holo-input::placeholder { color: #B4B2A9; }
.holo-send-btn {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #534AB7;
    border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s;
}
.holo-send-btn:hover { background: #3C3489; }
.holo-send-btn:disabled { background: #D3D1C7; cursor: default; }
.holo-send-btn svg { width: 15px; height: 15px; }
</style>

{{-- Bubble button --}}
<div class="holo-bubble" id="holoBubble" onclick="holoToggle()" title="Chat với Holo">
    <span class="holo-unread" id="holoUnread">1</span>
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C6.48 2 2 6.1 2 11.1c0 2.8 1.4 5.3 3.6 7L4 22l4.3-1.4c1.2.4 2.4.6 3.7.6 5.52 0 10-4.1 10-9.1S17.52 2 12 2Z" fill="#fff"/>
    </svg>
</div>

{{-- Chat panel --}}
<div class="holo-panel holo-hidden" id="holoPanel">
    <div class="holo-header">
        <div class="holo-avatar">H</div>
        <div>
            <div class="holo-header-name">Holo</div>
            <div class="holo-header-sub">
                <span class="holo-online-dot"></span> Tư vấn Holomia VR
            </div>
        </div>
        <button class="holo-close-btn" onclick="holoToggle()">×</button>
    </div>

    <div class="holo-messages" id="holoMessages">
        <div class="holo-msg bot">
            Xin chào! Em là <strong>Holo</strong> 👋<br>
            Bạn cần tư vấn gì về Holomia VR không ạ? Em có thể giúp bạn tìm game, kiểm tra chỗ trống hoặc đặt chỗ!
        </div>
    </div>

    <div class="holo-suggestions" id="holoSuggestions">
        <button class="holo-sug-btn" onclick="holoAsk('Game nào được chơi nhiều nhất?')">Game phổ biến nhất</button>
        <button class="holo-sug-btn" onclick="holoAsk('Còn chỗ trống hôm nay không?')">Còn chỗ hôm nay?</button>
        <button class="holo-sug-btn" onclick="holoAsk('Giá vé bao nhiêu?')">Giá vé</button>
        <button class="holo-sug-btn" onclick="holoAsk('Có thể đặt chỗ cho nhóm không?')">Đặt nhóm</button>
    </div>

    <div class="holo-input-row">
        <input
            class="holo-input"
            id="holoInput"
            placeholder="Nhập câu hỏi..."
            maxlength="300"
            onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); holoSubmit(); }"
        >
        <button class="holo-send-btn" id="holoSendBtn" onclick="holoSubmit()">
            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 8L14.5 1.5L10 8L14.5 14.5L1.5 8Z" fill="#fff"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function () {
    var isOpen   = false;
    var isLoading = false;
    var chatUrl  = '{{ route("ai.chat") }}';
    var csrfToken = '{{ csrf_token() }}';

    window.holoToggle = function () {
        isOpen = !isOpen;
        var panel = document.getElementById('holoPanel');
        var badge = document.getElementById('holoUnread');
        panel.classList.toggle('holo-hidden', !isOpen);
        if (isOpen) {
            badge.style.display = 'none';
            document.getElementById('holoInput').focus();
            scrollBottom();
        }
    };

    window.holoAsk = function (question) {
        document.getElementById('holoSuggestions').style.display = 'none';
        holoSendMessage(question);
    };

    window.holoSubmit = function () {
        var inp = document.getElementById('holoInput');
        var q   = inp.value.trim();
        if (!q || isLoading) return;
        inp.value = '';
        document.getElementById('holoSuggestions').style.display = 'none';
        holoSendMessage(q);
    };

    function holoSendMessage(question) {
        if (isLoading) return;
        isLoading = true;

        addMsg(question, 'user');

        var typingEl = addMsg('Holo đang trả lời...', 'typing');
        setBtnState(true);

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':  csrfToken,
                'Accept':        'application/json'
            },
            body: JSON.stringify({ message: question })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            typingEl.remove();
            addMsg(data.reply || 'Xin lỗi, em chưa trả lời được lúc này. Bạn thử lại nhé!', 'bot');
        })
        .catch(function () {
            typingEl.remove();
            addMsg('Kết nối lỗi, bạn thử lại sau nhé! 🙏', 'bot');
        })
        .finally(function () {
            isLoading = false;
            setBtnState(false);
        });
    }

    function addMsg(text, type) {
        var wrap = document.getElementById('holoMessages');
        var div  = document.createElement('div');
        div.className = 'holo-msg ' + type;
        div.innerHTML  = text;
        wrap.appendChild(div);
        scrollBottom();
        return div;
    }

    function scrollBottom() {
        var wrap = document.getElementById('holoMessages');
        wrap.scrollTop = wrap.scrollHeight;
    }

    function setBtnState(loading) {
        var btn = document.getElementById('holoSendBtn');
        btn.disabled = loading;
        var inp = document.getElementById('holoInput');
        inp.disabled = loading;
        inp.placeholder = loading ? 'Đang trả lời...' : 'Nhập câu hỏi...';
    }

    // Hiện badge sau 3 giây nếu chưa mở
    setTimeout(function () {
        if (!isOpen) {
            document.getElementById('holoUnread').style.display = 'flex';
        }
    }, 3000);
})();
</script>