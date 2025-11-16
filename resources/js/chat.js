import Echo from 'laravel-echo';
import { EmojiButton } from '@joeattardi/emoji-button'; // Vite import

document.addEventListener("DOMContentLoaded", () => {
    // --- Initialize Reverb Echo ---
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

    const userId = parseInt(container.dataset.userId);
    let receiverId = parseInt(container.dataset.receiverId) || null;
    const chatSendRoute = container.dataset.chatSendRoute;
    const chatSendAudioRoute = container.dataset.chatSendAudioRoute;
    const csrfToken = container.dataset.csrf;

    let currentChannel = null;

    // --- Render message safely ---
    function renderChatMessage(chat) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('flex', chat.sender_id === userId ? 'justify-end' : 'justify-start');

    let content = chat.message || '';
    if (chat.file) {
        const ext = chat.file.split('.').pop().toLowerCase();
        if (['png','jpg','jpeg','gif'].includes(ext)) {
            content += `<img src="/storage/${chat.file}" class="mt-2 max-w-xs rounded">`;
        } else {
            content += `<a href="/storage/${chat.file}" target="_blank" class="block mt-2 text-blue-600 underline">📎 ${chat.file.split('/').pop()}</a>`;
        }
    }

    if (chat.audio) {
        content += `<audio controls class="mt-2" src="/storage/${chat.audio}"></audio>`;
    }

    msgDiv.innerHTML = `
        <div class="${chat.sender_id === userId ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} 
            px-4 py-2 rounded-lg max-w-xs break-words">
            ${content}
            <div class="text-xs text-gray-400 mt-1 text-right">
                ${new Date(chat.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
            </div>
        </div>
    `;
    messagesDiv.appendChild(msgDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

    // --- Initial render ---
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
            renderChatMessage(e.chat);
        });
    }

    if (receiverId) listenChannel();

    // --- Send text message ---
    sendBtn.addEventListener('click', async () => {
        let message = messageInput.value.trim();
        if (!message || !receiverId) return;

        // Ensure message is string
        if (typeof message !== 'string') message = JSON.stringify(message);

        try {
            const res = await fetch(chatSendRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ message, receiver_id: receiverId })
            });

            if (!res.ok) throw new Error("Failed to send message");

            const chat = await res.json();
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

    // --- Voice Recording ---
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

    // --- Emoji Picker ---
    const picker = new EmojiButton({
        position: 'bottom-end',
        theme: 'light',
        autoHide: false
    });

    emojiBtn.addEventListener('click', () => picker.togglePicker(emojiBtn));

    picker.on('emoji', emoji => {
        messageInput.value += emoji; // Keep adding emojis
    });

   // --- Declare selectedFile once ---
    let selectedFile = null;

    // --- File input change ---
    attachInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;

        selectedFile = file;

        // Clear previous preview
        filePreview.innerHTML = '';

        // Show image preview
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'h-20 rounded border';
            filePreview.appendChild(img);
        } else {
            // Show file name for docs/other files
            const fileDiv = document.createElement('div');
            fileDiv.classList.add('flex', 'items-center', 'gap-2', 'border', 'rounded', 'px-2', 'py-1');
            fileDiv.innerHTML = `📎 <span class="text-sm">${file.name}</span>`;
            filePreview.appendChild(fileDiv);
        }

        // Add remove button
        const removeBtn = document.createElement('button');
        removeBtn.textContent = '✖';
        removeBtn.classList.add('ml-2', 'text-red-500', 'hover:text-red-700');
        removeBtn.addEventListener('click', () => {
            selectedFile = null;
            attachInput.value = '';
            filePreview.innerHTML = '';
        });
        filePreview.appendChild(removeBtn);
    });

    // --- Send button logic ---
    sendBtn.addEventListener('click', async () => {
        const message = messageInput.value.trim();

        if (!message && !selectedFile) return; // prevent empty send

        try {
            let chat;

            if (selectedFile) {
                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('receiver_id', receiverId);
                formData.append('message', message || '');

                const response = await fetch('/chat/send-file', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!response.ok) throw new Error('Error sending file');
                chat = await response.json();

                // Reset file input & preview
                selectedFile = null;
                attachInput.value = '';
                filePreview.innerHTML = '';
            } else {
                // Send text message (existing logic)
                const res = await fetch('/chat/send', {
                    method: 'POST',
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
});