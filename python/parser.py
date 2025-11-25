#!/usr/bin/env python3
"""Простой CLI‑скрипт для парсинга JSON и HTML по указанному URL.

Использование (примеры):
  python parser.py --url https://example.com/api --type json
  python parser.py --url https://example.com --type html --selector "title"

Параметры:
  --url       Обязательный URL для запроса
  --type      Тип ожидаемого ответа: auto | json | html (по умолчанию auto)
  --selector  Для HTML: CSS‑селектор для выборки элементов (необязательный)
"""

import argparse
import json
import sys
from typing import Any, Optional

import requests
from bs4 import BeautifulSoup


def fetch_url(url: str) -> requests.Response:
    """Выполняет HTTP‑запрос к указанному URL."""
    try:
        response = requests.get(url, timeout=15)
        response.raise_for_status()
        return response
    except requests.RequestException as exc:
        print(f"Ошибка запроса: {exc}", file=sys.stderr)
        sys.exit(1)


def parse_json(response: requests.Response) -> Any:
    """Парсит JSON‑ответ и возвращает объект Python."""
    try:
        return response.json()
    except json.JSONDecodeError as exc:
        print(f"Не удалось разобрать JSON: {exc}", file=sys.stderr)
        sys.exit(1)


def parse_html(response: requests.Response, selector: Optional[str] = None) -> None:
    """Парсит HTML‑ответ и выводит содержимое.

    Если указан CSS‑селектор, будут выведены все совпадающие элементы.
    Если селектор не указан, выводится заголовок страницы и первые 500 символов текста.
    """
    html = response.text
    soup = BeautifulSoup(html, "html.parser")

    if selector:
        elements = soup.select(selector)
        if not elements:
            print("По указанному селектору ничего не найдено.")
            return
        for idx, element in enumerate(elements, start=1):
            print(f"==== Элемент {idx} ====")
            print(element.get_text(strip=True))
    else:
        title = soup.title.string.strip() if soup.title and soup.title.string else "(без заголовка)"
        text = soup.get_text(separator=" ", strip=True)
        short_text = text[:500] + ("..." if len(text) > 500 else "")
        print(f"Заголовок: {title}")
        print("Текст (первые 500 символов):")
        print(short_text)


def auto_detect_and_parse(response: requests.Response, selector: Optional[str] = None) -> None:
    """Автоматически определяет тип ответа (JSON/HTML) по заголовкам и содержимому."""
    content_type = response.headers.get("Content-Type", "").lower()

    if "application/json" in content_type:
        data = parse_json(response)
        print(json.dumps(data, ensure_ascii=False, indent=2))
        return

    # Пробуем как JSON для текстового ответа
    if "text/" in content_type:
        try:
            data = response.json()
        except json.JSONDecodeError:
            # Считаем, что это HTML или обычный текст
            parse_html(response, selector=selector)
        else:
            print(json.dumps(data, ensure_ascii=False, indent=2))
        return

    # По умолчанию пробуем HTML
    parse_html(response, selector=selector)


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        description="Утилита для парсинга JSON и HTML по URL.",
    )
    parser.add_argument(
        "--url",
        required=True,
        help="URL, с которого нужно получить данные",
    )
    parser.add_argument(
        "--type",
        choices=["auto", "json", "html"],
        default="auto",
        help="Тип ожидаемого ответа: auto (по умолчанию), json или html",
    )
    parser.add_argument(
        "--selector",
        help="Для HTML: CSS‑селектор, по которому нужно выбрать элементы (необязательно)",
    )
    return parser


def main() -> None:
    parser = build_parser()
    args = parser.parse_args()

    response = fetch_url(args.url)

    if args.type == "json":
        data = parse_json(response)
        print(json.dumps(data, ensure_ascii=False, indent=2))
    elif args.type == "html":
        parse_html(response, selector=args.selector)
    else:
        auto_detect_and_parse(response, selector=args.selector)


if __name__ == "__main__":
    main()
