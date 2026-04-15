/**
 * ============================================================================
 * PARTICIPANTES.JS - SISTEMA DE GERENCIAMENTO DE PARTICIPANTES
 * ============================================================================
 *
 * Funcionalidades:
 * - Tabela Tabulator com filtros avançados
 * - Seleção múltipla com bulk actions
 * - Menu de colunas (mostrar/ocultar, congelar, reordenar)
 * - Filtros com autocomplete e busca fuzzy
 * - Exportação (Excel, PDF, Imprimir)
 * - Persistência de configurações no localStorage
 *
 * ============================================================================
 */

// ============================================================================
// CONSTANTES E VARIÁVEIS GLOBAIS
// ============================================================================

const STORAGE_KEY = "participantes_table_config_v6";
const STORAGE_COLUMNS = "table-columns";

let table = null;
let selectedRows = new Set();
let currentField = null;
let activeColumnFilters = {};
let tempColumnState = {};
let lastChecked = null;
// ============================================================================
// SEÇÃO 1: GERENCIAMENTO DE CONFIGURAÇÕES
// ============================================================================

/**
 * Salva a configuração da tabela (colunas visíveis, largura, etc)
 */
function saveTableConfig(table) {
    if (!table) return;

    const config = table.getColumns().map(col => {
        const def = col.getDefinition();
        return {
            field: def.field || def.title,
            visible: col.isVisible(),
            width: col.getWidth()
        };
    });

    localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
}

/**
 * Carrega a configuração salva da tabela
 */
function loadTableConfig() {
    const saved = localStorage.getItem(STORAGE_KEY);
    return saved ? JSON.parse(saved) : null;
}

/**
 * Obtém o estado atual das colunas
 */
function getColumnState() {
    return table.getColumns().map(col => {
        const def = col.getDefinition();
        return {
            field: def.field,
            title: def.title,
            visible: col.isVisible(),
            width: col.getWidth(),
            frozen: col.isFrozen(),
            position: col.getPosition()
        };
    });
}

/**
 * Salva o estado das colunas no localStorage
 */
function saveColumnState() {
    const state = getColumnState();
    localStorage.setItem(STORAGE_COLUMNS, JSON.stringify(state));
}

/**
 * Carrega o estado das colunas do localStorage
 */
function loadColumnState() {
    const saved = JSON.parse(localStorage.getItem(STORAGE_COLUMNS) || "[]");

    if (!saved.length) return;

    saved.forEach(col => {
        if (!col.field) return;

        col.visible
            ? table.showColumn(col.field)
            : table.hideColumn(col.field);

        if (col.frozen) table.getColumn(col.field).freeze();
    });
}

// ============================================================================
// SEÇÃO 2: GERENCIAMENTO DE SELEÇÃO (CHECKBOXES E BULK ACTIONS)
// ============================================================================

/**
 * Atualiza a barra de ações em massa
 */
function updateBulkActionsBar() {
    const bulkBar = document.getElementById("bulk-bar");
    const countSpan = document.getElementById("selected-count");

    if (!bulkBar) return;

    countSpan.textContent = selectedRows.size;

    if (selectedRows.size > 0) {
        bulkBar.classList.add("active");
    } else {
        bulkBar.classList.remove("active");
    }
}

function updateRowUI() {
    document.querySelectorAll(".tabulator-row").forEach(row => {
        const checkbox = row.querySelector(".participante-checkbox");
        if (!checkbox) return;

        row.classList.toggle("selected-row", checkbox.checked);
    });
}

/**
 * Event listener para checkboxes individuais e "Selecionar Todos"
 */
document.addEventListener("change", function (e) {
    // Checkbox individual
    if (e.target.classList.contains("participante-checkbox")) {
        const id = e.target.value;

        e.target.checked ? selectedRows.add(id) : selectedRows.delete(id);

        updateBulkActionsBar();
        updateSelectAllState();
        updateRowUI(); // 🔥 adiciona isso
    }

    // Checkbox "Selecionar Todos"
    if (e.target.id === "select-all-participantes") {
        const checked = e.target.checked;

        document.querySelectorAll(".participante-checkbox").forEach(cb => {
            cb.checked = checked;

            checked ? selectedRows.add(cb.value) : selectedRows.delete(cb.value);
        });

        updateBulkActionsBar();
        updateRowUI(); // 🔥 importante
    }
});

/**
 * Event listener para clicar em uma linha (editar)
 */
document.addEventListener("click", function (e) {
    const rowEl = e.target.closest(".tabulator-row");
    if (!rowEl) return;

    // Ignorar checkbox
    if (e.target.closest("input[type='checkbox']")) return;

    // Ignorar botões e links
    if (e.target.closest("a, button")) return;

    const checkbox = rowEl.querySelector(".participante-checkbox");
    if (!checkbox) {
        console.warn("Checkbox não encontrado na linha");
        return;
    }

    const id = checkbox.value;
    if (id) {
        window.location.href = `/participantes/${id}/edit`;
    }
});

document.addEventListener("click", function (e) {

    if (!e.target.classList.contains("participante-checkbox")) return;

    const checkboxes = [...document.querySelectorAll(".participante-checkbox")];
    const current = e.target;

    if (e.shiftKey && lastChecked) {

        const start = checkboxes.indexOf(current);
        const end = checkboxes.indexOf(lastChecked);

        const [min, max] = [start, end].sort((a, b) => a - b);

        for (let i = min; i <= max; i++) {
            checkboxes[i].checked = lastChecked.checked;

            if (lastChecked.checked) {
                selectedRows.add(checkboxes[i].value);
            } else {
                selectedRows.delete(checkboxes[i].value);
            }
        }
    }

    lastChecked = current;

    updateBulkActionsBar();
    updateSelectAllState();
    updateRowUI();
});

// ============================================================================
// SEÇÃO 3: GERENCIAMENTO DE COLUNAS (MENU)
// ============================================================================

/**
 * Abre o menu de gerenciamento de colunas
 */
function openColumnMenu() {
    const list = document.getElementById("column-list");
    list.innerHTML = "";

    tempColumnState = {};

    table.getColumns().forEach(col => {
        const def = col.getDefinition();

        if (!def.field || def.title === "Ações") return;

        const visible = col.isVisible();
        tempColumnState[def.field] = visible;

        const item = document.createElement("div");
        item.className = "column-item";

        item.innerHTML = `
            <div class="column-left">
                <span>📄</span>
                <span>${def.title}</span>
            </div>
            <div class="column-check ${visible ? "active" : ""}">
                ${visible ? "✓" : ""}
            </div>
        `;

        item.addEventListener("click", () => {
            tempColumnState[def.field] = !tempColumnState[def.field];

            item.querySelector(".column-check").classList.toggle("active");
            item.querySelector(".column-check").innerHTML =
                tempColumnState[def.field] ? "✓" : "";
        });

        list.appendChild(item);
    });
}

/**
 * Abre o gerenciador avançado de colunas (com drag-drop)
 */
function openColumnManager() {
    const list = document.getElementById("column-list");
    list.innerHTML = "";

    const cols = getColumnState();

    cols.forEach(col => {
        if (!col.field) return;

        const item = document.createElement("div");
        item.className = "column-item";
        item.draggable = true;
        item.dataset.field = col.field;

        item.innerHTML = `
            <div class="column-left">
                <span class="drag">⋮⋮</span>
                <span>${col.title}</span>
            </div>
            <div class="column-actions">
                <span class="pin">${col.frozen ? "📌" : "📍"}</span>
                <span class="check ${col.visible ? "active" : ""}">
                    ${col.visible ? "✓" : ""}
                </span>
            </div>
        `;

        // Toggle visibilidade
        item.querySelector(".check").onclick = (e) => {
            e.stopPropagation();

            const visible = table.getColumn(col.field).isVisible();
            visible
                ? table.hideColumn(col.field)
                : table.showColumn(col.field);

            openColumnManager();
            saveColumnState();
        };

        // Pin/unpin
        item.querySelector(".pin").onclick = (e) => {
            e.stopPropagation();

            const column = table.getColumn(col.field);
            column.isFrozen()
                ? column.unfreeze()
                : column.freeze();

            openColumnManager();
            saveColumnState();
        };

        list.appendChild(item);
    });

    enableDragSort();
}

/**
 * Ativa o drag-drop para reordenar colunas
 */
function enableDragSort() {
    let dragged;

    document.querySelectorAll(".column-item").forEach(item => {
        item.addEventListener("dragstart", () => {
            dragged = item;
        });

        item.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        item.addEventListener("drop", () => {
            if (dragged === item) return;

            const from = dragged.dataset.field;
            const to = item.dataset.field;

            table.moveColumn(from, to, true);

            openColumnManager();
            saveColumnState();
        });
    });
}

/**
 * Atualiza o menu de colunas (versão simples)
 */
function updateColumnMenu() {
    const colDropdown = document.getElementById("col-dropdown");
    if (!colDropdown || !table) return;

    colDropdown.innerHTML = "";

    const columns = table.getColumns();

    columns.forEach(column => {
        const def = column.getDefinition();
        const title = def.title;
        const field = def.field || title;

        if (title && title.toUpperCase() !== "AÇÕES") {
            const option = document.createElement("div");

            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.checked = column.isVisible();

            checkbox.addEventListener("change", function () {
                this.checked ? table.showColumn(field) : table.hideColumn(field);
                saveTableConfig(table);
            });

            const label = document.createElement("span");
            label.textContent = title;

            option.appendChild(checkbox);
            option.appendChild(label);

            colDropdown.appendChild(option);
        }
    });
}

// ============================================================================
// SEÇÃO 4: GERENCIAMENTO DE FILTROS
// ============================================================================

/**
 * Abre o menu principal de filtros (nível 1)
 */
function openFilterMenu() {
    const dropdown = document.getElementById("filter-dropdown");
    dropdown.innerHTML = "";

    table.getColumns().forEach(col => {
        const def = col.getDefinition();

        if (!def.field || def.title === "Ações") return;

        const item = document.createElement("div");
        item.className = "dropdown-item";
        item.textContent = def.title;

        item.addEventListener("click", (e) => {
            e.stopPropagation();
            currentField = def.field;
            openValueMenu(def.field);
        });

        dropdown.appendChild(item);
    });
}

/**
 * Abre o menu de valores para filtrar (nível 2)
 */
function openValueMenu(field) {
    const dropdown = document.getElementById("filter-dropdown");
    dropdown.innerHTML = "";

    // Pegar valores da tabela
    const cells = document.querySelectorAll(`.tabulator-cell[tabulator-field="${field}"]`);

    const valuesCount = {};
    cells.forEach(cell => {
        const text = cell.innerText.trim();
        if (!text) return;

        valuesCount[text] = (valuesCount[text] || 0) + 1;
    });

    const values = Object.keys(valuesCount);

    // Inicializar filtro para este campo
    if (!activeColumnFilters[field]) {
        activeColumnFilters[field] = [];
    }

    // Header com botão voltar
    const header = document.createElement("div");
    header.className = "dropdown-header";
    header.innerHTML = `<span class="back-btn">←</span><strong>${field}</strong>`;

    header.querySelector(".back-btn").onclick = (e) => {
        e.stopPropagation();
        openFilterMenu();
    };

    dropdown.appendChild(header);

    // Input de busca
    const input = document.createElement("input");
    input.placeholder = "Buscar valor...";
    input.className = "dropdown-search";
    dropdown.appendChild(input);

    // Autocomplete
    const suggestions = document.createElement("div");
    suggestions.className = "autocomplete-box";
    dropdown.appendChild(suggestions);

    // Lista de valores
    const list = document.createElement("div");
    list.className = "dropdown-list";
    dropdown.appendChild(list);

    let currentIndex = -1;

    // Função para renderizar a lista
    function render(filter = "") {
        list.innerHTML = "";

        values
            .filter(v => v.toLowerCase().includes(filter.toLowerCase()))
            .forEach((value) => {
                const checked = activeColumnFilters[field].includes(value);

                const item = document.createElement("div");
                item.className = "dropdown-item";
                const clean = value
                    .replace(/<[^>]+>/g, " ")
                    .replace(/\s+/g, " ")
                    .trim();

                item.innerHTML = `
                    <div>
                        <input type="checkbox" ${checked ? "checked" : ""}>
                        <span>${clean}</span>
                    </div>
                    <div>(${valuesCount[value]})</div>
                `;

                item.querySelector("input").addEventListener("change", (e) => {
                    e.stopPropagation();

                    if (e.target.checked) {
                        activeColumnFilters[field].push(value);
                    } else {
                        activeColumnFilters[field] =
                            activeColumnFilters[field].filter(v => v !== value);
                    }
                });

                item.addEventListener("click", (e) => {
                    if (e.target.tagName !== "INPUT") {
                        const cb = item.querySelector("input");
                        cb.checked = !cb.checked;
                        cb.dispatchEvent(new Event("change"));
                    }
                });

                list.appendChild(item);
            });
    }

    // Renderizar sugestões de autocomplete
    function renderSuggestions(search) {
        suggestions.innerHTML = "";

        if (!search) return;

        const matches = values
            .filter(v => smartMatch(v, search))
            .slice(0, 8);

        matches.forEach((value, index) => {
            const item = document.createElement("div");
            item.className = "autocomplete-item";

            item.innerHTML = highlightMatch(value, search);

            item.addEventListener("click", () => {
                selectAutocomplete(field, value);
            });

            suggestions.appendChild(item);
        });
    }

    // Event listener para input
    input.addEventListener("input", (e) => {
        const val = e.target.value;
        render(val);
        renderSuggestions(val);
    });

    // Navegação por teclado
    input.addEventListener("keydown", (e) => {
        const items = suggestions.querySelectorAll(".autocomplete-item");

        if (e.key === "ArrowDown") {
            currentIndex = Math.min(currentIndex + 1, items.length - 1);
        }

        if (e.key === "ArrowUp") {
            currentIndex = Math.max(currentIndex - 1, 0);
        }

        if (e.key === "Enter" && items[currentIndex]) {
            items[currentIndex].click();
        }

        items.forEach((el, i) => {
            el.classList.toggle("active", i === currentIndex);
        });
    });

    // Botões de ação
    const actions = document.createElement("div");
    actions.className = "dropdown-actions";

    const applyBtn = document.createElement("button");
    applyBtn.textContent = "Aplicar";
    applyBtn.className = "btn-apply";
    applyBtn.onclick = () => {
        applyAllFilters();
        dropdown.style.display = "none";
    };

    const resetBtn = document.createElement("button");
    resetBtn.textContent = "Limpar";
    resetBtn.className = "btn-reset";
    resetBtn.onclick = () => {
        activeColumnFilters[field] = [];
        render();
        applyAllFilters();
    };

    actions.appendChild(resetBtn);
    actions.appendChild(applyBtn);

    dropdown.appendChild(actions);

    render();

    setTimeout(() => input.focus(), 50);
}

/**
 * Aplica todos os filtros ativos na tabela
 */
function applyAllFilters() {
    table.setFilter(function (data) {
        return Object.keys(activeColumnFilters).every(field => {
            const selected = activeColumnFilters[field];

            if (!selected || selected.length === 0) {
                return true;
            }

            const raw = data[field];

            return selected.some(val => smartMatch(raw, val));
        });
    });

    renderFilterChips();
}

/**
 * Renderiza os chips de filtros ativos
 */
function renderFilterChips() {
    const container = document.getElementById("active-filters");
    container.innerHTML = "";

    Object.entries(activeColumnFilters).forEach(([field, values]) => {
        values.forEach(value => {
            const chip = document.createElement("div");
            chip.className = "filter-chip";

            chip.innerHTML = `
                <strong>${field}</strong>: ${value}
                <span>×</span>
            `;

            chip.querySelector("span").onclick = () => {
                activeColumnFilters[field] =
                    activeColumnFilters[field].filter(v => v !== value);

                applyAllFilters();
            };

            container.appendChild(chip);
        });
    });
}

/**
 * Seleciona um valor do autocomplete
 */
function selectAutocomplete(field, value) {
    if (!activeColumnFilters[field]) {
        activeColumnFilters[field] = [];
    }

    if (!activeColumnFilters[field].includes(value)) {
        activeColumnFilters[field].push(value);
    }

    applyAllFilters();

    document.getElementById("filter-dropdown").style.display = "none";
}

// ============================================================================
// SEÇÃO 5: FUNÇÕES AUXILIARES DE BUSCA (FUZZY MATCHING)
// ============================================================================

/**
 * Normaliza texto (remove acentos, espaços extras, etc)
 */
function normalize(text) {
    return String(text || "")
        .replace(/<[^>]+>/g, " ")
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .replace(/\s+/g, " ")
        .trim();
}

/**
 * Calcula a similaridade entre dois strings (Levenshtein)
 */
function similarity(a, b) {
    if (!a || !b) return 0;

    const longer = a.length > b.length ? a : b;
    const shorter = a.length > b.length ? b : a;

    const longerLength = longer.length;
    if (longerLength === 0) return 1.0;

    return (longerLength - editDistance(longer, shorter)) / longerLength;
}

/**
 * Calcula a distância de edição (Levenshtein distance)
 */
function editDistance(a, b) {
    const matrix = [];

    for (let i = 0; i <= b.length; i++) matrix[i] = [i];
    for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            if (b.charAt(i - 1) === a.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i - 1][j - 1] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j] + 1
                );
            }
        }
    }

    return matrix[b.length][a.length];
}

/**
 * Busca inteligente com suporte a fuzzy matching
 */
function smartMatch(text, search) {
    const t = normalize(text);
    const s = normalize(search);

    // Match direto
    if (t.includes(s)) return true;

    // Match por palavra
    const words = t.split(" ");
    if (words.some(w => w.includes(s))) return true;

    // Match fuzzy (tolerância a erros)
    return similarity(t, s) > 0.6;
}

/**
 * Destaca o texto encontrado
 */
function highlightMatch(text, search) {
    const safe = search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${safe})`, "gi");
    return text.replace(regex, "<mark>$1</mark>");
}

// ============================================================================
// SEÇÃO 6: EXPORTAÇÃO E RELATÓRIOS
// ============================================================================

/**
 * Gera um relatório customizado
 */
function generateReport({ table, title = "Relatório", logo = "/logo.png" }) {
    const data = table.getData("active");
    const columns = table.getColumns().filter(c => c.isVisible());

    const headers = columns.map(col => ({
        title: col.getDefinition().title,
        field: col.getDefinition().field
    }));

    const html = buildReportHTML({
        title,
        logo,
        headers,
        data
    });

    openPrintWindow(html);
}

/**
 * Constrói o HTML do relatório
 */
function buildReportHTML({ title, logo, headers, data }) {
    return `
    <html>
    <head>
        <title>${title}</title>
        <style>
            body {
                font-family: Arial;
                margin: 0;
                padding: 20px;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 80px;
                border-bottom: 2px solid #000;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 20px;
                background: white;
            }

            .logo {
                height: 50px;
            }

            .title {
                font-size: 18px;
                font-weight: bold;
            }

            .date {
                font-size: 12px;
            }

            .content {
                margin-top: 100px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }

            thead {
                display: table-header-group;
            }

            th, td {
                border: 1px solid #000;
                padding: 6px;
                font-size: 12px;
            }

            th {
                background: #eee;
            }

            tr {
                page-break-inside: avoid;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 40px;
                text-align: center;
                font-size: 12px;
            }

            @media print {
                .footer:after {
                    content: "Página " counter(page) " de " counter(pages);
                }
            }
        </style>
    </head>

    <body>
        <div class="header">
            <img src="${logo}" class="logo">
            <div class="title">${title}</div>
            <div class="date">${new Date().toLocaleString()}</div>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        ${headers.map(h => `<th>${h.title}</th>`).join("")}
                    </tr>
                </thead>
                <tbody>
                    ${data.map(row => `
                        <tr>
                            ${headers.map(h => `
                                <td>${clean(row[h.field])}</td>
                            `).join("")}
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        </div>

        <div class="footer"></div>
    </body>
    </html>
    `;
}

/**
 * Abre a janela de impressão
 */
function openPrintWindow(html) {
    const win = window.open('', '_blank');

    win.document.write(html);
    win.document.close();

    setTimeout(() => {
        win.focus();
        win.print();
    }, 500);
}

/**
 * Remove HTML de um valor
 */
function clean(val) {
    return String(val || "")
        .replace(/<[^>]+>/g, "")
        .trim();
}

// ============================================================================
// SEÇÃO 7: INICIALIZAÇÃO
// ============================================================================

document.addEventListener("DOMContentLoaded", function () {

    // ===== INICIALIZAR TABULATOR =====
    table = new Tabulator("#participantes-table", {
        height: "auto",
        layout: "fitDataFill",
        responsiveLayout: false,
        pagination: false,
        movableColumns: true,
        resizableColumnFit: true,
        placeholder: "Nenhum registro encontrado",
        headerFilterLiveFilter: false,
        columnMinWidth: 80,
        columnDefaults: {
            formatter: "html",
            headerSort: true,
            maxWidth: 400,
            minWidth: 80,
            headerFilter: "input",
            headerFilterPlaceholder: "Filtrar..."
        },
        headerSort: false
    });

    // Evento ao renderizar
    table.on("renderComplete", function () {
        const header = document.querySelector(".tabulator-col:first-child");

        if (header && !header.querySelector("#select-all-participantes")) {
            header.innerHTML = `<div class="header-checkbox">
            <input type="checkbox" id="select-all-participantes">
        </div>`;
        }

        const checkbox = document.getElementById("select-all-participantes");

        if (checkbox) {
            checkbox.addEventListener("click", function (e) {
                e.stopPropagation(); // 🔥 ESSENCIAL
            });

            checkbox.addEventListener("mousedown", function (e) {
                e.stopPropagation(); // 🔥 MAIS IMPORTANTE AINDA
            });
        }

        const firstCol = document.querySelector(".tabulator-col:first-child");
        if (firstCol) {
            firstCol.classList.add("no-sort");
        }

        updateBulkActionsBar();
    });

    // ===== MENU DE COLUNAS =====
    const colToggleBtn = document.getElementById("col-toggle-btn");
    const colDropdown = document.getElementById("column-menu");

    if (colToggleBtn && colDropdown) {
        colToggleBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            colDropdown.classList.toggle('show');
            colDropdown.style.display = colDropdown.classList.contains('show') ? "block" : "none";
            if (colDropdown.classList.contains('show')) {
                openColumnMenu();
            }
        });

        document.addEventListener('click', function (e) {
            if (!colDropdown.contains(e.target) && e.target !== colToggleBtn) {
                colDropdown.classList.remove('show');
                colDropdown.style.display = "none";
            }
        });
    }

    // ===== BUSCA GLOBAL =====
    const searchInput = document.getElementById("search-input");
    if (searchInput) {
        searchInput.addEventListener("keyup", function (e) {
            const value = e.target.value.toLowerCase();
            if (table) {
                table.setFilter(function (data) {
                    return Object.values(data).some(val =>
                        String(val).toLowerCase().includes(value)
                    );
                });
            }
        });
    }

    // ===== EXPORTAÇÃO =====
    const btnExcel = document.getElementById("btn-excel");
    if (btnExcel) {
        btnExcel.addEventListener("click", function () {
            if (table) {
                table.download("xlsx", "participantes.xlsx", { sheetName: "Participantes" });
            }
        });
    }

    const btnPdf = document.getElementById("btn-pdf");
    if (btnPdf) {
        btnPdf.addEventListener("click", function () {
            if (table) {
                table.download("pdf", "participantes.pdf", {
                    orientation: "landscape",
                    title: "Relatório de Participantes"
                });
            }
        });
    }

    const btnPrint = document.getElementById("btn-print");
    if (btnPrint) {
        btnPrint.addEventListener("click", function () {
            if (table) {
                generateReport({
                    table,
                    title: "Relatório de Participantes",
                    logo: "/logo.png"
                });
            }
        });
    }

    // ===== BULK ACTIONS =====
    const bulkBar = document.getElementById("bulk-bar");

    if (bulkBar) {
        const bulkDelete = bulkBar.querySelector(".btn-danger");

        if (bulkDelete) {
            bulkDelete.addEventListener("click", function () {
                if (selectedRows.size > 0) {
                    const ids = Array.from(selectedRows);
                    const confirmDelete = confirm(`Tem certeza que deseja excluir ${ids.length}?`);

                    if (confirmDelete) {
                        console.log("Excluindo:", ids);
                        // TODO: Implementar fetch para deletar
                        alert("Exclusão simulada");
                    }
                }
            });
        }
    }

    // ===== APLICAR COLUNAS =====
    const applyColumnsBtn = document.getElementById("apply-columns");
    if (applyColumnsBtn) {
        applyColumnsBtn.addEventListener("click", () => {
            Object.entries(tempColumnState).forEach(([field, visible]) => {
                visible ? table.showColumn(field) : table.hideColumn(field);
            });

            saveTableConfig(table);

            document.getElementById("column-menu").style.display = "none";
        });
    }

    // ===== BUSCA DE COLUNAS =====
    const columnSearch = document.getElementById("column-search");
    if (columnSearch) {
        columnSearch.addEventListener("input", function () {
            const value = this.value.toLowerCase();

            document.querySelectorAll(".column-item").forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(value) ? "flex" : "none";
            });
        });
    }

    // ===== REDIMENSIONAR =====
    window.addEventListener("resize", function () {
        if (table) table.redraw(true);
    });

    // ===== SALVAR CONFIGURAÇÕES =====
    table.on("columnVisibilityChanged", function () {
        saveTableConfig(table);
    });

    table.on("columnMoved", function () {
        saveTableConfig(table);
    });
});

// ===== FILTRO =====
document.getElementById("filter-btn").addEventListener("click", (e) => {
    e.stopPropagation();

    const dropdown = document.getElementById("filter-dropdown");
    dropdown.style.display = "block";

    openFilterMenu();
});

document.addEventListener("click", (e) => {
    if (!e.target.closest("#filter-dropdown") && !e.target.closest("#filter-btn")) {
        document.getElementById("filter-dropdown").style.display = "none";
    }
});

// ===== LIMPAR FILTROS =====
document.getElementById("clear-filters").addEventListener("click", () => {
    activeColumnFilters = {};
    table.clearFilter();
    renderFilterChips();
});
