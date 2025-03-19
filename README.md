# Стек реализации
- PHP 8.2-fpm
- Symfony 7.2
- MySQL 8.0.40
- Nginx 1.2.27
- Docker

# Инструкция для запуска
## 1. Сборка docker-контейнеров:
```bash

cd ./docker
docker compose build
```

## 2. Запуск контейнеров
```bash

docker compose up -d
```

## 3. Миграция БД

### 3.1 Переход в контейнер php
```bash

docker exec -it app-symfony-php /bin/bash
```

### 3.2 Запуск миграции БД
```bash

php bin/console doctrine:migrations:migrate
```

### 4. Тестирование API
После выполнения всех шагов API будет готов к использованию. Для удобства тестирования прилагается JSON-файл "test_task.postman_collection.json" с коллекцией запросов для Postman. Вы можете импортировать его в Postman, чтобы не создавать запросы вручную.

# Описание API гостей

## Базовый url:
```http
http://localhost:80
```

## Debug-заголовки ответов:
- X-Debug-Time - время работы скрипта
- X-Debug-Memory - затраченная память

## Методы API:

### 1. Добавление гостя

**Метод:** ```POST /api/v1/guest```

**Заголовок запроса:** ```Content-Type: application/json```

**Ограничения**: 
    
- ```name, surname, phone - обязательные поля``` 
- ```phone, email - уникальные значения```

**Тело запроса (JSON):**
```json
{
    "name": "name",
    "surname": "surname",
    "phone": "79876543210",
    "email": "mail@mail.ru",
    "country": "Россия"
}
```

**Возможные ответы:**

- Успех:
```json
{
  "code": 201,
  "data": "Guest has been created"
}
```

- Ошибка:
```json
{
  "code": 400,
  "data": [
    {
      'field': <field_name>,
      "message": <error>
    }
  ]
}
```

### 2. Получение всех гостей

**Метод:** ```GET /api/v1/guests```

**Возможные ответы:**

- Успех:
```json
{
  "code": 200,
  "data": [
    {
      "id": 1,
      "name": "name3",
      "surname": "surnamee3",
      "phone": "79876543210",
      "country": "Россия",
      "email": null
    },
    {
      "id": 2,
      "name": "name",
      "surname": "surname",
      "phone": "79876543211",
      "country": "Россия",
      "email": "email1@mail.ru"
    }
  ]
}
```

- Если гость не добавлен - пустой ответ со статусом **204**

### 3. Получение гостя по ID

**Метод:** ```GET /api/v1/guests/{id}```

**Ограничения**: ```id - должно быть целым числом больше нуля```

**Возможные ответы:**

- Успех:
```json
{
  "code": 200,
  "data": {
    "id": 1,
    "name": "name3",
    "surname": "surnamee3",
    "phone": "79876543210",
    "country": "Россия",
    "email": null
  }
}
```

- Если гость не найден:
```json
{
  "code": 404,
  "data": "Guest not found"
}
```

### 4. Обновление всех полей гостя

**Метод:** ```PUT /api/v1/guests/{id}```

**Заголовок запроса:** ```Content-Type: application/json```

**Ограничения**: 
- ```id - должно быть целым числом больше нуля```
- ```name, surname, phone - обязательные поля```
- ```phone, email - уникальные значения```

**Возможные ответы:**

- Успех:
```json
{
  "code": 200,
  "data": "Guest has been updated"
}
```

- Гость не найден:
```json
{
  "code": 404,
  "data": "Guest not found"
}
```

- Ошибка в параметрах:
```json
{
  "code": 400,
  "data": [
    {
      "field": <field_name>,
      "message": <error>
    }
  ]
}
```

- Указанный телефон или email уже принадлежит другому гостю:
```json
{
  "code": 409,
  "data": "Guest with this phone or email already exists"
}
```

### 5. Обновление только переданных полей гостя

**Метод:** ```PATCH /api/v1/guests/{id}```

**Заголовок запроса:** ```Content-Type: application/json```

**Ограничения**:
- ```id - должно быть целым числом больше нуля```
- ```phone, email - уникальные значения```

**Возможные ответы:**

- Успех:
```json
{
  "code": 200,
  "data": "Guest has been updated"
}
```

- Гость не найден:
```json
{
  "code": 404,
  "data": "Guest not found"
}
```

- Ошибка в параметрах:
```json
{
  "code": 400,
  "data": [
    {
      "field": <field_name>,
      "message": <error>
    }
  ]
}
```

- Указанный телефон или email уже принадлежит другому гостю:
```json
{
  "code": 409,
  "data": "Guest with this phone or email already exists"
}
```

### 6. Обновление только переданных полей гостя

**Метод:** ```DELETE /api/v1/guests/{id}```

**Ограничения**:
- ```id - должно быть целым числом больше нуля```

**Возможные ответы:**

- Успех - пустой ответ со статусом **204**

- Гость не найден:
```json
{
  "code": 404,
  "data": "Guest not found"
}
```