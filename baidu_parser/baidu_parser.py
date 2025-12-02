"""
Baidu Search Parser - извлечение контактов компаний (телефон, email)
"""

import re
import time
import random
import csv
import requests
from datetime import datetime
from typing import Optional
from dataclasses import dataclass, field
from playwright.sync_api import sync_playwright, Page, Browser


# API конфигурация
API_BASE_URL = "https://tenders.qbs.ru"
API_TIMEOUT = 15  # секунд
MAX_FIELD_LENGTH = 255  # Максимальная длина полей для API


@dataclass
class CompanyContact:
    """Контактные данные компании"""
    keyword: str
    title: str
    url: str
    phones: list[str] = field(default_factory=list)
    emails: list[str] = field(default_factory=list)


class BaiduParser:
    """Парсер поисковой системы Baidu для извлечения контактов компаний"""
    
    # Регулярные выражения для поиска китайских телефонов
    PHONE_PATTERNS = [
        r'(\+86[\s\-]?1[3-9]\d[\s\-]?\d{4}[\s\-]?\d{4})',  # +86 мобильный
        r'(?<!\d)(86[\s\-]?1[3-9]\d[\s\-]?\d{4}[\s\-]?\d{4})',  # 86 мобильный
        r'(\+86[\s\-]?[02]\d{1,2}[\s\-]?\d{7,8})',  # +86 стационарный с кодом города
        r'(?<!\d)(86[\s\-]?[02]\d{1,2}[\s\-]?\d{7,8})',  # 86 стационарный
    ]
    
    EMAIL_PATTERN = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'
    
    def __init__(self, headless: bool = False, delay_range: tuple = (2, 5), api_base_url: str = API_BASE_URL):
        """
        Args:
            headless: Запуск браузера в фоновом режиме
            delay_range: Диапазон задержки между запросами (сек)
            api_base_url: Базовый URL API сервера
        """
        self.headless = headless
        self.delay_range = delay_range
        self.api_base_url = api_base_url.rstrip('/') if api_base_url else None
        self.browser: Optional[Browser] = None
        self.page: Optional[Page] = None
        self.results: list[CompanyContact] = []
        self.skipped_existing = 0  # Счётчик пропущенных (уже в БД)
    
    def __enter__(self):
        self.start()
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        self.stop()
    
    def start(self):
        """Запуск браузера"""
        self.playwright = sync_playwright().start()
        self.browser = self.playwright.chromium.launch(headless=self.headless)
        self.context = self.browser.new_context(
            viewport={'width': 1920, 'height': 1080},
            locale='zh-CN',
        )
        self.page = self.context.new_page()
        print("[+] Браузер запущен")
    
    def stop(self):
        """Остановка браузера"""
        if self.browser:
            self.browser.close()
        if hasattr(self, 'playwright'):
            self.playwright.stop()
        print("[+] Браузер остановлен")
    
    def _random_delay(self):
        """Случайная задержка для имитации человека"""
        delay = random.uniform(*self.delay_range)
        time.sleep(delay)
    
    def _truncate_field(self, value: str, max_length: int = MAX_FIELD_LENGTH) -> str:
        """Обрезает строку до максимальной длины"""
        if not value:
            return ""
        return value[:max_length] if len(value) > max_length else value
    
    def _normalize_payload(self, payload: dict) -> dict:
        """Нормализует payload: обрезает все строковые поля до MAX_FIELD_LENGTH"""
        normalized = {}
        for key, value in payload.items():
            if isinstance(value, str):
                normalized[key] = self._truncate_field(value)
            else:
                normalized[key] = value
        return normalized
    
    def _api_request(self, endpoint: str, payload: dict) -> Optional[dict]:
        """
        Выполнение POST запроса к API
        
        Args:
            endpoint: Путь API (например /parser/platform-suppliers/check)
            payload: Данные для отправки
            
        Returns:
            JSON ответ или None при ошибке
        """
        if not self.api_base_url:
            return None
        
        url = f"{self.api_base_url}{endpoint}"
        
        # Нормализуем payload перед отправкой
        normalized_payload = self._normalize_payload(payload)
        
        try:
            response = requests.post(
                url,
                json=normalized_payload,
                headers={
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                timeout=API_TIMEOUT
            )
            
            if not response.ok:
                print(f"    [!] API ошибка {response.status_code}: {url}")
                # Логируем тело ответа для отладки (особенно важно для 422)
                try:
                    error_body = response.json()
                    print(f"    [!] Детали ошибки: {error_body}")
                except Exception:
                    print(f"    [!] Тело ответа: {response.text[:500]}")
                return None
            
            return response.json()
        except requests.exceptions.Timeout:
            print(f"    [!] API таймаут: {url}")
            return None
        except Exception as e:
            print(f"    [!] API ошибка: {e}")
            return None
    
    def api_check_company(self, name: str, website: str = "", email: str = "") -> bool:
        """
        Проверка, существует ли компания в удалённой БД
        
        Returns:
            True если компания уже есть в БД
        """
        if not name:
            return False
        
        payload = {"name": name}
        if website:
            payload["website"] = website
        if email:
            payload["email"] = email
        
        result = self._api_request("/parser/platform-suppliers/check", payload)
        
        if result and result.get("exists"):
            return True
        return False
    
    def api_store_company(self, name: str, website: str = "", email: str = "", phone: str = "") -> bool:
        """
        Сохранение компании в удалённую БД
        
        Returns:
            True если успешно сохранено
        """
        if not name:
            return False
        
        payload = {"name": name}
        if website:
            payload["website"] = website
        if email:
            payload["email"] = email
        if phone:
            payload["phone"] = phone
        
        result = self._api_request("/parser/platform-suppliers/store", payload)
        return result is not None
    
    def search_baidu(self, keyword: str, max_pages: int = 3) -> list[dict]:
        """
        Поиск по ключевому слову в Baidu
        
        Args:
            keyword: Ключевое слово для поиска
            max_pages: Максимальное количество страниц результатов
            
        Returns:
            Список найденных ссылок
        """
        search_results = []
        
        for page_num in range(max_pages):
            url = f"https://www.baidu.com/s?wd={keyword}&pn={page_num * 10}"
            print(f"[*] Поиск: '{keyword}', страница {page_num + 1}")
            
            try:
                self.page.goto(url, wait_until='networkidle', timeout=30000)
                self._random_delay()
                
                # Извлекаем результаты поиска
                results = self.page.query_selector_all('.result.c-container')
                
                for result in results:
                    try:
                        title_el = result.query_selector('h3 a')
                        if title_el:
                            title = title_el.inner_text()
                            href = title_el.get_attribute('href')
                            if href:
                                search_results.append({
                                    'title': title,
                                    'url': href,
                                    'keyword': keyword
                                })
                    except Exception as e:
                        continue
                
                print(f"    Найдено {len(results)} результатов на странице")
                
            except Exception as e:
                print(f"[!] Ошибка при поиске: {e}")
                break
        
        return search_results
    
    def _get_final_url(self, url: str) -> str:
        """
        Получает финальный URL после редиректов.
        Baidu часто использует редирект-ссылки вида baidu.com/link?url=...
        которые могут быть очень длинными.
        """
        try:
            # Если это не baidu-редирект, возвращаем как есть
            if 'baidu.com/link' not in url:
                return url
            
            # Пытаемся получить финальный URL через HEAD-запрос
            response = requests.head(url, allow_redirects=True, timeout=10)
            final_url = response.url
            
            # Если финальный URL короче, используем его
            if len(final_url) < len(url):
                return final_url
            return url
        except Exception:
            return url
    
    def extract_contacts_from_page(self, url: str) -> tuple[list[str], list[str], str]:
        """
        Извлечение контактов со страницы
        
        Returns:
            (phones, emails, final_url)
        """
        phones = set()
        emails = set()
        final_url = url
        
        try:
            self.page.goto(url, wait_until='domcontentloaded', timeout=15000)
            self._random_delay()
            
            # Получаем финальный URL после редиректов браузера
            final_url = self.page.url
            
            # Получаем текст страницы
            content = self.page.content()
            text = self.page.inner_text('body')
            
            # Ищем email
            found_emails = re.findall(self.EMAIL_PATTERN, content, re.IGNORECASE)
            emails.update(found_emails)
            
            # Ищем телефоны (только китайские с кодом 86)
            for pattern in self.PHONE_PATTERNS:
                found_phones = re.findall(pattern, text + content)
                for phone in found_phones:
                    # Очистка и нормализация номера
                    clean_phone = re.sub(r'[\s\-()]', '', phone)
                    # Приводим к формату +86...
                    if clean_phone.startswith('86') and not clean_phone.startswith('+'):
                        clean_phone = '+' + clean_phone
                    phones.add(clean_phone)
            
        except Exception as e:
            print(f"    [!] Ошибка при загрузке {url[:50]}...: {e}")
        
        return list(phones), list(emails), final_url
    
    def parse_keywords(self, keywords: list[str], max_pages: int = 2, max_results_per_keyword: int = 10):
        """
        Парсинг по списку ключевых слов
        
        Args:
            keywords: Список ключевых слов
            max_pages: Макс. страниц поиска на ключевое слово
            max_results_per_keyword: Макс. сайтов для парсинга на ключевое слово
        """
        for keyword in keywords:
            print(f"\n{'='*50}")
            print(f"[*] Обработка ключевого слова: {keyword}")
            print('='*50)
            
            # Поиск в Baidu
            search_results = self.search_baidu(keyword, max_pages)
            
            # Ограничиваем количество результатов
            search_results = search_results[:max_results_per_keyword]
            
            # Парсим каждый сайт
            for i, result in enumerate(search_results, 1):
                company_name = result['title']
                website = result['url']
                
                print(f"\n[{i}/{len(search_results)}] Парсинг: {company_name[:40]}...")
                
                # Извлекаем контакты и получаем финальный URL
                phones, emails, final_url = self.extract_contacts_from_page(website)
                
                # Используем финальный URL вместо baidu-редиректа
                # (он обычно короче и более информативен)
                actual_website = final_url if final_url else website
                
                # Проверяем, есть ли компания уже в БД
                if self.api_base_url:
                    if self.api_check_company(company_name, actual_website):
                        print(f"    ⏭ Компания уже есть в БД, пропускаем")
                        self.skipped_existing += 1
                        continue
                
                if phones or emails:
                    contact = CompanyContact(
                        keyword=keyword,
                        title=company_name,
                        url=actual_website,
                        phones=phones,
                        emails=emails
                    )
                    self.results.append(contact)
                    print(f"    ✓ Найдено: {len(phones)} тел., {len(emails)} email")
                    
                    # Сохраняем в удалённую БД
                    if self.api_base_url:
                        email = emails[0] if emails else ""
                        phone = phones[0] if phones else ""
                        if self.api_store_company(company_name, actual_website, email, phone):
                            print(f"    ✓ Сохранено в БД")
                        else:
                            print(f"    [!] Ошибка сохранения в БД")
                else:
                    print(f"    - Контакты не найдены")
        
        print(f"\n[+] Всего найдено контактов: {len(self.results)}")
        if self.skipped_existing:
            print(f"[+] Пропущено (уже в БД): {self.skipped_existing}")
    
    def save_to_csv(self, filename: str = None):
        """Сохранение результатов в CSV"""
        if not filename:
            filename = f"contacts_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        
        with open(filename, 'w', newline='', encoding='utf-8-sig') as f:
            writer = csv.writer(f)
            writer.writerow(['Ключевое слово', 'Название', 'URL', 'Телефоны', 'Email'])
            
            for contact in self.results:
                writer.writerow([
                    contact.keyword,
                    contact.title,
                    contact.url,
                    '; '.join(contact.phones),
                    '; '.join(contact.emails)
                ])
        
        print(f"[+] Результаты сохранены в {filename}")
        return filename


def main():
    """Пример использования парсера"""
    
    # Ключевые слова для поиска - HPE серверное оборудование
    keywords = [
        "HPE ProLiant DL380 Gen11 8SFF NC CTO Server",
        "Intel Xeon-Gold 6444Y 3.6GHz 16-core 270W Processor for HPE",
        "HPE 32GB (1x32GB) Dual Rank x8 DDR5-4800 CAS-40-39-39 EC8 Registered Smart Memory Kit",
        "HPE DDR4 DIMM Blank Kit",
        "Mellanox MCX631432AS-ADAI Ethernet 10/25Gb 2-port SFP28 OCP3 Adapter for HPE",
        "HPE ProLiant DL380 Gen11 2U 8SFF x1 Tri-Mode U.3 Drive Cage Kit",
        "Broadcom BCM57414 Ethernet 10/25Gb 2-port SFP28 OCP3 Adapter for HPE",
        "HPE ProLiant DL3XX Gen11 CPU2 to OCP2 x8 Enablement Kit",
        "HPE 1600W Flex Slot Platinum Hot Plug Low Halogen Power Supply Kit",
        "HPE Gen11 2U Bezel Kit",
        "HPE ProLiant DL3XX Gen11 Easy Install Rail 3 Kit",
        "HPE ProLiant DL380/DL560 Gen11 High Performance 2U Heat Sink Kit",
        "HPE ProLiant DL380/DL560 Gen11 2U High Performance Fan Kit",
        "HPE ProLiant DL380 Gen11 2U x16/x16/x16 Primary Riser Kit",
        "HPE ProLiant DL380 Gen11 2U x16/x16/x16 Secondary Riser Kit",
        "HPE ProLiant DL380/DL560 Gen11 2U GPU Power Cable Kit",
    ]
    
    # Суффиксы для расширения поисковых запросов (китайские термины)
    search_suffixes = [
        "供应商",    # поставщик
        "采购",      # закупка
        "经销商",    # дистрибьютор
        "价格",      # цена
        "批发",      # оптом
        "代理商",    # агент/дилер
        "销售",      # продажа
        "公司",      # компания
        "提供",      # предоставить
        "厂商",      # производитель
    ]
    
    # Создание расширенных поисковых запросов
    baidu_queries = []
    for keyword in keywords:
        for suffix in search_suffixes:
            baidu_queries.append(f"{keyword} {suffix}")
    
    print("="*60)
    print("Baidu Parser - Извлечение контактов компаний")
    print(f"Всего запросов: {len(baidu_queries)}")
    print("="*60)
    
    with BaiduParser(headless=False, delay_range=(3, 6)) as parser:
        parser.parse_keywords(
            keywords=baidu_queries,
            max_pages=5,              # 5 страниц результатов Baidu на запрос
            max_results_per_keyword=15  # до 15 сайтов на запрос
        )
        parser.save_to_csv()


if __name__ == "__main__":
    main()
