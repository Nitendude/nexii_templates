const fs = require('fs');

let html = fs.readFileSync('erp-business-management.html', 'utf8');

// 1. Update typePrefix
html = html.replace(
  "const typePrefix = id === 'receivables-menu' ? 'ar-' : (id === 'purchases-menu' ? 'po-' : '');",
  "const typePrefix = id === 'receivables-menu' ? 'ar-' : (id === 'purchases-menu' ? 'po-' : (id === 'payables-menu' ? 'ap-' : ''));"
);

// 2. Add global click interceptor
html = html.replace(
  "if(type && type.startsWith('po-')) {\n      openPurchasesDemo(action, type);\n      return;\n    }",
  "if(type && type.startsWith('po-')) {\n      openPurchasesDemo(action, type);\n      return;\n    }\n    if(type && type.startsWith('ap-')) {\n      openPayablesDemo(action, type);\n      return;\n    }"
);

// 3. Ensure we inject the new block right before function doLogout()
const block = `// --- PAYABLES MODULE FULL DEMO ---
function initPayablesDemoData() {
  let d = JSON.parse(localStorage.getItem('payablesDemoData'));
  if(!d) {
    d = {
      bills: [],
      adjustments: [],
      checksAndPayments: [],
      vendors: [
        { id: 'VND-001', name: 'Honda Philippines', contact: 'Juan Dela Cruz', phone: '09171112222', email: 'sales@hondaph.com', address: 'Batangas', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-002', name: 'Yamaha Motor Philippines', contact: 'Maria Santos', phone: '09182223333', email: 'orders@yamaha-motor.com.ph', address: 'Laguna', terms: 'NET15', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-003', name: 'Kawasaki Motors Philippines', contact: 'Pedro Reyes', phone: '09193334444', email: 'info@kawasaki.ph', address: 'Muntinlupa', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-004', name: 'Suzuki Philippines', contact: 'Ana Lim', phone: '09204445555', email: 'sales@suzuki.com.ph', address: 'Laguna', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-005', name: 'TVS Motor Philippines', contact: 'Carlos Chua', phone: '09215556666', email: 'contact@tvs.ph', address: 'Manila', terms: 'CASH', type: 'Distributor', status: 'Active' },
        { id: 'VND-006', name: 'Office Supplies PH', contact: 'John Doe', phone: '09223334444', email: 'sales@officesupplies.ph', address: 'Makati', terms: 'NET15', type: 'Supplier', status: 'Active' },
        { id: 'VND-007', name: 'Logistics Partner Inc.', contact: 'Jane Smith', phone: '09334445555', email: 'ops@logistics.ph', address: 'Pasay', terms: 'NET30', type: 'Service', status: 'Active' },
        { id: 'VND-008', name: 'Utility Provider', contact: 'Customer Service', phone: '1622-0000', email: 'billing@utility.ph', address: 'Quezon City', terms: 'Due on Receipt', type: 'Service', status: 'Active' }
      ],
      creditTerms: [
        { code: 'NET15', desc: 'Net 15 Days', dueDays: 15, discount: '0%', status: 'Active' },
        { code: 'NET30', desc: 'Net 30 Days', dueDays: 30, discount: '0%', status: 'Active' },
        { code: 'CASH', desc: 'Cash on Delivery', dueDays: 0, discount: '0%', status: 'Active' },
        { code: 'DUE-REC', desc: 'Due on Receipt', dueDays: 0, discount: '0%', status: 'Active' }
      ],
      paymentBatches: [],
      intercompanyDocuments: [],
      logs: []
    };
    
    // Generate some demo bills
    d.bills.push({ no: 'BILL-1001', date: '2026-05-10', due: '2026-06-09', vendor: 'Honda Philippines', branch: 'Manila Main', desc: 'Motorcycle unit purchase', amount: 850000, paid: 0, balance: 850000, status: 'Open' });
    d.bills.push({ no: 'BILL-1002', date: '2026-05-12', due: '2026-05-27', vendor: 'Yamaha Motor Philippines', branch: 'Quezon City North', desc: 'Spare parts purchase', amount: 150000, paid: 0, balance: 150000, status: 'Open' });
    d.bills.push({ no: 'BILL-1003', date: '2026-05-14', due: '2026-05-29', vendor: 'Office Supplies PH', branch: 'Makati Ayala', desc: 'Office supplies', amount: 12500, paid: 12500, balance: 0, status: 'Closed' });
    d.bills.push({ no: 'BILL-1004', date: '2026-05-01', due: '2026-05-15', vendor: 'Utility Provider', branch: 'Manila Main', desc: 'Utilities', amount: 45000, paid: 0, balance: 45000, status: 'Open' });
    
    // Generate demo payments
    d.checksAndPayments.push({ no: 'PAY-5001', date: '2026-05-15', vendor: 'Office Supplies PH', billNo: 'BILL-1003', method: 'Check', bank: 'BDO-Main', ref: 'CHK-00123', amount: 12500, status: 'Released' });

    localStorage.setItem('payablesDemoData', JSON.stringify(d));
  }
  return d;
}

function updatePayablesLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('payablesDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
    }
  }
}

function openPayablesDemo(action, type) {
  const data = initPayablesDemoData();
  
  if(type === 'ap-shortcut') {
    if(action === 'New Bill') openApNewBill(data);
    else if(action === 'New Payment') openApNewPayment(data);
    else if(action === 'Vendor Details') openApVendors(data); // Reuses the vendor list
    else if(action === 'New Vendor') openApNewVendor(data);
  }
  else if(type === 'ap-transaction') {
    if(action === 'Bills and Adjustments') openApBills(data);
    else if(action === 'Checks and Payments') openApPayments(data);
  }
  else if(type === 'ap-profile') {
    if(action === 'Vendors') openApVendors(data);
    else if(action === 'Credit Terms') openApCreditTerms(data);
  }
  else if(type === 'ap-process') {
    if(action === 'Release AP Documents') openApProcessReleaseDocs(data);
    else if(action === 'Prepare Payments') openApProcessPreparePayments(data);
    else if(action === 'Process Payments / Print Checks') openApProcessProcessPayments(data);
    else if(action === 'Release Payments') openApProcessReleasePayments(data);
    else if(action === 'Generate Intercompany Documents') openApProcessIntercompany(data);
    else if(action === 'Close Financial Periods') openApProcessClosePeriod(data);
  }
  else if(type === 'ap-inquiry') {
    if(action === 'Vendor Details') openApVendors(data); // Fallback to vendors profile
    else if(action === 'Vendor Summary') openApVendorSummary(data);
  }
  else if(type === 'ap-report') {
    openApReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openApNewBill(data) {
  const vOpts = data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="ab-vnd">\${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select id="ab-branch">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Bill Date</label><input type="date" id="ab-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Due Date</label><input type="date" id="ab-due" value="2026-06-15"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="ab-ref" placeholder="Vendor Invoice No."></div>
      <div class="demo-form-row"><label>Description</label><input type="text" id="ab-desc" placeholder="E.g. Motorcycle unit purchase"></div>
      <div class="demo-form-row"><label>Expense Account</label><select><option>5000 - Cost of Goods Sold</option><option>6000 - Operating Expenses</option></select></div>
      <div class="demo-form-row"><label>Amount</label><input type="number" id="ab-amt" value="0"></div>
      <div class="demo-form-row"><label>Tax Amount</label><input type="number" id="ab-tax" value="0"></div>
      <div class="demo-form-row"><label>Terms</label><select id="ab-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="ab-rem" placeholder="Optional notes"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitApNewBill(this)">Submit Bill</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>\`;
  openCenterModal('New Bill', html, actions);
}

function submitApNewBill(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const no = 'BILL-' + String(data.bills.length + 1001);
    const amt = parseFloat(document.getElementById('ab-amt').value)||0;
    data.bills.unshift({
      no,
      date: document.getElementById('ab-date').value,
      due: document.getElementById('ab-due').value,
      vendor: document.getElementById('ab-vnd').value,
      branch: document.getElementById('ab-branch').value,
      desc: document.getElementById('ab-desc').value || 'AP Bill',
      amount: amt, paid: 0, balance: amt,
      status: 'Open'
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo bill submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openApNewPayment(data) {
  const vOpts = data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const openBills = data.bills.filter(b=>b.status!=='Closed');
  const bOpts = openBills.map(b=>\`<option value="\${b.no}">\${b.no} - &#8369;\${fmt(b.balance)} (\${b.vendor})</option>\`).join('') || '<option value="">No Open Bills</option>';
  
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="apy-vnd">\${vOpts}</select></div>
      <div class="demo-form-row"><label>Bill No.</label><select id="apy-bill" onchange="document.getElementById('apy-amt').value = JSON.parse(localStorage.getItem('payablesDemoData')).bills.find(x=>x.no===this.value)?.balance||0">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Payment Date</label><input type="date" id="apy-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Payment Method</label><select id="apy-met"><option>Check</option><option>Bank Transfer</option><option>Cash</option></select></div>
      <div class="demo-form-row"><label>Bank Account</label><select id="apy-bank"><option>BDO-Main</option><option>BPI-Operations</option></select></div>
      <div class="demo-form-row"><label>Reference / Check No.</label><input type="text" id="apy-ref" placeholder="CHK-XXXXX"></div>
      <div class="demo-form-row"><label>Amount Paid</label><input type="number" id="apy-amt" value="\${openBills[0]?.balance||0}"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="apy-rem"></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitApNewPayment(this)">Save Payment</button><button class="btn btn-sm" onclick="showToast('Voucher printed');">Print Voucher</button>\`;
  openCenterModal('New Payment', html, actions);
}

function submitApNewPayment(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const billNo = document.getElementById('apy-bill').value;
    const amt = parseFloat(document.getElementById('apy-amt').value)||0;
    
    data.checksAndPayments.unshift({
      no: 'PAY-' + String(data.checksAndPayments.length + 5001),
      date: document.getElementById('apy-date').value,
      vendor: document.getElementById('apy-vnd').value,
      billNo,
      method: document.getElementById('apy-met').value,
      bank: document.getElementById('apy-bank').value,
      ref: document.getElementById('apy-ref').value,
      amount: amt,
      status: 'Released'
    });
    
    const bill = data.bills.find(b=>b.no===billNo);
    if(bill) {
      bill.paid += amt;
      bill.balance -= amt;
      if(bill.balance <= 0) bill.status = 'Closed';
    }
    
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo vendor payment recorded successfully.');
    closeCenterModal();
  }, 600);
}

function openApNewVendor(data) {
  const html = \`
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="nv-name"></div>
      <div class="demo-form-row"><label>Contact Person</label><input type="text" id="nv-contact"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="nv-phone"></div>
      <div class="demo-form-row"><label>Email</label><input type="email" id="nv-email"></div>
      <div class="demo-form-row"><label>Address</label><input type="text" id="nv-addr"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="nv-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row"><label>Vendor Type</label><select id="nv-type"><option>Manufacturer</option><option>Distributor</option><option>Service</option></select></div>
      <div class="demo-form-row"><label>Tax ID</label><input type="text" id="nv-tax"></div>
      <div class="demo-form-row"><label>Status</label><select id="nv-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm btn-primary" onclick="submitApNewVendor(this)">Save Vendor</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>\`;
  openCenterModal('New Vendor', html, actions);
}

function submitApNewVendor(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const id = 'VND-' + String(data.vendors.length + 1).padStart(3, '0');
    data.vendors.push({
      id,
      name: document.getElementById('nv-name').value || 'New Vendor',
      contact: document.getElementById('nv-contact').value,
      phone: document.getElementById('nv-phone').value,
      email: document.getElementById('nv-email').value,
      address: document.getElementById('nv-addr').value,
      terms: document.getElementById('nv-terms').value,
      type: document.getElementById('nv-type').value,
      status: document.getElementById('nv-status').value
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo vendor saved successfully.');
    // If called from the Vendors modal, it might be nice to refresh, but closeCenterModal is safe.
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function openApBills(data) {
  const rows = data.bills.map(b=>\`<tr>
    <td class="mono">\${b.no}</td><td class="dim">\${b.date}</td><td class="dim">\${b.due}</td>
    <td><strong>\${b.vendor}</strong></td><td>\${b.branch}</td><td>\${b.desc}</td>
    <td class="amt">&#8369;\${fmt(b.amount)}</td><td class="amt">&#8369;\${fmt(b.paid)}</td><td class="amt">&#8369;\${fmt(b.balance)}</td>
    <td>\${badge(b.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Bill...')">View Bill</button>
      \${b.status==='Open' ? \`<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePayablesLocalData('bills','no','\${b.no}',{status:'Ready for Payment'});showToast('Bill marked for payment.');openApBills(JSON.parse(localStorage.getItem('payablesDemoData')))">Mark for Payment</button>\` : ''}
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Adjusting Bill...')">Adjust</button>
    </td>
  </tr>\`).join('') || '<tr><td colspan="11" style="text-align:center;color:var(--text-tertiary)">No bills</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Bill No.</th><th>Date</th><th>Due Date</th><th>Vendor</th><th>Branch</th><th>Description</th><th>Original Amt</th><th>Paid Amt</th><th>Balance</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('bills.csv', JSON.parse(localStorage.getItem('payablesDemoData')).bills)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Bills and Adjustments', html, actions, {width: 'min(1250px, calc(100vw - 48px))'});
}

function openApPayments(data) {
  const rows = data.checksAndPayments.map(p=>\`<tr>
    <td class="mono">\${p.no}</td><td class="dim">\${p.date}</td><td><strong>\${p.vendor}</strong></td>
    <td class="mono">\${p.billNo}</td><td>\${p.method}</td><td>\${p.bank}</td><td class="mono">\${p.ref}</td>
    <td class="amt">&#8369;\${fmt(p.amount)}</td><td>\${badge(p.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Payment')">View Payment</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing Voucher...')">Print Voucher</button>
      \${p.status!=='Voided' ? \`<button class="btn btn-sm" style="font-size:10px;padding:2px 6px;color:red" onclick="voidApPayment('\${p.no}')">Void Demo Payment</button>\` : ''}
    </td>
  </tr>\`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No payments</td></tr>';
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Payment No.</th><th>Date</th><th>Vendor</th><th>Bill No.</th><th>Method</th><th>Bank Account</th><th>Ref/Check No.</th><th>Amount Paid</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('payments.csv', JSON.parse(localStorage.getItem('payablesDemoData')).checksAndPayments)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Checks and Payments', html, actions, {width: '1150px'});
}

function voidApPayment(no) {
  let data = JSON.parse(localStorage.getItem('payablesDemoData'));
  let p = data.checksAndPayments.find(x=>x.no===no);
  if(!p) return;
  p.status = 'Voided';
  let b = data.bills.find(x=>x.no===p.billNo);
  if(b) {
    b.paid -= p.amount;
    b.balance += p.amount;
    if(b.balance > 0 && b.status==='Closed') b.status = 'Open';
  }
  localStorage.setItem('payablesDemoData', JSON.stringify(data));
  showToast('Demo payment voided and balance restored.');
  openApPayments(data);
}

// ======================= PROFILES =======================
function openApVendors(data) {
  const rows = data.vendors.map(v=>{
    const bal = data.bills.filter(b=>b.vendor===v.name).reduce((sum,b)=>sum+b.balance,0);
    return \`<tr>
      <td class="mono">\${v.id}</td><td><strong>\${v.name}</strong></td><td>\${v.contact}</td><td class="mono dim">\${v.phone}</td>
      <td class="dim">\${v.email}</td><td class="mono">\${v.terms}</td><td class="amt">\${bal>0?('&#8369;'+fmt(bal)):''}</td><td>\${badge(v.status)}</td>
      <td style="text-align:center"><button class="btn btn-sm" onclick="openApVendorProfile('\${v.id}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
    </tr>\`;
  }).join('');
  
  const html = \`<div class="table-wrap"><table>
    <thead><tr><th>Vendor ID</th><th>Vendor Name</th><th>Contact Person</th><th>Contact Number</th><th>Email</th><th>Terms</th><th>Outstanding Bal</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('ap-vendors.csv', JSON.parse(localStorage.getItem('payablesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Vendors', html, actions, {width: '1050px'});
}

function openApVendorProfile(id) {
  const data = JSON.parse(localStorage.getItem('payablesDemoData'));
  const v = data.vendors.find(x=>x.id===id);
  if(!v) return;
  
  const bills = data.bills.filter(b=>b.vendor===v.name);
  const pays = data.checksAndPayments.filter(p=>p.vendor===v.name);
  const totalBills = bills.reduce((sum,b)=>sum+b.amount,0);
  const totalPaid = bills.reduce((sum,b)=>sum+b.paid,0);
  const outstanding = bills.reduce((sum,b)=>sum+b.balance,0);
  
  const billHtml = bills.length ? bills.map(b=>\`<tr><td class="mono">\${b.no}</td><td class="dim">\${b.due}</td><td class="amt">&#8369;\${fmt(b.amount)}</td><td class="amt">&#8369;\${fmt(b.paid)}</td><td class="amt">&#8369;\${fmt(b.balance)}</td><td>\${badge(b.status)}</td></tr>\`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No bills</td></tr>';
  const payHtml = pays.length ? pays.map(p=>\`<tr><td class="mono">\${p.no}</td><td class="dim">\${p.date}</td><td class="mono">\${p.billNo}</td><td>\${p.method}</td><td class="mono">\${p.ref}</td><td class="amt">&#8369;\${fmt(p.amount)}</td><td>\${badge(p.status)}</td></tr>\`).join('') : '<tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No payments</td></tr>';

  const html = \`
    <div class="sp-section" id="ap-vnd-view-mode">
      <div class="sp-section-label">Vendor Information</div>
      <div class="sp-row"><span class="sp-key">Vendor ID</span><span class="sp-val mono">\${v.id}</span></div>
      <div class="sp-row"><span class="sp-key">Vendor Name</span><span class="sp-val"><strong>\${v.name}</strong></span></div>
      <div class="sp-row"><span class="sp-key">Contact</span><span class="sp-val">\${v.contact} - \${v.phone}</span></div>
      <div class="sp-row"><span class="sp-key">Email</span><span class="sp-val">\${v.email}</span></div>
      <div class="sp-row"><span class="sp-key">Address</span><span class="sp-val">\${v.address||'-'}</span></div>
      <div class="sp-row"><span class="sp-key">Type / Tax ID</span><span class="sp-val">\${v.type||'-'} / \${v.tax||'-'}</span></div>
      <div class="sp-row"><span class="sp-key">Payment Terms</span><span class="sp-val mono">\${v.terms}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">\${badge(v.status)}</span></div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:15px;background:#f9f9f9;padding:10px;border-radius:6px;border:1px solid var(--border)">
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Total Bills</div><div class="amt" style="font-size:15px">&#8369;\${fmt(totalBills)}</div></div>
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Total Paid</div><div class="amt" style="font-size:15px;color:var(--green)">&#8369;\${fmt(totalPaid)}</div></div>
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Outstanding Balance</div><div class="amt" style="font-size:15px;color:var(--red)">&#8369;\${fmt(outstanding)}</div></div>
      </div>
    </div>
    
    <div class="sp-section" id="ap-vnd-edit-mode" style="display:none">
      <div class="demo-form-grid">
        <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="evap-name" value="\${v.name}"></div>
        <div class="demo-form-row"><label>Contact Person</label><input type="text" id="evap-contact" value="\${v.contact}"></div>
        <div class="demo-form-row"><label>Contact Number</label><input type="text" id="evap-phone" value="\${v.phone}"></div>
        <div class="demo-form-row"><label>Email</label><input type="email" id="evap-email" value="\${v.email}"></div>
        <div class="demo-form-row"><label>Payment Terms</label><input type="text" id="evap-terms" value="\${v.terms}"></div>
        <div class="demo-form-row"><label>Status</label><select id="evap-status"><option \${v.status==='Active'?'selected':''}>Active</option><option \${v.status==='Inactive'?'selected':''}>Inactive</option></select></div>
        <div class="demo-form-row" style="grid-column:1/-1;display:flex;gap:10px">
          <button class="btn btn-sm btn-primary" onclick="saveApEditVendor('\${v.id}')">Save Changes</button>
          <button class="btn btn-sm" onclick="document.getElementById('ap-vnd-edit-mode').style.display='none';document.getElementById('ap-vnd-view-mode').style.display='block';">Cancel</button>
        </div>
      </div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Open Bills</div>
      <div class="table-wrap"><table><thead><tr><th>Bill No.</th><th>Due Date</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead><tbody>\${billHtml}</tbody></table></div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Payment History</div>
      <div class="table-wrap"><table><thead><tr><th>Payment No.</th><th>Date</th><th>Bill No.</th><th>Method</th><th>Ref No.</th><th>Amount</th><th>Status</th></tr></thead><tbody>\${payHtml}</tbody></table></div>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm" onclick="document.getElementById('ap-vnd-view-mode').style.display='none';document.getElementById('ap-vnd-edit-mode').style.display='block';">Edit Demo Vendor</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Vendor...');">Export Vendor CSV</button>
    <button class="btn btn-sm" onclick="showToast('Profile printed');setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm btn-primary" onclick="openApVendors(JSON.parse(localStorage.getItem('payablesDemoData')))">Close</button>
  \`;
  openCenterModal('Vendor Profile: ' + v.name, html, actions, {width: '950px'});
}

function saveApEditVendor(id) {
  updatePayablesLocalData('vendors', 'id', id, {
    name: document.getElementById('evap-name').value,
    contact: document.getElementById('evap-contact').value,
    phone: document.getElementById('evap-phone').value,
    email: document.getElementById('evap-email').value,
    terms: document.getElementById('evap-terms').value,
    status: document.getElementById('evap-status').value
  });
  showToast('Demo vendor updated successfully.');
  openApVendorProfile(id);
}

function openApCreditTerms(data) {
  const rows = data.creditTerms.map(t=>\`<tr>
    <td class="mono"><strong>\${t.code}</strong></td><td>\${t.desc}</td><td class="mono" style="text-align:center">\${t.dueDays}</td>
    <td class="mono" style="text-align:center">\${t.discount}</td><td>\${badge(t.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Editing Term...')">Edit Term</button></td>
  </tr>\`).join('');
  const html = \`<div class="table-wrap"><table><thead><tr><th>Term Code</th><th>Description</th><th style="text-align:center">Due Days</th><th style="text-align:center">Discount</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>\${rows}</tbody></table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('credit-terms.csv', JSON.parse(localStorage.getItem('payablesDemoData')).creditTerms)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Credit Terms', html, actions, {width: '800px'});
}

// ======================= PROCESSES =======================
function openApProcessReleaseDocs(data) {
  const bills = data.bills.filter(b=>b.status==='Open');
  const rows = bills.map(b=>\`<tr>
    <td class="mono">\${b.no}</td><td><strong>\${b.vendor}</strong></td><td>Bill</td>
    <td class="amt">&#8369;\${fmt(b.amount)}</td><td>\${badge('Pending Release')}</td>
  </tr>\`).join('') || '<tr><td colspan="5" style="text-align:center;color:var(--text-tertiary)">No pending documents to release</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All Vendors</option></select></div>
      <div class="demo-form-row"><label>Document Status</label><select><option>Unreleased</option></select></div>
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Document No.</th><th>Vendor</th><th>Document Type</th><th>Amount</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApProcessReleaseDocs(this)" \${bills.length===0?'disabled':''}>Release Documents</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Release AP Documents', html, actions, {width: '850px'});
}

function runApProcessReleaseDocs(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    data.bills.filter(b=>b.status==='Open').forEach(b => b.status = 'Released');
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Documents Released';
    btn.disabled = true;
    showToast('Demo AP documents released successfully.');
  }, 1000);
}

function openApProcessPreparePayments(data) {
  // Pull bills that are Open/Released and have balance
  const bills = data.bills.filter(b=>b.balance > 0 && b.status!=='Closed' && b.status!=='Prepared');
  const rows = bills.map(b=>\`<tr>
    <td class="mono">\${b.no}</td><td><strong>\${b.vendor}</strong></td><td class="dim">\${b.due}</td>
    <td class="amt">&#8369;\${fmt(b.balance)}</td><td>Check</td><td>\${badge('Pending')}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No open bills available</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Vendor</label><select><option>All Vendors</option></select></div>
      <div class="demo-form-row"><label>Due Date Cutoff</label><input type="date" value="2026-06-30"></div>
      <div class="demo-form-row"><label>Bank Account</label><select><option>BDO-Main</option><option>BPI-Operations</option></select></div>
      <div class="demo-form-row"><label>Payment Method</label><select><option>Check</option><option>Transfer</option></select></div>
      <div class="demo-form-row"><label>Minimum Amount</label><input type="number" value="0"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Bill No.</th><th>Vendor</th><th>Due Date</th><th>Balance</th><th>Payment Method</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApProcessPreparePayments(this)" \${bills.length===0?'disabled':''}>Prepare Payment Batch</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Batch...')">Export Batch</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Prepare Payments', html, actions, {width: '900px'});
}

function runApProcessPreparePayments(btn) {
  setButtonLoading(btn, 'Preparing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const bills = data.bills.filter(b=>b.balance > 0 && b.status!=='Closed' && b.status!=='Prepared');
    
    if(bills.length) {
      const batchNo = 'BAT-' + String(data.paymentBatches.length + 100);
      data.paymentBatches.push({ no: batchNo, status: 'Prepared', bills: bills.map(b=>b.no) });
      bills.forEach(b => b.status = 'Prepared');
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
      showToast('Demo payment batch prepared successfully.');
    }
    
    resetButtonLoading(btn);
    btn.textContent = 'Batch Prepared';
    btn.disabled = true;
  }, 1000);
}

function openApProcessProcessPayments(data) {
  const batch = data.paymentBatches.find(b=>b.status==='Prepared');
  let rows = '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No prepared batches found. Run Prepare Payments first.</td></tr>';
  let batchStr = '<option>None</option>';
  if(batch) {
    batchStr = \`<option>\${batch.no}</option>\`;
    const batchBills = data.bills.filter(b=>batch.bills.includes(b.no));
    rows = batchBills.map((b,i)=>\`<tr>
      <td class="mono">PAY-\${5100+i}</td><td><strong>\${b.vendor}</strong></td><td class="mono">\${b.no}</td>
      <td class="amt">&#8369;\${fmt(b.balance)}</td><td class="mono">CHK-\${10020+i}</td><td>\${badge('Prepared')}</td>
    </tr>\`).join('');
  }
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Payment Batch</label><select>\${batchStr}</select></div>
      <div class="demo-form-row"><label>Bank Account</label><select><option>BDO-Main</option></select></div>
      <div class="demo-form-row"><label>Check Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Starting Check No.</label><input type="text" value="CHK-10020"></div>
      <div class="demo-form-row"><label>Print Option</label><select><option>Print Checks</option><option>Export PDF File</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Payment No.</th><th>Vendor</th><th>Bill No.</th><th>Amount</th><th>Check No.</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApProcessProcessPayments(this)" \${!batch?'disabled':''}>Process Payments</button>
    <button class="btn btn-sm" onclick="showToast('Checks sent to printer.')" \${!batch?'disabled':''}>Print Checks</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Process Payments / Print Checks', html, actions, {width: '900px'});
}

function runApProcessProcessPayments(btn) {
  setButtonLoading(btn, 'Processing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const batch = data.paymentBatches.find(b=>b.status==='Prepared');
    if(batch) {
      batch.status = 'Processed';
      const batchBills = data.bills.filter(b=>batch.bills.includes(b.no));
      batchBills.forEach((b,i) => {
        data.checksAndPayments.unshift({
          no: 'PAY-' + String(5100+i),
          date: '2026-05-16',
          vendor: b.vendor,
          billNo: b.no,
          method: 'Check',
          bank: 'BDO-Main',
          ref: 'CHK-' + String(10020+i),
          amount: b.balance,
          status: 'Processed'
        });
        b.status = 'Payment Processed';
      });
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
      showToast('Demo payments processed successfully.');
    }
    resetButtonLoading(btn);
    btn.textContent = 'Payments Processed';
    btn.disabled = true;
  }, 1200);
}

function openApProcessReleasePayments(data) {
  const processed = data.checksAndPayments.filter(p=>p.status==='Processed');
  const rows = processed.map(p=>\`<tr>
    <td class="mono">\${p.no}</td><td><strong>\${p.vendor}</strong></td><td class="amt">&#8369;\${fmt(p.amount)}</td>
    <td class="mono">\${p.billNo}</td><td>\${badge('Processed')}</td>
  </tr>\`).join('') || '<tr><td colspan="5" style="text-align:center;color:var(--text-tertiary)">No processed payments waiting for release.</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Payment Batch</label><select><option>All Unreleased</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Payment Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Status</label><select><option>Processed</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Payment No.</th><th>Vendor</th><th>Amount</th><th>Bill No.</th><th>Release Status</th></tr></thead><tbody>\${rows}</tbody></table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApProcessReleasePayments(this)" \${processed.length===0?'disabled':''}>Release Payments</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Release Payments', html, actions, {width: '850px'});
}

function runApProcessReleasePayments(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const processed = data.checksAndPayments.filter(p=>p.status==='Processed');
    processed.forEach(p => {
      p.status = 'Released';
      const b = data.bills.find(x=>x.no===p.billNo);
      if(b) {
        b.paid += p.amount;
        b.balance -= p.amount;
        if(b.balance <= 0) b.status = 'Closed';
      }
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Payments Released';
    btn.disabled = true;
    showToast('Demo payments released successfully.');
  }, 1000);
}

function openApProcessIntercompany(data) {
  const bOpts = branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  const rows = data.intercompanyDocuments.map(d=>\`<tr>
    <td class="mono">\${d.no}</td><td>\${d.source}</td><td>\${d.dest}</td><td>\${d.vendor}</td>
    <td class="amt">&#8369;\${fmt(d.amt)}</td><td>\${badge(d.status)}</td>
  </tr>\`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No intercompany documents</td></tr>';
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Source Branch</label><select id="api-src">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Destination Branch</label><select id="api-dest">\${bOpts}</select></div>
      <div class="demo-form-row"><label>Vendor / Internal</label><select id="api-vnd"><option>NEXII HQ</option><option>Interbranch Logistics</option></select></div>
      <div class="demo-form-row"><label>Transaction Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Reason</label><input type="text" id="api-rsn" placeholder="Cost allocation"></div>
      <div class="demo-form-row"><label>Amount</label><input type="number" id="api-amt" value="25000"></div>
    </div>
    <div class="table-wrap"><table><thead><tr><th>Intercompany Doc No.</th><th>Source Branch</th><th>Destination Branch</th><th>Vendor</th><th>Amount</th><th>Status</th></tr></thead><tbody>\${rows}</tbody></table></div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApIntercompany(this)">Generate Intercompany Documents</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Log...')">Export Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Generate Intercompany Documents', html, actions, {width: '900px'});
}

function runApIntercompany(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    data.intercompanyDocuments.unshift({
      no: 'ICD-' + String(data.intercompanyDocuments.length + 100),
      source: document.getElementById('api-src').value,
      dest: document.getElementById('api-dest').value,
      vendor: document.getElementById('api-vnd').value,
      amt: parseFloat(document.getElementById('api-amt').value)||0,
      status: 'Generated'
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    showToast('Demo intercompany AP documents generated successfully.');
    openApProcessIntercompany(data);
  }, 800);
}

function openApProcessClosePeriod(data) {
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Financial Period</label><select><option>05-2026</option></select></div>
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Module</label><select><option>Accounts Payable</option></select></div>
      <div class="demo-form-row"><label>Closing Option</label><select><option>Soft Close</option><option>Hard Close</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table>
        <thead><tr><th>Validation Item</th><th>Result</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>Unreleased AP documents</td><td class="dim" id="val-1">-</td><td id="stat-1">\${badge('Pending')}</td></tr>
          <tr><td>Unprocessed payments</td><td class="dim" id="val-2">-</td><td id="stat-2">\${badge('Pending')}</td></tr>
          <tr><td>Open payment batches</td><td class="dim" id="val-3">-</td><td id="stat-3">\${badge('Pending')}</td></tr>
          <tr><td>Vendor balance validation</td><td class="dim" id="val-4">-</td><td id="stat-4">\${badge('Pending')}</td></tr>
          <tr><td>GL posting check</td><td class="dim" id="val-5">-</td><td id="stat-5">\${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  \`;
  const actions = \`
    <button class="btn btn-sm btn-primary" onclick="runApValidatePeriod(this)" id="ap-val-btn">Validate Period</button>
    <button class="btn btn-sm btn-primary" onclick="runApClosePeriod(this)" id="ap-clo-btn" disabled>Close Period</button>
    <button class="btn btn-sm" onclick="showToast('Printing Log...')">Print Closing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  \`;
  openCenterModal('Close Financial Periods', html, actions, {width: '800px'});
}

function runApValidatePeriod(btn) {
  setButtonLoading(btn, 'Validating...');
  setTimeout(() => {
    document.getElementById('val-1').textContent = '0 Documents'; document.getElementById('stat-1').innerHTML = badge('OK');
    document.getElementById('val-2').textContent = '0 Payments'; document.getElementById('stat-2').innerHTML = badge('OK');
    document.getElementById('val-3').textContent = 'None'; document.getElementById('stat-3').innerHTML = badge('OK');
    document.getElementById('val-4').textContent = 'Balanced'; document.getElementById('stat-4').innerHTML = badge('OK');
    document.getElementById('val-5').textContent = 'Posted'; document.getElementById('stat-5').innerHTML = badge('OK');
    resetButtonLoading(btn);
    btn.textContent = 'Validated';
    btn.disabled = true;
    document.getElementById('ap-clo-btn').disabled = false;
    showToast('Period validation complete. Ready to close.');
  }, 1500);
}

function runApClosePeriod(btn) {
  setButtonLoading(btn, 'Closing Period...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Period Closed';
    btn.disabled = true;
    showToast('Demo AP financial period closed successfully.');
  }, 1000);
}

// ======================= INQUIRIES =======================
function openApVendorSummary(data) {
  const rows = data.vendors.map(v=>{
    const bills = data.bills.filter(b=>b.vendor===v.name);
    const tb = bills.reduce((sum,b)=>sum+b.amount,0);
    const tp = bills.reduce((sum,b)=>sum+b.paid,0);
    const ob = bills.reduce((sum,b)=>sum+b.balance,0);
    return \`<tr>
      <td><strong>\${v.name}</strong></td><td class="amt">&#8369;\${fmt(tb)}</td><td class="amt">&#8369;\${fmt(tp)}</td>
      <td class="amt">&#8369;\${fmt(ob)}</td><td class="amt">&#8369;\${fmt(ob*0.4)}</td><td class="amt">&#8369;\${fmt(ob*0.3)}</td>
      <td class="amt">&#8369;\${fmt(ob*0.2)}</td><td class="amt">&#8369;\${fmt(ob*0.1)}</td><td>\${badge(v.status)}</td>
    </tr>\`;
  }).join('');
  
  const html = \`
    <div style="margin-bottom:12px;display:flex;gap:10px">
      <input type="text" placeholder="Search Vendor..." style="padding:6px 10px;border:1px solid var(--border);border-radius:4px;flex:1">
      <button class="btn btn-sm">Search</button>
    </div>
    <div class="table-wrap"><table>
    <thead><tr><th>Vendor</th><th>Total Bills</th><th>Total Paid</th><th>Outstanding Bal</th><th>Current</th><th>1-30 Days</th><th>31-60 Days</th><th>61+ Days</th><th>Status</th></tr></thead>
    <tbody>\${rows}</tbody>
  </table></div>\`;
  const actions = \`<button class="btn btn-sm" onclick="exportRowsToCsv('vendor-summary.csv', JSON.parse(localStorage.getItem('payablesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Summary...')">Print Summary</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Vendor Summary', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

// ======================= REPORTS =======================
function openApReportModal(reportName, data) {
  const vOpts = \`<option value="">All Vendors</option>\` + data.vendors.map(v=>\`<option value="\${v.name}">\${v.name}</option>\`).join('');
  const bOpts = \`<option value="">All Branches</option>\` + branchData.slice(0,10).map(b=>\`<option value="\${b.name}">\${b.name}</option>\`).join('');
  
  const html = \`
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Vendor</label><select>\${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select>\${bOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option><option>Open</option><option>Closed</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runApDemoReport(this, '\${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="ap-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  \`;
  const actions = \`<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>\`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1050px, calc(100vw - 48px))'});
}

function runApDemoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'AP Balance by GL Account') {
      thead = \`<tr><th>GL Account</th><th>Account Name</th><th style="text-align:center">Vendor Count</th><th style="text-align:center">Bill Count</th><th>Total Balance</th><th>Status</th></tr>\`;
      rows = \`<tr><td class="mono">20000</td><td>Accounts Payable - Trade</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">12</td><td class="amt">&#8369;1,250,000</td><td>\${badge('Active')}</td></tr>
              <tr><td class="mono">20100</td><td>Accounts Payable - Non-Trade</td><td class="mono" style="text-align:center">3</td><td class="mono" style="text-align:center">4</td><td class="amt">&#8369;45,000</td><td>\${badge('Active')}</td></tr>\`;
    } else if (reportName === 'AP Balance by Vendor') {
      thead = \`<tr><th>Vendor</th><th style="text-align:center">Total Bills</th><th>Total Paid</th><th>Outstanding Balance</th><th>Status</th></tr>\`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const tb = bills.reduce((sum,b)=>sum+b.amount,0);
        const tp = bills.reduce((sum,b)=>sum+b.paid,0);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(tb===0) return '';
        return \`<tr><td><strong>\${v.name}</strong></td><td class="mono" style="text-align:center">\${bills.length}</td><td class="amt">&#8369;\${fmt(tp)}</td><td class="amt">&#8369;\${fmt(ob)}</td><td>\${badge('Active')}</td></tr>\`;
      }).join('');
    } else if (reportName === 'AP Aging') {
      thead = \`<tr><th>Vendor</th><th>Current</th><th>1-30 Days</th><th>31-60 Days</th><th>61-90 Days</th><th>Over 90 Days</th><th>Total Balance</th></tr>\`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(ob===0) return '';
        return \`<tr><td><strong>\${v.name}</strong></td><td class="amt">&#8369;\${fmt(ob*0.4)}</td><td class="amt">&#8369;\${fmt(ob*0.3)}</td><td class="amt">&#8369;\${fmt(ob*0.2)}</td><td class="amt">&#8369;\${fmt(ob*0.1)}</td><td class="amt">&#8369;0</td><td class="amt" style="font-weight:bold">&#8369;\${fmt(ob)}</td></tr>\`;
      }).join('');
    } else if (reportName === 'AP Aged Period Sensitive') {
      thead = \`<tr><th>Vendor</th><th>Financial Period</th><th>Opening Balance</th><th>New Bills</th><th>Payments</th><th>Ending Balance</th><th>Aging Status</th></tr>\`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const tb = bills.reduce((sum,b)=>sum+b.amount,0);
        const tp = bills.reduce((sum,b)=>sum+b.paid,0);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(tb===0) return '';
        return \`<tr><td><strong>\${v.name}</strong></td><td class="mono">05-2026</td><td class="amt">&#8369;0</td><td class="amt">&#8369;\${fmt(tb)}</td><td class="amt">&#8369;\${fmt(tp)}</td><td class="amt" style="font-weight:bold">&#8369;\${fmt(ob)}</td><td>\${badge('Current')}</td></tr>\`;
      }).join('');
    } else {
      thead = \`<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>\`;
      rows = \`<tr><td class="mono">REC-001</td><td class="dim">2026-05-16</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>\${badge('Active')}</td></tr>\`;
    }
    
    document.getElementById('ap-report-body').innerHTML = \`<table><thead>\${thead}</thead><tbody>\${rows}</tbody></table>\`;
    resetButtonLoading(btn);
    showToast('Demo AP report generated successfully.');
  }, 800);
}
// --- END PAYABLES MODULE DEMO ---
`;
html = html.replace('function doLogout(){', block + '\nfunction doLogout(){');

fs.writeFileSync('erp-business-management.html', html, 'utf8');
console.log('Successfully injected payables module demo block.');
