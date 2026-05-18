const fs = require('fs');

let html = fs.readFileSync('erp-business-management.html', 'utf8');

// 1. Update typePrefix
html = html.replace(
  "const typePrefix = id === 'receivables-menu' ? 'ar-' : (id === 'purchases-menu' ? 'po-' : (id === 'payables-menu' ? 'ap-' : ''));",
  "const typePrefix = id === 'receivables-menu' ? 'ar-' : (id === 'purchases-menu' ? 'po-' : (id === 'payables-menu' ? 'ap-' : (id === 'inventory-menu' ? 'in-' : '')));"
);

// 2. Add global click interceptor
html = html.replace(
  "      if (type && type.startsWith('ap-')) {\n        openPayablesDemo(action, type);\n        return;\n      }",
  "      if (type && type.startsWith('ap-')) {\n        openPayablesDemo(action, type);\n        return;\n      }\n      \n      if (type && type.startsWith('in-')) {\n        openInventoryDemo(action, type);\n        return;\n      }"
);

// 3. Inject the new block right before function doLogout()
const block = `// --- INVENTORY MODULE FULL DEMO ---
function initInventoryDemoData() {
  let d = JSON.parse(localStorage.getItem('inventoryDemoData'));
  if(!d) {
    d = {
      stockItems: [
        { code: 'ITM-001', desc: 'Honda Click 125i', class: 'Motor Unit', brand: 'Honda', uom: 'Unit', cost: 65000, price: 81400, qty: 15, reorder: 5, status: 'Active' },
        { code: 'ITM-002', desc: 'Honda ADV 160', class: 'Motor Unit', brand: 'Honda', uom: 'Unit', cost: 130000, price: 166900, qty: 8, reorder: 3, status: 'Active' },
        { code: 'ITM-003', desc: 'Yamaha Mio Gear', class: 'Motor Unit', brand: 'Yamaha', uom: 'Unit', cost: 60000, price: 77400, qty: 20, reorder: 5, status: 'Active' },
        { code: 'ITM-004', desc: 'Helmet Standard', class: 'Accessories', brand: 'Generic', uom: 'Pcs', cost: 800, price: 1500, qty: 100, reorder: 20, status: 'Active' },
        { code: 'ITM-005', desc: 'Engine Oil 10W-40', class: 'Consumables', brand: 'Generic', uom: 'Liters', cost: 200, price: 350, qty: 250, reorder: 50, status: 'Active' },
        { code: 'ITM-006', desc: 'Spark Plug', class: 'Spare Parts', brand: 'NGK', uom: 'Pcs', cost: 150, price: 250, qty: 500, reorder: 100, status: 'Active' }
      ],
      nonStockItems: [
        { code: 'NS-001', desc: 'Delivery Charge', cat: 'Service', account: '4001', cost: 500, status: 'Active' },
        { code: 'NS-002', desc: 'Warranty Labor', cat: 'Labor', account: '6001', cost: 0, status: 'Active' }
      ],
      warehouses: [
        { id: 'WH-MNL', name: 'Manila Main Warehouse', branch: 'Manila Main', loc: 'Metro Manila', manager: 'Admin', cap: 500, util: 60, status: 'Active' },
        { id: 'WH-QC', name: 'Quezon City Warehouse', branch: 'Quezon City North', loc: 'Quezon City', manager: 'Admin', cap: 300, util: 45, status: 'Active' },
        { id: 'WH-CEB', name: 'Cebu Warehouse', branch: 'Cebu Branch', loc: 'Cebu', manager: 'Admin', cap: 400, util: 20, status: 'Active' }
      ],
      warehouseBuildings: [
        { id: 'BLDG-A', name: 'Main Assembly Hall', wh: 'WH-MNL', loc: 'Compound A', area: '1000 sqm', zones: 4, status: 'Active' }
      ],
      receipts: [
        { no: 'REC-2001', date: '2026-05-10', wh: 'WH-MNL', source: 'Honda Philippines', item: 'Honda Click 125i', qty: 5, status: 'Released' }
      ],
      issues: [
        { no: 'ISS-3001', date: '2026-05-12', wh: 'WH-QC', to: 'Sales Dept', item: 'Helmet Standard', qty: 2, reason: 'Demo Unit', status: 'Released' }
      ],
      adjustments: [],
      transfers: [
        { no: 'TRF-4001', date: '2026-05-14', from: 'WH-MNL', to: 'WH-QC', item: 'Engine Oil 10W-40', qty: 50, status: 'In Transit' }
      ],
      kitAssemblies: [],
      physicalCounts: []
    };
    localStorage.setItem('inventoryDemoData', JSON.stringify(d));
  }
  return d;
}

function updateInventoryLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    }
  }
}

function openInventoryDemo(action, type) {
  const data = initInventoryDemoData();
  
  if(type === 'in-shortcut') {
    if(action === 'New Adjustment') openInNewAdjustment(data);
    else if(action === 'New Transfer') openInNewTransfer(data);
    else if(action === 'New Kit Assembly') openInNewKitAssembly(data);
    else if(action === 'New Stock Item') openInNewStockItem(data);
  }
  else if(type === 'in-transaction') {
    if(action === 'Receipts') openInReceipts(data);
    else if(action === 'Issues') openInIssues(data);
    else if(action === 'Adjustments') openInAdjustments(data);
    else if(action === 'Transfers') openInTransfers(data);
    else if(action === 'Kit Assembly') openInKitAssembly(data);
  }
  else if(type === 'in-profile') {
    if(action === 'Stock Items') openInStockItems(data);
    else if(action === 'Item Warehouse Details') openInWarehouseDetails(data);
    else if(action === 'Non-Stock Items') openInNonStockItems(data);
    else if(action === 'Warehouses') openInWarehouses(data);
    else if(action === 'Warehouse Buildings') openInBuildings(data);
  }
  else if(type === 'in-physical') {
    if(action === 'Prepare Physical Count') openInPrepareCount(data);
    else if(action === 'Physical Inventory Count') openInPhysicalCount(data);
  }
  else if(type === 'in-process') {
    if(action === 'Release IN Documents') openInProcessReleaseDocs(data);
    else if(action === 'Close Financial Periods') openInProcessClosePeriod(data);
  }
  else if(type === 'in-inquiry') {
    openInInquiryModal(action, data);
  }
  else if(type === 'in-report') {
    openInReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openInNewAdjustment(data) {
  const whOpts = data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  const itemOpts = data.stockItems.map(i=>\`<option value="\${i.code}">\${i.code} - \${i.desc}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Adjustment Date</label><input type="date" id="na-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Warehouse</label><select id="na-wh">\${whOpts}</select></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="na-item">\${itemOpts}</select></div>
      <div class="demo-form-row"><label>Lot / Serial No.</label><input type="text" id="na-lot" placeholder="Optional"></div>
      <div class="demo-form-row"><label>Adjustment Type</label><select id="na-type"><option>Increase</option><option>Decrease</option><option>Reclassification</option><option>Damage</option><option>Correction</option></select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="na-qty" value="1"></div>
      <div class="demo-form-row"><label>Reason</label><input type="text" id="na-rsn"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="na-ref"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="na-rem"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitInAdjustment(this)">Submit Adjustment</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>\`;
  openCenterModal('New Adjustment', html, actions);
}

function submitInAdjustment(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const itemCode = document.getElementById('na-item').value;
    const type = document.getElementById('na-type').value;
    const qty = parseInt(document.getElementById('na-qty').value)||0;
    
    // Create adjustment
    data.adjustments.unshift({
      no: 'ADJ-' + String(data.adjustments.length + 1001),
      date: document.getElementById('na-date').value,
      wh: document.getElementById('na-wh').value,
      item: itemCode,
      lot: document.getElementById('na-lot').value,
      type, qty,
      reason: document.getElementById('na-rsn').value,
      status: 'Released'
    });
    
    // Update stock quantity directly because we are releasing it instantly in this shortcut
    let item = data.stockItems.find(i=>i.code===itemCode);
    if(item) {
      if(type==='Increase' || type==='Correction') item.qty += qty;
      else if(type==='Decrease' || type==='Damage') item.qty = Math.max(0, item.qty - qty);
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo inventory adjustment submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openInNewTransfer(data) {
  const whOpts = data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  const itemOpts = data.stockItems.map(i=>\`<option value="\${i.code}">\${i.code} - \${i.desc}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Transfer Date</label><input type="date" id="nt-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>From Warehouse</label><select id="nt-from">\${whOpts}</select></div>
      <div class="demo-form-row"><label>To Warehouse</label><select id="nt-to">\${whOpts}</select></div>
      <div class="demo-form-row"><label>Item</label><select id="nt-item">\${itemOpts}</select></div>
      <div class="demo-form-row"><label>Lot / Serial No.</label><input type="text" id="nt-lot"></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="nt-qty" value="1"></div>
      <div class="demo-form-row"><label>Transfer Reason</label><input type="text" id="nt-rsn" value="Stock Balancing"></div>
      <div class="demo-form-row"><label>Requested By</label><input type="text" id="nt-req" value="System Admin"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="nt-rem"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitInTransfer(this)">Submit Transfer</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>\`;
  openCenterModal('New Transfer', html, actions);
}

function submitInTransfer(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const itemCode = document.getElementById('nt-item').value;
    const qty = parseInt(document.getElementById('nt-qty').value)||0;
    
    data.transfers.unshift({
      no: 'TRF-' + String(data.transfers.length + 4001),
      date: document.getElementById('nt-date').value,
      from: document.getElementById('nt-from').value,
      to: document.getElementById('nt-to').value,
      item: itemCode,
      lot: document.getElementById('nt-lot').value,
      qty,
      status: 'In Transit' // deduct source immediately
    });
    
    let item = data.stockItems.find(i=>i.code===itemCode);
    if(item) {
      item.qty = Math.max(0, item.qty - qty); // deduct from global available (for demo simplicity)
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo inventory transfer submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openInNewKitAssembly(data) {
  const whOpts = data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Assembly Date</label><input type="date" id="nk-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Warehouse</label><select id="nk-wh">\${whOpts}</select></div>
      <div class="demo-form-row"><label>Kit Item</label><input type="text" id="nk-item" value="Motorcycle Starter Kit" readonly style="background:#f5f5f5"></div>
      <div class="demo-form-row"><label>Quantity to Assemble</label><input type="number" id="nk-qty" value="5"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="nk-ref"></div>
      <div class="demo-form-row"><label>Remarks</label><input type="text" id="nk-rem"></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Component Item</th><th>Qty per Kit</th><th>Total Required</th><th>Available</th></tr></thead>
        <tbody>
          <tr><td>Helmet Standard</td><td class="mono" style="text-align:center">1</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">100</td></tr>
          <tr><td>Engine Oil 10W-40</td><td class="mono" style="text-align:center">2</td><td class="mono" style="text-align:center">10</td><td class="mono" style="text-align:center">250</td></tr>
          <tr><td>Spark Plug</td><td class="mono" style="text-align:center">1</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">500</td></tr>
        </tbody>
      </table>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitInKitAssembly(this)">Assemble Kit</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>\`;
  openCenterModal('New Kit Assembly', html, actions, {width: '800px'});
}

function submitInKitAssembly(btn) {
  setButtonLoading(btn, 'Assembling...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const qty = parseInt(document.getElementById('nk-qty').value)||0;
    
    data.kitAssemblies.unshift({
      no: 'KIT-' + String(data.kitAssemblies.length + 101),
      date: document.getElementById('nk-date').value,
      wh: document.getElementById('nk-wh').value,
      item: 'Motorcycle Starter Kit',
      qty,
      comps: \`Helmet(\${1*qty}), Oil(\${2*qty}), Plug(\${1*qty})\`,
      status: 'Completed'
    });
    
    // Deduct components
    let helm = data.stockItems.find(i=>i.code==='ITM-004'); if(helm) helm.qty = Math.max(0, helm.qty - qty);
    let oil = data.stockItems.find(i=>i.code==='ITM-005'); if(oil) oil.qty = Math.max(0, oil.qty - (qty*2));
    let plug = data.stockItems.find(i=>i.code==='ITM-006'); if(plug) plug.qty = Math.max(0, plug.qty - qty);
    
    // Add Kit as stock item if not exist
    let kit = data.stockItems.find(i=>i.code==='ITM-KIT1');
    if(!kit) {
      data.stockItems.push({ code: 'ITM-KIT1', desc: 'Motorcycle Starter Kit', class: 'Accessories', brand: 'NEXII', uom: 'Kit', cost: 1350, price: 2000, qty: qty, reorder: 5, status: 'Active' });
    } else {
      kit.qty += qty;
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo kit assembly completed successfully.');
    closeCenterModal();
  }, 800);
}

function openInNewStockItem(data) {
  const whOpts = data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Item Code</label><input type="text" id="ni-code" value="ITM-00X"></div>
      <div class="demo-form-row"><label>Item Description</label><input type="text" id="ni-desc"></div>
      <div class="demo-form-row"><label>Item Class</label><select id="ni-class"><option>Motor Unit</option><option>Spare Parts</option><option>Accessories</option><option>Consumables</option></select></div>
      <div class="demo-form-row"><label>Brand</label><input type="text" id="ni-brand"></div>
      <div class="demo-form-row"><label>Unit of Measure</label><select id="ni-uom"><option>Unit</option><option>Pcs</option><option>Liters</option><option>Box</option></select></div>
      <div class="demo-form-row"><label>Standard Cost</label><input type="number" id="ni-cost" value="0"></div>
      <div class="demo-form-row"><label>Sales Price</label><input type="number" id="ni-price" value="0"></div>
      <div class="demo-form-row"><label>Reorder Point</label><input type="number" id="ni-reorder" value="5"></div>
      <div class="demo-form-row"><label>Warehouse</label><select>\${whOpts}</select></div>
      <div class="demo-form-row"><label>Initial Quantity</label><input type="number" id="ni-qty" value="0"></div>
      <div class="demo-form-row"><label>Lot/Serial Tracking</label><select><option>Not Tracked</option><option>Lot Tracked</option><option>Serial Tracked</option></select></div>
      <div class="demo-form-row"><label>Status</label><select id="ni-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitInStockItem(this)">Save Stock Item</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>\`;
  openCenterModal('New Stock Item', html, actions);
}

function submitInStockItem(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    data.stockItems.push({
      code: document.getElementById('ni-code').value || 'ITM-NEW',
      desc: document.getElementById('ni-desc').value || 'New Demo Item',
      class: document.getElementById('ni-class').value,
      brand: document.getElementById('ni-brand').value || 'Generic',
      uom: document.getElementById('ni-uom').value,
      cost: parseFloat(document.getElementById('ni-cost').value)||0,
      price: parseFloat(document.getElementById('ni-price').value)||0,
      qty: parseInt(document.getElementById('ni-qty').value)||0,
      reorder: parseInt(document.getElementById('ni-reorder').value)||0,
      status: document.getElementById('ni-status').value
    });
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo stock item created successfully.');
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function openInReceipts(data) {
  const rows = data.receipts.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.wh}</td><td>\${r.source}</td>
    <td>\${r.item}</td><td class="dim">-</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Receipt...')">View Receipt</button>
      \${r.status==='Pending' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('receipts','no','\${r.no}',{status:'Released'});showToast('Receipt Released.');openInReceipts(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release Receipt</button>\` : ''}
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing...')">Print</button>
    </td>
  </tr>\`).join('') || '<tr><td colspan="9" style="text-align:center;color:var(--text-tertiary)">No receipts</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Receipt No.</th><th>Date</th><th>Warehouse</th><th>Source</th><th>Item</th><th>Lot/Serial No.</th><th style="text-align:center">Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-receipts.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).receipts)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Inventory Receipts', html, actions, {width: 'min(1100px, calc(100vw - 48px))'});
}

function openInIssues(data) {
  const rows = data.issues.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.wh}</td><td>\${r.to}</td>
    <td>\${r.item}</td><td class="dim">-</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${r.reason}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Issue...')">View Issue</button>
      \${r.status==='Pending' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('issues','no','\${r.no}',{status:'Released'});showToast('Issue Released.');openInIssues(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release Issue</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No issues</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Issue No.</th><th>Date</th><th>Warehouse</th><th>Issued To</th><th>Item</th><th>Lot/Serial No.</th><th style="text-align:center">Qty</th><th>Reason</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-issues.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).issues)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Inventory Issues', html, actions, {width: 'min(1150px, calc(100vw - 48px))'});
}

function openInAdjustments(data) {
  const rows = data.adjustments.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.wh}</td><td>\${r.item}</td>
    <td class="dim">\${r.lot||'-'}</td><td>\${r.type}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${r.reason}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Adj...')">View</button>
      \${r.status==='Pending' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('adjustments','no','\${r.no}',{status:'Released'});showToast('Adj Released.');openInAdjustments(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release</button>\` : ''}
      \${r.status==='Released' ? \`<button class="btn btn-sm" style="font-size:10px;padding:2px 6px;color:red" onclick="updateInventoryLocalData('adjustments','no','\${r.no}',{status:'Reversed'});showToast('Reversed.');openInAdjustments(JSON.parse(localStorage.getItem('inventoryDemoData')))">Reverse</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No adjustments</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Adj No.</th><th>Date</th><th>Warehouse</th><th>Item</th><th>Lot/Serial</th><th>Type</th><th style="text-align:center">Qty</th><th>Reason</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-adjustments.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).adjustments)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Inventory Adjustments', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

function openInTransfers(data) {
  const rows = data.transfers.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.from}</td><td>\${r.to}</td>
    <td>\${r.item}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Transfer...')">View Transfer</button>
      \${r.status==='Pending' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('transfers','no','\${r.no}',{status:'In Transit'});showToast('Shipped.');openInTransfers(JSON.parse(localStorage.getItem('inventoryDemoData')))">Ship Transfer</button>\` : ''}
      \${r.status==='In Transit' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="receiveInTransfer('\${r.no}')">Receive Transfer</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No transfers</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Transfer No.</th><th>Date</th><th>From Whse</th><th>To Whse</th><th>Item</th><th style="text-align:center">Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-transfers.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).transfers)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Inventory Transfers', html, actions, {width: '1000px'});
}

function receiveInTransfer(no) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  let trf = data.transfers.find(t=>t.no===no);
  if(trf) {
    trf.status = 'Received';
    let item = data.stockItems.find(i=>i.code===trf.item);
    if(item) item.qty += trf.qty; // add to dest (for demo global pool)
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Transfer received successfully.');
    openInTransfers(data);
  }
}

function openInKitAssembly(data) {
  const rows = data.kitAssemblies.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.wh}</td><td>\${r.item}</td>
    <td class="mono" style="text-align:center">\${r.qty}</td><td class="dim" style="font-size:11px">\${r.comps}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Assembly...')">View</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing Slip...')">Print</button>
    </td>
  </tr>\`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No kit assemblies</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Assembly No.</th><th>Date</th><th>Warehouse</th><th>Kit Item</th><th style="text-align:center">Qty</th><th>Components</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-assemblies.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).kitAssemblies)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Kit Assembly', html, actions, {width: '1050px'});
}

// ======================= PROFILES =======================
function openInStockItems(data) {
  const rows = data.stockItems.map(i=>\`<tr>
    <td class="mono">\${i.code}</td><td><strong>\${i.desc}</strong></td><td>\${i.class}</td><td>\${i.brand}</td>
    <td class="mono">\${i.uom}</td><td class="amt">&#8369;\${fmt(i.cost)}</td><td class="amt">&#8369;\${fmt(i.price)}</td>
    <td class="mono" style="text-align:center">\${i.qty}</td><td>\${badge(i.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="openStockItemProfile('\${i.code}')">View Profile</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Edit item...')">Edit</button>
    </td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Item Code</th><th>Description</th><th>Item Class</th><th>Brand</th><th>UOM</th><th>Std Cost</th><th>Sales Price</th><th style="text-align:center">Total Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('stock-items.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).stockItems)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Stock Items', html, actions, {width: 'min(1250px, calc(100vw - 48px))'});
}

function openStockItemProfile(code) {
  const data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  const i = data.stockItems.find(x=>x.code===code);
  if(!i) return;
  
  const val = i.qty * i.cost;
  
  const html = \`
    <div class="sp-section">
      <div class="sp-section-label">Item Information</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div>
          <div class="sp-row"><span class="sp-key">Item Code</span><span class="sp-val mono">\${i.code}</span></div>
          <div class="sp-row"><span class="sp-key">Description</span><span class="sp-val"><strong>\${i.desc}</strong></span></div>
          <div class="sp-row"><span class="sp-key">Item Class</span><span class="sp-val">\${i.class}</span></div>
          <div class="sp-row"><span class="sp-key">Brand</span><span class="sp-val">\${i.brand}</span></div>
          <div class="sp-row"><span class="sp-key">UOM</span><span class="sp-val mono">\${i.uom}</span></div>
        </div>
        <div>
          <div class="sp-row"><span class="sp-key">Standard Cost</span><span class="sp-val amt">&#8369;\${fmt(i.cost)}</span></div>
          <div class="sp-row"><span class="sp-key">Sales Price</span><span class="sp-val amt">&#8369;\${fmt(i.price)}</span></div>
          <div class="sp-row"><span class="sp-key">Total Qty</span><span class="sp-val mono">\${i.qty}</span></div>
          <div class="sp-row"><span class="sp-key">Reorder Point</span><span class="sp-val mono">\${i.reorder}</span></div>
          <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">\${badge(i.status)}</span></div>
        </div>
      </div>
      <div style="margin-top:15px;background:#f9f9f9;padding:10px;border-radius:6px;border:1px solid var(--border);text-align:center">
        <div class="dim" style="font-size:11px;text-transform:uppercase">Valuation Summary</div>
        <div class="amt" style="font-size:18px;color:var(--text)">&#8369;\${fmt(val)}</div>
      </div>
    </div>
    <div class="sp-section">
      <div class="sp-section-label">Warehouse Availability</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Warehouse</th><th>Qty On Hand</th><th>Allocated</th><th>Available</th></tr></thead>
          <tbody>
            <tr><td>WH-MNL (Manila Main)</td><td class="mono" style="text-align:center">\${Math.floor(i.qty*0.6)}</td><td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">\${Math.floor(i.qty*0.6)}</td></tr>
            <tr><td>WH-QC (Quezon City)</td><td class="mono" style="text-align:center">\${Math.ceil(i.qty*0.4)}</td><td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">\${Math.ceil(i.qty*0.4)}</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm" onclick="showToast('Edit mode...')">Edit Demo Item</button><button class="btn btn-sm btn-primary" onclick="openInStockItems(JSON.parse(localStorage.getItem('inventoryDemoData')))">Back</button>\`;
  openCenterModal('Item Profile: ' + i.desc, html, actions, {width: '800px'});
}

function openInWarehouseDetails(data) {
  const rows = data.stockItems.map(i=>\`<tr>
    <td class="mono">\${i.code}</td><td>\${i.desc}</td><td>WH-MNL</td>
    <td class="mono" style="text-align:center">\${i.qty}</td><td class="mono" style="text-align:center">\${i.qty}</td>
    <td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">\${i.reorder}</td><td>\${badge(i.status)}</td>
    <td style="text-align:center;white-space:nowrap"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Adjusting...')">Adjust</button> <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Transferring...')">Transfer</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th style="text-align:center">Reorder Pt</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('wh-details.csv', [])">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Item Warehouse Details', html, actions, {width: '1100px'});
}

function openInNonStockItems(data) {
  const rows = data.nonStockItems.map(i=>\`<tr>
    <td class="mono">\${i.code}</td><td><strong>\${i.desc}</strong></td><td>\${i.cat}</td>
    <td class="mono">\${i.account}</td><td class="amt">&#8369;\${fmt(i.cost)}</td><td>\${badge(i.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing details...')">View Details</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>Item Code</th><th>Description</th><th>Category</th><th>Expense Account</th><th>Standard Cost</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('non-stock.csv', [])">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Non-Stock Items', html, actions, {width: '850px'});
}

function openInWarehouses(data) {
  const rows = data.warehouses.map(w=>\`<tr>
    <td class="mono">\${w.id}</td><td><strong>\${w.name}</strong></td><td>\${w.branch}</td><td>\${w.loc}</td>
    <td>\${w.manager}</td><td class="mono" style="text-align:center">\${w.cap}</td><td class="mono" style="text-align:center">\${w.util}%</td><td>\${badge(w.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing warehouse...')">View</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>WH ID</th><th>Warehouse Name</th><th>Branch</th><th>Location</th><th>Manager</th><th style="text-align:center">Capacity</th><th style="text-align:center">Utilization</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('warehouses.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).warehouses)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Warehouses', html, actions, {width: '1000px'});
}

function openInBuildings(data) {
  const rows = data.warehouseBuildings.map(b=>\`<tr>
    <td class="mono">\${b.id}</td><td><strong>\${b.name}</strong></td><td>\${b.wh}</td><td>\${b.loc}</td>
    <td>\${b.area}</td><td class="mono" style="text-align:center">\${b.zones}</td><td>\${badge(b.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing building...')">View</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>Bldg ID</th><th>Building Name</th><th>Warehouse</th><th>Location</th><th>Floor Area</th><th style="text-align:center">Zones</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('buildings.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).warehouseBuildings)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Warehouse Buildings', html, actions, {width: '900px'});
}

// ======================= PHYSICAL INVENTORY =======================
function openInPrepareCount(data) {
  const whOpts = data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  const rows = data.stockItems.map(i=>\`<tr>
    <td class="mono">\${i.code}</td><td>\${i.desc}</td><td>WH-MNL</td>
    <td class="mono" style="text-align:center">\${i.qty}</td><td>\${badge('Uncounted')}</td>
  </tr>\`).join('');
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Warehouse</label><select>\${whOpts}</select></div>
      <div class="demo-form-row"><label>Count Date</label><input type="date" value="2026-05-30"></div>
      <div class="demo-form-row"><label>Item Class</label><select><option>All</option><option>Motor Unit</option></select></div>
      <div class="demo-form-row"><label>Count Type</label><select><option>Full Physical</option><option>Cycle Count</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end"><label style="display:flex;align-items:center;gap:6px"><input type="checkbox" checked> Freeze Inventory</label></div>
      <div class="demo-form-row"><label>Assigned Counter</label><input type="text" value="Warehouse Team"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Notes</label><input type="text" placeholder="End of month physical count"></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table><thead><tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">System Qty</th><th>Count Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runInPrepareCount(this)">Generate Count Sheet</button>
    <button class="btn btn-sm" onclick="showToast('Printing Count Sheet...')">Print Count Sheet</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Prepare Physical Count', html, actions, {width: '950px'});
}

function runInPrepareCount(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const cntNo = 'CNT-' + String(data.physicalCounts.length + 101);
    const lines = data.stockItems.map(i=>({code:i.code, desc:i.desc, sysQty:i.qty, countedQty: 0, variance: 0, status: 'Pending Entry'}));
    data.physicalCounts.unshift({
      no: cntNo, date: '2026-05-30', wh: 'WH-MNL', lines, status: 'Generated'
    });
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Count Sheet Generated';
    btn.disabled = true;
    showToast('Demo physical count sheet generated successfully.');
  }, 1000);
}

function openInPhysicalCount(data) {
  const cnt = data.physicalCounts[0];
  if(!cnt) {
    openCenterModal('Physical Inventory Count', '<div style="padding:40px;text-align:center;color:var(--text-tertiary)">No count sheets generated yet. Please prepare one first.</div>', '<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>');
    return;
  }
  
  const rows = cnt.lines.map(l=>\`<tr>
    <td class="mono">\${l.code}</td><td>\${l.desc}</td>
    <td class="mono" style="text-align:center">\${l.sysQty}</td>
    <td class="mono" style="text-align:center;background:var(--bg-light);font-weight:bold">\${l.countedQty}</td>
    <td class="mono" style="text-align:center;color:\${l.variance<0?'var(--red)':'var(--text)'}">\${l.variance}</td>
    <td>\${badge(l.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      \${cnt.status!=='Posted' ? \`<button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="enterInCount('\${l.code}')">Enter Count</button>\` : ''}
    </td>
  </tr>\`).join('');
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Count Sheet No.</label><input type="text" value="\${cnt.no}" readonly></div>
      <div class="demo-form-row"><label>Count Date</label><input type="date" value="\${cnt.date}" readonly></div>
      <div class="demo-form-row"><label>Warehouse</label><input type="text" value="\${cnt.wh}" readonly></div>
      <div class="demo-form-row"><label>Overall Status</label><input type="text" value="\${cnt.status}" readonly></div>
    </div>
    <div class="table-wrap">
      <table><thead><tr><th>Item Code</th><th>Description</th><th style="text-align:center">Sys Qty</th><th style="text-align:center">Counted</th><th style="text-align:center">Variance</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    \${cnt.status!=='Posted' ? \`<button class="btn btn-sm btn-primary" onclick="approveInCount(this)">Approve Count</button>\` : ''}
    \${cnt.status==='Approved' ? \`<button class="btn btn-sm btn-primary" onclick="postInCountAdjustment(this)">Post Adjustment</button>\` : ''}
    <button class="btn btn-sm" onclick="exportRowsToCsv('physical-count.csv', [])">Export CSV</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Physical Inventory Count: ' + cnt.no, html, actions, {width: '1000px'});
}

function enterInCount(code) {
  let qty = prompt('Enter physical counted quantity for ' + code + ':');
  if(qty !== null) {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let cnt = data.physicalCounts[0];
    let line = cnt.lines.find(l=>l.code===code);
    if(line) {
      line.countedQty = parseInt(qty)||0;
      line.variance = line.countedQty - line.sysQty;
      line.status = 'Counted';
      localStorage.setItem('inventoryDemoData', JSON.stringify(data));
      openInPhysicalCount(data);
    }
  }
}

function approveInCount(btn) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  if(data.physicalCounts[0]) {
    data.physicalCounts[0].status = 'Approved';
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Physical count approved.');
    openInPhysicalCount(data);
  }
}

function postInCountAdjustment(btn) {
  setButtonLoading(btn, 'Posting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let cnt = data.physicalCounts[0];
    cnt.status = 'Posted';
    
    // Auto generate adjustments for variances
    cnt.lines.filter(l=>l.variance !== 0).forEach(l => {
      data.adjustments.unshift({
        no: 'ADJ-VAR-' + String(data.adjustments.length + 100),
        date: cnt.date, wh: cnt.wh, item: l.code, lot: '',
        type: l.variance > 0 ? 'Increase' : 'Decrease',
        qty: Math.abs(l.variance),
        reason: 'Physical Count Variance',
        status: 'Released'
      });
      let item = data.stockItems.find(i=>i.code===l.code);
      if(item) item.qty = l.countedQty; // fix stock to counted qty
    });
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo adjustments posted successfully.');
    openInPhysicalCount(data);
  }, 1000);
}

// ======================= PROCESSES =======================
function openInProcessReleaseDocs(data) {
  const rows = data.adjustments.filter(a=>a.status==='Pending').map(a=>\`<tr>
    <td class="mono">\${a.no}</td><td>Adjustment</td><td>\${a.wh}</td>
    <td>\${a.item}</td><td class="mono" style="text-align:center">\${a.qty}</td><td>\${badge('Pending')}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No pending documents to release</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Document Type</label><select><option>All</option><option>Receipts</option><option>Issues</option><option>Adjustments</option></select></div>
      <div class="demo-form-row"><label>Warehouse</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Status</label><select><option>Unreleased</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table><thead><tr><th>Document No.</th><th>Type</th><th>Warehouse</th><th>Item</th><th style="text-align:center">Qty</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runInReleaseDocs(this)">Release Documents</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Release IN Documents', html, actions, {width: '850px'});
}

function runInReleaseDocs(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Documents Released';
    btn.disabled = true;
    showToast('Demo inventory documents released successfully.');
  }, 1000);
}

function openInProcessClosePeriod(data) {
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Financial Period</label><select><option>05-2026</option></select></div>
      <div class="demo-form-row"><label>Warehouse</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Module</label><select><option>Inventory</option></select></div>
      <div class="demo-form-row"><label>Closing Option</label><select><option>Soft Close</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table>
        <thead><tr><th>Validation Item</th><th>Result</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>Unreleased inventory documents</td><td class="dim" id="in-val-1">-</td><td id="in-stat-1">\${badge('Pending')}</td></tr>
          <tr><td>Negative stock check</td><td class="dim" id="in-val-2">-</td><td id="in-stat-2">\${badge('Pending')}</td></tr>
          <tr><td>Physical count variance check</td><td class="dim" id="in-val-3">-</td><td id="in-stat-3">\${badge('Pending')}</td></tr>
          <tr><td>Goods in transit validation</td><td class="dim" id="in-val-4">-</td><td id="in-stat-4">\${badge('Pending')}</td></tr>
          <tr><td>Inventory valuation check</td><td class="dim" id="in-val-5">-</td><td id="in-stat-5">\${badge('Pending')}</td></tr>
          <tr><td>GL posting check</td><td class="dim" id="in-val-6">-</td><td id="in-stat-6">\${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runInValidatePeriod(this)" id="in-val-btn">Validate Period</button>
    <button class="btn btn-sm btn-primary" onclick="runInClosePeriod(this)" id="in-clo-btn" disabled>Close Period</button>
    <button class="btn btn-sm" onclick="showToast('Printing Log...')">Print Closing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Close Financial Periods (IN)', html, actions, {width: '800px'});
}

function runInValidatePeriod(btn) {
  setButtonLoading(btn, 'Validating...');
  setTimeout(() => {
    document.getElementById('in-val-1').textContent = '0 Documents'; document.getElementById('in-stat-1').innerHTML = badge('OK');
    document.getElementById('in-val-2').textContent = 'Passed'; document.getElementById('in-stat-2').innerHTML = badge('OK');
    document.getElementById('in-val-3').textContent = 'Posted'; document.getElementById('in-stat-3').innerHTML = badge('OK');
    document.getElementById('in-val-4').textContent = '1 Transit'; document.getElementById('in-stat-4').innerHTML = badge('OK');
    document.getElementById('in-val-5').textContent = 'Balanced'; document.getElementById('in-stat-5').innerHTML = badge('OK');
    document.getElementById('in-val-6').textContent = 'Posted'; document.getElementById('in-stat-6').innerHTML = badge('OK');
    resetButtonLoading(btn);
    btn.textContent = 'Validated';
    btn.disabled = true;
    document.getElementById('in-clo-btn').disabled = false;
    showToast('Period validation complete. Ready to close.');
  }, 1500);
}

function runInClosePeriod(btn) {
  setButtonLoading(btn, 'Closing Period...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Period Closed';
    btn.disabled = true;
    showToast('Demo inventory financial period closed successfully.');
  }, 1000);
}

// ======================= INQUIRIES =======================
function openInInquiryModal(action, data) {
  let content = '';
  let width = '1000px';
  
  if (action === 'Inventory Summary') {
    const rows = data.stockItems.map(i=>\`<tr><td class="mono">\${i.code}</td><td>\${i.desc}</td><td>\${i.class}</td><td class="mono" style="text-align:center">\${i.qty}</td><td class="mono" style="text-align:center">\${i.qty}</td><td class="mono" style="text-align:center">0</td><td class="amt">&#8369;\${fmt(i.qty*i.cost)}</td><td>\${badge(i.status)}</td></tr>\`).join('');
    content = \`<div class="table-wrap"><table><thead><tr><th>Item Code</th><th>Description</th><th>Item Class</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th>Inventory Value</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  } 
  else if (action === 'Storage Summary') {
    const rows = data.warehouses.map(w=>\`<tr><td>\${w.id}</td><td class="mono" style="text-align:center">\${w.cap}</td><td class="mono" style="text-align:center">\${Math.floor(w.cap*(w.util/100))}</td><td class="mono" style="text-align:center">\${w.cap - Math.floor(w.cap*(w.util/100))}</td><td class="mono" style="text-align:center">\${w.util}%</td><td>\${badge(w.status)}</td></tr>\`).join('');
    content = \`<div class="table-wrap"><table><thead><tr><th>Warehouse</th><th style="text-align:center">Capacity</th><th style="text-align:center">Used</th><th style="text-align:center">Available</th><th style="text-align:center">Utilization %</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  }
  else if (action === 'Inventory Allocation Details') {
    content = \`<div class="table-wrap"><table><thead><tr><th>Item</th><th>Warehouse</th><th>Sales Order No.</th><th style="text-align:center">Allocated Qty</th><th>Date</th><th>Status</th></tr></thead><tbody><tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No allocations</td></tr></tbody></table></div>\`;
  }
  else if (action === 'Inventory Transactions by Account') {
    content = \`<div class="table-wrap"><table><thead><tr><th>GL Account</th><th>Account Name</th><th>Transaction Type</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody><tr><td class="mono">12000</td><td>Inventory Asset</td><td>Receipt</td><td class="amt">&#8369;325,000</td><td class="amt">-</td><td class="amt">&#8369;325,000</td></tr></tbody></table></div>\`;
  }
  else if (action === 'Inventory Lot/Serial History') {
    content = \`<div class="table-wrap"><table><thead><tr><th>Lot/Serial No.</th><th>Item</th><th>Warehouse</th><th>Transaction Date</th><th>Type</th><th>Ref No.</th><th>Status</th></tr></thead><tbody><tr><td class="mono">LOT-009A</td><td>Honda Click 125i</td><td>WH-MNL</td><td>2026-05-10</td><td>Receipt</td><td class="mono">REC-2001</td><td>\${badge('In Stock')}</td></tr></tbody></table></div>\`;
  }
  else if (action === 'Inventory by Item Class') {
    content = \`<div class="table-wrap"><table><thead><tr><th>Item Class</th><th style="text-align:center">Item Count</th><th style="text-align:center">On Hand Qty</th><th>Inventory Value</th><th>Reorder Alerts</th></tr></thead><tbody><tr><td>Motor Unit</td><td class="mono" style="text-align:center">3</td><td class="mono" style="text-align:center">43</td><td class="amt">&#8369;3,215,000</td><td class="mono" style="text-align:center;color:var(--red)">0</td></tr></tbody></table></div>\`;
  }
  else if (action === 'Dead Stock') {
    content = \`<div class="table-wrap"><table><thead><tr><th>Item</th><th>Warehouse</th><th style="text-align:center">Qty</th><th>Last Movement Date</th><th style="text-align:center">Days Without Movement</th><th>Action</th></tr></thead><tbody><tr><td>Brake Pad Set Old</td><td>WH-CEB</td><td class="mono" style="text-align:center">25</td><td>2025-01-10</td><td class="mono" style="text-align:center;color:var(--red)">491</td><td>Mark for clearance</td></tr></tbody></table></div>\`;
  }
  else if (action === 'Intercompany Goods in Transit') {
    const rows = data.transfers.filter(t=>t.status==='In Transit').map(t=>\`<tr><td class="mono">\${t.no}</td><td>\${t.from}</td><td>\${t.to}</td><td>\${t.item}</td><td class="mono" style="text-align:center">\${t.qty}</td><td>\${t.date}</td><td>\${badge(t.status)}</td></tr>\`).join('') || '<tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No goods in transit</td></tr>';
    content = \`<div class="table-wrap"><table><thead><tr><th>Transfer No.</th><th>Source</th><th>Destination</th><th>Item</th><th style="text-align:center">Qty</th><th>Ship Date</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  }
  else if (action === 'Intercompany Returned Goods in Transit') {
    content = \`<div class="table-wrap"><table><thead><tr><th>Return No.</th><th>Source</th><th>Destination</th><th>Item</th><th style="text-align:center">Qty</th><th>Return Date</th><th>Status</th></tr></thead><tbody><tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No returned goods in transit</td></tr></tbody></table></div>\`;
  }

  const wrapper = \`
    <div style="margin-bottom:12px;display:flex;gap:10px">
      <input type="text" placeholder="Search..." style="padding:6px 10px;border:1px solid var(--border);border-radius:4px;flex:1">
      <button class="btn btn-sm">Search</button>
    </div>
    \${content}
  \`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('in-inquiry.csv', [])">Export CSV</button><button class="btn btn-sm" onclick="showToast('Printing...')">Print</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Inquiry: ' + action, wrapper, actions, {width});
}

// ======================= REPORTS =======================
function openInReportModal(reportName, data) {
  const whOpts = \`<option value="">All Warehouses</option>\` + data.warehouses.map(w=>\`<option value="\${w.id}">\${w.name}</option>\`).join('');
  const classOpts = \`<option value="">All</option><option>Motor Unit</option><option>Accessories</option>\`;
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Warehouse</label><select>\${whOpts}</select></div>
      <div class="demo-form-row"><label>Item Class</label><select>\${classOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runInDemoReport(this, '\${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="in-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1050px, calc(100vw - 48px))'});
}

function runInDemoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'Inventory Balance') {
      thead = \`<tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th>Status</th></tr>\`;
      rows = data.stockItems.map(i=>\`<tr><td class="mono">\${i.code}</td><td>\${i.desc}</td><td>WH-MNL</td><td class="mono" style="text-align:center">\${i.qty}</td><td class="mono" style="text-align:center">\${i.qty}</td><td class="mono" style="text-align:center">0</td><td>\${badge(i.status)}</td></tr>\`).join('');
    } else if (reportName === 'Inventory Valuation') {
      thead = \`<tr><th>Item Code</th><th>Description</th><th style="text-align:center">Qty</th><th>Unit Cost</th><th>Total Value</th><th>Valuation Method</th></tr>\`;
      rows = data.stockItems.map(i=>\`<tr><td class="mono">\${i.code}</td><td>\${i.desc}</td><td class="mono" style="text-align:center">\${i.qty}</td><td class="amt">&#8369;\${fmt(i.cost)}</td><td class="amt" style="font-weight:bold">&#8369;\${fmt(i.qty*i.cost)}</td><td>Average Cost</td></tr>\`).join('');
    } else if (reportName === 'Inventory Register') {
      thead = \`<tr><th>Date</th><th>Reference No.</th><th>Type</th><th>Item</th><th>Warehouse</th><th style="text-align:center">Qty In</th><th style="text-align:center">Qty Out</th><th>Balance</th></tr>\`;
      rows = \`<tr><td class="dim">2026-05-10</td><td class="mono">REC-2001</td><td>Receipt</td><td>Honda Click 125i</td><td>WH-MNL</td><td class="mono" style="text-align:center;color:var(--green)">+5</td><td class="mono" style="text-align:center">-</td><td class="mono">15</td></tr>\`;
    } else if (reportName === 'Goods in Transit') {
      thead = \`<tr><th>Transfer No.</th><th>From WH</th><th>To WH</th><th>Item</th><th style="text-align:center">Qty</th><th>Ship Date</th><th>Exp Receipt Date</th><th>Status</th></tr>\`;
      rows = data.transfers.filter(t=>t.status==='In Transit').map(t=>\`<tr><td class="mono">\${t.no}</td><td>\${t.from}</td><td>\${t.to}</td><td>\${t.item}</td><td class="mono" style="text-align:center">\${t.qty}</td><td class="dim">\${t.date}</td><td class="dim">2026-05-18</td><td>\${badge(t.status)}</td></tr>\`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No goods in transit</td></tr>';
    } else if (reportName === 'Lot/Serial Numbers') {
      thead = \`<tr><th>Lot/Serial No.</th><th>Item</th><th>Warehouse</th><th>Received Date</th><th>Current Status</th><th>Reference No.</th></tr>\`;
      rows = \`<tr><td class="mono" style="font-weight:bold">ENG-X890123</td><td>Honda Click 125i</td><td>WH-MNL</td><td>2026-05-10</td><td>\${badge('Available')}</td><td class="mono">REC-2001</td></tr>\`;
    } else {
      thead = \`<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>\`;
      rows = \`<tr><td class="mono">REC-001</td><td class="dim">2026-05-16</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>\${badge('Active')}</td></tr>\`;
    }
    
    document.getElementById('in-report-body').innerHTML = \`<table><thead>\${thead}</thead><tbody>\${rows}</tbody></table>\`;
    resetButtonLoading(btn);
    showToast('Demo inventory report generated successfully.');
  }, 800);
}
// --- END INVENTORY MODULE DEMO ---
`;
html = html.replace('function doLogout(){', block + '\nfunction doLogout(){');

fs.writeFileSync('erp-business-management.html', html, 'utf8');
console.log('Successfully injected inventory module demo block.');
