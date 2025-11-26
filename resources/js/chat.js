import Echo from 'laravel-echo';
import { EmojiButton } from '@joeattardi/emoji-button'; // Vite import

document.addEventListener("DOMContentLoaded", () => {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        host: `${import.meta.env.VITE_REVERB_HOST}:${import.meta.env.VITE_REVERB_PORT}`,
        scheme: import.meta.env.VITE_REVERB_SCHEME
    });

    const container = document.getElementById('chatContainer');
    if (!container) return;

    const messagesDiv = document.getElementById('messages');
    const chatHeader = document.getElementById('chatHeader');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const voiceRecordBtn = document.getElementById('voiceRecordBtn');
    const emojiBtn = document.getElementById('emojiBtn');
    const attachInput = document.getElementById('attachInput');
    const filePreview = document.getElementById('filePreview');

    const userId = parseInt(container.dataset.userId);
    let receiverId = parseInt(container.dataset.receiverId) || null;
    const chatSendRoute = container.dataset.chatSendRoute;
    const chatSendAudioRoute = container.dataset.chatSendAudioRoute;
    const csrfToken = container.dataset.csrf;

    let currentChannel = null;

    const chatBadge = document.getElementById('chatUnreadBadge');
    let unreadCount = chatBadge ? parseInt(chatBadge.dataset.count || '0', 10) : 0;

    function updateChatBadge() {
        if (!chatBadge) return;

        if (unreadCount > 0) {
            chatBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            chatBadge.classList.remove('hidden');
            chatBadge.classList.add('flex');
        } else {
            chatBadge.classList.add('hidden');
            chatBadge.classList.remove('flex');
        }
    }
    updateChatBadge();

    function renderChatMessage(chat) {
        const wrapper = document.createElement('div');
        wrapper.classList.add(
            "message-wrapper",
            "relative",
            "group",
            "flex",
            chat.sender_id === userId ? "justify-end" : "justify-start"
        );

        const bubble = `
        <div class="${chat.sender_id === userId ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} 
            px-4 py-2 rounded-lg max-w-xs break-words relative">

            ${chat.message ?? ''}

            ${chat.file ? (
                ['png','jpg','jpeg','gif'].includes(chat.file.split('.').pop().toLowerCase())
                ? `<img src="/storage/${chat.file}" class="mt-2 max-w-xs rounded">`
                : `<a href="/storage/${chat.file}" class="mt-2 block underline text-blue-600">📎 File</a>`
            ) : ''}

            ${chat.audio ? `<audio class="mt-2" controls src="/storage/${chat.audio}"></audio>` : ''}

            <div class="text-xs text-gray-300 mt-1 text-right">
                ${new Date(chat.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
            </div>
        </div>
    `;

        wrapper.innerHTML = bubble;

        // --- THREE DOTS BUTTON ---
        const menuBtn = document.createElement("button");
        menuBtn.innerHTML = "•••";
        menuBtn.classList.add(
            "msg-menu-btn",
            "hidden",
            "group-hover:flex",
            "absolute",
            chat.sender_id === userId ? "-left-6" : "-right-6",
            "top-2",
            "text-gray-400",
            "hover:text-gray-600",
            "cursor-pointer"
        );

        // --- ACTION MENU ---
        const menuBox = document.createElement("div");
        menuBox.classList.add(
            "msg-menu",
            "hidden",
            "absolute",
            chat.sender_id === userId ? "-left-20" : "-right-20",
            "top-6",
            "bg-white",
            "shadow-md",
            "rounded",
            "p-2",
            "text-sm",
            "z-10"
        );
        menuBox.innerHTML = `
        <button class="delete-msg text-red-600 hover:text-red-800" data-id="${chat.id}">
            Delete
        </button>
    `;

        menuBtn.addEventListener("click", () => {
            menuBox.classList.toggle("hidden");
        });

        wrapper.appendChild(menuBtn);
        wrapper.appendChild(menuBox);

        messagesDiv.appendChild(wrapper);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }


    try {
        JSON.parse(container.dataset.chats).forEach(renderChatMessage);
    } catch {}

    // --- Listen to channel ---
    function listenChannel() {
        if (!receiverId) return;
        if (currentChannel) window.Echo.leave(currentChannel);

        const low = Math.min(userId, receiverId);
        const high = Math.max(userId, receiverId);
        currentChannel = `chat.${low}.${high}`;

        window.Echo.channel(currentChannel).listen("NewChatMessage", (e) => {
            if (e.chat.sender_id === userId) return;
            renderChatMessage(e.chat);
            // NOTE: these messages are for the *currently open* conversation,
            // so we treat them as read immediately and do NOT change unreadCount here.
        });
    }

    if (receiverId) listenChannel();

    sendBtn.addEventListener('click', async () => {
        const message = messageInput.value.trim();

        // Need at least a receiver and either message or file
        if (!receiverId) return;
        if (!message && !selectedFile) return;

        try {
            let chat;

            if (selectedFile) {
                // --- SEND FILE + OPTIONAL MESSAGE ---
                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('receiver_id', receiverId);
                formData.append('message', message || '');

                // use whatever your file route is
                const res = await fetch('/chat/send-file', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!res.ok) throw new Error('Failed to send file');

                chat = await res.json();

                // reset file state + preview
                selectedFile = null;
                attachInput.value = '';
                if (filePreview) filePreview.innerHTML = '';
            } else {
                // --- SEND NORMAL TEXT MESSAGE ---
                const res = await fetch(chatSendRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({ message, receiver_id: receiverId })
                });

                if (!res.ok) throw new Error("Failed to send message");

                chat = await res.json();
            }

            renderChatMessage(chat);
            messageInput.value = '';
            messageInput.focus();
        } catch (err) {
            console.error(err);
        }
    });


    messageInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            sendBtn.click();
        }
    });

    // --- Switch chat user ---
    document.querySelectorAll('.chat-user-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            receiverId = parseInt(btn.dataset.userId);

            // 🔹 Mark this conversation as "read" in the UI
            const nameSpan = btn.querySelector('.chat-user-name');
            const previewSpan = btn.querySelector('.chat-user-preview');
            const dot = btn.querySelector('.chat-unread-dot');

            if (nameSpan) {
                nameSpan.classList.remove('font-semibold', 'text-gray-900');
                nameSpan.classList.add('font-medium', 'text-gray-700');
            }

            if (previewSpan) {
                previewSpan.classList.remove('font-semibold', 'text-gray-900');
                previewSpan.classList.add('text-gray-500');
            }

            if (dot) {
                dot.remove();
            }

            // existing logic
            messagesDiv.innerHTML = '';
            chatHeader.innerText = "Loading...";

            try {
                const res = await fetch(`/chat/${receiverId}`, { headers: { "Accept": "application/json" } });
                const data = await res.json();

                chatHeader.innerText = "Chat with " + (data.receiverName || "Unknown");
                data.messages.forEach(renderChatMessage);
                listenChannel();
            } catch (err) {
                console.error(err);
                chatHeader.innerText = "Chat with Unknown";
            }
        });
    });

    let recorder;
    let audioChunks = [];

    voiceRecordBtn.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            recorder = new MediaRecorder(stream);
            recorder.start();
            audioChunks = [];

            recorder.ondataavailable = event => audioChunks.push(event.data);

            recorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const formData = new FormData();
                formData.append('audio', audioBlob);
                formData.append('receiver_id', receiverId);

                const response = await fetch(chatSendAudioRoute, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!response.ok) {
                    console.error('Error sending audio message');
                    return;
                }

                const newMsg = await response.json();
                renderChatMessage(newMsg);
            };

            setTimeout(() => recorder.stop(), 5000);
        } catch (err) {
            console.error('Microphone access denied or error:', err);
        }
    });

    const picker = new EmojiButton({
        position: 'bottom-end',
        theme: 'light',
        autoHide: false
    });

    emojiBtn.addEventListener('click', () => picker.togglePicker(emojiBtn));

    picker.on('emoji', emoji => {
        messageInput.value += emoji;
    });

    let selectedFile = null;

    attachInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;

        selectedFile = file;

        if (filePreview) filePreview.innerHTML = '';

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'h-20 rounded border';
            if (filePreview) filePreview.appendChild(img);
        } else {
            const fileDiv = document.createElement('div');
            fileDiv.classList.add('flex', 'items-center', 'gap-2', 'border', 'rounded', 'px-2', 'py-1');
            fileDiv.innerHTML = `📎 <span class="text-sm">${file.name}</span>`;
            if (filePreview) filePreview.appendChild(fileDiv);
        }

        const removeBtn = document.createElement('button');
        removeBtn.textContent = '✖';
        removeBtn.classList.add('ml-2', 'text-red-500', 'hover:text-red-700');
        removeBtn.addEventListener('click', () => {
            selectedFile = null;
            attachInput.value = '';
            if (filePreview) filePreview.innerHTML = '';
        });
        if (filePreview) filePreview.appendChild(removeBtn);
    });
});
