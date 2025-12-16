@extends('admin.layouts.app')

@section('title', 'Chat Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chat with Users</h3>
                </div>
                <div class="card-body">
                    <div class="row" style="height: 70vh;">
                        <!-- Conversations List -->
                        <div class="col-md-4 border-end">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-3">
                                    <input type="text" id="searchConversations" class="form-control" placeholder="Search conversations...">
                                </div>
                                <div id="conversationsList" class="flex-grow-1 overflow-auto">
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-comments fa-3x mb-3"></i>
                                        <p>Loading conversations...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-8">
                            <div id="noChatSelected" class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <div class="text-center">
                                    <i class="fas fa-comment-dots fa-4x mb-3"></i>
                                    <h4>Select a conversation to start chatting</h4>
                                </div>
                            </div>

                            <div id="chatArea" class="d-none d-flex flex-column h-100">
                                <!-- Chat Header -->
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <img id="chatUserAvatar" src="/placeholder.svg" alt="User" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                        <div>
                                            <h5 id="chatUserName" class="mb-0"></h5>
                                            <small id="chatUserStatus" class="text-muted"></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages Area -->
                                <div id="messagesArea" class="flex-grow-1 overflow-auto mb-3" style="max-height: calc(70vh - 180px);">
                                    <!-- Messages will be loaded here -->
                                </div>

                                <!-- Message Input -->
                                <div class="mt-auto">
                                    <form id="sendMessageForm" class="d-flex gap-2">
                                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-item {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.conversation-item.active {
    background-color: #e7f1ff;
}

.message-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    margin-bottom: 10px;
    word-wrap: break-word;
}

.message-mine {
    background-color: #0084ff;
    color: white;
    margin-left: auto;
}

.message-theirs {
    background-color: #e4e6eb;
    color: #050505;
}

.unread-badge {
    background-color: #dc3545;
    color: white;
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 12px;
}
</style>

<script>
let currentConversationId = null;
let messagesPolling = null;
let conversationsPolling = null;

// Load conversations
function loadConversations() {
    fetch('{{ route("admin.chat.conversations") }}')
        .then(response => response.json())
        .then(conversations => {
            const list = document.getElementById('conversationsList');
            if (conversations.length === 0) {
                list.innerHTML = '<div class="text-center py-5 text-muted"><p>No conversations yet</p></div>';
                return;
            }

            list.innerHTML = conversations.map(conv => `
                <div class="conversation-item ${conv.id === currentConversationId ? 'active' : ''}" 
                     data-id="${conv.id}"
                     onclick="selectConversation(${conv.id})">
                    <div class="d-flex align-items-start">
                        <img src="${conv.user.avatar}" alt="${conv.user.name}" 
                             class="rounded-circle me-3" style="width: 45px; height: 45px;">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>${conv.user.name}</strong>
                                ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
                            </div>
                            ${conv.last_message ? `
                                <div class="text-muted small">
                                    ${conv.last_message.is_mine ? 'You: ' : ''}${conv.last_message.message.substring(0, 50)}${conv.last_message.message.length > 50 ? '...' : ''}
                                </div>
                                <small class="text-muted">${formatTime(conv.last_message.created_at)}</small>
                            ` : '<div class="text-muted small">No messages yet</div>'}
                        </div>
                    </div>
                </div>
            `).join('');
        });
}

// Select conversation
function selectConversation(conversationId) {
    currentConversationId = conversationId;
    
    // Assign conversation to admin
    fetch(`{{ url('admin/chat/conversations') }}/${conversationId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    });

    loadConversations();
    loadMessages(conversationId);
    
    document.getElementById('noChatSelected').classList.add('d-none');
    document.getElementById('chatArea').classList.remove('d-none');

    // Start polling for new messages
    if (messagesPolling) clearInterval(messagesPolling);
    messagesPolling = setInterval(() => loadMessages(conversationId), 3000);
}

// Load messages
function loadMessages(conversationId) {
    fetch(`{{ url('admin/chat/conversations') }}/${conversationId}/messages`)
        .then(response => response.json())
        .then(messages => {
            const area = document.getElementById('messagesArea');
            const shouldScroll = area.scrollHeight - area.scrollTop === area.clientHeight;
            
            area.innerHTML = messages.map(msg => `
                <div class="d-flex ${msg.is_mine ? 'justify-content-end' : 'justify-content-start'} mb-2">
                    <div>
                        ${!msg.is_mine ? `<small class="text-muted ms-3">${msg.sender.name}</small>` : ''}
                        <div class="message-bubble ${msg.is_mine ? 'message-mine' : 'message-theirs'}">
                            ${msg.message}
                        </div>
                        <small class="text-muted ${msg.is_mine ? 'float-end me-3' : 'ms-3'}">${formatTime(msg.created_at)}</small>
                    </div>
                </div>
            `).join('');

            if (shouldScroll || messages.length > 0) {
                area.scrollTop = area.scrollHeight;
            }
        });
}

// Send message
document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentConversationId) return;
    
    fetch(`{{ url('admin/chat/conversations') }}/${currentConversationId}/messages`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message })
    })
    .then(response => response.json())
    .then(() => {
        input.value = '';
        loadMessages(currentConversationId);
        loadConversations();
    });
});

// Format time
function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    return date.toLocaleDateString();
}

// Initialize
loadConversations();
conversationsPolling = setInterval(loadConversations, 5000);

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (messagesPolling) clearInterval(messagesPolling);
    if (conversationsPolling) clearInterval(conversationsPolling);
});
</script>
@endsection
