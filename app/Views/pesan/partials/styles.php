<style>
    .chat-container {
        max-width: 900px;
        margin: 0 auto;
        height: calc(100vh - 180px);
        display: flex;
        flex-direction: column;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fc;
        border-radius: 8px;
        margin-bottom: 15px;
        min-height: 300px;
    }

    .chat-msg {
        padding: 10px 14px;
        margin-bottom: 10px;
        border-radius: 10px;
        max-width: 55%;
        clear: both;
    }

    .chat-msg.mine {
        background: #144d37;
        color: white;
        margin-left: auto;
    }

    .chat-msg.other {
        background: white;
        border: 1 #144d37;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .chat-msg .msg-meta {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 10px;
    }

    .chat-msg.other .msg-meta {
        color: blue;
    }

    .chat-msg.mine .msg-meta {
        color: yellow;
    }

    .chat-msg .msg-text {
        word-wrap: break-word;
    }

    .msg-attachment {
        margin-top: 8px;
        font-size: 14px;
    }

    .msg-attachment a {
        color: inherit;
        text-decoration: underline;
    }

    .msg-attachment .file-size {
        opacity: 0.8;
    }

    .chat-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .chat-form input[type="text"] {
        flex: 1;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        min-width: 220px;
    }

    .file-input {
        display: none;
    }

    .file-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        background: white;
        cursor: pointer;
    }

    .file-label:hover {
        border-color: #144d37;
    }

    .file-name {
        font-size: 12px;
        color: #6c757d;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-form button {
        padding: 12px 24px;
        border-radius: 8px;
        background: #144d37;
        color: white;
        border: none;
        cursor: pointer;
    }

    .chat-form button:hover {
        background: rgb(57, 199, 116);
    }
</style>
