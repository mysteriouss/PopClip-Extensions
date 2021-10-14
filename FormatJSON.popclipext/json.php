<?php
$input = getenv('POPCLIP_TEXT');
echo json_encode(json_decode($input), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );