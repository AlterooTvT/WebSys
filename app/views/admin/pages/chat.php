<?php
$admin_id = $_SESSION['user_id'];
$selected_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$clients = $user->getAllClients();
$conversation = $selected_user_id ? $message->getConversation($selected_user_id, $admin_id) : null;

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $selected_user_id) {
    $messageText = trim($_POST['message']);
    if (!empty($messageText)) {
        $message->sendMessage($admin_id, $selected_user_id, $messageText);
        header("Location: ?page=chat&user_id=" . $selected_user_id);
        exit();
    }
}

// Mark messages as read when admin views them
if ($selected_user_id) {
    $message->markAsRead($selected_user_id, $admin_id);
}
?>

<div class="chat-page">
    <!-- Client List Sidebar -->
    <div class="client-list">
        <h3>Client Conversations</h3>
        <?php foreach ($clients as $client): ?>
            <a href="?page=chat&user_id=<?php echo $client['user_id']; ?>" 
               class="client-item <?php echo $client['user_id'] == $selected_user_id ? 'active' : ''; ?>">
                <div class="client-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="client-info">
                    <div class="client-name">
                        <?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?>
                    </div>
                    <?php if ($message->hasUnreadMessages($admin_id, $client['user_id'])): ?>
                        <span class="unread-badge"></span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Chat Area -->
    <div class="chat-container">
        <?php if ($selected_user_id): ?>
            <?php 
            $current_client = $user->getUserById($selected_user_id);
            ?>
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-with">
                    Chat with <?php echo htmlspecialchars($current_client['first_name'] . ' ' . $current_client['last_name']); ?>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="chat-messages" id="chatMessages">
                <?php if ($conversation && $conversation->rowCount() > 0): ?>
                    <?php while($msg = $conversation->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="message <?php echo $msg['sender_id'] == $admin_id ? 'sent' : 'received'; ?>">
                            <div class="message-content">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </div>
                            <div class="message-time">
                                <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-messages">
                        <p>No messages yet in this conversation.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Message Input Form -->
            <form method="POST" class="chat-form" id="chatForm">
                <div class="input-group">
                    <input type="text" name="message" placeholder="Type your message..." required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="no-chat-selected">
                <p>Select a client to start chatting</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.chat-page {
    display: flex;
    height: calc(100vh - 150px);
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin: 20px;
}

.client-list {
    width: 280px;
    background: #f5f5f5;
    border-right: 1px solid #ddd;
    overflow-y: auto;
}

.client-list h3 {
    padding: 20px;
    margin: 0;
    border-bottom: 1px solid #ddd;
    font-size: 16px;
    color: #333;
    background: #fff;
}

.client-item {
    display: flex;
    align-items: center;
    padding: 15px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #eee;
    position: relative;
    transition: all 0.3s ease;
    background: #fff;
    margin: 5px;
    border-radius: 8px;
}

.client-item:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.client-item.active {
    background: #e7f1ff;
    border-left: 3px solid #007bff;
}

.client-avatar {
    width: 45px;
    height: 45px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.client-avatar i {
    color: #6c757d;
    font-size: 20px;
}

.client-info {
    flex: 1;
}

.client-name {
    font-weight: 500;
    margin-bottom: 3px;
    color: #212529;
}

.unread-badge {
    width: 8px;
    height: 8px;
    background: #ff4444;
    border-radius: 50%;
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

.chat-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

.chat-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
}

.chat-with {
    font-weight: 500;
    color: #212529;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 15px;
    margin-bottom: 5px;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.message.sent {
    background: #007bff;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
}

.message.received {
    background: #fff;
    color: #212529;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
}

.message-content {
    margin-bottom: 5px;
    line-height: 1.4;
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 5px;
}

.chat-form {
    padding: 20px;
    border-top: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.03);
}

.input-group {
    display: flex;
    gap: 10px;
}

.input-group input {
    flex: 1;
    padding: 12px 20px;
    border: 1px solid #dee2e6;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.input-group input:focus {
    border-color: #007bff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.input-group button {
    padding: 12px 24px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.input-group button:hover {
    background: #0056b3;
    transform: translateY(-1px);
}

.input-group button i {
    font-size: 16px;
}

.no-messages {
    text-align: center;
    color: #6c757d;
    margin-top: 20px;
    padding: 40px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.no-chat-selected {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    font-size: 16px;
    background: #f8f9fa;
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for new messages */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message {
    animation: fadeIn 0.3s ease;
}

/* Additional styles for admin chat */
.chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-status {
    font-size: 12px;
    color: #6c757d;
}

.chat-actions {
    display: flex;
    gap: 10px;
}

.chat-actions button {
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    background: #f8f9fa;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.chat-actions button:hover {
    background: #e9ecef;
    color: #212529;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Real-time updates
        setInterval(function() {
            const selectedUserId = new URLSearchParams(window.location.search).get('user_id');
            if (selectedUserId) {
                fetch(`../api/chat.php?action=get_messages&user_id=<?php echo $admin_id; ?>&client_id=${selectedUserId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.messages) {
                            updateChatMessages(data.messages);
                        }
                    });
            }
        }, 5000);
    }
});

function updateChatMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    // Update logic here
}
</script>
