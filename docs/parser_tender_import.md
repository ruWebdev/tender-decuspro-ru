# Импорт тендера из JSON (для внешней программы)

## Endpoint
- **POST** `/parser/tenders/import`

## Заголовки
- **Content-Type:** `application/json`
- **X-Parser-Token:** `<TOKEN>`

`TOKEN` хранится в БД в таблице `settings` по ключу `parser_tender_import_token`.

## Тело запроса
Передавайте JSON объект со структурой:
- `data.items` — массив позиций (как в `request_example.json`)
- (опционально) `customer_id` — UUID заказчика (если не передать, будет выбран первый пользователь с ролью `customer`)
- (опционально) `valid_until_days_from_now` — срок в днях (1..60), по умолчанию 7

Минимальный пример:
```json
{
  "data": {
    "items": [
      {
        "id": 1,
        "parent_item_id": null,
        "description": "Монитор 27\"",
        "quantity": 1,
        "price": "13990.00",
        "price_sale": "18886.50",
        "supplier_name": "Citilink",
        "alternatives": []
      }
    ]
  }
}
```

## Пример curl
```bash
curl -X POST "https://<HOST>/parser/tenders/import" \
  -H "Content-Type: application/json" \
  -H "X-Parser-Token: <TOKEN>" \
  --data-binary @request_example.json
```

## Ответ
Успех (HTTP 200):
```json
{ "tender_id": "<UUID>" }
```

## Ошибки
- **401/403** — нет/неверный `X-Parser-Token`
- **422** — ошибка валидации или не найден заказчик
- **500** — ошибка создания тендера
