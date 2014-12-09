<?php
include '../src/Router.php';

function codeFile($filename) {
    return '<div class="code-block"><div class="code">' . highlight_file($filename, true) . '</div></div>';
}

function codeString($code) {
    $result = var_export($code, true);
    $result = str_replace(array(
            'array (',
            "=> \n  ",
            ",\n  )",
            "),\n)"
        ), array(
            'array(',
            '=> ',
            "\n  )",
            ")\n)"
        ),
        $result);

    $result = stripcslashes($result);
    $result = htmlspecialchars_decode($result);

    return '<div class="code-block"><div class="code">' . highlight_string("<?php \r\n" . $result . ";\r\n?>", true) . '</div></div>';
}
?>