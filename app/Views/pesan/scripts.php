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
            if (!d) return '--:--';
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

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                fileName.textContent = file ? file.name : '';
            });
        }

        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });
        }

        loadMessages();
        setInterval(loadMessages, 2500);
    })();
</script>
