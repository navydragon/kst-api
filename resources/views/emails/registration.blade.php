<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
</head>
<body>
    <h1>Добро пожаловать, {{ $user->name }}!</h1>
    <p>Вы зарегистрированы в косплексной системе тестов по вопросам охраны труда.</p>
    <p>Данные для входа:</p>
    <ul>
      <li>Адрес: http://kst.rzd</li>
      <li>Логин: {{ $user->login }}</li>
      <li>Пароль: 159753</li>
    </ul>
    @if($user->change_password)
    <p>При первом входе пароль нужно будет сменить.</p>
    @endif
</body>
</html>
