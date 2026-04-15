// Theme Toggle Logic
// Modal Logic
function openModal(id) {
    document.getElementById(id).classList.add('active');
    if (id === 'search-modal') setTimeout(() => document.getElementById('global-search-input').focus(), 100);
}
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

const htmlElement = document.documentElement;

// Profile Dropdown Toggle
const profileTrigger = document.getElementById('profile-trigger');
const profileMenu = document.getElementById('profile-menu');

const profileContainer = document.querySelector('.profile-dropdown-container');
if (profileTrigger) {
    profileTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
        profileContainer.classList.toggle('active');
    });
}

const themeToggle = document.getElementById('theme-toggle');
themeToggle.addEventListener('click', () => {
    const isDark = htmlElement.getAttribute('data-theme') === 'dark';
    htmlElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
    themeToggle.textContent = isDark ? '🌙' : '☀️';
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
    updateChartsTheme();
});

document.addEventListener('click', (e) => {
    if (profileMenu && !profileMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
        profileMenu.classList.remove('active');
        profileContainer.classList.remove('active');
    }
});

// Theme Toggle (Updated for Dropdown Switch)
const themeCheckbox = document.getElementById('theme-toggle-checkbox');
if (themeCheckbox) {
    themeCheckbox.checked = (htmlElement.getAttribute('data-theme') === 'dark');
    themeCheckbox.addEventListener('change', () => {
        const next = themeCheckbox.checked ? 'dark' : 'light';
        htmlElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        updateChartsTheme();
    });
}

// Sidebar Logic
const miniLinks = document.querySelectorAll('.mini-link[data-target]');
const detailPanels = document.querySelectorAll('.detail-panel');
const detailSidebar = document.getElementById('detail-sidebar');
const dashboardContainer = document.getElementById('dashboard-container');
const overlay = document.getElementById('sidebar-overlay');

function activatePanel(targetId) {
    miniLinks.forEach(l => {
        l.classList.remove('active');
        if (l.getAttribute('data-target') === targetId) l.classList.add('active');
    });
    detailPanels.forEach(p => p.classList.remove('active'));
    const targetPanel = document.getElementById(targetId);
    if (targetPanel) targetPanel.classList.add('active');

    detailSidebar.classList.add('open');
    dashboardContainer.classList.remove('sidebar-collapsed');
}

miniLinks.forEach(link => {
    link.addEventListener('click', () => {
        const target = link.getAttribute('data-target');
        activatePanel(target);
        if (window.innerWidth < 1024) overlay.classList.add('active');
    });
});

// Auto-activate menu based on URL (Supports Friendly Routes)
function autoActivateMenu() {
    const currentPath = window.location.pathname;
    const allLinks = document.querySelectorAll('.detail-link');
    let found = false;

    allLinks.forEach(link => {
        const linkPath = link.getAttribute('href');

        if (linkPath && linkPath !== '#' && (currentPath.includes(linkPath) || linkPath.includes(currentPath))) {
            link.classList.add('active');
            const parentPanel = link.closest('.detail-panel');
            if (parentPanel) {
                activatePanel(parentPanel.id);
                found = true;
            }
        }
    });

    // Se não encontrar uma rota específica, ativa o Home por padrão
    if (!found) {
        activatePanel('home-panel');
    }
}

//window.addEventListener('DOMContentLoaded', autoActivateMenu);

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    autoActivateMenu(); // Ensure menu is activated on load
    const selectAllCheckbox = document.getElementById("select-all-nfe");
    const nfeCheckboxes = document.querySelectorAll(".nfe-checkbox");
    const bulkActionsBar = document.getElementById("bulk-actions-bar");
    const selectedCountSpan = document.getElementById("selected-count");
    let selectedNFe = new Set();

    function updateBulkActionsBar() {
        console.log("Updating bulk actions bar. Selected count:", selectedNFe.size);
        selectedCountSpan.innerText = selectedNFe.size;
        if (selectedNFe.size > 0) {
            bulkActionsBar.classList.add("active");
        } else {
            bulkActionsBar.classList.remove("active");
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            console.log("Select all checkbox changed", this.checked);
            nfeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    selectedNFe.add(checkbox.value);
                } else {
                    selectedNFe.delete(checkbox.value);
                }
            });
            updateBulkActionsBar();
        });
    }

    nfeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            console.log("NFe checkbox changed", this.value, this.checked);
            if (this.checked) {
                selectedNFe.add(this.value);
            } else {
                selectedNFe.delete(this.value);
            }
            updateBulkActionsBar();
        });
    });

    window.simulateBulkAction = function (action) {
        if (selectedNFe.size === 0) {
            alert("Nenhuma NF-e selecionada para esta ação.");
            return;
        }

        const nfeNumbers = Array.from(selectedNFe).join(", ");
        alert(`Ação em lote de ${action} para as NF-e(s) ${nfeNumbers} simulada com sucesso!`);

        // Reset selection
        selectedNFe.clear();
        selectAllCheckbox.checked = false;
        nfeCheckboxes.forEach(cb => cb.checked = false);
        updateBulkActionsBar();
    }

    const miniLinks = document.querySelectorAll(".mini-link");
    const detailPanels = document.querySelectorAll(".detail-panel");
    const profileTrigger = document.getElementById('profile-trigger');
    const profileMenu = document.getElementById('profile-menu');


    miniLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetPanelId = e.currentTarget.dataset.target;
            detailPanels.forEach(panel => {
                if (panel.id === targetPanelId) {
                    panel.classList.add('active');
                } else {
                    panel.classList.remove('active');
                }
            });
            // Ensure nfe-panel is active when navigating to nfe_lista.html
            if (targetPanelId === 'nfe-panel') {
                document.getElementById('nfe-panel').classList.add('active');
            }
        });
    });

    // Profile Dropdown Toggle
    if (profileTrigger) {
        profileTrigger.addEventListener('click', () => {
            profileMenu.classList.toggle('show');
        });
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', (e) => {
        if (profileMenu && !profileTrigger.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.classList.remove('show');
        }
    });




    window.simulateNFeAction = function (action, nfeNumber = '') {
        let message = `Ação de ${action} para a NF-e ${nfeNumber || 'selecionada'} simulada com sucesso!`;
        if (action === 'transmit') {
            message = `NF-e ${nfeNumber} transmitida com sucesso!`;
            closeModal('transmit-nfe-modal');
        } else if (action === 'cancel') {
            message = `NF-e ${nfeNumber} cancelada com sucesso!`;
            closeModal('cancel-nfe-modal');
        } else if (action === 'cc') {
            message = `Carta de Correção para NF-e ${nfeNumber} emitida com sucesso!`;
            closeModal('cc-nfe-modal');
        } else if (action === 'download_xml') {
            message = `Download do XML da NF-e ${nfeNumber} iniciado.`;
        } else if (action === 'print_danfe') {
            message = `Impressão do DANFE da NF-e ${nfeNumber} iniciada.`;
        } else if (action === 'view_details') {
            message = `Detalhes da NF-e ${nfeNumber} exibidos.`;
        }
        alert(message);
    }

    // Filters Card Toggle
    const filtersCardHeader = document.querySelector(".filters-card .card-header");
    if (filtersCardHeader) {
        filtersCardHeader.addEventListener('click', () => {
            filtersCardHeader.closest(".card").classList.toggle('collapsed');
        });
    }

});

document.getElementById('close-sidebar').addEventListener('click', () => {
    detailSidebar.classList.remove('open');
    dashboardContainer.classList.add('sidebar-collapsed');
    setTimeout(() => {
        table.redraw(true);
        table.setColumns(table.getColumnDefinitions());
    }, 300);
});

document.getElementById('menu-toggle').addEventListener('click', () => {
    document.querySelector('.mini-sidebar').classList.toggle('active');
    overlay.classList.toggle('active');
});

overlay.addEventListener('click', () => {
    overlay.classList.remove('active');
    document.querySelector('.mini-sidebar').classList.remove('active');
    detailSidebar.classList.remove('open');
    dashboardContainer.classList.add('sidebar-collapsed');
});


/**
 * Aqui você pode adicionar funções para abrir janelas específicas do sistema, como detalhes de clientes, NF-e, etc.
 * Use a função abrirJanelaUnica para garantir que apenas uma janela de cada tipo seja aberta.
 * Exemplo:
 * abrirJanelaUnica('cliente-123', 'Detalhes do Cliente 123', '/clientes/123');
 * abrirJanelaUnica('nfe-456', 'Detalhes da NF-e 456', '/nfe/456');
 *
 * Certifique-se de substituir as URLs pelos caminhos corretos do seu sistema.
 * Você pode criar funções específicas para cada tipo de janela, se desejar, para facilitar a chamada em outros lugares do código.
 * Exemplo:
 */

let janelasAbertas = {};

function abrirJanelaUnica(id, titulo, url) {
    if (janelasAbertas[id]) {
        janelasAbertas[id].focus();
        return;
    }

    const win = new WinBox({
        id: "win-" + id,
        title: titulo,
        url: url,
        width: 900,
        height: 600,
        x: "center",
        y: "center",
        onclose: function () {
            delete janelasAbertas[id];
            removerDaTaskbar(id);
        },
        onfocus: function () {
            atualizarStatusTaskbar(id, true);
        },
        onblur: function () {
            atualizarStatusTaskbar(id, false);
        }
    });

    janelasAbertas[id] = win;
    adicionarNaTaskbar(id, titulo);
}

function adicionarNaTaskbar(id, titulo) {
    const taskbar = document.getElementById('winbox-taskbar');
    taskbar.style.display = 'flex';

    const item = document.createElement('div');
    item.className = 'taskbar-item active';
    item.id = 'task-item-' + id;
    item.innerHTML = `<i class="fas fa-window-maximize"></i> <span>${titulo}</span>`;
    item.onclick = function () {
        const win = janelasAbertas[id];
        if (win.min) {
            win.minimize(false);
            win.focus();
        } else if (document.activeElement === win.dom) {
            win.minimize(true);
        } else {
            win.focus();
        }
    };

    taskbar.appendChild(item);
}

function removerDaTaskbar(id) {
    const item = document.getElementById('task-item-' + id);
    if (item) item.remove();

    const taskbar = document.getElementById('winbox-taskbar');
    if (taskbar.children.length === 0) {
        taskbar.style.display = 'none';
    }
}

function atualizarStatusTaskbar(id, isActive) {
    const item = document.getElementById('task-item-' + id);
    if (item) {
        if (isActive) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    }
}
// Charts Implementation
let salesChart, clientsChart;

function initCharts() {
    const isDark = htmlElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? '#374151' : '#e5e7eb';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    // Sales Line Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Vendas (R$)',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });

    // Clients Bar Chart
    const clientsCtx = document.getElementById('clientsChart').getContext('2d');
    clientsChart = new Chart(clientsCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Novos Clientes',
                data: [35, 48, 42, 65, 58, 72],
                backgroundColor: '#4f46e5',
                borderRadius: 6,
                hoverBackgroundColor: '#4338ca'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor } },
                x: { grid: { display: false }, ticks: { color: textColor } }
            }
        }
    });
}

function updateChartsTheme() {
    const isDark = htmlElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? '#374151' : '#e5e7eb';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    [salesChart, clientsChart].forEach(chart => {
        if (chart) {
            chart.options.scales.y.grid.color = gridColor;
            chart.options.scales.y.ticks.color = textColor;
            chart.options.scales.x.ticks.color = textColor;
            chart.update();
        }
    });
}
if (document.getElementById('salesChart')) {
    initCharts();
}
