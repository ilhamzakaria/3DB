<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

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

<div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-comments"></i> Chat - PPIC, Produksi, Gudang, Admin, Manager
            </h6>
        </div>
        <div class="card-body">
            <div class="chat-container">
                <div class="chat-messages" id="chatBox">
                    <div class="text-muted text-center py-4" id="loadingMsg">Memuat pesan...</div>
                </div>
                <form class="chat-form" id="chatForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>">
                    <label class="file-label" for="fileInput">
                        <i class="fas fa-paperclip fw-bold"></i> File
                    </label>
                    <input type="file" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                    <span class="file-name" id="fileName"></span>
                    <input type="text" id="msgInput" placeholder="Ketik pesan..." autocomplete="off" maxlength="500">
                    <button type="submit" id="btnSend">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    (function() {
        var baseUrl = '<?= base_url() ?>';
        var myId = <?= session()->get('id_user') ?? 0 ?>;
        var chatBox = document.getElementById('chatBox');
        var chatForm = document.getElementById('chatForm');
        var msgInput = document.getElementById('msgInput');
        var btnSend = document.getElementById('btnSend');
        var fileInput = document.getElementById('fileInput');
        var fileName = document.getElementById('fileName');
        var lastId = 0;

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(d) {
            var d2 = new Date(d);
            var h = d2.getHours();
            var m = d2.getMinutes();
            return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
        }

        function formatSize(bytes) {
            var b = parseInt(bytes || 0, 10);
            if (!b) return '';
            if (b < 1024) return b + ' B';
            if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
            return (b / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function renderMessage(msg) {
            var isMine = parseInt(msg.id_user) === myId;
            var meta = escapeHtml(msg.username) + ' (' + escapeHtml(msg.role) + ') - ' + formatTime(msg.created_at || '');
            var textHtml = '';
            if (msg.message) {
                textHtml = '<div class="msg-text">' + escapeHtml(msg.message) + '</div>';
            }

            var attachmentHtml = '';
            if (msg.attachment_name) {
                var sizeText = formatSize(msg.attachment_size);
                var label = escapeHtml(msg.attachment_name) + (sizeText ? ' <span class="file-size">(' + sizeText + ')</span>' : '');
                attachmentHtml = '<div class="msg-attachment">' +
                    '<a href="' + baseUrl + 'pesan/download/' + msg.id + '" target="_blank" rel="noopener">' +
                    '<i class="fas fa-paperclip"></i> ' + label +
                    '</a>' +
                    '</div>';
            }

            var body = textHtml + attachmentHtml;
            if (!body) {
                body = '<div class="msg-text text-muted">(tanpa pesan)</div>';
            }

            var html = '<div class="chat-msg ' + (isMine ? 'mine' : 'other') + '">' +
                '<div class="msg-meta">' + meta + '</div>' +
                body +
                '</div>';
            return html;
        }

        function loadMessages() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', baseUrl + 'pesan/getMessages?last_id=' + lastId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                var loading = document.getElementById('loadingMsg');
                if (loading) loading.remove();
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success && res.messages) {
                        if (res.messages.length) {
                            res.messages.forEach(function(m) {
                                chatBox.insertAdjacentHTML('beforeend', renderMessage(m));
                                if (m.id > lastId) lastId = m.id;
                            });

                            chatBox.scrollTop = chatBox.scrollHeight;
                        } else if (lastId === 0 && chatBox.children.length === 0) {
                            chatBox.innerHTML = '<div class="text-muted text-center py-4">Belum ada pesan. Mulai percakapan!</div>';
                        }
                    }
                } catch (e) {}
            };
            xhr.send();
        }

        function sendMessage() {
            var text = (msgInput.value || '').trim();
            var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (!text && !file) return;

            btnSend.disabled = true;
            var xhr = new XMLHttpRequest();
            var fd = new FormData();
            fd.append('message', text);
            if (file) {
                fd.append('attachment', file);
            }

            xhr.open('POST', baseUrl + 'pesan/sendMessage', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                btnSend.disabled = false;
                msgInput.value = '';
                fileInput.value = '';
                fileName.textContent = '';
                msgInput.focus();
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success && res.message) {
                        chatBox.insertAdjacentHTML('beforeend', renderMessage(res.message));
                        lastId = Math.max(lastId, res.message.id);
                        chatBox.scrollTop = chatBox.scrollHeight;
                    } else if (res.error) {
                        alert(res.error);
                    }
                } catch (e) {
                    alert('Gagal mengirim pesan.');
                }
            };
            xhr.send(fd);
        }

        fileInput.addEventListener('change', function() {
            var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            fileName.textContent = file ? file.name : '';
        });

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        loadMessages();
        setInterval(loadMessages, 2500);
    })();
</script>

<?= $this->endSection() ?>