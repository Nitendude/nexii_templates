const fs = require('fs');

const replacement = `// --- PURCHASES MODULE FULL DEMO ---
function initPurchasesDemoData() {
  let d = JSON.parse(localStorage.getItem('purchasesDemoData'));
  if(!d) {
    d = {
      purchaseRequests: [],
      requisitions: [],
      purchaseOrders: [],
      purchaseReceipts: [],
      landedCosts: [],
      vendors: [
        { id: 'VND-001', name: 'Honda Philippines', contact: 'Juan Dela Cruz', phone: '09171112222', email: 'sales@hondaph.com', address: 'Batangas', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-002', name: 'Yamaha Motor Philippines', contact: 'Maria Santos', phone: '09182223333', email: 'orders@yamaha-motor.com.ph', address: 'Laguna', terms: 'NET15', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-003', name: 'Kawasaki Motors Philippines', contact: 'Pedro Reyes', phone: '09193334444', email: 'info@kawasaki.ph', address: 'Muntinlupa', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-004', name: 'Suzuki Philippines', contact: 'Ana Lim', phone: '09204445555', email: 'sales@suzuki.com.ph', address: 'Laguna', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-005', name: 'TVS Motor Philippines', contact: 'Carlos Chua', phone: '09215556666', email: 'contact@tvs.ph', address: 'Manila', terms: 'CASH', type: 'Distributor', status: 'Active' }
      ],
      vendorPrices: [],
      vendorInventory: [],
      intercompanyPurchaseOrders: [],
      logs: []
    };
    
    const vendorsMap = {'Honda':'Honda Philippines','Yamaha':'Yamaha Motor Philippines','Kawasaki':'Kawasaki Motors Philippines','Suzuki':'Suzuki Philippines','TVS':'TVS Motor Philippines'};
    catalog.forEach(item => {
       const vendor = vendorsMap[item.brand] || 'Honda Philippines';
       d.vendorPrices.push({ vendor, model: item.model, standardCost: item.price * 0.75, discount: '5%', effective: '2026-01-01', status: 'Active' });
       d.vendorInventory.push({ vendor, model: item.model, available: Math.floor(Math.random()*100)+10, leadTime: '7 Days', lastPurchase: '2026-04-15', status: 'Active' });
    });

    localStorage.setItem('purchasesDemoData', JSON.stringify(d));
  }
  return d;
}

function updateLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    }
  }
}

function openPurchasesDemo(action, type) {
  const data = initPurchasesDemoData();
  
  if(type === 'po-shortcut') {
    if(action === 'New Purchase Order') openPoShortcutModal(data);
    else if(action === 'New Purchase Receipt') openReceiptShortcutModal(data);
    else if(action === 'New Purchase Request') openRequestShortcutModal(data);
    else if(action === 'New Vendor') openVendorShortcutModal(data);
  }
  else if(type === 'po-transaction') {
    if(action === 'Requests') openPoRequestsModal(data);
    else if(action === 'Requisitions') openPoRequisitionsModal(data);
    else if(action === 'Purchase Orders') openPoOrdersModal(data);
    else if(action === 'Purchase Receipts') openPoReceiptsModal(data);
    else if(action === 'Landed Costs') openPoLandedCostsModal(data);
  }
  else if(type === 'po-profile') {
    if(action === 'Vendors') openPoVendorsModal(data);
    else if(action === 'Vendor Prices') openPoVendorPricesModal(data);
    else if(action === 'Vendor Inventory') openPoVendorInventoryModal(data);
  }
  else if(type === 'po-process') {
    if(action === 'Create Purchase Orders') openProcessCreatePO(data);
    else if(action === 'Print/Email Purchase Orders') openProcessEmailPO(data);
    else if(action === 'Generate Intercompany Purchase Orders') openProcessIntercompanyPO(data);
  }
  else if(type === 'po-printed-form') {
    if(action === 'Item Request') openFormItemRequest(data);
    else if(action === 'Purchase Order') openFormPO(data);
    else if(action === 'Purchase Receipt') openFormReceipt(data);
  }
  else if(type === 'po-report') {
    openPoReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openPoShortcutModal(data) {
  const vOpts = data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  const iOpts = catalog.map(c=>\`<option value="\${c.model}">\${c.brand} \${c.model}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="po-vnd">\${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch / Destination</label><select id="po-branch">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Order Date</label><input type="date" id="po-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Expected Delivery Date</label><input type="date" id="po-del" value="2026-05-22"></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="po-item">\${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="po-qty" value="10"></div>
      <div class="demo-form-row"><label>Unit Cost</label><input type="number" id="po-cost" value="65000"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="po-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="po-rem" placeholder="Optional notes"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitNewPO(this)">Submit Purchase Order</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>\`;
  openCenterModal('New Purchase Order', html, actions);
}

function submitNewPO(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const poNo = 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0');
    const qty = parseFloat(document.getElementById('po-qty').value)||0;
    const cost = parseFloat(document.getElementById('po-cost').value)||0;
    data.purchaseOrders.unshift({
      no: poNo,
      date: document.getElementById('po-date').value,
      vendor: document.getElementById('po-vnd').value,
      branch: document.getElementById('po-branch').value,
      item: document.getElementById('po-item').value,
      qty, cost, total: qty * cost,
      delivery: document.getElementById('po-del').value,
      status: 'Open'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase order submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openReceiptShortcutModal(data) {
  const vOpts = data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const poOpts = data.purchaseOrders.map(p=>\`<option value="\${p.no}">\${p.no} - \${p.item}</option>\`).join('') || '<option value="">No Open POs</option>';
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Purchase Order No.</label><select id="pr-po">\${poOpts}</select></div>
      <div class="demo-form-row"><label>Vendor</label><select id="pr-vnd">\${vOpts}</select></div>
      <div class="demo-form-row"><label>Receipt Date</label><input type="date" id="pr-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Received By</label><input type="text" id="pr-by" value="Warehouse Staff"></div>
      <div class="demo-form-row"><label>Quantity Received</label><input type="number" id="pr-qty" value="10"></div>
      <div class="demo-form-row"><label>Warehouse / Branch</label><select><option>Main Warehouse</option></select></div>
      <div class="demo-form-row"><label>Condition</label><select id="pr-cond"><option>Good</option><option>Damaged</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="pr-rem"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitNewReceipt(this)">Save Receipt</button><button class="btn btn-sm" onclick="showToast('Receipt printed');">Print Receipt</button>\`;
  openCenterModal('New Purchase Receipt', html, actions);
}

function submitNewReceipt(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const recNo = 'PR-' + String(data.purchaseReceipts.length + 5001).padStart(4, '0');
    const poNo = document.getElementById('pr-po').value;
    data.purchaseReceipts.unshift({
      no: recNo, poNo,
      vendor: document.getElementById('pr-vnd').value,
      date: document.getElementById('pr-date').value,
      item: 'Unit Items', // simplified
      qty: parseFloat(document.getElementById('pr-qty').value)||0,
      condition: document.getElementById('pr-cond').value,
      status: 'Received'
    });
    const po = data.purchaseOrders.find(p=>p.no===poNo);
    if(po) po.status = 'Received';
    
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase receipt saved successfully.');
    closeCenterModal();
  }, 600);
}

function openRequestShortcutModal(data) {
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  const iOpts = catalog.map(c=>\`<option value="\${c.model}">\${c.brand} \${c.model}</option>\`).join('');
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Requesting Branch</label><select id="req-br">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Requested By</label><input type="text" id="req-by" value="Branch Manager"></div>
      <div class="demo-form-row"><label>Request Date</label><input type="date" id="req-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="req-item">\${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity Requested</label><input type="number" id="req-qty" value="5"></div>
      <div class="demo-form-row"><label>Priority</label><select id="req-pri"><option>Normal</option><option>High</option><option>Urgent</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Reason / Notes</label><input type="text" id="req-note" placeholder="E.g., Stock depletion"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitNewRequest(this)">Submit for Approval</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Request</button>\`;
  openCenterModal('New Purchase Request', html, actions);
}

function submitNewRequest(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const no = 'REQ-' + String(data.purchaseRequests.length + 101).padStart(4, '0');
    data.purchaseRequests.unshift({
      no,
      date: document.getElementById('req-date').value,
      branch: document.getElementById('req-br').value,
      by: document.getElementById('req-by').value,
      item: document.getElementById('req-item').value,
      qty: document.getElementById('req-qty').value,
      priority: document.getElementById('req-pri').value,
      status: 'Pending Approval'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase request submitted for approval.');
    closeCenterModal();
  }, 600);
}

function openVendorShortcutModal(data) {
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="vnd-name"></div>
      <div class="demo-form-row"><label>Contact Person</label><input type="text" id="vnd-contact"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="vnd-phone"></div>
      <div class="demo-form-row"><label>Email</label><input type="email" id="vnd-email"></div>
      <div class="demo-form-row"><label>Address</label><input type="text" id="vnd-addr"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="vnd-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row"><label>Vendor Type</label><select id="vnd-type"><option>Manufacturer</option><option>Distributor</option><option>Service Provider</option></select></div>
      <div class="demo-form-row"><label>Status</label><select id="vnd-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitNewVendor(this)">Save Vendor</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>\`;
  openCenterModal('New Vendor', html, actions);
}

function submitNewVendor(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const id = 'VND-' + String(data.vendors.length + 1).padStart(3, '0');
    data.vendors.push({
      id,
      name: document.getElementById('vnd-name').value || 'Unknown Vendor',
      contact: document.getElementById('vnd-contact').value,
      phone: document.getElementById('vnd-phone').value,
      email: document.getElementById('vnd-email').value,
      address: document.getElementById('vnd-addr').value,
      terms: document.getElementById('vnd-terms').value,
      type: document.getElementById('vnd-type').value,
      status: document.getElementById('vnd-status').value
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo vendor saved successfully.');
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function exportPurchasesCsv(filename, arrayData) {
  if(!arrayData || arrayData.length===0) { showToast('No data to export'); return; }
  exportRowsToCsv(filename, arrayData);
}

function openPoRequestsModal(data) {
  const rows = data.purchaseRequests.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="dim">\${r.date}</td><td>\${r.branch}</td><td>\${r.by}</td>
    <td>\${r.item}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${badge(r.priority==='Urgent'?'Late':(r.priority==='High'?'Partial':'Normal'))}</td>
    <td>\${badge(r.status==='Approved'?'Approved':(r.status==='Rejected'?'Cancelled':'Pending'))}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Request')">View</button>
      \${r.status.includes('Pending') ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateReqStatus('\${r.no}','Approved', this)">Approve</button> <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="updateReqStatus('\${r.no}','Rejected', this)">Reject</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="9" style="text-align:center;color:var(--text-tertiary)">No requests</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Request No.</th><th>Date</th><th>Branch</th><th>Requested By</th><th>Item</th><th style="text-align:center">Qty</th><th>Priority</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('requests.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseRequests)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Purchase Requests', html, actions, {width: '1000px'});
}

function updateReqStatus(no, stat, btn) {
  updateLocalData('purchaseRequests', 'no', no, {status: stat});
  showToast('Request updated successfully.');
  openPoRequestsModal(JSON.parse(localStorage.getItem('purchasesDemoData')));
}

function openPoRequisitionsModal(data) {
  const rows = data.purchaseRequests.filter(r=>r.status==='Approved').map(r=>\`<tr>
    <td class="mono">RQZ-\${r.no.split('-')[1]}</td><td class="mono dim">\${r.no}</td><td>\${r.branch}</td>
    <td>\${r.item}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>System Admin</td><td>\${badge('Approved')}</td>
    <td style="text-align:center">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="reqConvertToPo('\${r.no}', this)">Convert to PO</button>
    </td>
  </tr>\`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No approved requisitions</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Requisition No.</th><th>Request No.</th><th>Branch</th><th>Item</th><th style="text-align:center">Qty</th><th>Approved By</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('requisitions.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseRequests.filter(r=>r.status==='Approved'))">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Requisitions', html, actions, {width: '950px'});
}

function reqConvertToPo(no, btn) {
  setButtonLoading(btn, 'Converting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const req = data.purchaseRequests.find(r=>r.no===no);
    if(req) req.status = 'Converted';
    
    data.purchaseOrders.unshift({
      no: 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0'),
      date: new Date().toISOString().split('T')[0],
      vendor: 'Honda Philippines',
      branch: req.branch,
      item: req.item,
      qty: parseInt(req.qty)||1,
      cost: 65000, total: 65000*(parseInt(req.qty)||1),
      delivery: '2026-06-01',
      status: 'Open'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Requisition converted to Purchase Order.');
    openPoRequisitionsModal(data);
  }, 600);
}

function openPoOrdersModal(data) {
  const rows = data.purchaseOrders.map(p=>\`<tr>
    <td class="mono">\${p.no}</td><td class="dim">\${p.date}</td><td><strong>\${p.vendor}</strong></td><td>\${p.branch}</td>
    <td>\${p.item}</td><td class="mono" style="text-align:center">\${p.qty}</td><td class="amt">&#8369;\${fmt(p.cost)}</td><td class="amt">&#8369;\${fmt(p.total)}</td>
    <td class="dim">\${p.delivery}</td><td>\${badge(p.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing PO')">View PO</button>
      \${p.status==='Open' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('\${p.no}','Approved', this)">Approve</button>\` : ''}
      \${p.status==='Approved' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('\${p.no}','Ordered', this)">Mark Ordered</button>\` : ''}
      \${p.status==='Ordered' ? \`<button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('\${p.no}','Received', this)">Mark Received</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="11" style="text-align:center;color:var(--text-tertiary)">No purchase orders</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>PO No.</th><th>Date</th><th>Vendor</th><th>Branch</th><th>Item</th><th style="text-align:center">Qty</th><th>Unit Cost</th><th>Total Amount</th><th>Expected Delivery</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('purchase-orders.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseOrders)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Purchase Orders', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

function updatePoStatus(no, stat, btn) {
  updateLocalData('purchaseOrders', 'no', no, {status: stat});
  showToast('PO status updated.');
  openPoOrdersModal(JSON.parse(localStorage.getItem('purchasesDemoData')));
}

function openPoReceiptsModal(data) {
  const rows = data.purchaseReceipts.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td class="mono dim">\${r.poNo}</td><td><strong>\${r.vendor}</strong></td><td class="dim">\${r.date}</td>
    <td>\${r.item}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>Warehouse</td><td>\${r.condition}</td><td>\${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Receipt')">View Receipt</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Receipt printed')">Print</button>
    </td>
  </tr>\`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No purchase receipts</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Receipt No.</th><th>PO No.</th><th>Vendor</th><th>Receipt Date</th><th>Item</th><th style="text-align:center">Qty Recv</th><th>Branch</th><th>Condition</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('purchase-receipts.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseReceipts)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Purchase Receipts', html, actions, {width: '1050px'});
}

function openPoLandedCostsModal(data) {
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Landed Cost No.</th><th>PO No.</th><th>Vendor</th><th>Freight</th><th>Insurance</th><th>Duties</th><th>Other</th><th>Total</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody><tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No landed cost records yet. Add one via Data View.</td></tr></tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="showToast('Adding landed cost...')">Add Landed Cost</button><button class="btn btn-sm" onclick="showToast('Allocating costs...')">Allocate Cost</button><button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Landed Costs', html, actions, {width: '950px'});
}

// ======================= PROFILES =======================
function openPoVendorsModal(data) {
  const rows = data.vendors.map(v=>\`<tr>
    <td class="mono">\${v.id}</td><td><strong>\${v.name}</strong></td><td>\${v.contact}</td><td class="mono dim">\${v.phone}</td>
    <td class="dim">\${v.email}</td><td class="mono">\${v.terms}</td><td>\${badge(v.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" onclick="openVendorProfileModal('\${v.id}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
  </tr>\`).join('');
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Vendor ID</th><th>Vendor Name</th><th>Contact Person</th><th>Contact Number</th><th>Email</th><th>Terms</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('vendors.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Vendors', html, actions, {width: '1000px'});
}

function openVendorProfileModal(id) {
  const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
  const v = data.vendors.find(x=>x.id===id);
  if(!v) return;
  
  const pos = data.purchaseOrders.filter(p=>p.vendor===v.name);
  const receipts = data.purchaseReceipts.filter(r=>r.vendor===v.name);
  const total = pos.reduce((sum, p) => sum + (p.total || 0), 0);
  
  const poHtml = pos.length ? pos.map(p=>\`<tr><td class="mono">\${p.no}</td><td class="dim">\${p.date}</td><td>\${p.item}</td><td class="mono">\${p.qty}</td><td class="amt">&#8369;\${fmt(p.total)}</td><td>\${badge(p.status)}</td></tr>\`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No orders</td></tr>';
  const recHtml = receipts.length ? receipts.map(r=>\`<tr><td class="mono">\${r.no}</td><td class="mono">\${r.poNo}</td><td class="dim">\${r.date}</td><td>\${r.item}</td><td class="mono">\${r.qty}</td><td>\${badge(r.status)}</td></tr>\`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No receipts</td></tr>';

  const html = \`
    <div class="sp-section" id="vnd-view-mode">
      <div class="sp-section-label">Vendor Information</div>
      <div class="sp-row"><span class="sp-key">Vendor ID</span><span class="sp-val mono">\${v.id}</span></div>
      <div class="sp-row"><span class="sp-key">Vendor Name</span><span class="sp-val"><strong>\${v.name}</strong></span></div>
      <div class="sp-row"><span class="sp-key">Contact Person</span><span class="sp-val">\${v.contact}</span></div>
      <div class="sp-row"><span class="sp-key">Contact Number</span><span class="sp-val mono">\${v.phone}</span></div>
      <div class="sp-row"><span class="sp-key">Email</span><span class="sp-val">\${v.email}</span></div>
      <div class="sp-row"><span class="sp-key">Payment Terms</span><span class="sp-val mono">\${v.terms}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">\${badge(v.status)}</span></div>
      <div class="sp-row"><span class="sp-key">Total Purchase Amount</span><span class="sp-val amt" style="color:var(--green)">&#8369;\${fmt(total)}</span></div>
    </div>
    <div class="sp-section" id="vnd-edit-mode" style="display:none">
      <div class="demo-form-grid">
        <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="ev-name" value="\${v.name}"></div>
        <div class="demo-form-row"><label>Contact Person</label><input type="text" id="ev-contact" value="\${v.contact}"></div>
        <div class="demo-form-row"><label>Contact Number</label><input type="text" id="ev-phone" value="\${v.phone}"></div>
        <div class="demo-form-row"><label>Email</label><input type="email" id="ev-email" value="\${v.email}"></div>
        <div class="demo-form-row"><label>Payment Terms</label><input type="text" id="ev-terms" value="\${v.terms}"></div>
        <div class="demo-form-row"><label>Status</label><select id="ev-status"><option \${v.status==='Active'?'selected':''}>Active</option><option \${v.status==='Inactive'?'selected':''}>Inactive</option></select></div>
        <div class="demo-form-row" style="grid-column:1/-1;display:flex;gap:10px">
          <button class="btn btn-sm btn-primary" onclick="saveEditVendor('\${v.id}')">Save Changes</button>
          <button class="btn btn-sm" onclick="document.getElementById('vnd-edit-mode').style.display='none';document.getElementById('vnd-view-mode').style.display='block';">Cancel</button>
        </div>
      </div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Purchase Order History</div>
      <div class="table-wrap"><table><thead><tr><th>PO No.</th><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead><tbody>\${poHtml}</tbody></table></div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Receipt History</div>
      <div class="table-wrap"><table><thead><tr><th>Receipt No.</th><th>PO No.</th><th>Date</th><th>Item</th><th>Qty</th><th>Status</th></tr></thead><tbody>\${recHtml}</tbody></table></div>
    </div>
    <div class="sp-section">
      <div class="sp-section-label">Activity Log</div>
      <div class="sp-row"><span class="dim" style="font-size:12px">Last activity: Demo data generation.</span></div>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm" onclick="document.getElementById('vnd-view-mode').style.display='none';document.getElementById('vnd-edit-mode').style.display='block';">Edit Demo Vendor</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Vendor...');">Export Vendor CSV</button>
    <button class="btn btn-sm" onclick="showToast('Profile printed');setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm btn-primary" onclick="openPoVendorsModal(JSON.parse(localStorage.getItem('purchasesDemoData')))">Close</button>
  \`;
  openCenterModal('Vendor Profile: ' + v.name, html, actions, {width: '900px'});
}

function saveEditVendor(id) {
  updateLocalData('vendors', 'id', id, {
    name: document.getElementById('ev-name').value,
    contact: document.getElementById('ev-contact').value,
    phone: document.getElementById('ev-phone').value,
    email: document.getElementById('ev-email').value,
    terms: document.getElementById('ev-terms').value,
    status: document.getElementById('ev-status').value
  });
  showToast('Vendor profile updated successfully.');
  openVendorProfileModal(id);
}

function openPoVendorPricesModal(data) {
  const rows = data.vendorPrices.map(p=>\`<tr>
    <td><strong>\${p.vendor}</strong></td><td>\${p.model}</td><td class="amt">&#8369;\${fmt(p.standardCost)}</td>
    <td class="mono">\${p.discount}</td><td class="mono dim">\${p.effective}</td><td>\${badge(p.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Editing price')">Edit Price</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>Vendor</th><th>Unit Model</th><th>Standard Cost</th><th>Discount</th><th>Effective Date</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('prices.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendorPrices)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Vendor Prices', html, actions, {width: '850px'});
}

function openPoVendorInventoryModal(data) {
  const rows = data.vendorInventory.map(p=>\`<tr>
    <td><strong>\${p.vendor}</strong></td><td>\${p.model}</td><td class="mono" style="text-align:center">\${p.available}</td>
    <td class="dim">\${p.leadTime}</td><td class="mono dim">\${p.lastPurchase}</td><td>\${badge(p.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Stock requested')">Request Stock</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>Vendor</th><th>Unit Model</th><th style="text-align:center">Available Qty</th><th>Lead Time</th><th>Last Purchase</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportPurchasesCsv('inventory.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendorInventory)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Vendor Inventory', html, actions, {width: '900px'});
}

// ======================= PROCESSES =======================
function openProcessCreatePO(data) {
  const vOpts = data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  const reqs = data.purchaseRequests.filter(r=>r.status==='Approved');
  const rows = reqs.map(r=>\`<tr>
    <td class="mono">\${r.no}</td><td>\${r.branch}</td><td>\${r.item}</td><td class="mono">\${r.qty}</td>
    <td><select class="req-ven-sel" style="width:120px;padding:2px;font-size:11px">\${vOpts}</select></td>
    <td>\${badge(r.status)}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No approved requests to convert</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Target Branch</label><select><option>All</option>\${bOpts}</select></div>
      <div class="demo-form-row"><label>Target Vendor</label><select><option>Select per item</option>\${vOpts}</select></div>
      <div class="demo-form-row"><label>Request Status</label><select><option>Approved</option></select></div>
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Request No.</th><th>Branch</th><th>Item</th><th>Qty</th><th>Vendor</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runGeneratePOProcess(this)" \${reqs.length===0?'disabled':''}>Generate Purchase Orders</button>
    <button class="btn btn-sm" onclick="showToast('Printing Process Log...')">Print Process Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Create Purchase Orders', html, actions, {width: '850px'});
}

function runGeneratePOProcess(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const reqs = data.purchaseRequests.filter(r=>r.status==='Approved');
    let generatedCount = 0;
    reqs.forEach(r => {
      r.status = 'Converted';
      const cost = Math.floor(Math.random()*20000)+60000;
      data.purchaseOrders.unshift({
        no: 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0'),
        date: new Date().toISOString().split('T')[0],
        vendor: 'Honda Philippines', // simulated selection
        branch: r.branch,
        item: r.item,
        qty: parseInt(r.qty)||1,
        cost, total: cost*(parseInt(r.qty)||1),
        delivery: '2026-05-30',
        status: 'Open'
      });
      generatedCount++;
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Process Complete';
    btn.disabled = true;
    showToast(\`Demo purchase orders generated successfully.\`);
  }, 1200);
}

function openProcessEmailPO(data) {
  const pos = data.purchaseOrders.filter(p=>p.status==='Open');
  const rows = pos.map(p=>\`<tr>
    <td class="mono"><input type="checkbox" checked> \${p.no}</td><td><strong>\${p.vendor}</strong></td>
    <td class="dim">Purchase Order</td><td class="dim">orders@\${p.vendor.replace(/\\s/g,'').toLowerCase()}.com</td>
    <td>Email</td><td>\${badge('Pending')}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No open POs to send</td></tr>';

  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Purchase Order</label><select><option>All Open</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Recipient Email</label><input type="email" placeholder="Optional override"></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end"><label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="checkbox" checked> Include Attachments</label></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Message</label><textarea rows="2" style="width:100%;border:1px solid var(--border);border-radius:4px;padding:8px">Please find attached the latest Purchase Orders.</textarea></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>PO No.</th><th>Vendor</th><th>Document Type</th><th>Recipient Email</th><th>Method</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runEmailPOProcess(this)" \${pos.length===0?'disabled':''}>Send Demo Email</button>
    <button class="btn btn-sm" onclick="showToast('Email Prepared.')">Prepare Email</button>
    <button class="btn btn-sm" onclick="showToast('Print preview mode')">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Print/Email Purchase Orders', html, actions, {width: '900px'});
}

function runEmailPOProcess(btn) {
  setButtonLoading(btn, 'Sending Emails...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Emails Sent';
    btn.disabled = true;
    showToast('Demo purchase order emails processed successfully.');
  }, 1000);
}

function openProcessIntercompanyPO(data) {
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  const iOpts = catalog.map(c=>\`<option value="\${c.model}">\${c.brand} \${c.model}</option>\`).join('');
  
  const rows = data.intercompanyPurchaseOrders.map(p=>\`<tr>
    <td class="mono">\${p.no}</td><td>\${p.source}</td><td>\${p.dest}</td><td>\${p.item}</td>
    <td class="mono">\${p.qty}</td><td>\${badge(p.status)}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No recent intercompany orders</td></tr>';

  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Source Branch</label><select id="ip-src">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Destination Branch</label><select id="ip-dest">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Vendor / Internal Supplier</label><select><option>NEXII Internal Logistics</option></select></div>
      <div class="demo-form-row"><label>Transaction Date</label><input type="date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Item / Model</label><select id="ip-item">\${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="ip-qty" value="5"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Reason</label><input type="text" placeholder="Stock balancing"></div>
    </div>
    <div class="table-wrap">
      <table><thead><tr><th>Intercompany PO</th><th>Source Branch</th><th>Destination Branch</th><th>Item</th><th>Qty</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runIntercompanyPOProcess(this)">Generate Intercompany PO</button>
    <button class="btn btn-sm" onclick="showToast('Exporting log...')">Export Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Generate Intercompany Purchase Orders', html, actions, {width: '850px'});
}

function runIntercompanyPOProcess(btn) {
  setButtonLoading(btn, 'Processing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const src = document.getElementById('ip-src').value;
    const dest = document.getElementById('ip-dest').value;
    const item = document.getElementById('ip-item').value;
    const qty = document.getElementById('ip-qty').value;
    data.intercompanyPurchaseOrders.unshift({
      no: 'IPO-2026-' + String(data.intercompanyPurchaseOrders.length + 100).padStart(3, '0'),
      source: src, dest, item, qty, status: 'Generated'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    showToast('Demo intercompany purchase order generated successfully.');
    openProcessIntercompanyPO(data); // refresh
  }, 800);
}

// ======================= PRINTED FORMS =======================
function openFormItemRequest(data) {
  const req = data.purchaseRequests[0] || {no: 'REQ-DEMO', branch: 'Main', by: 'Admin', date: '2026-05-15', item: 'Sample Item', qty: 10, reason: 'Demo', status: 'Pending'};
  const html = \`
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">ITEM REQUEST FORM</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">Request No:</td><td style="padding:4px">\${req.no}</td><td style="padding:4px;width:120px;font-weight:bold">Date:</td><td style="padding:4px">\${req.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Branch:</td><td style="padding:4px">\${req.branch}</td><td style="padding:4px;font-weight:bold">Requested By:</td><td style="padding:4px">\${req.by}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Status:</td><td style="padding:4px">\${req.status}</td><td style="padding:4px;font-weight:bold"></td><td style="padding:4px"></td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty</th><th style="border:1px solid #000;padding:8px">Reason</th></tr></thead>
        <tbody><tr><td style="border:1px solid #000;padding:8px">\${req.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">\${req.qty}</td><td style="border:1px solid #000;padding:8px">\${req.reason||'Stock Replenishment'}</td></tr></tbody>
      </table>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Print Preview: Item Request', html, actions, {width:'800px'});
}

function openFormPO(data) {
  const po = data.purchaseOrders[0] || {no: 'PO-DEMO', date: '2026-05-15', vendor: 'Sample Vendor', branch: 'Main', item: 'Sample Item', qty: 10, cost: 50000, total: 500000, delivery: '2026-05-30'};
  const html = \`
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">PURCHASE ORDER</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">PO No:</td><td style="padding:4px">\${po.no}</td><td style="padding:4px;width:120px;font-weight:bold">Order Date:</td><td style="padding:4px">\${po.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Vendor:</td><td style="padding:4px"><strong>\${po.vendor}</strong></td><td style="padding:4px;font-weight:bold">Delivery Date:</td><td style="padding:4px">\${po.delivery}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Ship To:</td><td style="padding:4px">\${po.branch}</td><td style="padding:4px;font-weight:bold">Terms:</td><td style="padding:4px">NET30</td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty</th><th style="border:1px solid #000;padding:8px">Unit Cost</th><th style="border:1px solid #000;padding:8px">Total Amount</th></tr></thead>
        <tbody>
          <tr><td style="border:1px solid #000;padding:8px">\${po.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">\${po.qty}</td><td style="border:1px solid #000;padding:8px;text-align:right">&#8369;\${fmt(po.cost)}</td><td style="border:1px solid #000;padding:8px;text-align:right">&#8369;\${fmt(po.total)}</td></tr>
        </tbody>
      </table>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print Purchase Order</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Print Preview: Purchase Order', html, actions, {width:'800px'});
}

function openFormReceipt(data) {
  const rec = data.purchaseReceipts[0] || {no: 'PR-DEMO', poNo: 'PO-DEMO', date: '2026-05-15', vendor: 'Sample Vendor', item: 'Sample Item', qty: 10, condition: 'Good'};
  const html = \`
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">RECEIVING REPORT</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">Receipt No:</td><td style="padding:4px">\${rec.no}</td><td style="padding:4px;width:120px;font-weight:bold">Receipt Date:</td><td style="padding:4px">\${rec.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Vendor:</td><td style="padding:4px">\${rec.vendor}</td><td style="padding:4px;font-weight:bold">PO Reference:</td><td style="padding:4px">\${rec.poNo}</td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty Received</th><th style="border:1px solid #000;padding:8px">Condition</th></tr></thead>
        <tbody><tr><td style="border:1px solid #000;padding:8px">\${rec.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">\${rec.qty}</td><td style="border:1px solid #000;padding:8px;text-align:center">\${rec.condition}</td></tr></tbody>
      </table>
      <div style="margin-top:40px;display:flex;justify-content:space-between">
        <div style="width:45%;border-top:1px solid #000;text-align:center;padding-top:8px">Received By / Signature</div>
        <div style="width:45%;border-top:1px solid #000;text-align:center;padding-top:8px">Inspected By / Signature</div>
      </div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print Receipt</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Print Preview: Purchase Receipt', html, actions, {width:'800px'});
}

// ======================= REPORTS =======================
function openPoReportModal(reportName, data) {
  const vOpts = \`<option value="">All Vendors</option>\` + data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const bOpts = \`<option value="">All Branches</option>\` + branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Vendor</label><select>\${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select>\${bOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option><option>Open</option><option>Received</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runDemoPoReport(this, '\${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="po-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1000px, calc(100vw - 48px))'});
}

function runDemoPoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'Purchase Order Summary') {
      thead = \`<tr><th>Vendor</th><th style="text-align:center">Total POs</th><th style="text-align:center">Total Quantity</th><th>Total Amount</th><th>Open Amount</th><th>Status</th></tr>\`;
      rows = \`<tr><td><strong>Honda Philippines</strong></td><td class="mono" style="text-align:center">12</td><td class="mono" style="text-align:center">120</td><td class="amt">&#8369;8,500,000</td><td class="amt">&#8369;1,200,000</td><td>\${badge('Active')}</td></tr>\`;
    } else if (reportName === 'Purchase Order Details by Vendor' || reportName.includes('Vendor')) {
      thead = \`<tr><th>PO No.</th><th>Vendor</th><th>Item</th><th style="text-align:center">Quantity</th><th>Total Amount</th><th>Status</th></tr>\`;
      rows = data.purchaseOrders.map(p=>\`<tr><td class="mono">\${p.no}</td><td><strong>\${p.vendor}</strong></td><td>\${p.item}</td><td class="mono" style="text-align:center">\${p.qty}</td><td class="amt">&#8369;\${fmt(p.total)}</td><td>\${badge(p.status)}</td></tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No data available</td></tr>';
    } else if (reportName.includes('Inventory')) {
      thead = \`<tr><th>Item</th><th>Vendor</th><th style="text-align:center">Qty Ordered</th><th style="text-align:center">Qty Received</th><th style="text-align:center">Remaining</th><th>Total Cost</th></tr>\`;
      rows = data.purchaseOrders.map(p=>\`<tr><td>\${p.item}</td><td><strong>\${p.vendor}</strong></td><td class="mono" style="text-align:center">\${p.qty}</td><td class="mono" style="text-align:center">\${p.status==='Received'?p.qty:0}</td><td class="mono" style="text-align:center">\${p.status==='Received'?0:p.qty}</td><td class="amt">&#8369;\${fmt(p.total)}</td></tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No data available</td></tr>';
    } else if (reportName === 'Purchase Receipt Details by Vendor') {
      thead = \`<tr><th>Receipt No.</th><th>Vendor</th><th>PO No.</th><th>Item</th><th style="text-align:center">Qty Received</th><th>Receipt Date</th><th>Status</th></tr>\`;
      rows = data.purchaseReceipts.map(r=>\`<tr><td class="mono">\${r.no}</td><td><strong>\${r.vendor}</strong></td><td class="mono">\${r.poNo}</td><td>\${r.item}</td><td class="mono" style="text-align:center">\${r.qty}</td><td>\${r.date}</td><td>\${badge(r.status)}</td></tr>\`).join('');
    } else {
      thead = \`<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>\`;
      rows = \`<tr><td class="mono">REC-001</td><td class="dim">2026-05-15</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>\${badge('Active')}</td></tr>\`;
    }
    
    document.getElementById('po-report-body').innerHTML = \`<table><thead>\${thead}</thead><tbody>\${rows}</tbody></table>\`;
    resetButtonLoading(btn);
    showToast('Demo report generated successfully.');
  }, 800);
}
// --- END PURCHASES MODULE DEMO ---`;

const fileContent = fs.readFileSync('erp-business-management.html', 'utf8');
const pattern = /\/\/ --- PURCHASES MODULE FULL DEMO ---[\s\S]*?\/\/ --- END PURCHASES MODULE DEMO ---/;
const updatedContent = fileContent.replace(pattern, replacement);

fs.writeFileSync('erp-business-management.html', updatedContent, 'utf8');
console.log('Successfully updated purchases module block.');
