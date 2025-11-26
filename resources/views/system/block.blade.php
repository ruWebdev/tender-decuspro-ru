<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление проектом</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1a1a2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            padding: 40px;
            background: #16213e;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .status {
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }

        .status-value {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            margin-left: 8px;
        }

        .status-blocked {
            background: #ff4757;
            color: #fff;
        }

        .status-active {
            background: #2ed573;
            color: #fff;
        }

        .btn {
            display: inline-block;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-block {
            background: #ff4757;
            color: #fff;
        }

        .btn-block:hover {
            background: #ff6b7a;
            transform: translateY(-2px);
        }

        .btn-unblock {
            background: #2ed573;
            color: #fff;
        }

        .btn-unblock:hover {
            background: #7bed9f;
            transform: translateY(-2px);
        }

        .message {
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        .message-success {
            background: rgba(46, 213, 115, 0.2);
            color: #2ed573;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="status">
            Текущий статус:
            @if($isBlocked)
            <span class="status-value status-blocked">ЗАБЛОКИРОВАН</span>
            @else
            <span class="status-value status-active">АКТИВЕН</span>
            @endif
        </div>

        <form action="{{ route('system.block.toggle') }}" method="POST">
            @csrf
            @if($isBlocked)
            <button type="submit" class="btn btn-unblock">
                Разблокировать проект
            </button>
            @else
            <button type="submit" class="btn btn-block">
                Заблокировать проект
            </button>
            @endif
        </form>

        @if(session('success'))
        <div class="message message-success">
            {{ session('success') }}
        </div>
        @endif
    </div>
</body>

</html>