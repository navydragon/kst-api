<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
</head>
<body>
    <h1>Уважаемый (-ая), {{ $user->name }}!</h1>
    <p>Ваш пароль был сброшен на базовый.</p>
    <p>Данные для входа:</p>
    <ul>
      <li>Адрес: http://kst.rzd</li>
      <li>Логин: {{ $user->login }}</li>
      <li>Пароль: 159753</li>
    </ul>
    <p>При следующем входе пароль нужно будет сменить.</p>
</body>
</html>
