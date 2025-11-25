#!/usr/bin/env python3
"""Парсер через Baidu по списку ключевых фраз.

Алгоритм:
1. Читает ключевые фразы из файла (по одной в строке).
2. Для каждой фразы открывает страницу поиска на https://www.baidu.com/.
3. Находит сайты в результатах поиска.
4. Переходит на каждый найденный сайт.
5. Ищет контактные e-mail на сайте.
6. Сохраняет в текстовый файл строки вида: название (заголовок результата); сайт; e-mail.

Зависимости: requests, beautifulsoup4
Перед запуском установите зависимости:
  python install_requirements.py

Пример запуска:
  python baidu_scraper.py --keywords-file keywords.txt --output-file baidu_results.txt
"""

import argparse
import csv
import re
import sys
import time
from pathlib import Path
from typing import List, Optional, Sequence, Tuple
from urllib.parse import urljoin, urlparse, quote_plus

import requests
from bs4 import BeautifulSoup

BASE_SEARCH_URL = "https://www.baidu.com/s"

HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/123.0 Safari/537.36"
    ),
    # Добавляем китайский, чтобы результаты Baidu были максимально полными
    "Accept-Language": "zh-CN,zh;q=0.9,ru-RU,ru;q=0.8,en-US;q=0.7,en;q=0.6",
}

SOCIAL_DOMAINS = (
    "facebook.com",
    "linkedin.com",
    "twitter.com",
    "instagram.com",
    "youtube.com",
)

EMAIL_RE = re.compile(r"[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}", re.IGNORECASE)

# Регулярное выражение для URL сайта (http/https, www, либо просто домен.tld/...)
URL_RE = re.compile(
    r"((?:https?://|www\.)[^\s\"'<>]+|[A-Za-z0-9.-]+\.[A-Za-z]{2,}(?:/[^\s\"'<>]*)?)",
    re.IGNORECASE,
)


def load_keywords(path: Path) -> List[str]:
    """Загружает ключевые фразы из файла (по одной в строке)."""
    if not path.exists():
        print(f"Файл с ключевыми фразами не найден: {path}", file=sys.stderr)
        sys.exit(1)

    raw_text = path.read_text(encoding="utf-8", errors="ignore")
    keywords: List[str] = []
    for line in raw_text.splitlines():
        value = line.strip()
        if not value or value.startswith("#"):
            continue
        keywords.append(value)

    if not keywords:
        print("Файл с ключевыми фразами пуст.", file=sys.stderr)
        sys.exit(1)

    return keywords


def build_search_url(keyword: str) -> str:
    """Формирует URL для поиска в Baidu."""
    query = quote_plus(keyword)
    return f"{BASE_SEARCH_URL}?wd={query}&ie=utf-8"


def create_session() -> requests.Session:
    """Создает сессию requests с базовыми заголовками."""
    session = requests.Session()
    session.headers.update(HEADERS)
    return session


def fetch(session: requests.Session, url: str, *, timeout: int = 20) -> Optional[requests.Response]:
    """Безопасно выполняет GET-запрос.

    В случае ошибки выводит сообщение и возвращает None.
    """
    try:
        response = session.get(url, timeout=timeout)
        response.raise_for_status()
        # Дополнительная проверка на страницу с капчей/защитой
        if is_captcha_response(response, url):
            return None
        return response
    except requests.RequestException as exc:
        print(f"Ошибка запроса {url}: {exc}", file=sys.stderr)
        return None


def is_captcha_response(response: requests.Response, url: str) -> bool:
    """Пытается определить, что вместо обычной страницы получена капча/страница защиты.

    Скрипт не решает капчу автоматически, только сообщает о факте её появления.
    """
    status = response.status_code
    content_type = response.headers.get("Content-Type", "").lower()

    # Часто капча/защита сопровождается кодами 403/429
    if status in (403, 429):
        print(
            "Поисковая система вернула страницу защиты (HTTP статус 403/429). "
            "Уменьшите частоту запросов или выполните часть действий вручную в браузере.",
            file=sys.stderr,
        )
        print(f"URL: {url}", file=sys.stderr)
        return True

    if "text/html" not in content_type:
        return False

    try:
        html = response.text.lower()
    except Exception:
        return False

    # Набор характерных фраз для страниц с проверкой пользователя
    captcha_markers = [
        "captcha",  # общее слово
        "are you human",
        "security verification",
        "verify your identity",
        "verify you are human",
        "robot check",
        "滑块验证",  # slider captcha
        "安全验证",  # security verification (cn)
    ]

    if any(marker in html for marker in captcha_markers):
        print(
            "Получена страница с капчей/проверкой пользователя. "
            "Скрипт не решает капчу автоматически. Попробуйте снизить частоту запросов "
            "(параметр --delay), уменьшить число результатов на одну фразу "
            "или выполнить часть действий вручную в браузере.",
            file=sys.stderr,
        )
        print(f"URL: {url}", file=sys.stderr)
        return True

    return False


def normalize_company_base_url(href: str) -> Optional[str]:
    """Преобразует ссылку компании в базовый URL вида https://xxx.en.alibaba.com."""
    if not href:
        return None

    href = href.strip()
    if href.startswith("//"):
        href = "https:" + href
    elif href.startswith("/"):
        href = "https://www.alibaba.com" + href
    elif not href.startswith("http"):
        return None

    parsed = urlparse(href)
    if not parsed.scheme.startswith("http") or not parsed.netloc:
        return None

    return f"{parsed.scheme}://{parsed.netloc}"


def normalize_external_site_url(url: str, base_url: str) -> Optional[str]:
    """Нормализует URL внешнего сайта компании (http/https, без Alibaba и соцсетей)."""
    url = (url or "").strip()
    if not url:
        return None

    lower = url.lower()
    # Добавляем схему, если она отсутствует
    if lower.startswith("//"):
        url = "https:" + url
    elif not lower.startswith("http://") and not lower.startswith("https://"):
        if " " not in url:
            url = "http://" + url

    parsed = urlparse(urljoin(base_url, url))
    if parsed.scheme not in ("http", "https") or not parsed.netloc:
        return None

    domain = parsed.netloc.lower()
    # Игнорируем домены Alibaba и связанные сервисы
    if "alibaba.com" in domain or "alicdn.com" in domain or "aliexpress.com" in domain:
        return None
    if any(social in domain for social in SOCIAL_DOMAINS):
        return None

    return f"{parsed.scheme}://{parsed.netloc}{parsed.path or ''}".rstrip("/")


def parse_search_companies(html: str, max_companies: int) -> List[Tuple[str, str]]:
    """Извлекает сайты из результатов поиска Baidu.

    Возвращает список (заголовок результата, URL).
    """
    soup = BeautifulSoup(html, "html.parser")

    results: List[Tuple[str, str]] = []
    seen_urls = set()

    # Основной вариант: результаты находятся в заголовках h3 с вложенной ссылкой
    for h3 in soup.find_all("h3"):
        a = h3.find("a", href=True)
        if not a:
            continue

        title = a.get_text(strip=True)
        href = (a.get("href") or "").strip()
        if not title or not href:
            continue

        if href in seen_urls:
            continue
        seen_urls.add(href)
        results.append((title, href))

        if len(results) >= max_companies:
            break

    # Резервный вариант: ищем ссылки внутри блоков результатов, если ничего не нашли
    if not results:
        for div in soup.find_all("div"):
            classes = " ".join(div.get("class") or [])
            if "result" not in classes:
                continue

            a = div.find("a", href=True)
            if not a:
                continue

            title = a.get_text(strip=True)
            href = (a.get("href") or "").strip()
            if not title or not href or href in seen_urls:
                continue

            seen_urls.add(href)
            results.append((title, href))

            if len(results) >= max_companies:
                break

    return results


def build_contact_url(base_url: str) -> str:
    """Строит URL страницы контактов компании."""
    base = base_url.rstrip("/") + "/"
    return urljoin(base, "contactinfo.html")


def find_company_website_url(html: str, page_url: str) -> Optional[str]:
    """Ищет внешний сайт компании на странице контактов.

    Сначала пытается найти блок вида:
      <div class="msg-item"> ... <span class="msg-title">Сайт компании:</span> ...
      <span class="value">http://example.com</span>
    Если такой блок не найден, использует резервный поиск по ссылкам.
    """
    soup = BeautifulSoup(html, "html.parser")

    # 1. Специализированный блок contactinfo: div.msg-item + msg-title + value
    for item in soup.find_all("div", class_="msg-item"):
        title_span = item.find("span", class_="msg-title")
        if not title_span:
            continue
        title_text = (title_span.get_text(strip=True) or "").lower()
        if "сайт компании" not in title_text and "company website" not in title_text and "website" not in title_text:
            continue

        value_span = item.find("span", class_="value") or item.find("a", class_="value")
        if not value_span:
            value_span = item.find("span", class_="value")
        if not value_span:
            continue

        site_text = (value_span.get_text(strip=True) or "").strip()
        if not site_text:
            continue

        site_url = normalize_external_site_url(site_text, page_url)
        if site_url:
            return site_url

    # 2. Резервный вариант: ищем подходящие внешние ссылки
    candidates: List[Tuple[int, str]] = []

    for a in soup.find_all("a", href=True):
        href = (a.get("href") or "").strip()
        if not href or href.lower().startswith("mailto:"):
            continue

        site_url = normalize_external_site_url(href, page_url)
        if not site_url:
            continue

        text = (a.get_text(strip=True) or "").lower()
        score = 0
        if "website" in text or "сайт" in text or "official" in text:
            score += 2
        if "home" in text or "company" in text:
            score += 1

        candidates.append((score, site_url))

    if candidates:
        candidates.sort(key=lambda item: item[0], reverse=True)
        return candidates[0][1]

    # 3. Дополнительный fallback: ищем URL по regex в текстовом содержимом
    text_content = soup.get_text(separator=" ", strip=True)
    for match in URL_RE.finditer(text_content):
        candidate = match.group(1)
        site_url = normalize_external_site_url(candidate, page_url)
        if site_url:
            return site_url

    return None


def extract_emails_from_html(html: str) -> List[str]:
    """Ищет e-mail адреса в HTML-тексте."""
    found = EMAIL_RE.findall(html)
    emails: List[str] = []
    seen_lower = set()

    for raw in found:
        email = raw.strip().strip(".,;:()[]<>")
        lower = email.lower()
        if "@" not in email:
            continue
        if lower in seen_lower:
            continue
        seen_lower.add(lower)
        emails.append(email)

    return emails


def find_email_on_page(html: str) -> Optional[str]:
    """Возвращает первый найденный e-mail на странице."""
    emails = extract_emails_from_html(html)
    return emails[0] if emails else None


def find_email_on_site(
    session: requests.Session,
    start_url: str,
    *,
    timeout: int = 20,
    max_pages: int = 2,
) -> Optional[str]:
    """Ищет e-mail на сайте компании.

    Проверяет главную страницу и до max_pages страниц с "contact"/"контакт" в ссылке.
    """
    to_visit: List[str] = [start_url]
    visited = set()
    pages_checked = 0

    while to_visit and pages_checked < max_pages:
        current = to_visit.pop(0)
        if current in visited:
            continue
        visited.add(current)

        response = fetch(session, current, timeout=timeout)
        if not response:
            continue

        pages_checked += 1
        email = find_email_on_page(response.text)
        if email:
            return email

        # Ищем ссылки на страницы контактов
        soup = BeautifulSoup(response.text, "html.parser")
        for a in soup.find_all("a", href=True):
            href = a["href"]
            text = (a.get_text(strip=True) or "").lower()
            href_lower = href.lower()
            if (
                "contact" in href_lower
                or "контакт" in href_lower
                or "contacts" in text
                or "contact" in text
                or "контакты" in text
            ):
                full_url = urljoin(current, href)
                if full_url not in visited and full_url not in to_visit:
                    to_visit.append(full_url)

    return None


def save_results(path: Path, rows: Sequence[Tuple[str, str, str]]) -> None:
    """Сохраняет результаты в текстовый файл.

    Формат: название компании; сайт; e-mail (одна запись на строку).
    """
    with path.open("w", encoding="utf-8", newline="") as f:
        writer = csv.writer(f, delimiter=";", quoting=csv.QUOTE_MINIMAL)
        for name, site, email in rows:
            writer.writerow([name, site, email])


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Парсер Baidu по списку ключевых фраз.",
    )
    parser.add_argument(
        "--keywords-file",
        default="keywords.txt",
        help="Путь к файлу с ключевыми фразами (по умолчанию keywords.txt)",
    )
    parser.add_argument(
        "--output-file",
        default="baidu_results.txt",
        help="Файл для сохранения результатов (по умолчанию baidu_results.txt)",
    )
    parser.add_argument(
        "--max-companies",
        type=int,
        default=5,
        help="Максимальное число сайтов (результатов) на одну фразу (по умолчанию 5)",
    )
    parser.add_argument(
        "--delay",
        type=float,
        default=2.0,
        help="Пауза между запросами (секунды, по умолчанию 2.0)",
    )

    args = parser.parse_args()

    keywords = load_keywords(Path(args.keywords_file))
    session = create_session()

    results: List[Tuple[str, str, str]] = []

    for keyword in keywords:
        print(f"=== Ключевая фраза: {keyword} ===")

        search_url = build_search_url(keyword)
        print(f"URL поиска: {search_url}")
        response = fetch(session, search_url)
        if not response:
            continue

        sites = parse_search_companies(response.text, args.max_companies)
        if not sites:
            print("Результаты поиска не найдены.", file=sys.stderr)
            continue

        for title, href in sites:
            print(f"- Результат: {title} ({href})")

            time.sleep(args.delay)
            first_resp = fetch(session, href)

            site_url: str = ""
            email: str = ""

            if first_resp:
                # После возможных редиректов получаем конечный URL сайта
                site_url = first_resp.url
                # Пытаемся найти e-mail прямо на открытой странице
                email = find_email_on_page(first_resp.text) or ""

            if site_url and not email:
                # Если есть сайт, но нет e-mail, пытаемся найти на других страницах сайта
                print(f"  Сайт: {site_url}")
                time.sleep(args.delay)
                email_from_site = find_email_on_site(session, site_url)
                if email_from_site:
                    email = email_from_site

            print(f"  Результат: сайт={site_url or '-'}, email={email or '-'}")
            results.append((title, site_url, email))

    if not results:
        print("Не удалось собрать ни одной записи.", file=sys.stderr)
        return

    output_path = Path(args.output_file)
    save_results(output_path, results)
    print(f"Сохранено записей: {len(results)}")
    print(f"Файл с результатами: {output_path}")


if __name__ == "__main__":
    main()
