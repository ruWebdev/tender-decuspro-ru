<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблиц для интеграции с WeChat
     */
    public function up(): void
    {
        // Таблица для хранения настроек WeChat Official Account
        Schema::create('wechat_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('app_id')->nullable()->comment('WeChat AppID');
            $table->text('app_secret')->nullable()->comment('WeChat AppSecret (зашифрованный)');
            $table->string('token')->nullable()->comment('Токен для верификации сервера');
            $table->string('encoding_aes_key')->nullable()->comment('Ключ шифрования сообщений');
            $table->text('access_token')->nullable()->comment('Текущий access_token');
            $table->timestamp('access_token_expires_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Таблица для связи поставщиков с WeChat OpenID
        Schema::create('wechat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('platform_supplier_id')->nullable()->constrained('platform_suppliers')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('wechat_openid')->index()->comment('WeChat OpenID пользователя');
            $table->string('wechat_unionid')->nullable()->index()->comment('WeChat UnionID');
            $table->string('nickname')->nullable()->comment('Никнейм в WeChat');
            $table->string('avatar_url')->nullable()->comment('URL аватара');
            $table->string('remark')->nullable()->comment('Заметка администратора');
            $table->boolean('is_subscribed')->default(true)->comment('Подписан ли на Official Account');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->unique('wechat_openid');
        });

        // Таблица для хранения сообщений WeChat
        Schema::create('wechat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('wechat_conversations')->cascadeOnDelete();
            $table->enum('direction', ['incoming', 'outgoing'])->comment('Направление: входящее/исходящее');
            $table->enum('msg_type', ['text', 'image', 'voice', 'video', 'location', 'link', 'event'])->default('text');
            $table->text('content')->nullable()->comment('Текст сообщения');
            $table->text('translated_content_ru')->nullable()->comment('Перевод на русский');
            $table->text('media_id')->nullable()->comment('ID медиафайла в WeChat');
            $table->string('media_url')->nullable()->comment('URL медиафайла');
            $table->json('raw_data')->nullable()->comment('Сырые данные сообщения от WeChat');
            $table->string('wechat_msg_id')->nullable()->index()->comment('ID сообщения в WeChat');
            $table->boolean('is_read')->default(false);
            $table->foreignUuid('sender_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Отправитель (если исходящее)');
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wechat_messages');
        Schema::dropIfExists('wechat_conversations');
        Schema::dropIfExists('we_chat_settings');
    }
};
