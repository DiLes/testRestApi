# testRestApi
Test REST-API for import

Для проверки можно использоавть POSTMAN:
1. Создайте новый http-запрос. 
2. Дать имя вашему запросу, например, Test API, и сохраните его в коллекции.
3. Выбрать метод POST
4. URL: http://localhost/api.php (localhost - ваш домен).
5. Добавьте Basic Auth:
    a. Перейдите на вкладку "Authorization".
    b. В выпадающем списке выберите "Basic Auth".
    c. Укажите логин и пароль:
        Username: admin
        Password: password
6. Добавьте тело запроса:
    a. Перейдите на вкладку "Body".
    b. Выберите "raw" и установите тип данных JSON (в выпадающем меню справа).
    c. Введите пример данных, которые хотите отправить:
        [
           {
               "uuid": "warehouse1",
               "stocks": [
                   {"uuid": "item1", "quantity": 10},
                   {"uuid": "item2", "quantity": 20}
               ]
           },
           {
               "uuid": "warehouse2",
               "stocks": [
                   {"uuid": "item3", "quantity": 15}
               ]
           }
        ]

7. Отправьте запрос: 
    a. Нажмите кнопку "Send".
    b. Проверьте ответ в нижней части окна Postman.
8. Ожидаемый результат: 
    a. Если запрос выполнен успешно:
        {
            "success": true
        }
    b. Если возникли ошибки, например, неверный формат данных:
        {
            "success": false,
            "errors": ["Invalid request format"]
        }
9. 