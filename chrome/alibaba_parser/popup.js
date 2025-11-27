// Логика popup-окна: кнопки Старт/Стоп и отображение прогресса

function applyState(state) {
    if (!state) return;

    const startBtn = document.getElementById("startBtn");
    const stopBtn = document.getElementById("stopBtn");
    const maxLinksInput = document.getElementById("maxLinksInput");
    const maxCompaniesPerKeywordInput = document.getElementById("maxCompaniesPerKeywordInput");
    const statusText = document.getElementById("statusText");
    const keywordCurrent = document.getElementById("keywordCurrent");
    const keywordTotal = document.getElementById("keywordTotal");
    const companyCurrent = document.getElementById("companyCurrent");
    const companyTotal = document.getElementById("companyTotal");
    const linksProcessed = document.getElementById("linksProcessed");
    const linksPlanned = document.getElementById("linksPlanned");
    const emailsFound = document.getElementById("emailsFound");
    const phonesFound = document.getElementById("phonesFound");
    const messageText = document.getElementById("messageText");

    const running = !!state.isRunning;

    startBtn.disabled = running;
    stopBtn.disabled = !running;

    statusText.textContent = running ? "выполняется" : "остановлен";

    keywordCurrent.textContent = state.currentKeywordIndex || 0;
    keywordTotal.textContent = state.totalKeywords || 0;

    companyCurrent.textContent = state.currentCompanyIndex || 0;
    companyTotal.textContent = state.totalCompaniesCurrentKeyword || 0;

    linksProcessed.textContent = state.processedCompanies || 0;
    linksPlanned.textContent = state.totalCompaniesPlanned || 0;

    emailsFound.textContent = state.foundEmails || 0;
    phonesFound.textContent = state.foundPhones || 0;

    if (maxLinksInput && typeof state.maxLinks === "number" && state.maxLinks > 0) {
        maxLinksInput.value = String(state.maxLinks);
    }
    if (maxCompaniesPerKeywordInput && typeof state.maxCompaniesPerKeyword === "number" && state.maxCompaniesPerKeyword > 0) {
        maxCompaniesPerKeywordInput.value = String(state.maxCompaniesPerKeyword);
    }

    messageText.textContent = state.lastMessage || "";
}

async function ensureServiceWorkerActive() {
    // В Manifest V3 service worker может "засыпать", пробуем его разбудить
    try {
        await chrome.runtime.getBackgroundPage?.();
    } catch (e) {
        // getBackgroundPage недоступен в Manifest V3, это нормально
    }
}

function requestState() {
    ensureServiceWorkerActive().then(() => {
        chrome.runtime.sendMessage({ type: "get_state" }, (response) => {
            if (chrome.runtime.lastError) {
                // Service worker может быть неактивен, это не критично
                console.warn("Ошибка запроса состояния:", chrome.runtime.lastError.message);
                // Показываем состояние по умолчанию
                applyState({
                    isRunning: false,
                    stopRequested: false,
                    currentKeywordIndex: 0,
                    totalKeywords: 0,
                    currentCompanyIndex: 0,
                    totalCompaniesCurrentKeyword: 0,
                    processedCompanies: 0,
                    totalCompaniesPlanned: 0,
                    foundEmails: 0,
                    foundPhones: 0,
                    maxLinks: null,
                    maxCompaniesPerKeyword: null,
                    lastMessage: "",
                });
                return;
            }
            if (response && response.ok && response.state) {
                applyState(response.state);
            }
        });
    });
}

function setupControls() {
    const startBtn = document.getElementById("startBtn");
    const stopBtn = document.getElementById("stopBtn");
    const messageText = document.getElementById("messageText");
    const maxLinksInput = document.getElementById("maxLinksInput");
    const maxCompaniesPerKeywordInput = document.getElementById("maxCompaniesPerKeywordInput");
    const apiBaseUrlInput = document.getElementById("apiBaseUrlInput");

    // Загрузка всех сохранённых настроек
    chrome.storage.sync.get(["apiBaseUrl", "maxLinks", "maxCompaniesPerKeyword"], (result) => {
        if (chrome.runtime.lastError) {
            console.error("Ошибка чтения настроек:", chrome.runtime.lastError);
            return;
        }
        if (apiBaseUrlInput && typeof result.apiBaseUrl === "string") {
            apiBaseUrlInput.value = result.apiBaseUrl;
        }
        if (maxLinksInput && typeof result.maxLinks === "number" && result.maxLinks > 0) {
            maxLinksInput.value = String(result.maxLinks);
        }
        if (maxCompaniesPerKeywordInput && typeof result.maxCompaniesPerKeyword === "number" && result.maxCompaniesPerKeyword > 0) {
            maxCompaniesPerKeywordInput.value = String(result.maxCompaniesPerKeyword);
        }
    });

    // Сохранение настроек при изменении
    if (apiBaseUrlInput) {
        apiBaseUrlInput.addEventListener("change", () => {
            const value = apiBaseUrlInput.value.trim();
            chrome.storage.sync.set({ apiBaseUrl: value });
        });
    }

    if (maxLinksInput) {
        maxLinksInput.addEventListener("change", () => {
            const parsed = parseInt(maxLinksInput.value, 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                chrome.storage.sync.set({ maxLinks: parsed });
            } else {
                chrome.storage.sync.remove("maxLinks");
            }
        });
    }

    if (maxCompaniesPerKeywordInput) {
        maxCompaniesPerKeywordInput.addEventListener("change", () => {
            const parsed = parseInt(maxCompaniesPerKeywordInput.value, 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                chrome.storage.sync.set({ maxCompaniesPerKeyword: parsed });
            } else {
                chrome.storage.sync.remove("maxCompaniesPerKeyword");
            }
        });
    }

    startBtn.addEventListener("click", () => {
        messageText.textContent = "";
        startBtn.disabled = true;
        let maxLinks = null;
        if (maxLinksInput && maxLinksInput.value.trim() !== "") {
            const parsed = parseInt(maxLinksInput.value, 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                maxLinks = parsed;
            }
        }

        let maxCompaniesPerKeyword = null;
        if (maxCompaniesPerKeywordInput && maxCompaniesPerKeywordInput.value.trim() !== "") {
            const parsed = parseInt(maxCompaniesPerKeywordInput.value, 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                maxCompaniesPerKeyword = parsed;
            }
        }

        let apiBaseUrl = null;
        if (apiBaseUrlInput && apiBaseUrlInput.value.trim() !== "") {
            apiBaseUrl = apiBaseUrlInput.value.trim();
        }

        chrome.runtime.sendMessage({ type: "start", maxLinks, maxCompaniesPerKeyword, apiBaseUrl }, (response) => {
            if (chrome.runtime.lastError) {
                console.error("Ошибка запуска парсера:", chrome.runtime.lastError);
                messageText.textContent = "Ошибка запуска парсера. См. консоль.";
                startBtn.disabled = false;
                return;
            }
            if (!response || !response.ok) {
                messageText.textContent = (response && response.error) || "Не удалось запустить парсер.";
                startBtn.disabled = false;
                return;
            }
            requestState();
        });
    });

    stopBtn.addEventListener("click", () => {
        messageText.textContent = "";
        chrome.runtime.sendMessage({ type: "stop" }, (response) => {
            if (chrome.runtime.lastError) {
                console.error("Ошибка остановки парсера:", chrome.runtime.lastError);
                messageText.textContent = "Ошибка остановки парсера. См. консоль.";
                return;
            }
            if (!response || !response.ok) {
                messageText.textContent = (response && response.error) || "Не удалось остановить парсер.";
                return;
            }
            requestState();
        });
    });
}

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (!message || message.type !== "progress_update") return;
    if (!message.state) return;
    applyState(message.state);
});

document.addEventListener("DOMContentLoaded", () => {
    setupControls();
    requestState();
});
