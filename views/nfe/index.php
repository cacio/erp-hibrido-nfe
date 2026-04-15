<?php ob_start(); ?>


<div class="content-header">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar NF-e por número, cliente, etc.">
        <button class="btn btn-primary">Buscar</button>
    </div>
    <div class="filter-actions">
        <button class="btn btn-secondary"><i class="fa fa-file-export"></i> Exportar</button>
        <a href="/nfe/create" class="btn btn-primary">+ Nova NF-e</a>
    </div>
</div>

<div class="card filters-card">
    <div class="card-header">
        <h2>Filtros Avançados</h2>
        <i class="fas fa-chevron-down"></i>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="filter-period-start">Período Inicial</label>
            <input type="date" id="filter-period-start">
        </div>
        <div class="form-group">
            <label for="filter-period-end">Período Final</label>
            <input type="date" id="filter-period-end">
        </div>
        <div class="form-group">
            <label for="filter-status">Status</label>
            <select id="filter-status">
                <option>Todos</option>
                <option>Autorizada</option>
                <option>Rejeitada</option>
                <option>Cancelada</option>
                <option>Em Processamento</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filter-client">Cliente/Fornecedor</label>
            <input type="text" id="filter-client" placeholder="Nome ou CNPJ">
        </div>
        <div class="form-group">
            <label for="filter-serie">Série</label>
            <input type="text" id="filter-serie" placeholder="Ex: 1, 2, ...">
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">Aplicar Filtros</button>
            <button class="btn btn-secondary">Limpar Filtros</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>NF-e Emitidas</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-nfe"></th>
                        <th>Número</th>
                        <th>Série</th>
                        <th>Cliente/Fornecedor</th>
                        <th>Emissão</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox" class="nfe-checkbox" value="12345"></td>
                        <td>12345</td>
                        <td>1</td>
                        <td>FRIGORÍFICO MODELO LTDA</td>
                        <td>05/04/2026</td>
                        <td>R$ 15.250,00</td>
                        <td><span class="badge badge-success">Autorizada</span></td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('download_xml', '12345')" title="Download XML"><i class="fas fa-file-code"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('print_danfe', '12345')" title="Imprimir DANFE"><i class="fas fa-print"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="openModal('cc-nfe-modal', '12345')" title="Carta de Correção"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="openModal('cancel-nfe-modal', '12345')" title="Cancelar NF-e"><i class="fas fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="nfe-checkbox" value="12346"></td>
                        <td>12346</td>
                        <td>1</td>
                        <td>SUPERMERCADO BOM PREÇO</td>
                        <td>05/04/2026</td>
                        <td>R$ 8.730,50</td>
                        <td><span class="badge badge-danger">Rejeitada</span></td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('download_xml', '12346')" title="Download XML"><i class="fas fa-file-code"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('print_danfe', '12346')" title="Imprimir DANFE"><i class="fas fa-print"></i></button>
                                <button class="btn btn-sm btn-primary" onclick="openModal('transmit-nfe-modal', '12346')" title="Transmitir Novamente"><i class="fas fa-paper-plane"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="openModal('cancel-nfe-modal', '12346')" title="Cancelar NF-e"><i class="fas fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="nfe-checkbox" value="12347"></td>
                        <td>12347</td>
                        <td>1</td>
                        <td>AÇOUGUE DO POVO</td>
                        <td>04/04/2026</td>
                        <td>R$ 2.100,00</td>
                        <td><span class="badge badge-warning">Em Processamento</span></td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('download_xml', '12347')" title="Download XML"><i class="fas fa-file-code"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('print_danfe', '12347')" title="Imprimir DANFE"><i class="fas fa-print"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="openModal('cc-nfe-modal', '12347')" title="Carta de Correção"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="openModal('cancel-nfe-modal', '12347')" title="Cancelar NF-e"><i class="fas fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="nfe-checkbox" value="12348"></td>
                        <td>12348</td>
                        <td>1</td>
                        <td>DISTRIBUIDORA ALIMENTOS SA</td>
                        <td>03/04/2026</td>
                        <td>R$ 30.500,00</td>
                        <td><span class="badge badge-secondary">Cancelada</span></td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('download_xml', '12348')" title="Download XML"><i class="fas fa-file-code"></i></button>
                                <button class="btn btn-sm btn-outline" onclick="simulateNFeAction('print_danfe', '12348')" title="Imprimir DANFE"><i class="fas fa-print"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="openModal('cancel-nfe-modal', '12348')" title="Cancelar NF-e"><i class="fas fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Transmit NFe Modal -->
<div id="transmit-nfe-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirmar Transmissão</h2>
            <button class="modal-close" onclick="closeModal('transmit-nfe-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Deseja realmente transmitir a NF-e <span id="nfe-transmit-number"></span>?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('transmit-nfe-modal')">Cancelar</button>
            <button class="btn btn-primary" onclick="simulateNFeAction('transmit', document.getElementById('nfe-transmit-number').innerText)">Transmitir</button>
        </div>
    </div>
</div>

<!-- Cancel NFe Modal -->
<div id="cancel-nfe-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirmar Cancelamento</h2>
            <button class="modal-close" onclick="closeModal('cancel-nfe-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Deseja realmente cancelar a NF-e <span id="nfe-cancel-number"></span>?</p>
            <div class="form-group">
                <label for="cancel-justification">Justificativa de Cancelamento:</label>
                <textarea id="cancel-justification" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('cancel-nfe-modal')">Cancelar</button>
            <button class="btn btn-danger" onclick="simulateNFeAction('cancel', document.getElementById('nfe-cancel-number').innerText)">Cancelar NF-e</button>
        </div>
    </div>
</div>

<!-- Carta de Correção Modal -->
<div id="cc-nfe-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Carta de Correção</h2>
            <button class="modal-close" onclick="closeModal('cc-nfe-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Informe a correção para a NF-e <span id="nfe-cc-number"></span>:</p>
            <div class="form-group">
                <label for="cc-text">Texto da Correção:</label>
                <textarea id="cc-text" class="form-control" rows="5"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('cc-nfe-modal')">Cancelar</button>
            <button class="btn btn-primary" onclick="simulateNFeAction('cc', document.getElementById('nfe-cc-number').innerText)">Emitir CC-e</button>
        </div>
    </div>
</div>


<!-- Bulk Actions Bar -->
<div class="bulk-actions-bar" id="bulk-actions-bar">
    <span id="selected-count">0</span> NF-e(s) selecionada(s)
    <div class="bulk-actions-buttons">
        <button class="btn btn-sm" onclick="simulateBulkAction('transmit')"><i class="fas fa-paper-plane"></i> Transmitir</button>
        <button class="btn btn-sm" onclick="simulateBulkAction('download_xml')"><i class="fas fa-download"></i> Download XML</button>
        <button class="btn btn-sm" onclick="simulateBulkAction('print_danfe')"><i class="fas fa-print"></i> Imprimir DANFE</button>
        <button class="btn btn-sm btn-danger" onclick="simulateBulkAction('cancel')"><i class="fas fa-times-circle"></i> Cancelar</button>
    </div>
</div>


<?php
$content = ob_get_clean();
$title = 'Listagem de NF-e';
$titletopbar = 'Listagem de NF-e';
include __DIR__ . '/../layouts/app.php';
