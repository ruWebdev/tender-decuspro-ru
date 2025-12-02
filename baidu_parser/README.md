# Baidu Parser - Парсер контактов компаний

Парсер для извлечения телефонов и email компаний из поисковой системы Baidu.

## Установка

```bash
cd baidu_parser
pip install -r requirements.txt
playwright install chromium
```

## Использование

### Базовый пример

```python
from baidu_parser import BaiduParser

keywords = [
    "深圳电子公司 联系方式",  # Электронные компании Шэньчжэня
    "广州贸易公司 电话",      # Торговые компании Гуанчжоу
]

with BaiduParser(headless=False) as parser:
    parser.parse_keywords(keywords, max_pages=2, max_results_per_keyword=10)
    parser.save_to_csv("results.csv")
```

### Параметры

- `headless` - запуск браузера в фоновом режиме (True/False)
- `delay_range` - диапазон задержки между запросами в секундах (по умолчанию 2-5 сек)
- `max_pages` - количество страниц поиска Baidu
- `max_results_per_keyword` - максимум сайтов для парсинга на каждое ключевое слово

## Формат вывода

CSV файл с колонками:
- Ключевое слово
- Название компании
- URL
- Телефоны (через ;)
- Email (через ;)

## Важные замечания

1. **Rate Limiting** - Baidu может заблокировать IP при слишком частых запросах. Используйте разумные задержки.

2. **CAPTCHA** - При обнаружении бота Baidu может показать капчу. В режиме `headless=False` можно решить её вручную.

3. **VPN** - Для доступа к Baidu из некоторых регионов может потребоваться VPN.

4. **Легальность** - Убедитесь, что парсинг соответствует законодательству вашей юрисдикции и условиям использования Baidu.
