<?php
$config = require_once(__DIR__ . '/../../config/config.php');
$db = new Database($config['database'], DB_USER, DB_PASSWORD);
$sanFilters = require_once(__DIR__ . '/../../core/libs/sanitize/sanitization.php');
$errorMsgs = require_once(__DIR__ . '/../../core/libs/validate/validation.php');
require_once(__DIR__ . '/../../core/libs/filter/Filter.php');
$filter = new Filter();

$postFieldRules = [
    "itemTitle" => "string | required | min: 8 | max: 128",
    "itemDescription" => "string | required | between: 8,255",
    "itemUserId" => "number_int | required"
];


if (is_get() && isset($_SESSION['inputs'])) {
    $userPost = $db->query('SELECT * FROM market WHERE user_id = :uId', [
        'uId' => $_SESSION['inputs']['itemUserId'],
    ])->fetchAllOrAbort();
}

if (is_post()) {

    [$inputs, $errors] = $filter->filter($_POST, $postFieldRules, $errorMsgs, $sanFilters);

    if ($errors) {
        redirect_with(['inputs' => $inputs, 'errors' => $errors]);
    } else {
        $db->query('INSERT INTO market(title, description, user_id)VALUES(:itemTitle, :itemDescription, :itemUserId)', [
            'itemTitle' => $_POST['itemTitle'],
            'itemDescription' => $_POST['itemDescription'],
            'itemUserId' => $_POST['itemUserId'],
        ])->successPost();
    }
}

require_once(__DIR__ . '/../../views/market/post.view.php');

?>