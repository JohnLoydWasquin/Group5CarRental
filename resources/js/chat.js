import Echo from 'laravel-echo';

document.addEventListener("DOMContentLoaded", () => {

    // Initialize Reverb Echo
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        host: import.meta.env.VITE_REVERB_HOST + ':' + import.meta.env.VITE_REVERB_PORT,
        scheme: import.meta.env.VITE_REVERB_SCHEME
    });

    const container = document.getElementById('chatContainer');
    if (!container) return;

    const messagesDiv = document.getElementById('messages');
    const chatHeader = document.getElementById('chatHeader');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    let userId = parseInt(container.dataset.userId);
    let receiverId = parseInt(container.dataset.receiverId) || null;
    const chatSendRoute = container.dataset.chatSendRoute;
    const csrfToken = container.dataset.csrf;

    let currentChannel = null;

    function renderChatMessage(chat) {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('flex', chat.sender_id === userId ? 'justify-end' : 'justify-start');

        msgDiv.innerHTML = `
            <div class="${chat.sender_id === userId ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} 
                px-4 py-2 rounded-lg max-w-xs break-words">
                ${chat.message}
                <div class="text-xs text-gray-400 mt-1 text-right">
                    ${new Date(chat.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                </div>
            </div>
        `;
        messagesDiv.appendChild(msgDiv);
    }

    // Load initial messages
    try {
        JSON.parse(container.dataset.chats).forEach(renderChatMessage);
    } catch {}

    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    function listenChannel() {
        if (!receiverId) return;

        if (currentChannel) window.Echo.leave(currentChannel);

        const low = Math.min(userId, receiverId);
        const high = Math.max(userId, receiverId);

        currentChannel = `chat.${low}.${high}`;

        window.Echo.channel(currentChannel).listen("NewChatMessage", (e) => {
            renderChatMessage(e.chat);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });
    }

    if(receiverId) listenChannel();

    // Send message
    sendBtn.addEventListener('click', async () => {
        const message = messageInput.value.trim();
        if (!message || !receiverId) return;

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
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

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

    // Switch chat user
    document.querySelectorAll('.chat-user-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            receiverId = parseInt(btn.dataset.userId);
            messagesDiv.innerHTML = '';
            chatHeader.innerText = "Loading...";

            try {
                const res = await fetch(`/chat/${receiverId}`, {
                    headers: { "Accept": "application/json" }
                });
                const data = await res.json();

                chatHeader.innerText = "Chat with " + (data.receiverName || "Unknown");
                data.messages.forEach(renderChatMessage);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;

                listenChannel();
            } catch (err) {
                console.error(err);
                chatHeader.innerText = "Chat with Unknown";
            }
        });
    });
});
