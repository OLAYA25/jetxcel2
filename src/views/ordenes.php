<?php include '../includes/partials/header.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Pedido - Sistema Integrado</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #f8f9fa;
            --border-color: #dee2e6;
            --text-color: #212529;
            --danger-color: #dc3545;
            --focus-border-color: #007bff;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--text-color);
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .logo-title {
            display: flex;
            flex-direction: column;
            min-width: 300px;
        }
        
        .logo-title .select2-container {
            width: 100% !important;
            margin: 0 !important;
        }
        
        .logo-title .select2-container .select2-selection--single {
            height: auto;
            padding: 0;
            border: none !important;
            background: transparent !important;
            display: flex;
            align-items: center;
        }
        
        .logo-title .select2-container--open .select2-selection--single {
            background: white !important;
            border: 1px solid #ddd !important;
            border-radius: 4px;
        }
        
        .logo-title .select2-dropdown {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            min-width: 400px;
        }
        
        .logo-title .select2-search--dropdown {
            padding: 8px;
            background: #f8f8f8;
            border-bottom: 1px solid #eee;
        }
        
        .logo-title .select2-search--dropdown .select2-search__field {
            width: 100%;
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .select2-selection__arrow { 
            display: none !important; 
        }
        
        .logo-title .empresa h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            padding: 2px 0;
            white-space: nowrap;
            line-height: 1;
        }
        
        .logo-title .nit p {
            color: #666;
            font-size: 14px;
            margin: 0;
            padding: 2px 0;
            white-space: nowrap;
            line-height: 1;
        }
        
        .logo-title .select2-selection__rendered {
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }
        
        .order-number {
            border: 2px solid var(--primary-color);
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            min-width: 150px;
        }
        
        .order-number h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .order-number p {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .form-section {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .select2-form-container .select2-container {
            width: 100% !important;
        }
        
        .select2-form-container .select2-selection--single {
            height: 38px;
            border: 1px solid var(--border-color) !important;
            border-radius: 4px !important;
        }
        
        .select2-form-container .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 10px;
            text-align: left;
        }
        
        .items-table td {
            padding: 8px;
            border: 1px solid var(--border-color);
            position: relative;
        }
        
        .items-table input {
            width: 100%;
            padding: 4px;
            border: 1px solid transparent;
            background: transparent;
            outline: none;
        }
        
        .items-table input.default-value {
            color: #999;
            font-style: italic;
        }
        
        .items-table input:focus.default-value {
            color: #000;
            font-style: normal;
        }
        
        .items-table td.selected-cell {
            border: 2px solid var(--primary-color) !important;
            background-color: rgba(0, 123, 255, 0.05);
            box-shadow: 0 0 0 1px var(--primary-color);
        }
        
        .add-row-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        
        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 5px;
            border: 1px solid var(--border-color);
        }
        
        .totals-table td:first-child {
            font-weight: bold;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        
        .signature {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-top: 1px solid var(--border-color);
            margin: 0 10px;
        }
        
        .signature p {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .delete-btn {
            background-color: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .delete-btn:hover {
            background-color: #bd2130;
            transform: scale(1.1);
        }
        
        .delete-btn i {
            font-size: 12px;
        }
        
        .delete-cell {
            width: 40px;
            padding: 5px 0 !important;
            text-align: center;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 10px;
            }
            
            .action-buttons {
                display: none;
            }
            
            .add-row-btn {
                display: none;
            }
            
            .delete-btn {
                display: none;
            }
            
            .logo-title .select2-container {
                display: block;
            }
            .logo-title .select2-selection--single {
                background: transparent !important;
                border: none !important;
            }
            .logo-title .select2-selection__rendered {
                display: block !important;
                padding: 0 !important;
            }
            .logo-title .empresa h1,
            .logo-title .nit p {
                display: inline !important;
            }
            .select2-dropdown {
                display: none !important;
            }
            
            .items-table td.selected-cell {
                border: 1px solid var(--border-color) !important;
                background-color: transparent;
                box-shadow: none;
            }
        }
        
        @media (max-width: 768px) {
            .form-group {
                min-width: 100%;
            }
            
            .signatures {
                flex-direction: column;
            }
            
            .signature {
                margin-bottom: 20px;
            }
            
            .logo-title .empresa h1 {
                font-size: 20px;
            }
            
            .logo-title .nit p {
                font-size: 12px;
            }
            
            .logo-title .select2-dropdown {
                min-width: 300px;
            }
            
            .items-table th, .items-table td {
                padding: 5px;
                font-size: 12px;
            }
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification.error {
            background-color: #f44336;
        }
        
        .notification .close-btn {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <form id="orderForm">
            <input type="hidden" id="empresa_id" name="empresa_id" value="1">
            <input type="hidden" id="proveedor_id" name="proveedor_id" value="2">
            
            <div class="header">
                <div class="logo-title">
                    <select id="empresa-select">
                        <option value="1" data-nit="900123456-7" data-nombre="CONSORCIO CYM" data-abreviatura="CYM" selected>CONSORCIO CYM</option>
                        <option value="2" data-nit="800987654-3" data-nombre="EMPRESA CONSTRUCTORA ANDINA" data-abreviatura="ECA">EMPRESA CONSTRUCTORA ANDINA</option>
                        <option value="3" data-nit="890765432-1" data-nombre="INGENIERÍA Y PROYECTOS S.A." data-abreviatura="INPROSA">INGENIERÍA Y PROYECTOS S.A.</option>
                    </select>
                    
                    <select id="nit-select">
                        <option value="900123456-7" data-id="1" data-nombre="CONSORCIO CYM" selected>900123456-7</option>
                        <option value="800987654-3" data-id="2" data-nombre="EMPRESA CONSTRUCTORA ANDINA">800987654-3</option>
                        <option value="890765432-1" data-id="3" data-nombre="INGENIERÍA Y PROYECTOS S.A.">890765432-1</option>
                    </select>
                </div>
                <div class="order-number">
                    <h2>No.</h2>
                    <p>
                        OP-<input type="text" id="orderNumber" value="000123" style="width: 100px; border: none; font-weight: bold; font-size: 18px; text-align: center;" readonly>
                    </p>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-row">
                    <div class="form-group select2-form-container">
                        <label for="provider">PROVEEDOR</label>
                        <select id="provider" name="provider" required>
                            <option value="2" data-nit="901234567-8" data-oficina="Oficina Principal" data-direccion="Calle 45 # 26-35" data-telefono="3214567890" selected>CONSTRU MATERIALES S.A.S.</option>
                            <option value="1" data-nit="890123456-7" data-oficina="Sucursal Norte" data-direccion="Carrera 15 # 85-24" data-telefono="3109876543">FERRETERÍA EL CONSTRUCTOR</option>
                            <option value="3" data-nit="912345678-9" data-oficina="Centro" data-direccion="Avenida 68 # 22-45" data-telefono="3151234567">DISTRIBUIDORA DE ACEROS</option>
                        </select>
                    </div>
                    <div class="form-group select2-form-container">
                        <label for="providerNit">NIT o C.C. No.</label>
                        <select id="providerNit" name="providerNit" required>
                            <option value="901234567-8" data-id="2" data-nombre="CONSTRU MATERIALES S.A.S." data-oficina="Oficina Principal" data-direccion="Calle 45 # 26-35" data-telefono="3214567890" selected>901234567-8</option>
                            <option value="890123456-7" data-id="1" data-nombre="FERRETERÍA EL CONSTRUCTOR" data-oficina="Sucursal Norte" data-direccion="Carrera 15 # 85-24" data-telefono="3109876543">890123456-7</option>
                            <option value="912345678-9" data-id="3" data-nombre="DISTRIBUIDORA DE ACEROS" data-oficina="Centro" data-direccion="Avenida 68 # 22-45" data-telefono="3151234567">912345678-9</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group select2-form-container">
                        <label for="office">OFICINA</label>
                        <select id="office" name="office" required>
                            <option value="Oficina Principal" data-id="2" data-nombre="CONSTRU MATERIALES S.A.S." data-nit="901234567-8" data-direccion="Calle 45 # 26-35" data-telefono="3214567890" selected>Oficina Principal</option>
                            <option value="Sucursal Norte" data-id="1" data-nombre="FERRETERÍA EL CONSTRUCTOR" data-nit="890123456-7" data-direccion="Carrera 15 # 85-24" data-telefono="3109876543">Sucursal Norte</option>
                            <option value="Centro" data-id="3" data-nombre="DISTRIBUIDORA DE ACEROS" data-nit="912345678-9" data-direccion="Avenida 68 # 22-45" data-telefono="3151234567">Centro</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group select2-form-container">
                        <label for="address">DIRECCIÓN</label>
                        <select id="address" name="address" required>
                            <option value="Calle 45 # 26-35" data-id="2" data-nombre="CONSTRU MATERIALES S.A.S." data-nit="901234567-8" data-oficina="Oficina Principal" data-telefono="3214567890" selected>Calle 45 # 26-35</option>
                            <option value="Carrera 15 # 85-24" data-id="1" data-nombre="FERRETERÍA EL CONSTRUCTOR" data-nit="890123456-7" data-oficina="Sucursal Norte" data-telefono="3109876543">Carrera 15 # 85-24</option>
                            <option value="Avenida 68 # 22-45" data-id="3" data-nombre="DISTRIBUIDORA DE ACEROS" data-nit="912345678-9" data-oficina="Centro" data-telefono="3151234567">Avenida 68 # 22-45</option>
                        </select>
                    </div>
                    <div class="form-group select2-form-container">
                        <label for="phone">TELÉFONO</label>
                        <select id="phone" name="phone" required>
                            <option value="3214567890" data-id="2" data-nombre="CONSTRU MATERIALES S.A.S." data-nit="901234567-8" data-oficina="Oficina Principal" data-direccion="Calle 45 # 26-35" selected>3214567890</option>
                            <option value="3109876543" data-id="1" data-nombre="FERRETERÍA EL CONSTRUCTOR" data-nit="890123456-7" data-oficina="Sucursal Norte" data-direccion="Carrera 15 # 85-24">3109876543</option>
                            <option value="3151234567" data-id="3" data-nombre="DISTRIBUIDORA DE ACEROS" data-nit="912345678-9" data-oficina="Centro" data-direccion="Avenida 68 # 22-45">3151234567</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">FECHA</label>
                        <input type="date" id="date" name="date" value="2023-09-19" required>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>SÍRVASE POR ESTE MEDIO SUMINISTRAR LOS SIGUIENTES ARTÍCULOS O SERVICIOS:</h3>
                <table class="items-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>ÍTEM</th>
                            <th>DESCRIPCIÓN</th>
                            <th>UNIDAD</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO UNITARIO</th>
                            <th>IVA (%)</th>
                            <th>VALOR IVA</th>
                            <th>VALOR TOTAL</th>
                            <th style="width: 40px; padding: 5px 0;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="item[]" value="1" class="default-value"></td>
                            <td><input type="text" name="description[]" value="Cemento gris 50kg" class="default-value" placeholder="Descripción"></td>
                            <td><input type="text" name="unit[]" value="Bulto" class="default-value" placeholder="Unidad"></td>
                            <td><input type="number" name="quantity[]" value="100" step="0.01" oninput="calculateRowTotal(this)"></td>
                            <td><input type="number" name="unitPrice[]" value="25000" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaPercent[]" value="19" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaAmount[]" value="475000" step="0.01" readonly></td>
                            <td><input type="number" name="total[]" value="2975000" step="0.01" readonly></td>
                            <td class="delete-cell">
                                <button type="button" class="delete-btn" onclick="deleteRow(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="item[]" value="2" class="default-value"></td>
                            <td><input type="text" name="description[]" value="Varilla corrugada 1/2″" class="default-value" placeholder="Descripción"></td>
                            <td><input type="text" name="unit[]" value="Unidad" class="default-value" placeholder="Unidad"></td>
                            <td><input type="number" name="quantity[]" value="200" step="0.01" oninput="calculateRowTotal(this)"></td>
                            <td><input type="number" name="unitPrice[]" value="18000" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaPercent[]" value="19" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaAmount[]" value="684000" step="0.01" readonly></td>
                            <td><input type="number" name="total[]" value="4284000" step="0.01" readonly></td>
                            <td class="delete-cell">
                                <button type="button" class="delete-btn" onclick="deleteRow(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="item[]" value="3" class="default-value"></td>
                            <td><input type="text" name="description[]" value="Block hueco 15x20x40" class="default-value" placeholder="Descripción"></td>
                            <td><input type="text" name="unit[]" value="Unidad" class="default-value" placeholder="Unidad"></td>
                            <td><input type="number" name="quantity[]" value="5000" step="0.01" oninput="calculateRowTotal(this)"></td>
                            <td><input type="number" name="unitPrice[]" value="2200" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaPercent[]" value="19" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                            <td><input type="number" name="ivaAmount[]" value="2090000" step="0.01" readonly></td>
                            <td><input type="number" name="total[]" value="13090000" step="0.01" readonly></td>
                            <td class="delete-cell">
                                <button type="button" class="delete-btn" onclick="deleteRow(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <button type="button" class="add-row-btn" onclick="addNewRow()">Agregar Fila</button>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="deliveryLocation">LUGAR DE ENTREGA</label>
                        <input type="text" id="deliveryLocation" name="deliveryLocation" value="Obra principal - Proyecto Box Culvert">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="observations">OBSERVACIONES</label>
                        <textarea id="observations" name="observations" rows="3">VIATICOS OBRA DE ARTE -PROYECTO BOX CULVERT. Entregar en horario de 8:00 am a 4:00 pm. Presentar documentación completa para pago.</textarea>
                    </div>
                </div>
            </div>
            
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td>VALOR GRAVADO</td>
                        <td><input type="number" id="taxableValue" name="taxableValue" value="20350000" readonly></td>
                    </tr>
                    <tr>
                        <td>VALOR EXENTO</td>
                        <td><input type="number" id="exemptValue" name="exemptValue" value="0" readonly></td>
                    </tr>
                    <tr>
                        <td>VALOR IVA</td>
                        <td><input type="number" id="ivaValue" name="ivaValue" value="3866500" readonly></td>
                    </tr>
                    <tr>
                        <td>VALOR RETENCIONES</td>
                        <td><input type="number" id="retentionValue" name="retentionValue" value="0" readonly></td>
                    </tr>
                    <tr>
                        <td>VALOR TOTAL</td>
                        <td><input type="number" id="totalValue" name="totalValue" value="24216500" readonly></td>
                    </tr>
                </table>
            </div>
            
            <div class="form-section">
                <p>Para tramite de su factura se debe tener presente los siguientes requisitos:</p>
                <p>Se solicita hacer llegar la siguiente información: RUT, Cedula (Si Aplica), Cámara de Comercio (Si Aplica), Orden de Pedido y Remisión debidamente firmada por Jefe de Compras o quien autoriza, Almacenista o quien recibe.</p>
            </div>
            
            <div class="signatures">
                <div class="signature">
                    <p>ELABORADO POR:</p>
                    <p>MARIA FERNANDA BECERRA</p>
                    <p>AREA DE COMPRAS</p>
                </div>
                <div class="signature">
                    <p>REVISADO POR:</p>
                    <p>MARLY MONCADA CASTELLANOS</p>
                    <p>Gerente Administrativo</p>
                </div>
                <div class="signature">
                    <p>AUTORIZADO POR:</p>
                    <p>CARLOS ALFREDO CUTA MORALES</p>
                    <p>Representante Legal</p>
                </div>
            </div>
            
            <div class="footer">
                <p>Carrera 14 No. 17 - 62 Barrio Las Ferias - (311) 558 2485 - consorciocymparques2023@gmail.com</p>
                <p>COPIA EMPRESA / COPIA PROVEEDOR</p>
            </div>
            
            <div class="action-buttons">
                <button type="button" class="btn btn-primary" onclick="simulateSave()">Guardar</button>
                <button type="button" class="btn btn-success" onclick="printOrder()">Imprimir</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()">Limpiar</button>
            </div>
        </form>
    </div>

    <script>
        // Datos de empresas de ejemplo
        const empresasData = [
            {id: 1, nombre: "CONSORCIO CYM", nit: "900123456-7", abreviatura: "CYM"},
            {id: 2, nombre: "EMPRESA CONSTRUCTORA ANDINA", nit: "800987654-3", abreviatura: "ECA"},
            {id: 3, nombre: "INGENIERÍA Y PROYECTOS S.A.", nit: "890765432-1", abreviatura: "INPROSA"}
        ];
        
        // Datos de proveedores de ejemplo
        const proveedoresData = [
            {id: 1, nombre: "FERRETERÍA EL CONSTRUCTOR", nit: "890123456-7", oficina: "Sucursal Norte", direccion: "Carrera 15 # 85-24", telefono: "3109876543"},
            {id: 2, nombre: "CONSTRU MATERIALES S.A.S.", nit: "901234567-8", oficina: "Oficina Principal", direccion: "Calle 45 # 26-35", telefono: "3214567890"},
            {id: 3, nombre: "DISTRIBUIDORA DE ACEROS", nit: "912345678-9", oficina: "Centro", direccion: "Avenida 68 # 22-45", telefono: "3151234567"}
        ];
        
        // Mapa para búsqueda rápida
        const empresasMap = {};
        empresasData.forEach(empresa => {
            empresasMap[empresa.id] = empresa;
        });
        
        const proveedoresMap = {};
        proveedoresData.forEach(proveedor => {
            proveedoresMap[proveedor.id] = proveedor;
        });

        // Variables para controlar actualizaciones internas
        let internalUpdate = false;
        let internalUpdateCompany = false;

        // Establecer la fecha actual por defecto
        document.addEventListener('DOMContentLoaded', function() {
            initSelect2();
            calculateTotals();
            setupDefaultValueFields();
            setupCellHighlight();
        });
        
        function initSelect2() {
            // Configuración para empresas
            $('#empresa-select').select2({
                width: '100%',
                templateResult: o => o.text ? $(`<div class="empresa"><h1>${o.text}</h1></div>`) : o.text,
                templateSelection: o => o.text ? $(`<div class="empresa"><h1>${o.text}</h1></div>`) : o.text,
                placeholder: 'Seleccione una empresa',
                language: {
                    noResults: function() {
                        return "No se encontraron empresas";
                    }
                },
                escapeMarkup: markup => markup
            }).on('change', function() {
                if (internalUpdateCompany) return;
                
                const empresaId = $(this).val();
                if (empresaId) {
                    const empresa = empresasMap[empresaId];
                    if (empresa) {
                        internalUpdateCompany = true;
                        
                        // Actualizar campo de NIT
                        $('#nit-select').val(empresa.nit).trigger('change');
                        
                        // Actualizar campos ocultos
                        document.getElementById('empresa_id').value = empresa.id;
                        
                        // Cerrar dropdown
                        $('#empresa-select').select2('close');
                        $('#nit-select').select2('close');
                        
                        internalUpdateCompany = false;
                    }
                }
            });
            
            // Configuración para NIT
            $('#nit-select').select2({
                width: '100%',
                templateResult: o => o.text ? $(`<div class="nit"><p>${o.text}</p></div>`) : o.text,
                templateSelection: o => o.text ? $(`<div class="nit"><p>${o.text}</p></div>`) : o.text,
                placeholder: 'NIT de la empresa',
                language: {
                    noResults: function() {
                        return "No se encontraron NITs";
                    }
                },
                escapeMarkup: markup => markup
            }).on('change', function() {
                if (internalUpdateCompany) return;
                
                const nit = $(this).val();
                if (nit) {
                    // Buscar empresa por NIT
                    const empresa = empresasData.find(e => e.nit === nit);
                    if (empresa) {
                        internalUpdateCompany = true;
                        
                        // Actualizar campo de empresa
                        $('#empresa-select').val(empresa.id).trigger('change');
                        
                        // Actualizar campos ocultos
                        document.getElementById('empresa_id').value = empresa.id;
                        
                        // Cerrar dropdown
                        $('#empresa-select').select2('close');
                        $('#nit-select').select2('close');
                        
                        internalUpdateCompany = false;
                    }
                }
            });
            
            // Configuración para proveedor
            $('#provider').select2({
                placeholder: 'Seleccione un proveedor',
                allowClear: false,
                width: '100%'
            }).on('change', function() {
                if (internalUpdate) return;
                
                const proveedorId = $(this).val();
                if (proveedorId) {
                    const proveedor = proveedoresMap[proveedorId];
                    if (proveedor) {
                        updateProviderFields(proveedor);
                    }
                }
            });
            
            // Configuración para NIT de proveedor
            $('#providerNit').select2({
                placeholder: 'Seleccione un NIT o C.C.',
                allowClear: false,
                width: '100%'
            }).on('change', function() {
                if (internalUpdate) return;
                
                const nit = $(this).val();
                if (nit) {
                    // Buscar proveedor por NIT
                    const proveedor = proveedoresData.find(p => p.nit === nit);
                    if (proveedor) {
                        updateProviderFields(proveedor);
                    }
                }
            });
            
            // Configuración para oficina
            $('#office').select2({
                placeholder: 'Seleccione una oficina',
                allowClear: false,
                width: '100%'
            }).on('change', function() {
                if (internalUpdate) return;
                
                const oficina = $(this).val();
                if (oficina) {
                    // Buscar proveedor por oficina
                    const proveedor = proveedoresData.find(p => p.oficina === oficina);
                    if (proveedor) {
                        updateProviderFields(proveedor);
                    }
                }
            });
            
            // Configuración para dirección
            $('#address').select2({
                placeholder: 'Seleccione una dirección',
                allowClear: false,
                width: '100%'
            }).on('change', function() {
                if (internalUpdate) return;
                
                const direccion = $(this).val();
                if (direccion) {
                    // Buscar proveedor por dirección
                    const proveedor = proveedoresData.find(p => p.direccion === direccion);
                    if (proveedor) {
                        updateProviderFields(proveedor);
                    }
                }
            });
            
            // Configuración para teléfono
            $('#phone').select2({
                placeholder: 'Seleccione un teléfono',
                allowClear: false,
                width: '100%'
            }).on('change', function() {
                if (internalUpdate) return;
                
                const telefono = $(this).val();
                if (telefono) {
                    // Buscar proveedor por teléfono
                    const proveedor = proveedoresData.find(p => p.telefono === telefono);
                    if (proveedor) {
                        updateProviderFields(proveedor);
                    }
                }
            });
        }
        
        // Actualizar todos los campos de proveedor
        function updateProviderFields(proveedor) {
            internalUpdate = true;
            
            // Actualizar todos los campos
            $('#provider').val(proveedor.id).trigger('change');
            $('#providerNit').val(proveedor.nit).trigger('change');
            $('#office').val(proveedor.oficina).trigger('change');
            $('#address').val(proveedor.direccion).trigger('change');
            $('#phone').val(proveedor.telefono).trigger('change');
            
            // Actualizar campo oculto
            document.getElementById('proveedor_id').value = proveedor.id;
            
            // Cerrar todos los dropdowns
            $('#provider').select2('close');
            $('#providerNit').select2('close');
            $('#office').select2('close');
            $('#address').select2('close');
            $('#phone').select2('close');
            
            internalUpdate = false;
        }
        
        // Agregar nueva fila a la tabla de items
        function addNewRow() {
            const tbody = document.querySelector('#itemsTable tbody');
            const rowCount = tbody.children.length;
            
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="item[]" value="${rowCount + 1}" class="default-value"></td>
                <td><input type="text" name="description[]" value="" class="default-value" placeholder="Descripción"></td>
                <td><input type="text" name="unit[]" value="" class="default-value" placeholder="Unidad"></td>
                <td><input type="number" name="quantity[]" value="1" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="unitPrice[]" value="0.0" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                <td><input type="number" name="ivaPercent[]" value="0" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                <td><input type="number" name="ivaAmount[]" value="0.00" step="0.01" readonly></td>
                <td><input type="number" name="total[]" value="0" step="0.01" readonly></td>
                <td class="delete-cell">
                    <button type="button" class="delete-btn" onclick="deleteRow(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            
            setupDefaultValueFields();
            setupCellHighlight();
        }
        
        // Configurar eventos para campos con valores por defecto
        function setupDefaultValueFields() {
            const inputs = document.querySelectorAll('#itemsTable input.default-value');
            
            inputs.forEach(input => {
                if (!input.dataset.originalValue) {
                    input.dataset.originalValue = input.value || input.placeholder;
                }
                
                input.addEventListener('focus', function() {
                    if (this.value === "0.0" || this.value === "0.00" || this.value === "0" || this.value === "") {
                        this.value = "";
                        this.classList.remove('default-value');
                    }
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === "") {
                        this.value = this.dataset.originalValue;
                        this.classList.add('default-value');
                    }
                });
            });
        }
        
        // Configurar resaltado de celdas
        function setupCellHighlight() {
            const inputs = document.querySelectorAll('#itemsTable input');
            const buttons = document.querySelectorAll('#itemsTable .delete-btn');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    document.querySelectorAll('#itemsTable .selected-cell').forEach(cell => {
                        cell.classList.remove('selected-cell');
                    });
                    
                    const cell = this.closest('td');
                    cell.classList.add('selected-cell');
                });
                
                input.addEventListener('blur', function() {
                    const cell = this.closest('td');
                    setTimeout(() => {
                        cell.classList.remove('selected-cell');
                    }, 100);
                });
            });
            
            buttons.forEach(button => {
                button.addEventListener('focus', function() {
                    document.querySelectorAll('#itemsTable .selected-cell').forEach(cell => {
                        cell.classList.remove('selected-cell');
                    });
                    
                    const cell = this.closest('td');
                    cell.classList.add('selected-cell');
                });
                
                button.addEventListener('blur', function() {
                    const cell = this.closest('td');
                    setTimeout(() => {
                        cell.classList.remove('selected-cell');
                    }, 100);
                });
            });
        }
        
        // Eliminar fila
        function deleteRow(button) {
            const row = button.closest('tr');
            const rowIndex = Array.from(row.parentNode.children).indexOf(row);
            
            if (document.querySelectorAll('#itemsTable tbody tr').length <= 1) {
                alert("Debe haber al menos una fila de producto/servicio");
                return;
            }
            
            row.remove();
            renumberItems();
            calculateTotals();
        }
        
        // Renumerar los ítems después de eliminar una fila
        function renumberItems() {
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            rows.forEach((row, index) => {
                const itemInput = row.querySelector('input[name="item[]"]');
                itemInput.value = index + 1;
                
                if (itemInput.value === "") {
                    itemInput.value = itemInput.dataset.originalValue;
                    itemInput.classList.add('default-value');
                }
            });
        }
        
        // Calcular el total de una fila
        function calculateRowTotal(input) {
            const row = input.closest('tr');
            const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const unitPrice = parseFloat(row.querySelector('input[name="unitPrice[]"]').value) || 0;
            const ivaPercent = parseFloat(row.querySelector('input[name="ivaPercent[]"]').value) || 0;
            
            const subtotal = quantity * unitPrice;
            const ivaAmount = subtotal * (ivaPercent / 100);
            const total = subtotal + ivaAmount;
            
            row.querySelector('input[name="ivaAmount[]"]').value = ivaAmount.toFixed(2);
            row.querySelector('input[name="total[]"]').value = total.toFixed(2);
            
            calculateTotals();
        }
        
        // Calcular totales generales
        function calculateTotals() {
            let taxableValue = 0;
            let ivaValue = 0;
            let totalValue = 0;
            
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            
            rows.forEach(row => {
                const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
                const unitPrice = parseFloat(row.querySelector('input[name="unitPrice[]"]').value) || 0;
                const ivaAmount = parseFloat(row.querySelector('input[name="ivaAmount[]"]').value) || 0;
                
                const subtotal = quantity * unitPrice;
                const total = subtotal + ivaAmount;
                
                taxableValue += subtotal;
                ivaValue += ivaAmount;
                totalValue += total;
            });
            
            document.getElementById('taxableValue').value = taxableValue.toFixed(0);
            document.getElementById('ivaValue').value = ivaValue.toFixed(0);
            document.getElementById('totalValue').value = totalValue.toFixed(0);
        }
        
        // Simular guardado de la orden
        function simulateSave() {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>¡Orden guardada correctamente! Número: OP-000123</span>
                <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
            `;
            document.body.appendChild(notification);
            
            // Ocultar la notificación después de 5 segundos
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
        
        // Imprimir orden
        function printOrder() {
            window.print();
        }
        
        // Limpiar formulario
        function resetForm() {
            // Restablecer campos principales
            $('#empresa-select').val(null).trigger('change');
            $('#nit-select').val(null).trigger('change');
            $('#provider').val(null).trigger('change');
            $('#providerNit').val(null).trigger('change');
            $('#office').val(null).trigger('change');
            $('#address').val(null).trigger('change');
            $('#phone').val(null).trigger('change');
            
            // Restablecer campos ocultos
            document.getElementById('empresa_id').value = '';
            document.getElementById('proveedor_id').value = '';
            
            // Mantener la fecha actual
            const today = new Date();
            const formattedDate = today.toISOString().substr(0, 10);
            document.getElementById('date').value = formattedDate;
            
            // Restablecer campos adicionales
            document.getElementById('deliveryLocation').value = '';
            document.getElementById('observations').value = 'VIATICOS OBRA DE ARTE -PROYECTO BOX CULVERT';
            
            // Restablecer tabla de items
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.innerHTML = '';
            
            // Agregar fila inicial
            const initialRow = document.createElement('tr');
            initialRow.innerHTML = `
                <td><input type="text" name="item[]" value="1" class="default-value"></td>
                <td><input type="text" name="description[]" value="" class="default-value" placeholder="Descripción"></td>
                <td><input type="text" name="unit[]" value="" class="default-value" placeholder="Unidad"></td>
                <td><input type="number" name="quantity[]" value="1" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="unitPrice[]" value="0.0" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                <td><input type="number" name="ivaPercent[]" value="0" step="0.01" oninput="calculateRowTotal(this)" class="default-value"></td>
                <td><input type="number" name="ivaAmount[]" value="0.00" step="0.01" readonly></td>
                <td><input type="number" name="total[]" value="0" step="0.01" readonly></td>
                <td class="delete-cell">
                    <button type="button" class="delete-btn" onclick="deleteRow(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(initialRow);
            
            // Restablecer totales
            document.getElementById('taxableValue').value = '0';
            document.getElementById('exemptValue').value = '0';
            document.getElementById('ivaValue').value = '0';
            document.getElementById('retentionValue').value = '0';
            document.getElementById('totalValue').value = '0';
            
            // Configurar eventos para campos con valores por defecto
            setupDefaultValueFields();
            setupCellHighlight();
        }
    </script>
</body>
</html>

<?php include '../includes/partials/footer.php'; ?>
