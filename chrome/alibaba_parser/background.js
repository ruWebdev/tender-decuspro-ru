// Фоновый скрипт расширения для парсинга Alibaba
// Алгоритм:
// 1. Читает ключевые фразы из keywords.txt.
// 2. Для каждой фразы открывает поиск на https://www.alibaba.com/.
// 3. На странице поиска находит компании и ссылки на их профили.
// 4. Для каждой компании открывает страницу contactinfo.html, ищет внешний сайт.
// 5. Переходит на сайт компании и ищет контактный e-mail.
// 6. Сохраняет результаты в файл alibaba_results.txt ("название; сайт; email" по строкам).

const MAX_COMPANIES_PER_KEYWORD = 50; // базовый максимум компаний на одну фразу (ограничивается ещё и полем "Макс. ссылок" в popup)
const DELAY_BETWEEN_STEPS_MS = 5000; // пауза между шагами (мс)
const PAGE_LOAD_TIMEOUT_MS = 60000; // таймаут загрузки страницы (мс)
const MAX_SEARCH_PAGES_PER_KEYWORD = 3; // максимум страниц результатов поиска на одну фразу
const SCRIPT_EXECUTION_TIMEOUT_MS = 30000; // таймаут выполнения скрипта на странице (мс)
const API_REQUEST_TIMEOUT_MS = 15000; // таймаут API-запроса (мс)
const MAX_RETRIES = 3; // максимум повторных попыток при ошибке
const RETRY_DELAY_MS = 2000; // задержка перед повторной попыткой (мс)

let isRunning = false;
let stopRequested = false;
let apiBaseUrl = null;

const progressState = {
    isRunning: false,
    stopRequested: false,
    currentKeywordIndex: 0,
    totalKeywords: 0,
    currentCompanyIndex: 0,
    totalCompaniesCurrentKeyword: 0,
    processedCompanies: 0,
    totalCompaniesPlanned: 0,
    foundEmails: 0,
    maxLinks: null,
    maxCompaniesPerKeyword: null,
    lastMessage: "",
};

function getPublicProgressState() {
    return {
        isRunning: progressState.isRunning,
        stopRequested: progressState.stopRequested,
        currentKeywordIndex: progressState.currentKeywordIndex,
        totalKeywords: progressState.totalKeywords,
        currentCompanyIndex: progressState.currentCompanyIndex,
        totalCompaniesCurrentKeyword: progressState.totalCompaniesCurrentKeyword,
        processedCompanies: progressState.processedCompanies,
        totalCompaniesPlanned: progressState.totalCompaniesPlanned,
        foundEmails: progressState.foundEmails,
        maxLinks: progressState.maxLinks,
        maxCompaniesPerKeyword: progressState.maxCompaniesPerKeyword,
        lastMessage: progressState.lastMessage,
    };
}

function updateProgress(partial) {
    Object.assign(progressState, partial);
    try {
        chrome.runtime.sendMessage({
            type: "progress_update",
            state: getPublicProgressState(),
        });
    } catch (e) {
        // Игнорируем ошибки, если popup не открыт
    }
}

function normalizeApiBaseUrl(raw) {
    if (!raw) return null;
    const value = String(raw).trim();
    if (!value) return null;
    return value.replace(/\/+$/, "");
}

/**
 * Обёртка для выполнения функции с таймаутом
 */
function withTimeout(promise, timeoutMs, errorMessage = "Таймаут операции") {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => {
            reject(new Error(errorMessage));
        }, timeoutMs);

        promise
            .then((result) => {
                clearTimeout(timer);
                resolve(result);
            })
            .catch((err) => {
                clearTimeout(timer);
                reject(err);
            });
    });
}

/**
 * Выполнение функции с повторными попытками
 */
async function withRetry(fn, maxRetries = MAX_RETRIES, delayMs = RETRY_DELAY_MS) {
    let lastError = null;
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            return await fn();
        } catch (e) {
            lastError = e;
            console.warn(`Попытка ${attempt}/${maxRetries} не удалась:`, e.message);
            if (attempt < maxRetries) {
                await delay(delayMs);
            }
        }
    }
    throw lastError;
}

async function callParserApi(path, payload) {
    if (!apiBaseUrl) {
        return null;
    }

    const base = normalizeApiBaseUrl(apiBaseUrl);
    if (!base) {
        return null;
    }

    const url = base + path;

    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), API_REQUEST_TIMEOUT_MS);

        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify(payload || {}),
            signal: controller.signal,
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            console.warn("API запрос завершился с ошибкой", response.status, url);
            return null;
        }

        return await response.json().catch(() => null);
    } catch (e) {
        if (e.name === "AbortError") {
            console.warn("API запрос прерван по таймауту:", url);
        } else {
            console.warn("Ошибка при выполнении API-запроса", e);
        }
        return null;
    }
}

async function apiCheckCompany(name, website, email) {
    if (!name) {
        return null;
    }

    const payload = { name };

    if (website) {
        payload.website = website;
    }

    if (email) {
        payload.email = email;
    }

    return callParserApi("/parser/platform-suppliers/check", payload);
}

async function apiStoreCompany(name, website, email) {
    if (!name) {
        return null;
    }

    const payload = { name };

    if (website) {
        payload.website = website;
    }

    if (email) {
        payload.email = email;
    }

    return callParserApi("/parser/platform-suppliers/store", payload);
}

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (!message || typeof message.type !== "string") {
        return;
    }

    if (message.type === "get_state") {
        sendResponse({ ok: true, state: getPublicProgressState() });
        return;
    }

    if (message.type === "start") {
        if (isRunning) {
            sendResponse({ ok: false, error: "Парсер уже запущен" });
            return;
        }

        let maxLinks = null;
        if (typeof message.maxLinks === "number") {
            const value = Math.floor(message.maxLinks);
            if (value > 0) {
                maxLinks = value;
            }
        }

        let maxCompaniesPerKeyword = null;
        if (typeof message.maxCompaniesPerKeyword === "number") {
            const value = Math.floor(message.maxCompaniesPerKeyword);
            if (value > 0) {
                maxCompaniesPerKeyword = value;
            }
        }

        let apiUrl = null;
        if (typeof message.apiBaseUrl === "string") {
            apiUrl = normalizeApiBaseUrl(message.apiBaseUrl);
        }

        apiBaseUrl = apiUrl;

        stopRequested = false;
        isRunning = true;

        updateProgress({
            isRunning: true,
            stopRequested: false,
            currentKeywordIndex: 0,
            totalKeywords: 0,
            currentCompanyIndex: 0,
            totalCompaniesCurrentKeyword: 0,
            processedCompanies: 0,
            totalCompaniesPlanned: 0,
            foundEmails: 0,
            maxLinks,
            maxCompaniesPerKeyword,
            lastMessage: "Запуск парсера...",
        });

        runAlibabaParser().catch((err) => {
            console.error("Ошибка работы парсера Alibaba", err);
            updateProgress({
                isRunning: false,
                stopRequested: false,
                lastMessage: "Ошибка работы парсера. Подробности в консоли.",
            });
        });

        sendResponse({ ok: true });
        return;
    }

    if (message.type === "stop") {
        if (!isRunning) {
            sendResponse({ ok: false, error: "Парсер не запущен" });
            return;
        }

        stopRequested = true;
        updateProgress({
            stopRequested: true,
            lastMessage: "Остановка по запросу пользователя...",
        });
        sendResponse({ ok: true });
        return;
    }
});

function delay(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function loadKeywords() {
    try {
        const url = chrome.runtime.getURL("keywords.txt");
        const response = await fetch(url);
        if (!response.ok) {
            console.error("Не удалось прочитать keywords.txt, статус:", response.status);
            return [];
        }
        const text = await response.text();
        const lines = text
            .split(/\r?\n/)
            .map((line) => line.trim())
            .filter((line) => line && !line.startsWith("#"));

        if (!lines.length) {
            console.warn("Файл keywords.txt пуст или содержит только комментарии.");
        }

        return lines;
    } catch (e) {
        console.error("Ошибка чтения keywords.txt", e);
        return [];
    }
}

async function getOrCreateTab() {
    const [activeTab] = await chrome.tabs.query({ active: true, currentWindow: true });
    if (activeTab) {
        return activeTab;
    }
    const tab = await chrome.tabs.create({ url: "about:blank" });
    return tab;
}

function buildAlibabaSearchUrl(keyword) {
    const base = "https://www.alibaba.com/trade/search";
    const params = new URLSearchParams();
    params.set("SearchText", keyword);
    return `${base}?${params.toString()}`;
}

async function waitForPageLoad(tabId, timeoutMs) {
    return new Promise((resolve) => {
        chrome.tabs.get(tabId, (tab) => {
            if (chrome.runtime.lastError) {
                console.warn(
                    "Не удалось получить вкладку для ожидания загрузки:",
                    chrome.runtime.lastError
                );
                resolve(); // Не блокируем выполнение при ошибке
                return;
            }

            if (tab && tab.status === "complete") {
                resolve();
                return;
            }

            let finished = false;

            const timeoutId = setTimeout(() => {
                if (finished) return;
                finished = true;
                chrome.tabs.onUpdated.removeListener(onUpdated);
                console.warn("Таймаут загрузки страницы, продолжаем выполнение...");
                resolve(); // Не блокируем выполнение при таймауте
            }, timeoutMs);

            function onUpdated(updatedTabId, changeInfo) {
                if (updatedTabId !== tabId) return;
                if (changeInfo.status === "complete") {
                    if (finished) return;
                    finished = true;
                    clearTimeout(timeoutId);
                    chrome.tabs.onUpdated.removeListener(onUpdated);
                    resolve();
                }
            }

            chrome.tabs.onUpdated.addListener(onUpdated);
        });
    });
}

async function navigateTo(tabId, url) {
    if (!url) {
        console.warn("navigateTo: пустой URL, переход пропущен.");
        return false;
    }

    const trimmed = String(url).trim();
    if (!trimmed) {
        console.warn("navigateTo: пустой URL после trim, переход пропущен.");
        return false;
    }

    // Явно блокируем javascript: и любые не-http(s) схемы
    const lower = trimmed.toLowerCase();
    if (lower.startsWith("javascript:")) {
        console.warn("navigateTo: javascript: URL запрещён для навигации:", trimmed);
        return false;
    }

    try {
        const parsed = new URL(trimmed);
        if (parsed.protocol !== "http:" && parsed.protocol !== "https:") {
            console.warn("navigateTo: неподдерживаемая схема URL:", parsed.protocol, trimmed);
            return false;
        }
    } catch (e) {
        console.warn("navigateTo: некорректный URL, переход пропущен:", trimmed, e);
        return false;
    }

    console.log("Переход на URL:", trimmed);

    try {
        await chrome.tabs.update(tabId, { url: trimmed });
        await waitForPageLoad(tabId, PAGE_LOAD_TIMEOUT_MS);
        return true;
    } catch (e) {
        console.warn("Ошибка при переходе на URL:", trimmed, e);
        return false;
    }
}

function normalizeCompanyBaseUrl(rawUrl) {
    if (!rawUrl) return null;
    let href = rawUrl.trim();
    if (!href) return null;

    if (href.startsWith("//")) {
        href = "https:" + href;
    } else if (href.startsWith("/")) {
        href = "https://www.alibaba.com" + href;
    }

    try {
        const url = new URL(href);
        if (!url.protocol.startsWith("http")) {
            return null;
        }
        return `${url.protocol}//${url.host}`;
    } catch (e) {
        console.warn("Не удалось нормализовать URL компании:", rawUrl, e);
        return null;
    }
}

function buildContactUrl(baseUrl) {
    if (!baseUrl) return null;
    try {
        const url = new URL(baseUrl);
        return `${url.protocol}//${url.host}/contactinfo.html`;
    } catch (e) {
        console.warn("Не удалось построить contactinfo URL для:", baseUrl, e);
        return null;
    }
}

/**
 * Безопасное выполнение скрипта на странице с таймаутом
 */
async function safeExecuteScript(tabId, func, args = [], defaultValue = null) {
    try {
        const scriptPromise = chrome.scripting.executeScript({
            target: { tabId },
            func,
            args,
        });

        const results = await withTimeout(
            scriptPromise,
            SCRIPT_EXECUTION_TIMEOUT_MS,
            "Таймаут выполнения скрипта на странице"
        );

        if (results && results[0] && results[0].result !== undefined) {
            return results[0].result;
        }
        return defaultValue;
    } catch (e) {
        console.warn("Ошибка выполнения скрипта на странице:", e.message);
        return defaultValue;
    }
}

async function extractCompaniesOnSearch(tabId, maxCompanies) {
    const result = await safeExecuteScript(tabId, (limit) => {
        function normalizeHref(href) {
            if (!href) return null;
            href = href.trim();
            if (!href) return null;
            if (href.startsWith("//")) {
                return window.location.protocol + href;
            }
            if (href.startsWith("/")) {
                return window.location.origin + href;
            }
            return href;
        }

        const companies = [];
        const seen = new Set();

        const anchors = Array.from(document.querySelectorAll("a.searchx-product-e-company[href]"));
        for (const a of anchors) {
            if (companies.length >= limit) break;
            const name = (a.textContent || "").trim();
            const href = normalizeHref(a.getAttribute("href"));
            if (!name || !href || seen.has(href)) continue;
            seen.add(href);
            companies.push({ name, profileUrl: href });
        }

        if (!companies.length) {
            const altAnchors = Array.from(
                document.querySelectorAll("a[href*='.en.alibaba.com'][data-spm*='d_companyName'], a[href*='.en.alibaba.com']")
            );
            for (const a of altAnchors) {
                if (companies.length >= limit) break;
                const name = (a.textContent || "").trim();
                const href = normalizeHref(a.getAttribute("href"));
                if (!name || !href || seen.has(href)) continue;
                seen.add(href);
                companies.push({ name, profileUrl: href });
            }
        }

        return companies;
    }, [maxCompanies], []);

    const companies = result || [];
    console.log("Найдено компаний на странице поиска:", companies.length);
    return companies;
}

async function getNextSearchPageUrl(tabId) {
    const result = await safeExecuteScript(tabId, () => {
        function normalizeHref(href) {
            if (!href) return null;
            let value = href.trim();
            if (!value) return null;
            const lower = value.toLowerCase();
            if (lower.startsWith("javascript:")) {
                return null;
            }
            if (value.startsWith("//")) {
                return window.location.protocol + value;
            }
            if (value.startsWith("/")) {
                return window.location.origin + value;
            }
            return value;
        }

        const anchors = Array.from(document.querySelectorAll("a[href]"));
        for (const a of anchors) {
            const text = (a.textContent || "").trim().toLowerCase();
            const aria = (a.getAttribute("aria-label") || "").trim().toLowerCase();
            const rel = (a.getAttribute("rel") || "").trim().toLowerCase();

            let isNext = false;
            if (rel === "next") {
                isNext = true;
            }
            if (!isNext && (aria.includes("next") || aria.includes("next page"))) {
                isNext = true;
            }
            if (
                !isNext &&
                (text === "next" ||
                    text.startsWith("next ") ||
                    text.includes("следующая") ||
                    text.includes("дальше") ||
                    text.includes("下一页"))
            ) {
                isNext = true;
            }

            if (!isNext) continue;

            const href = normalizeHref(a.getAttribute("href"));
            if (!href) continue;
            return href;
        }

        return null;
    }, [], null);

    return result;
}

async function collectCompaniesForKeyword(tabId, perKeywordLimit) {
    const allCompanies = [];
    const seenUrls = new Set();

    const absoluteLimit = perKeywordLimit && perKeywordLimit > 0 ? perKeywordLimit : null;

    let pageIndex = 0;
    // Первая страница уже открыта вызывающим кодом
    while (!stopRequested && pageIndex < MAX_SEARCH_PAGES_PER_KEYWORD) {
        let remainingForPage = MAX_COMPANIES_PER_KEYWORD;
        if (absoluteLimit) {
            remainingForPage = Math.min(remainingForPage, absoluteLimit - allCompanies.length);
            if (remainingForPage <= 0) break;
        }

        if (progressState.maxLinks && progressState.totalCompaniesPlanned + allCompanies.length >= progressState.maxLinks) {
            break;
        }

        let pageCompanies = [];
        try {
            pageCompanies = await extractCompaniesOnSearch(tabId, remainingForPage);
        } catch (e) {
            console.error("Ошибка извлечения компаний с страницы поиска", e);
            updateProgress({
                lastMessage:
                    "Ошибка извлечения компаний с страницы поиска. Подробности в консоли.",
            });
        }

        if (!pageCompanies.length) {
            break;
        }

        for (const company of pageCompanies) {
            if (!company || !company.profileUrl) continue;
            if (seenUrls.has(company.profileUrl)) continue;
            seenUrls.add(company.profileUrl);
            allCompanies.push(company);

            if (
                (absoluteLimit && allCompanies.length >= absoluteLimit) ||
                (progressState.maxLinks &&
                    progressState.totalCompaniesPlanned + allCompanies.length >=
                    progressState.maxLinks)
            ) {
                break;
            }
        }

        if (
            (absoluteLimit && allCompanies.length >= absoluteLimit) ||
            (progressState.maxLinks &&
                progressState.totalCompaniesPlanned + allCompanies.length >=
                progressState.maxLinks)
        ) {
            break;
        }

        const nextUrl = await getNextSearchPageUrl(tabId);
        if (!nextUrl) {
            break;
        }

        try {
            await navigateTo(tabId, nextUrl);
            await delay(DELAY_BETWEEN_STEPS_MS);
        } catch (e) {
            console.error("Ошибка перехода на следующую страницу поиска Alibaba", e);
            break;
        }

        pageIndex += 1;
    }

    return allCompanies;
}

async function extractCompanySiteOnContactInfo(tabId, fallbackName) {
    const result = await safeExecuteScript(tabId, (nameFromSearch) => {
        function normalizeUrl(raw, base) {
            if (!raw) return null;
            let value = raw.trim();
            if (!value) return null;

            const lower = value.toLowerCase();
            if (lower.startsWith("mailto:")) {
                value = value.slice(7);
            }
            // Игнорируем javascript: и прочие потенциально опасные схемы
            if (lower.startsWith("javascript:")) {
                return null;
            }

            try {
                const url = new URL(value, base);
                if (url.protocol !== "http:" && url.protocol !== "https:") {
                    return null;
                }
                return url.href;
            } catch (e) {
                return null;
            }
        }

        function isExternalDomain(url) {
            try {
                const u = new URL(url);
                const host = u.hostname.toLowerCase();
                if (
                    host.includes("alibaba.com") ||
                    host.includes("alicdn.com") ||
                    host.includes("aliexpress.com")
                ) {
                    return false;
                }

                // Исключаем локальные и приватные адреса
                if (
                    host === "localhost" ||
                    host === "127.0.0.1" ||
                    host.endsWith(".localhost") ||
                    /^127\./.test(host) ||
                    /^10\./.test(host) ||
                    /^192\.168\./.test(host) ||
                    /^172\.(1[6-9]|2\d|3[0-1])\./.test(host)
                ) {
                    return false;
                }

                const social = [
                    "facebook.com",
                    "linkedin.com",
                    "twitter.com",
                    "instagram.com",
                    "youtube.com",
                ];
                return !social.some((s) => host.includes(s));
            } catch (e) {
                return false;
            }
        }

        function pickCompanyName() {
            const el =
                document.querySelector(".company-name, .company-title, .company-name-title, h1") ||
                null;
            const text = el && el.textContent ? el.textContent.trim() : "";
            if (text) return text;
            if (document.title) return document.title.trim();
            return nameFromSearch || "";
        }

        const baseUrl = window.location.href;

        const items = Array.from(document.querySelectorAll("div.msg-item"));
        for (const item of items) {
            const titleEl = item.querySelector(".msg-title");
            const title = titleEl && titleEl.textContent ? titleEl.textContent.trim().toLowerCase() : "";
            if (!title.includes("сайт") && !title.includes("website")) continue;

            let valueEl =
                item.querySelector(".value a[href]") || item.querySelector("a[href]") || item.querySelector(".value");
            if (!valueEl) continue;

            let raw = (valueEl.getAttribute("href") || valueEl.textContent || "").trim();
            const url = normalizeUrl(raw, baseUrl);
            if (url && isExternalDomain(url)) {
                return { siteUrl: url, companyName: pickCompanyName() };
            }
        }

        const candidates = [];
        const anchors = Array.from(document.querySelectorAll("a[href]"));
        for (const a of anchors) {
            const rawHref = a.getAttribute("href") || "";
            const url = normalizeUrl(rawHref, baseUrl);
            if (!url || !isExternalDomain(url)) continue;
            const text = (a.textContent || "").trim().toLowerCase();
            let score = 0;
            if (text.includes("website") || text.includes("official") || text.includes("сайт")) {
                score += 2;
            }
            if (text.includes("home") || text.includes("company")) {
                score += 1;
            }
            candidates.push({ score, url });
        }

        if (candidates.length) {
            candidates.sort((a, b) => b.score - a.score);
            return { siteUrl: candidates[0].url, companyName: pickCompanyName() };
        }

        const textContent = document.body
            ? document.body.innerText
            : document.documentElement.innerText || "";
        const urlRe =
            /((?:https?:\/\/|www\.)[^\s"'<>]+|[A-Za-z0-9.-]+\.[A-Za-z]{2,}(?:\/[^\s"'<>]*)?)/gi;
        let match;
        while ((match = urlRe.exec(textContent)) !== null) {
            const candidate = match[1];
            const url = normalizeUrl(candidate, baseUrl);
            if (url && isExternalDomain(url)) {
                return { siteUrl: url, companyName: pickCompanyName() };
            }
        }

        return { siteUrl: null, companyName: pickCompanyName() };
    }, [fallbackName || ""], { siteUrl: null, companyName: fallbackName || "" });

    return result;
}

async function extractEmailOnCompanySite(tabId) {
    const result = await safeExecuteScript(tabId, () => {
        const emails = new Set();

        const mailtoLinks = Array.from(document.querySelectorAll("a[href^='mailto:']"));
        for (const a of mailtoLinks) {
            const href = a.getAttribute("href") || "";
            const raw = href.replace(/^mailto:/i, "");
            const email = raw.split(/[?;]/)[0].trim();
            if (email) emails.add(email);
        }

        const emailRe = /[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/g;
        const text = document.body
            ? document.body.innerText
            : document.documentElement.innerText || "";
        let match;
        while ((match = emailRe.exec(text)) !== null) {
            const value = match[0].trim().replace(/^[\s,;:()<>]+|[\s,;:()<>]+$/g, "");
            if (value) emails.add(value);
        }

        const arr = Array.from(emails);
        return { email: arr.length ? arr[0] : null };
    }, [], { email: null });

    return result;
}

async function saveResultsToFile(rows) {
    const header = "# Название компании; сайт; e-mail\n";
    const lines = rows.map((row) => `${row.name}; ${row.site}; ${row.email}`);
    const content = header + lines.join("\n");

    // В сервис-воркере Manifest V3 нельзя использовать URL.createObjectURL,
    // поэтому формируем data: URL вручную.
    const base64Content = btoa(unescape(encodeURIComponent(content)));
    const dataUrl = `data:text/plain;charset=utf-8;base64,${base64Content}`;

    try {
        await chrome.downloads.download({
            url: dataUrl,
            filename: "alibaba_results.txt",
            saveAs: true,
        });
    } catch (e) {
        console.error("Не удалось сохранить файл alibaba_results.txt", e);
    }
}

/**
 * Обработка одной компании с таймаутом для предотвращения зависаний
 */
async function processCompany(tabId, company) {
    const COMPANY_PROCESSING_TIMEOUT_MS = 120000; // 2 минуты на одну компанию

    return withTimeout(
        processCompanyInternal(tabId, company),
        COMPANY_PROCESSING_TIMEOUT_MS,
        `Таймаут обработки компании "${company.name}"`
    );
}

async function processCompanyInternal(tabId, company) {
    const baseCompanyUrl = normalizeCompanyBaseUrl(company.profileUrl);
    const contactUrl = buildContactUrl(baseCompanyUrl);
    let siteUrl = "";
    let companyName = company.name || "";
    let email = "";

    if (!contactUrl) {
        console.warn("Не удалось построить URL contactinfo для компании:", company.profileUrl);
    } else {
        try {
            const navSuccess = await navigateTo(tabId, contactUrl);
            if (navSuccess) {
                await delay(DELAY_BETWEEN_STEPS_MS);

                const contactInfo = await extractCompanySiteOnContactInfo(tabId, company.name);
                if (contactInfo) {
                    companyName = contactInfo.companyName || companyName;
                    if (contactInfo.siteUrl) {
                        siteUrl = contactInfo.siteUrl;
                    }
                }
            }
        } catch (e) {
            console.error("Ошибка обработки страницы contactinfo", e);
        }
    }

    // Проверка через API, есть ли компания в базе
    let apiSkipCompany = false;
    if (apiBaseUrl && companyName) {
        try {
            const apiCheck = await apiCheckCompany(companyName, siteUrl || "", "");
            if (apiCheck && apiCheck.exists) {
                apiSkipCompany = true;
                updateProgress({
                    lastMessage: `Компания уже есть в базе, пропуск: "${companyName}"`,
                });
            }
        } catch (e) {
            console.warn("Ошибка при проверке компании через API", e);
        }
    }

    if (apiSkipCompany) {
        return null; // Пропускаем компанию
    }

    // Переход на сайт компании для поиска email
    if (siteUrl) {
        try {
            const navSuccess = await navigateTo(tabId, siteUrl);
            if (navSuccess) {
                await delay(DELAY_BETWEEN_STEPS_MS);

                const emailInfo = await extractEmailOnCompanySite(tabId);
                if (emailInfo && emailInfo.email) {
                    email = emailInfo.email;
                    updateProgress({
                        foundEmails: progressState.foundEmails + 1,
                    });
                }
            }
        } catch (e) {
            console.error("Ошибка при переходе на сайт компании или поиске e-mail", e);
        }
    }

    const safeName = companyName || "-";
    const safeSite = siteUrl || "-";
    const safeEmail = email || "-";

    console.log("Результат для компании:", safeName, safeSite, safeEmail);

    // Сохранение через API
    if (apiBaseUrl && safeName && safeName !== "-") {
        try {
            await apiStoreCompany(
                safeName,
                safeSite !== "-" ? safeSite : "",
                safeEmail !== "-" ? safeEmail : ""
            );
        } catch (e) {
            console.warn("Ошибка при сохранении компании через API", e);
        }
    }

    return { name: safeName, site: safeSite, email: safeEmail };
}

async function runAlibabaParser() {
    const results = [];

    try {
        const keywords = await loadKeywords();
        if (!keywords.length) {
            console.warn(
                "Ключевые фразы не найдены. Добавьте строки в keywords.txt и перезагрузите расширение."
            );
            updateProgress({
                isRunning: false,
                stopRequested: false,
                lastMessage:
                    "Ключевые фразы не найдены. Добавьте строки в keywords.txt и перезагрузите расширение.",
            });
            return;
        }

        updateProgress({
            totalKeywords: keywords.length,
            currentKeywordIndex: 0,
            totalCompaniesCurrentKeyword: 0,
            currentCompanyIndex: 0,
            processedCompanies: 0,
            totalCompaniesPlanned: 0,
            foundEmails: 0,
            lastMessage: "Загружены ключевые фразы.",
        });

        const tab = await getOrCreateTab();

        for (let i = 0; i < keywords.length; i += 1) {
            if (stopRequested) break;
            const keyword = keywords[i];
            console.log(`=== Ключевая фраза [${i + 1}/${keywords.length}]: ${keyword} ===`);

            updateProgress({
                currentKeywordIndex: i + 1,
                totalCompaniesCurrentKeyword: 0,
                currentCompanyIndex: 0,
                lastMessage: `Поиск компаний для фразы "${keyword}"`,
            });

            const searchUrl = buildAlibabaSearchUrl(keyword);
            try {
                await navigateTo(tab.id, searchUrl);
            } catch (e) {
                console.error("Ошибка перехода на страницу поиска Alibaba", e);
                continue;
            }

            await delay(DELAY_BETWEEN_STEPS_MS);

            let companies = [];
            try {
                const perKeywordLimit = progressState.maxCompaniesPerKeyword || null;
                companies = await collectCompaniesForKeyword(tab.id, perKeywordLimit);
            } catch (e) {
                console.error("Ошибка извлечения компаний с результатов поиска", e);
                updateProgress({
                    lastMessage:
                        "Ошибка извлечения компаний с результатов поиска. Подробности в консоли.",
                });
            }

            updateProgress({
                totalCompaniesCurrentKeyword: companies.length,
                totalCompaniesPlanned: progressState.totalCompaniesPlanned + companies.length,
            });

            if (!companies.length) {
                console.warn("Компании на странице поиска не найдены.");
                updateProgress({
                    lastMessage: `Компании на странице поиска для фразы "${keyword}" не найдены.`,
                });
                continue;
            }

            for (let j = 0; j < companies.length; j += 1) {
                if (stopRequested) break;
                if (progressState.maxLinks && progressState.processedCompanies >= progressState.maxLinks) {
                    console.log("Достигнут лимит ссылок:", progressState.maxLinks);
                    updateProgress({
                        lastMessage: `Достигнут лимит ссылок: ${progressState.maxLinks}`,
                    });
                    stopRequested = true;
                    break;
                }
                const company = companies[j];
                console.log(
                    `--- Компания [${j + 1}/${companies.length}] для фразы "${keyword}":`,
                    company.name,
                    company.profileUrl
                );

                updateProgress({
                    currentCompanyIndex: j + 1,
                    processedCompanies: progressState.processedCompanies + 1,
                    lastMessage: `Обработка компании "${company.name}"`,
                });

                // Обработка одной компании с защитой от зависаний
                try {
                    const companyResult = await processCompany(tab.id, company);
                    if (companyResult) {
                        results.push(companyResult);
                    }
                } catch (e) {
                    console.error(`Ошибка обработки компании "${company.name}", пропускаем:`, e);
                    updateProgress({
                        lastMessage: `Ошибка обработки компании "${company.name}", переходим к следующей`,
                    });
                }
            }
        }

        if (!results.length) {
            console.warn("Не удалось собрать ни одной записи, файл не будет создан.");
            updateProgress({
                isRunning: false,
                stopRequested: false,
                lastMessage: "Не удалось собрать ни одной записи.",
            });
            return;
        }

        await saveResultsToFile(results);
        console.log("Готово. Собрано записей:", results.length);
        updateProgress({
            isRunning: false,
            stopRequested: false,
            lastMessage: `Готово. Собрано записей: ${results.length}`,
        });
    } finally {
        isRunning = false;
        stopRequested = false;
        updateProgress({
            isRunning: false,
            stopRequested: false,
        });
    }
}
