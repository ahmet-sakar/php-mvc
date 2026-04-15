<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="/css/style.css">
        <title>Settings Page</title>
    </head>
    <body>
        <div class="wrapper">
            <div class="sidebar">
                <div class="sidebar-header">
                    <img src="/images/logo.png" alt="logo" />
                    <h2>MVC</h2>
                </div>

                <ul class="sidebar-links">
                    <h4>
                        <span>General</span>
                        <div class="menu-separator"></div>
                    </h4>

                    <li><a href="/"><span class="material-symbols-outlined"> dashboard </span>Dashboard</a></li>
                    <li><a href="/users"><span class="material-symbols-outlined"> group </span>Users</a></li>

                    <h4>
                        <span>Account</span>
                        <div class="menu-separator"></div>
                    </h4>

                    <li><a href="/profile"><span class="material-symbols-outlined"> account_circle </span>Profile</a></li>
                    <li><a href="/settings"><span class="material-symbols-outlined"> settings </span>Settings</a></li>
                    <li><a href="#"><span class="material-symbols-outlined"> logout </span>Logout</a></li>
                </ul>

                <div class="user-account">
                    <div class="user-profile">
                        <img src="/images/profile.jpg">
                        <div class="user-detail">
                            <h3>Ahmet Şakar</h3>
                            <span>Full Stack Developer</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                Settings Page
            </div>
        </div>
    </body>
</html>