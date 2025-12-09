<?php
function generate_navbar($active_page = '', $is_admin = false) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $name = htmlspecialchars($_SESSION['name'] ?? 'Guest');
    $home_link = $is_admin ? '#' : 'user_page.php';
    $logout_path = $is_admin ? 'login/logout.php' : 'logout.php';

    $nav_links = [
        'HOME' => $is_admin ? '#' : 'user_page.php',
        'Book' => 'book.php',
        'History' => 'history.php',
        'Financial' => '#'
    ];

    echo <<<HTML
    <nav>
        <div class="profile">
            <i class="material-icons">account_circle</i>
            <h1>{$name}</h1>
            <a class="detail" href="profile.php">see profile</a>
        </div>
        <div>
HTML;
    foreach ($nav_links as $page => $link) {
        $class = (strtoupper($active_page) === $page) ? 'menu active' : 'menu';
        $href = (strtoupper($active_page) === $page) ? '#' : $link;
        echo "<a class=\"{$class}\" href=\"{$href}\">{$page}</a>";
    }
    echo <<<HTML
        </div>
        <div>
            <a class="menu" onclick="window.location.href='{$logout_path}'">LogOut</a>
        </div>
    </nav>
HTML;
}
?>