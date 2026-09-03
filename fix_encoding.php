<?php
$files = [
    "resources/views/admin/admins/create.blade.php",
    "resources/views/admin/admins/show.blade.php",
    "resources/views/admin/audit/index.blade.php",
    "resources/views/admin/files/permissions.blade.php",
    "resources/views/admin/files/show.blade.php",
    "resources/views/admin/users/create.blade.php",
    "resources/views/admin/users/edit.blade.php",
    "resources/views/user/dashboard.blade.php"
];

$replacements = [
    "Ã©" => "é",
    "Ã¨" => "è",
    "Ãª" => "ê",
    "Ã«" => "ë",
    "Ã" => "à", // Be careful, Ã alone is often à, but Ã¢ is â
    "Ã¢" => "â",
    "Ã§" => "ç",
    "Ã®" => "î",
    "Ã´" => "ô",
    "Ã¹" => "ù",
    "Ã»" => "û",
    "â€™" => "'",
    "â€¢" => "•",
    "Ã‰" => "É",
    "Ã€" => "À",
    "ÃŠ" => "Ê",
    "ÃŽ" => "Î",
    "Â" => "", // sometimes leftover
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // First pass for multi-char
    $content = str_replace(
        ["Ã©", "Ã¨", "Ãª", "Ã«", "Ã¢", "Ã§", "Ã®", "Ã´", "Ã¹", "Ã»", "â€™", "â€¢", "Ã‰", "Ã€", "ÃŠ", "ÃŽ", "Ãœ"],
        ["é", "è", "ê", "ë", "â", "ç", "î", "ô", "ù", "û", "'", "•", "É", "À", "Ê", "Î", "Ü"],
        $content
    );
    
    // Then single char Ã usually means à if followed by space or letter, let's check what's left
    $content = str_replace("Ã ", "à ", $content);
    $content = str_replace("Ã", "à", $content);
    $content = str_replace("Â", "", $content);
    
    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
