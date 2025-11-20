<?php
// views/messages.php - Vista de comunicación segura con mensajería completa
?>

<!-- Estilos específicos para el chat -->
<style>
.chat-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    height: 70vh;
}

.conversations-list {
    background: rgba(10, 14, 39, 0.95);
    border: 1px solid #00ff41;
    border-radius: 5px;
    overflow-y: auto;
    padding: 15px;
}

.conversation-item {
    padding: 15px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(0, 255, 65, 0.3);
    border-radius: 3px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.conversation-item:hover, .conversation-item.active {
    border-color: #00ff41;
    box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
}

.conversation-item.active {
    background: rgba(0, 255, 65, 0.1);
}

.chat-area {
    background: rgba(10, 14, 39, 0.95);
    border: 1px solid #00ff41;
    border-radius: 5px;
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 15px;
    border-bottom: 1px solid rgba(0, 255, 65, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 8px;
    word-wrap: break-word;
    position: relative;
}

.message-bubble.sent {
    align-self: flex-end;
    background: rgba(0, 255, 65, 0.2);
    border: 1px solid #00ff41;
    color: #00ff41;
}

.message-bubble.received {
    align-self: flex-start;
    background: rgba(0, 255, 255, 0.1);
    border: 1px solid #00ffff;
    color: #00ffff;
}

.message-time {
    font-size: 0.7em;
    opacity: 0.6;
    margin-top: 5px;
}

.chat-input-area {
    padding: 15px;
    border-top: 1px solid rgba(0, 255, 65, 0.3);
    display: flex;
    gap: 10px;
    align-items: center;
}

.chat-input {
    flex: 1;
    padding: 10px;
    background: rgba(0, 0, 0, 0.5);
    border: 1px solid #00ff41;
    border-radius: 3px;
    color: #00ff41;
    font-family: 'Fira Code', monospace;
}

.auto-delete-badge {
    background: rgba(255, 165, 0, 0.2);
    border: 1px solid #ffaa00;
    color: #ffaa00;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.7em;
    margin-left: 5px;
}

/* Estilos para el menú de opciones */
.message-options {
    position: absolute;
    top: 5px;
    right: 5px;
    opacity: 0;
    transition: opacity 0.3s;
}

.message-bubble:hover .message-options {
    opacity: 1;
}

.options-btn {
    background: rgba(0, 0, 0, 0.7);
    border: 1px solid currentColor;
    color: inherit;
    padding: 2px 6px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 0.8em;
}

.options-menu {
    position: absolute;
    top: 25px;
    right: 0;
    background: rgba(10, 14, 39, 0.98);
    border: 1px solid #00ff41;
    border-radius: 3px;
    padding: 5px;
    display: none;
    z-index: 1000;
    min-width: 150px;
}

.options-menu.show {
    display: block;
}

.option-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s;
    border-radius: 2px;
    font-size: 0.85em;
}

.option-item:hover {
    background: rgba(0, 255, 65, 0.2);
}

.option-item.danger:hover {
    background: rgba(255, 0, 0, 0.2);
    color: #ff4444;
}

@media (max-width: 768px) {
    .chat-container {
        grid-template-columns: 1fr;
    }
    
    .conversations-list {
        max-height: 200px;
    }
}
</style>

<div class="card">
    <h3>▓▒▒ SISTEMA DE MENSAJERÍA ENCRIPTADA ▒▒▓</h3>
    
    <!-- Selector de usuario y configuración -->
    <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <div class="terminal-prompt">INICIAR CONVERSACIÓN CON:</div>
            <select id="newChatUser" style="width: 100%; padding: 10px; background: rgba(0, 0, 0, 0.5); border: 1px solid #00ff41; color: #00ff41; font-family: 'Fira Code', monospace;">
                <option value="">Seleccionar usuario...</option>
            </select>
        </div>
        
        <div style="flex: 1; min-width: 200px;">
            <div class="terminal-prompt">AUTO-ELIMINACIÓN DE MENSAJES:</div>
            <select id="autoDeleteTime" style="width: 100%; padding: 10px; background: rgba(0, 0, 0, 0.5); border: 1px solid #00ff41; color: #00ff41; font-family: 'Fira Code', monospace;">
                <option value="0">No eliminar</option>
                <option value="1">1 minuto</option>
                <option value="10">10 minutos</option>
                <option value="30">30 minutos</option>
                <option value="60">1 hora</option>
                <option value="360">6 horas</option>
                <option value="720">12 horas</option>
                <option value="1440">24 horas</option>
            </select>
        </div>
    </div>
    
    <!-- Contenedor del chat -->
    <div class="chat-container">
        <!-- Lista de conversaciones -->
        <div class="conversations-list" id="conversationsList">
            <p style="color: rgba(0, 255, 65, 0.5); text-align: center;">Cargando conversaciones...</p>
        </div>
        
        <!-- Área de chat -->
        <div class="chat-area">
            <div class="chat-header" id="chatHeader">
                <span style="color: #00ffff;">Selecciona una conversación</span>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <p style="color: rgba(0, 255, 65, 0.5); text-align: center; margin: auto;">
                    Selecciona un usuario para iniciar una conversación encriptada
                </p>
            </div>
            
            <div class="chat-input-area" id="chatInputArea" style="display: none;">
                <button class="btn-small" onclick="document.getElementById('fileInput').click()">📎</button>
                <input type="file" id="fileInput" style="display: none;" onchange="sendFile()" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                <input type="text" id="messageInput" class="chat-input" placeholder="Escribe un mensaje..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button class="btn-small" onclick="sendMessage()">📤</button>
            </div>
        </div>
    </div>
</div>

<!-- Card info encriptación -->
<div class="card">
    <h3>▓▒▒ ENCRIPTACIÓN DE EXTREMO A EXTREMO ▒▒▓</h3>
    <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid #00ffff; padding: 15px; border-radius: 3px;">
        <p style="color: #00ffff; line-height: 1.8; font-size: 0.85em;">
            <strong>[ COMUNICACIÓN 100% SEGURA ]</strong><br><br>
            > <strong style="color: #00ff41;">Encriptación:</strong> AES-256-CBC de extremo a extremo<br>
            > <strong style="color: #00ff41;">Mensajes de texto:</strong> Encriptados automáticamente<br>
            > <strong style="color: #00ff41;">Archivos:</strong> Imágenes (5MB) y documentos (10MB) encriptados<br>
            > <strong style="color: #00ff41;">Auto-eliminación:</strong> Configurable desde 1 minuto hasta 24 horas<br>
            > <strong style="color: #00ff41;">Eliminar mensajes:</strong> Para ti o para todos<br>
            > <strong style="color: #00ff41;">Privacidad total:</strong> Solo emisor y receptor pueden leer mensajes<br><br>
            ⚠️ Los mensajes eliminados NO pueden recuperarse<br>
            🔒 Nadie, ni siquiera administradores, puede leer tus mensajes
        </p>
    </div>
</div>

<script>
let currentChat = null;
let messageCheckInterval = null;
let lastMessageId = 0;

// Cargar lista de usuarios disponibles
async function loadUsers() {
    try {
        const response = await fetch('api/messages.php?action=get_users');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('newChatUser');
            select.innerHTML = '<option value="">Seleccionar usuario...</option>';
            
            data.data.users.forEach(user => {
                select.innerHTML += `<option value="${user.id}">${user.username} (${user.email})</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Cargar conversaciones
async function loadConversations() {
    try {
        const response = await fetch('api/messages.php?action=get_conversations');
        const data = await response.json();
        
        if (data.success) {
            displayConversations(data.data.conversations);
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
    }
}

// Mostrar conversaciones
function displayConversations(conversations) {
    const list = document.getElementById('conversationsList');
    
    if (conversations.length === 0) {
        list.innerHTML = '<p style="color: rgba(0, 255, 65, 0.5); text-align: center;">No hay conversaciones</p>';
        return;
    }
    
    list.innerHTML = conversations.map(conv => `
        <div class="conversation-item ${currentChat == conv.other_user_id ? 'active' : ''}" 
             onclick="openChat(${conv.other_user_id}, '${conv.other_username}')">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong style="color: #00ff41;">👤 ${conv.other_username}</strong>
                ${conv.unread_count > 0 ? `<span style="background: #ff0000; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7em;">${conv.unread_count}</span>` : ''}
            </div>
            <p style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em; margin-top: 5px;">${conv.last_message_preview || 'Sin mensajes'}</p>
            <p style="color: rgba(0, 255, 255, 0.4); font-size: 0.7em; margin-top: 3px;">${new Date(conv.last_message_at).toLocaleString('es-PE')}</p>
        </div>
    `).join('');
}

// Abrir chat con usuario
async function openChat(userId, username) {
    currentChat = userId;
    lastMessageId = 0;
    
    // Actualizar UI
    document.getElementById('chatHeader').innerHTML = `
        <span style="color: #00ffff;">💬 Chat con: <strong>${username}</strong></span>
        <span style="color: rgba(0, 255, 65, 0.6); font-size: 0.8em;">🔒 Encriptado AES-256</span>
    `;
    
    document.getElementById('chatInputArea').style.display = 'flex';
    document.getElementById('chatMessages').innerHTML = '<p style="color: rgba(0, 255, 65, 0.5); text-align: center;">Cargando mensajes...</p>';
    
    // Cargar mensajes
    await loadMessages(userId);
    
    // Actualizar conversaciones
    loadConversations();
    
    // Auto-refresh cada 3 segundos
    if (messageCheckInterval) clearInterval(messageCheckInterval);
    messageCheckInterval = setInterval(() => loadMessages(userId, true), 3000);
}

// Cargar mensajes
async function loadMessages(userId, isRefresh = false) {
    try {
        const url = `api/messages.php?action=get_messages&other_user_id=${userId}${isRefresh ? '&last_message_id=' + lastMessageId : ''}`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            if (!isRefresh) {
                // Primera carga
                displayMessages(data.data.messages);
            } else if (data.data.messages.length > 0) {
                // Nuevos mensajes
                appendMessages(data.data.messages);
            }
            
            // Actualizar último ID
            if (data.data.messages.length > 0) {
                lastMessageId = Math.max(...data.data.messages.map(m => m.id));
            }
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Mostrar mensajes
function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    
    if (messages.length === 0) {
        container.innerHTML = '<p style="color: rgba(0, 255, 65, 0.5); text-align: center; margin: auto;">No hay mensajes aún. ¡Inicia la conversación!</p>';
        return;
    }
    
    container.innerHTML = messages.map(msg => formatMessage(msg)).join('');
    container.scrollTop = container.scrollHeight;
}

// Agregar nuevos mensajes
function appendMessages(messages) {
    const container = document.getElementById('chatMessages');
    messages.forEach(msg => {
        container.innerHTML += formatMessage(msg);
    });
    container.scrollTop = container.scrollHeight;
}

// Formatear mensaje
function formatMessage(msg) {
    const isSent = msg.sender_id == <?php echo $_SESSION['user_id']; ?>;
    const time = new Date(msg.sent_at).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'});
    const deleteInfo = msg.auto_delete_minutes ? `<span class="auto-delete-badge">⏱️ ${msg.auto_delete_minutes}min</span>` : '';
    
    let content = '';
    if (msg.message_type === 'text') {
        content = msg.content;
    } else if (msg.message_type === 'image') {
        content = `📷 <a href="api/messages.php?action=download_file&message_id=${msg.id}" target="_blank" style="color: inherit;">Ver imagen</a>`;
    } else if (msg.message_type === 'document') {
        content = `📄 <a href="api/messages.php?action=download_file&message_id=${msg.id}" style="color: inherit;">Descargar documento .${msg.file_extension}</a>`;
    }
    
    return `
        <div class="message-bubble ${isSent ? 'sent' : 'received'}" id="msg-${msg.id}">
            <div class="message-options">
                <button class="options-btn" onclick="toggleMessageOptions(${msg.id}, event)">⋮</button>
                <div class="options-menu" id="options-${msg.id}">
                    ${isSent ? `<div class="option-item danger" onclick="deleteMessage(${msg.id}, 'everyone')">🗑️ Eliminar para todos</div>` : ''}
                    <div class="option-item" onclick="deleteMessage(${msg.id}, 'me')">🗑️ Eliminar para mí</div>
                </div>
            </div>
            <div>${content}</div>
            <div class="message-time">${time} ${deleteInfo} ${msg.read_at && isSent ? '✓✓' : isSent ? '✓' : ''}</div>
        </div>
    `;
}

// Toggle menú de opciones
function toggleMessageOptions(messageId, event) {
    event.stopPropagation();
    const menu = document.getElementById(`options-${messageId}`);
    
    // Cerrar todos los demás menús
    document.querySelectorAll('.options-menu').forEach(m => {
        if (m.id !== `options-${messageId}`) {
            m.classList.remove('show');
        }
    });
    
    menu.classList.toggle('show');
}

// Cerrar menús al hacer click fuera
document.addEventListener('click', function() {
    document.querySelectorAll('.options-menu').forEach(m => m.classList.remove('show'));
});

// Eliminar mensaje
async function deleteMessage(messageId, deleteType) {
    const confirmMsg = deleteType === 'everyone' 
        ? '¿Eliminar este mensaje para todos? Esta acción no se puede deshacer.'
        : '¿Eliminar este mensaje para ti?';
    
    if (!confirm(confirmMsg)) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_message');
        formData.append('message_id', messageId);
        formData.append('delete_type', deleteType);
        
        const response = await fetch('api/messages.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Eliminar visualmente el mensaje
            const msgElement = document.getElementById(`msg-${messageId}`);
            if (msgElement) {
                msgElement.style.transition = 'opacity 0.3s';
                msgElement.style.opacity = '0';
                setTimeout(() => msgElement.remove(), 300);
            }
            
            showNotification(data.message);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al eliminar mensaje', 'error');
    }
}

// Enviar mensaje de texto
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    const autoDelete = document.getElementById('autoDeleteTime').value;
    
    if (!message || !currentChat) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('receiver_id', currentChat);
        formData.append('message', message);
        formData.append('auto_delete_minutes', autoDelete);
        
        const response = await fetch('api/messages.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            await loadMessages(currentChat, true);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al enviar mensaje', 'error');
    }
}

// Enviar archivo
async function sendFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    const autoDelete = document.getElementById('autoDeleteTime').value;
    
    if (!file || !currentChat) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'send_file');
        formData.append('receiver_id', currentChat);
        formData.append('file', file);
        formData.append('auto_delete_minutes', autoDelete);
        
        showNotification('Subiendo archivo...');
        
        const response = await fetch('api/messages.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            fileInput.value = '';
            showNotification(data.message);
            await loadMessages(currentChat, true);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('[ ERROR ] Error al enviar archivo', 'error');
    }
}

// Cambio de usuario en selector
document.getElementById('newChatUser')?.addEventListener('change', function() {
    if (this.value) {
        const username = this.options[this.selectedIndex].text.split(' (')[0];
        openChat(parseInt(this.value), username);
        this.value = '';
    }
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    loadConversations();
    
    // Actualizar conversaciones cada 10 segundos
    setInterval(loadConversations, 10000);
});

// Limpiar interval al salir
window.addEventListener('beforeunload', function() {
    if (messageCheckInterval) clearInterval(messageCheckInterval);
});
</script>