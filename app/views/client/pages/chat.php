<?php
// Get selected support staff (admin) ID – don’t default when none is provided.
$admin_id = (isset($_GET['admin_id']) && !empty($_GET['admin_id'])) ? $_GET['admin_id'] : null;
$user_id  = $_SESSION['user_id'];

// Get the list of available support staff
$admins   = $user->getAllAdmins();

// If an admin is selected, get the conversation between this client and that admin.
$conversation = $admin_id ? $message->getConversation($user_id, $admin_id) : null;

$error = '';

// Handle message sending. If a message is posted but no support staff is selected, set an error.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $messageText = trim($_POST['message']);
    if (!empty($messageText)) {
        if ($admin_id) {
            $message->sendMessage($user_id, $admin_id, $messageText);
            // Redirect to prevent duplicate submissions.
            header("Location: ?page=chat&admin_id=" . $admin_id);
            exit();
        } else {
            $error = "Please select a support staff member to start the conversation.";
        }
    }
}
?>
    <link rel="stylesheet" href="path/to/fontawesome.css">
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
        .admin-list {
            width: 280px;
            background: #f5f5f5;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        .admin-list h3 {
            padding: 20px;
            margin: 0;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            color: #333;
            background: #fff;
        }
        .admin-item {
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
        .admin-item:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .admin-item.active {
            background: #e7f1ff;
            border-left: 3px solid #007bff;
        }
        .admin-avatar {
            width: 45px;
            height: 45px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        .admin-avatar i {
            color: #6c757d;
            font-size: 20px;
        }
        .admin-info {
            flex: 1;
        }
        .admin-name {
            font-weight: 500;
            margin-bottom: 3px;
            color: #212529;
        }
        .admin-role {
            font-size: 12px;
            color: #6c757d;
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
        .no-chat-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
            font-size: 16px;
            background: #f8f9fa;
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
        .error-message {
            text-align: center;
            color: red;
            padding: 10px;
        }
    </style>
</head>
<div class="chat-page">
    <!-- Support Staff Sidebar -->
    <div class="admin-list">
        <h3>Support Staff</h3>
        <?php foreach ($admins as $admin): ?>
            <a href="?page=chat&admin_id=<?php echo $admin['user_id']; ?>" 
               class="admin-item <?php echo ($admin['user_id'] == $admin_id ? 'active' : ''); ?>">
                <div class="admin-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="admin-info">
                    <div class="admin-name">
                        <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                    </div>
                    <div class="admin-role">
                        <?php echo htmlspecialchars($admin['role']); ?>
                    </div>
                </div>
                <?php if ($message->hasUnreadMessages($user_id, $admin['user_id'])): ?>
                    <span class="unread-badge"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Chat Area -->
    <div class="chat-container">
        <?php if ($admin_id): ?>
            <?php $current_admin = $user->getUserById($admin_id); ?>
            <div class="chat-header">
                <?php if ($current_admin): ?>
                    <div class="chat-with">
                        Chat with <?php echo htmlspecialchars($current_admin['first_name'] . ' ' . $current_admin['last_name']); ?>
                    </div>
                <?php else: ?>
                    <div class="chat-with">
                        Selected support staff not found.
                    </div>
                <?php endif; ?>
            </div>

            <?php
            // Display any error message from attempting to send while no support staff was selected.
            if ($error) {
                echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
            }
            ?>

            <div class="chat-messages" id="chatMessages">
                <?php if ($conversation && $conversation->rowCount() > 0): ?>
                    <?php while ($msg = $conversation->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="message <?php echo ($msg['sender_id'] == $user_id ? 'sent' : 'received'); ?>">
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
                        <p>No messages yet. Start the conversation!</p>
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
            <!-- When no support staff is selected, show a single prompt container. -->
            <div class="no-chat-selected">
                <p>Select a support staff member to start chatting</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    <?php if ($admin_id): ?>
    // Real-time update may be executed only if a support staff member is selected.
    setInterval(function() {
        fetch(`../api/chat.php?action=get_messages&user_id=<?php echo $user_id; ?>&admin_id=<?php echo $admin_id; ?>`)
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    updateChatMessages(data.messages);
                }
            });
    }, 5000);
    <?php endif; ?>
});

function updateChatMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    messages.forEach(function(msg) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        if (msg.sender_id == <?php echo $user_id; ?>) {
            messageDiv.classList.add('sent');
        } else {
            messageDiv.classList.add('received');
        }
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');
        contentDiv.textContent = msg.message;
        messageDiv.appendChild(contentDiv);
        const timeDiv = document.createElement('div');
        timeDiv.classList.add('message-time');
        timeDiv.textContent = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        messageDiv.appendChild(timeDiv);
        chatMessages.appendChild(messageDiv);
    });
}
</script>