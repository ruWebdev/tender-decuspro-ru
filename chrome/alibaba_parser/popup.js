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

    if (maxLinksInput && typeof state.maxLinks === "number" && state.maxLinks > 0) {
        maxLinksInput.value = String(state.maxLinks);
    }
    if (maxCompaniesPerKeywordInput && typeof state.maxCompaniesPerKeyword === "number" && state.maxCompaniesPerKeyword > 0) {
        maxCompaniesPerKeywordInput.value = String(state.maxCompaniesPerKeyword);
    }

    messageText.textContent = state.lastMessage || "";
}

function requestState() {
    chrome.runtime.sendMessage({ type: "get_state" }, (response) => {
        if (chrome.runtime.lastError) {
            console.error("Ошибка запроса состояния:", chrome.runtime.lastError);
            return;
        }
        if (response && response.ok && response.state) {
            applyState(response.state);
        }
    });
}

function setupControls() {
    const startBtn = document.getElementById("startBtn");
    const stopBtn = document.getElementById("stopBtn");
    const messageText = document.getElementById("messageText");
    const maxLinksInput = document.getElementById("maxLinksInput");
    const maxCompaniesPerKeywordInput = document.getElementById("maxCompaniesPerKeywordInput");

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

        chrome.runtime.sendMessage({ type: "start", maxLinks, maxCompaniesPerKeyword }, (response) => {
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
