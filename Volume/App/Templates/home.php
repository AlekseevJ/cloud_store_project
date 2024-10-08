<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Файловое хранилище</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    nav ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
    }

    nav li {
      margin: 0 10px;
    }

    nav a {
      color: #ededed;
      text-decoration: none;
    }

    main {
      padding: 40px;
    }

    .hero {
      text-align: center;
      margin-bottom: 40px;
    }

    .features {
      display: flex;
      justify-content: space-around;
      margin-bottom: 40px;
    }

    .feature {
      text-align: center;
      width: 30%;
    }

    .login-form {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .login-form h2 {
      text-align: center;
    }

    .login-form input {
      width: 95%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .login-form-btn {
      width: 100%;
      padding: 10px;
      background-color: #333;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    footer {
      background-color: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    .registration-form {
      max-width: 400px;
      margin: 50px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .registration-form h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .registration-form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .registration-form input:focus {
      border-color: #666;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .registration-form button {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .registration-form button:hover {
      background-color: #45a049;
    }

    .registration-form p {
      text-align: center;
      margin-top: 20px;
    }

    .registration-form a {
      color: #4CAF50;
      text-decoration: none;
    }

    .registration-form a:hover {
      text-decoration: underline;
    }

    .logout-link {
      display: inline-block;
      padding: 8px 16px;
      background-color: #f44336;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    .user-name {
      text-align: right;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Файловое хранилище</h1>
    <nav>
      <ul>
        <!-- <li><a href="#">Главная</a></li> -->
        $$linktofiles$$
        $$adminpanel$$
        <!-- <li><a href="#">Цены</a></li> -->
        <!-- <li><a href="#">Контакты</a></li> -->
      </ul>
    </nav>
    $$hellouser$$
  </header>

  <main>
    <section class="hero">
      <h2>Надежное хранение ваших файлов</h2>
      <p>Храните свои файлы в безопасности с нашим файловым хранилищем.</p>
    </section>

    <section class="features">
      <div class="feature">
        <h3>Безопасность</h3>
        <p>Ваши файлы защищены современными методами шифрования.</p>
      </div>
      <div class="feature">
        <h3>Доступность</h3>
        <p>Доступ к файлам с любого устройства в любое время.</p>
      </div>
      <div class="feature">
        <h3>Простота</h3>
        <p>Интуитивно понятный интерфейс для легкого управления файлами.</p>
      </div>
    </section>

    <section class="registration-form">
      $$logform$$
    </section>
    <section class="registration-form">
      $$register$$
    </section>
    
  </main>

  <footer>
    <p>&copy; 2024 Файловое хранилище. Все права защищены.</p>
  </footer>
</body>

</html>