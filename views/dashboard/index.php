<?php ob_start(); ?>


<!-- Stats Cards -->
<section class="stats-grid">
    <div class="stat-card">
        <h3>Total de Vendas</h3>
        <p class="value">R$ 12.450,00</p>
    </div>
    <div class="stat-card">
        <h3>Novos Clientes</h3>
        <p class="value">48</p>
    </div>
    <div class="stat-card">
        <h3>Projetos Ativos</h3>
        <p class="value">12</p>
    </div>
    <div class="stat-card">
        <h3>Taxa de Conversão</h3>
        <p class="value">3.2%</p>
    </div>
</section>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Line Chart -->
    <section class="chart-container">
        <h2>Desempenho de Vendas</h2>
        <canvas id="salesChart"></canvas>
    </section>

    <!-- Bar Chart -->
    <section class="chart-container">
        <h2>Novos Clientes</h2>
        <canvas id="clientsChart"></canvas>
    </section>
</div>

<!-- Data Table -->
<section class="data-table-container">
    <h2>Atividades Recentes</h2>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Projeto</th>
                <th>Status</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>João Silva</td>
                <td>E-commerce</td>
                <td><span class="status completed">Concluído</span></td>
                <td>R$ 4.500</td>
            </tr>
            <tr>
                <td>Maria Oliveira</td>
                <td>App Mobile</td>
                <td><span class="status pending">Pendente</span></td>
                <td>R$ 8.200</td>
            </tr>
        </tbody>
    </table>
</section>

<?php
$content = ob_get_clean();
$title = 'Dashboard';
$titletopbar = "Visão Geral";
include __DIR__ . '/../layouts/app.php';
?>