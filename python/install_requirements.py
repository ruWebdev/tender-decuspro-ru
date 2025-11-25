#!/usr/bin/env python3
"""Скрипт для быстрой установки Python‑зависимостей проекта.

Запуск:
  python install_requirements.py

Скрипт ищет рядом файл requirements.txt и выполняет команду:
  python -m pip install -r requirements.txt
"""

import subprocess
import sys
from pathlib import Path


def main() -> None:
    base_dir = Path(__file__).resolve().parent
    requirements_file = base_dir / "requirements.txt"

    if not requirements_file.exists():
        print("Файл requirements.txt не найден.", file=sys.stderr)
        sys.exit(1)

    cmd = [sys.executable, "-m", "pip", "install", "-r", str(requirements_file)]

    print("Выполняется команда:")
    print(" ", " ".join(cmd))

    try:
        subprocess.check_call(cmd)
    except subprocess.CalledProcessError as exc:
        print(f"Ошибка установки зависимостей (код {exc.returncode}).", file=sys.stderr)
        sys.exit(exc.returncode)


if __name__ == "__main__":
    main()
