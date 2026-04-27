<?php

namespace App\Models;

use CodeIgniter\Model;

class MChat extends BaseMasterModel
{
    protected $table = 'chat_messages';
    protected $allowedFields = [
        'id_user',
        'username',
        'role',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
