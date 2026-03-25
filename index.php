<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atunera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1/dist/chartjs-plugin-annotation.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #F8FAFC; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        .card-pro { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #F1F5F9; }
        .text-primary { color: #0EA5E9; } .bg-primary { background-color: #0EA5E9; }
        .sidebar-item:hover, .sidebar-item.active { background-color: #F0F9FF; color: #0EA5E9; border-right: 4px solid #0EA5E9; }
        .screen-content { display: none; } .screen-content.active { display: block; }
        .chart-container { position: relative; height: 350px; width: 100%; }
        
        .loader-ai { border-top-color: #8B5CF6; animation: spinner 1.5s linear infinite; }
        @keyframes spinner { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        @media print {
            body { background-color: white !important; }
            aside, header, #predictionForm, .no-print { display: none !important; }
            .card-pro { border: none !important; box-shadow: none !important; margin-bottom: 20px; }
            .screen-content { display: block !important; }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden antialiased">

    <aside class="w-72 bg-white border-r border-slate-100 flex flex-col hidden md:flex h-full shrink-0">
        <div class="h-24 flex items-center px-8 border-b border-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center text-xl shadow-md"><i class="fas fa-fish"></i></div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Atunera
            </div>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="px-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3 mt-2">Analítica Gerencial</p>
            <a href="#" onclick="loadScreen('dashboard')" class="sidebar-item active flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-bold text-slate-600">
                <i class="fas fa-chart-line w-5 text-lg"></i> Dashboard
            </a>
            <a href="#" onclick="loadScreen('history')" class="sidebar-item flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-bold text-slate-600">
                <i class="fas fa-project-diagram w-5 text-lg"></i> Trazabilidad y Futuro
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <header class="bg-white border-b border-slate-100 h-24 px-10 flex justify-between items-center shrink-0">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight" id="screenTitle">Dashboard</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">SISTEMA TOMA DE DESICIONES</p>
            </div>
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-md">
                <i class="fas fa-file-pdf"></i> Generar Informe PDF
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50 h-full">
            <div class="max-w-[1500px] mx-auto space-y-8">
                
                <div id="dashboard-screen" class="screen-content active space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="card-pro p-6 border-l-4 border-emerald-500 flex justify-between items-center">
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Rendimiento Promedio</p><h3 class="text-3xl font-extrabold" id="kpiRendimiento">--%</h3></div>
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-xl"><i class="fas fa-check"></i></div>
                        </div>
                        <div class="card-pro p-6 border-l-4 border-rose-500 bg-rose-50 flex justify-between items-center">
                            <div><p class="text-xs font-bold text-rose-400 uppercase mb-1">Alertas Históricas</p><h3 class="text-3xl font-extrabold text-rose-600" id="kpiAlertas">0</h3></div>
                            <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center text-xl"><i class="fas fa-exclamation-triangle"></i></div>
                        </div>
                        <div class="card-pro p-6 border-l-4 border-primary flex justify-between items-center">
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Volumen Evaluado</p><h3 class="text-3xl font-extrabold" id="kpiVolumen">-- kg</h3></div>
                            <div class="w-12 h-12 bg-sky-50 text-primary rounded-full flex items-center justify-center text-xl"><i class="fas fa-box"></i></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                        <div class="xl:col-span-5 card-pro p-8 border-t-4 border-slate-800 no-print h-fit">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                                <i class="fas fa-microchip text-2xl text-slate-800"></i><h3 class="text-lg font-bold">Captura y Cierre de Lote</h3>
                            </div>
                            <form id="predictionForm" class="space-y-4">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">1. Variables Predictivas</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">COMPRA</label><input type="text" id="compra" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold" required></div>
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">LOTE</label><input type="text" id="lote" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold" required></div>
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">PARTIDA</label><input type="text" id="partida" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold" required></div>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-2"><label class="block text-[10px] font-bold text-slate-500 mb-1">PROVEEDOR</label><select id="proveedor" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold"></select></div>
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">ESPECIE</label><select id="especie" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold"></select></div>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">TALLA</label>
                                        <select id="talla" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold">
                                            <option value="1">-1.4</option><option value="2">1.4-1.8</option><option value="3" selected>1.8-3.4</option><option value="4">3.4-10</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">KG ROUND</label><input type="number" id="peso" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold" required></div>
                                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">TEMP (°C)</label><input type="number" id="temp" step="0.1" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold" required></div>
                                </div>

                                <div class="mt-6 border-t border-slate-100 pt-4">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">2. Resultados Reales (Upsert)</p>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1">TOTAL LOMO</label><input type="number" id="total_lomo" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold"></div>
                                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1">TOTAL MIGA</label><input type="number" id="total_miga" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold"></div>
                                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1">TOTAL PT</label><input type="number" id="total_pt" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold"></div>
                                    </div>
                                </div>
                                <button type="submit" id="btnSubmit" class="w-full mt-4 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition-colors"><i class="fas fa-save"></i> REGISTRAR LOTE</button>
                            </form>
                        </div>

                        <div class="xl:col-span-7 space-y-6">
                            <div class="card-pro p-8 bg-slate-50 flex justify-between items-center" id="resultPanel">
                                <div><p class="text-sm font-bold text-slate-500 uppercase">Proyección de Recuperación</p><h2 id="yieldResult" class="text-6xl font-black text-slate-300">--%</h2></div>
                                <div id="alertBox" class="hidden text-right"><div class="bg-white border-2 border-rose-500 px-6 py-4 rounded-xl shadow-lg"><p class="font-black text-rose-600"><i class="fas fa-exclamation-triangle"></i> ALERTA CRÍTICA</p></div></div>
                            </div>
                            <div class="card-pro p-8">
                                <h3 class="text-lg font-bold mb-4">Monitor en Vivo (Últimos Registros)</h3>
                                <div class="chart-container"><canvas id="liveChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="history-screen" class="screen-content space-y-8">
                    
                    <div class="card-pro p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-chart-area text-primary"></i> Rendimiento Histórico vs. Volumen de Procesamiento</h3>
                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded text-xs font-bold">Histórico Completo</span>
                        </div>
                        <div class="chart-container"><canvas id="traceabilityChart"></canvas></div>
                    </div>

                    <div class="card-pro p-8">
                        <h3 class="text-xl font-bold mb-6"><i class="fas fa-table text-primary"></i> Matriz de Trazabilidad</h3>
                        <div class="overflow-x-auto h-80 border border-slate-100 rounded-lg">
                            <table class="w-full text-left border-collapse min-w-[1000px]">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase sticky top-0 shadow-sm z-10">
                                    <tr>
                                        <th class="py-3 px-4">Fecha</th><th class="py-3 px-4">Lote / Compra</th><th class="py-3 px-4 text-right">Kg Round</th>
                                        <th class="py-3 px-4 text-center">Datos Reales (Cierre)</th><th class="py-3 px-4 text-right">Rendimiento</th><th class="py-3 px-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody" class="text-sm"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-pro p-8 border-t-4 border-indigo-500 bg-gradient-to-br from-white to-indigo-50/30">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-xl font-black text-indigo-900"><i class="fas fa-brain text-indigo-500"></i> Modelo predictivo</h3>
                            <button id="btnRunForecast" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-md flex items-center gap-2">
                                <i class="fas fa-magic"></i> Ejecutar Modelo Predictivo
                            </button>
                        </div>
                        <p class="text-sm text-slate-600 mb-6 font-medium max-w-3xl">Al ejecutar el algoritmo, el sistema analiza matemáticamente la tendencia histórica del rendimiento utilizando regresión lineal por mínimos cuadrados y proyecta el comportamiento esperado para los próximos 5 lotes de producción.</p>
                        
                        <div id="forecastWrapper" class="hidden">
                            <div class="chart-container border border-indigo-100 rounded-xl bg-white p-4" style="height: 400px;">
                                <canvas id="forecastingChart"></canvas>
                            </div>
                        </div>

                        <div id="forecastLoader" class="hidden flex flex-col items-center justify-center py-12">
                            <div class="w-12 h-12 border-4 border-slate-200 rounded-full loader-ai mb-4"></div>
                            <p class="text-sm font-bold text-indigo-800">Procesando vectores históricos y calculando proyecciones...</p>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        let globalHistoryData = []; 
        let liveChart = null;
        let traceabilityChart = null;
        let forecastingChart = null;

        function loadScreen(screenId) {
            document.querySelectorAll('.screen-content').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
            document.getElementById(`${screenId}-screen`).classList.add('active');
            
            const activeItem = Array.from(document.querySelectorAll('.sidebar-item')).find(i => i.getAttribute('onclick').includes(screenId));
            if(activeItem) {
                activeItem.classList.add('active');
                document.getElementById('screenTitle').innerText = activeItem.innerText.trim();
            }

            if (screenId === 'dashboard') loadCatalogs();
            if (screenId === 'history') fetchFullHistory();
        }

        async function loadCatalogs() {
            try {
                const res = await fetch('api/leer.php');
                const result = await res.json();
                if (result.status === 'success') {
                    populateSelect('especie', result.data.species);
                    populateSelect('proveedor', result.data.providers);
                }
            } catch (err) {}
        }
        function populateSelect(selectId, data) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';
            data.forEach(item => { select.innerHTML += `<option value="${item.id}">${item.nombre}</option>`; });
        }

        function initLiveChart() {
            const ctx = document.getElementById('liveChart').getContext('2d');
            liveChart = new Chart(ctx, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Rendimiento %', data: [], borderColor: '#0EA5E9', borderWidth: 3, tension: 0.3 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, annotation: { annotations: { line1: { type: 'line', yMin: 44, yMax: 44, borderColor: '#F43F5E', borderDash: [5, 5] } } } }, scales: { y: { min: 40, max: 50 } } }
            });
        }

       
        function initTraceabilityChart(historyData) {
            const ctx = document.getElementById('traceabilityChart').getContext('2d');
            const sortedData = [...historyData].reverse();
            const labels = sortedData.map(d => d.lote);
            const yields = sortedData.map(d => (d.rendimiento_esperado * 100).toFixed(2));
            const kgs = sortedData.map(d => d.peso_neto);

            if (traceabilityChart) { traceabilityChart.destroy(); }

            traceabilityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { type: 'line', label: 'Rendimiento (%)', data: yields, borderColor: '#10B981', borderWidth: 3, yAxisID: 'y', tension: 0.3, zIndex: 10 },
                        { type: 'bar', label: 'Kg Round (Volumen)', data: kgs, backgroundColor: '#E2E8F0', borderRadius: 4, yAxisID: 'y1', order: 2 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { type: 'linear', display: true, position: 'left', min: 40, max: 50 },
                        y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });
        }

      
        document.getElementById('btnRunForecast').addEventListener('click', function() {
            const btn = this;
            const loader = document.getElementById('forecastLoader');
            const wrapper = document.getElementById('forecastWrapper');
            
            
            btn.classList.add('hidden');
            wrapper.classList.add('hidden');
            loader.classList.remove('hidden');

            setTimeout(() => {
                loader.classList.add('hidden');
                wrapper.classList.remove('hidden');
                generateForecastChart(globalHistoryData);
                
                
                btn.classList.remove('hidden');
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Recalcular Proyección';
                btn.classList.replace('bg-indigo-600', 'bg-slate-800');
                btn.classList.replace('hover:bg-indigo-700', 'hover:bg-slate-900');
            }, 1200);
        });

        // ALGORITMO PREDICTIVO MATEMÁTICO (FORECASTING)
        function generateForecastChart(historyData) {
            const ctx = document.getElementById('forecastingChart').getContext('2d');
            
            const sortedData = [...historyData].reverse();
            let xValues = []; let yValues = []; let labels = [];

            sortedData.forEach((row, index) => {
                xValues.push(index);
                yValues.push(parseFloat((row.rendimiento_esperado * 100).toFixed(2)));
                labels.push(row.lote);
            });

           
            let n = yValues.length;
            if(n < 2) return; 

            let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
            for (let i = 0; i < n; i++) { sumX += xValues[i]; sumY += yValues[i]; sumXY += (xValues[i] * yValues[i]); sumXX += (xValues[i] * xValues[i]); }
            
            let m = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
            let b = (sumY - m * sumX) / n;

            let trendLine = xValues.map(x => (m * x + b));
            let futureLabels = [...labels];
            let futureTrend = Array(n).fill(null); 
            futureTrend[n-1] = trendLine[n-1]; 

            for (let i = 1; i <= 5; i++) {
                futureLabels.push(`Lote F+${i}`);
                trendLine.push(null); 
                futureTrend.push((m * ((n - 1) + i)) + b);
                yValues.push(null); 
            }

            if (forecastingChart) forecastingChart.destroy();

            forecastingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: futureLabels,
                    datasets: [
                        { label: 'Rendimiento Histórico Real', data: yValues, borderColor: '#334155', backgroundColor: '#334155', borderWidth: 2, tension: 0.3, pointRadius: 4 },
                        { label: 'Tendencia (Ajuste)', data: trendLine, borderColor: '#0EA5E9', borderWidth: 2, borderDash: [5, 5], pointRadius: 0 },
                        { label: 'Pronóstico (Futuro)', data: futureTrend, borderColor: '#8B5CF6', backgroundColor: 'rgba(139, 92, 246, 0.15)', borderWidth: 4, fill: true, tension: 0.1, pointRadius: 5, pointBackgroundColor: '#8B5CF6' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { tooltip: { mode: 'index', intersect: false }, annotation: { annotations: { line1: { type: 'line', yMin: 44, yMax: 44, borderColor: '#F43F5E', borderWidth: 2, borderDash: [3, 3] }, line2: { type: 'line', scaleID: 'x', value: labels[n-1], borderColor: '#94A3B8', borderWidth: 2 } } } },
                    scales: { y: { title: { display: true, text: 'Rendimiento (%)' } } }
                }
            });
        }

        document.getElementById('predictionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            const payload = {
                compra: document.getElementById('compra').value, lote: document.getElementById('lote').value, partida: document.getElementById('partida').value,
                especie: parseInt(document.getElementById('especie').value), talla: parseInt(document.getElementById('talla').value), proveedor: parseInt(document.getElementById('proveedor').value),
                peso: parseFloat(document.getElementById('peso').value), temperatura: parseFloat(document.getElementById('temp').value),
                total_lomo: document.getElementById('total_lomo').value ? parseFloat(document.getElementById('total_lomo').value) : null,
                total_miga: document.getElementById('total_miga').value ? parseFloat(document.getElementById('total_miga').value) : null,
                total_pt: document.getElementById('total_pt').value ? parseFloat(document.getElementById('total_pt').value) : null,
                proceso: "COCIDO"
            };

            try {
                const response = await fetch('api/predictivo.php', { method: 'POST', body: JSON.stringify(payload) });
                const result = await response.json();
                
                if (result.status === 'success') {
                    document.getElementById('yieldResult').innerText = result.data.percent + '%';
                    document.getElementById('yieldResult').className = result.data.alert ? "text-6xl font-black text-rose-600" : "text-6xl font-black text-emerald-500";
                    document.getElementById('alertBox').classList.toggle('hidden', !result.data.alert);
                    
                    liveChart.data.labels.push(payload.lote);
                    liveChart.data.datasets[0].data.push(parseFloat(result.data.percent));
                    if(liveChart.data.labels.length > 5) { liveChart.data.labels.shift(); liveChart.data.datasets[0].data.shift(); }
                    liveChart.update();

                    btn.innerHTML = '<i class="fas fa-check"></i> Lote Guardado';
                    setTimeout(() => { btn.innerHTML = '<i class="fas fa-play"></i> REGISTRAR LOTE'; }, 2000);
                }
            } catch (err) {}
        });

        async function fetchFullHistory() {
            const tableBody = document.getElementById('tableBody');
            try {
                const res = await fetch('api/leer.php');
                const result = await res.json();
                
                if (result.status === 'success' && result.data.history.length > 0) {
                    tableBody.innerHTML = '';
                    let sumYields = 0, alertasCount = 0, volTotal = 0;
                    
                    globalHistoryData = result.data.history; 

                    result.data.history.forEach(row => {
                        let pt = row.total_pt ? parseFloat(row.total_pt).toLocaleString() : '--';
                        let lomo = row.total_lomo ? parseFloat(row.total_lomo).toLocaleString() : '--';
                        
                        sumYields += parseFloat(row.rendimiento_esperado);
                        if(row.alerta_status == 1) alertasCount++;
                        volTotal += parseFloat(row.peso_neto);

                        const tr = document.createElement('tr');
                        tr.className = "border-b border-slate-100";
                        tr.innerHTML = `
                            <td class="py-2 px-4 text-slate-500 font-medium">${row.fecha_registro.split(' ')[0]}</td>
                            <td class="py-2 px-4 font-bold text-slate-800">${row.lote} <br><span class="text-xs text-blue-500">C: ${row.compra}</span></td>
                            <td class="py-2 px-4 text-right font-medium">${parseFloat(row.peso_neto).toLocaleString()}</td>
                            <td class="py-2 px-4 text-center text-xs text-slate-600"><span class="font-bold text-slate-800">PT: ${pt}</span><br>L: ${lomo}</td>
                            <td class="py-2 px-4 text-right font-black" style="color:${row.alerta_status == 1 ? '#E11D48' : '#10B981'}">${(row.rendimiento_esperado * 100).toFixed(1)}%</td>
                            <td class="py-2 px-4 text-center"><span class="px-2 py-1 rounded text-[10px] font-bold uppercase ${row.alerta_status == 1 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'}">${row.alerta_status == 1 ? 'Alerta' : 'Óptimo'}</span></td>
                        `;
                        tableBody.appendChild(tr);
                    });

                    document.getElementById('kpiRendimiento').innerText = ((sumYields / result.data.history.length) * 100).toFixed(1) + '%';
                    document.getElementById('kpiAlertas').innerText = alertasCount;
                    document.getElementById('kpiVolumen').innerText = volTotal.toLocaleString() + ' kg';

                    initTraceabilityChart(globalHistoryData); // Dibuja la histórica
                }
            } catch (err) {}
        }

        window.onload = () => { initLiveChart(); loadScreen('dashboard'); };
    </script>
</body>
</html>