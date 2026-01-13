// Theme Toggle Logic
        // Modal Logic
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            if(id === 'search-modal') setTimeout(() => document.getElementById('global-search-input').focus(), 100);
        }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        // Theme Logic
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        themeToggle.addEventListener('click', () => {
            const isDark = htmlElement.getAttribute('data-theme') === 'dark';
            htmlElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
            themeToggle.textContent = isDark ? '🌙' : '☀️';
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
            updateChartsTheme();
        });

        // Sidebar Logic
        const miniLinks = document.querySelectorAll('.mini-link[data-target]');
        const detailPanels = document.querySelectorAll('.detail-panel');
        const detailSidebar = document.getElementById('detail-sidebar');
        const dashboardContainer = document.getElementById('dashboard-container');
        const overlay = document.getElementById('sidebar-overlay');

        miniLinks.forEach(link => {
            link.addEventListener('click', () => {
                const target = link.getAttribute('data-target');
                miniLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                detailPanels.forEach(p => p.classList.remove('active'));
                document.getElementById(target).classList.add('active');
                detailSidebar.classList.add('open');
                dashboardContainer.classList.remove('sidebar-collapsed');

                if (window.innerWidth < 1024) {
                    overlay.classList.add('active');
                }
            });
        });

        document.getElementById('close-sidebar').addEventListener('click', () => {
            detailSidebar.classList.remove('open');
            dashboardContainer.classList.add('sidebar-collapsed');
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

        initCharts();