const app = document.querySelector("#erpApp");

const money = new Intl.NumberFormat("en-PH", {
  style: "currency",
  currency: "PHP",
  maximumFractionDigits: 0
});

const workspaces = {
  Favorites: {
    code: "FAV",
    forms: ["Executive Dashboard", "Approval Map", "Cash Position", "Sales Orders"],
    categories: {
      Dashboards: ["Executive Dashboard", "Department KPI Board"],
      "Pinned Forms": ["Approval Map", "Cash Position", "Open Invoices"]
    }
  },
  Finance: {
    code: "FIN",
    forms: ["Cash Position", "AP Bills", "AR Invoices", "Budget Review"],
    categories: {
      Transactions: ["AP Bills", "AR Invoices", "Journal Transactions", "Payment Runs"],
      Reports: ["Trial Balance", "Cash Position", "Budget Review", "Aging Report"]
    }
  },
  Distribution: {
    code: "DST",
    forms: ["Inventory Summary", "Sales Orders", "Purchase Orders", "Warehouse Transfers"],
    categories: {
      Inventory: ["Inventory Summary", "Stock Items", "Warehouse Transfers"],
      Orders: ["Sales Orders", "Purchase Orders", "Shipments", "Receipts"]
    }
  },
  Projects: {
    code: "PRJ",
    forms: ["Project Portfolio", "Change Orders", "Time and Expenses", "Project Billing"],
    categories: {
      Management: ["Project Portfolio", "Project Tasks", "Change Orders"],
      Billing: ["Time and Expenses", "Project Billing", "WIP Report"]
    }
  },
  CRM: {
    code: "CRM",
    forms: ["Opportunity Pipeline", "Business Accounts", "Cases", "Campaigns"],
    categories: {
      Sales: ["Opportunity Pipeline", "Quotes", "Sales Activities"],
      Service: ["Cases", "Business Accounts", "Customer Contracts"]
    }
  },
  Payroll: {
    code: "PAY",
    forms: ["Employee Center", "Payroll Batch", "Leave Requests", "Headcount Plan"],
    categories: {
      People: ["Employee Center", "Headcount Plan", "Leave Requests"],
      Payroll: ["Payroll Batch", "Benefits", "Compliance Report"]
    }
  },
  System: {
    code: "SYS",
    forms: ["Audit Trail", "Access Rights", "Integration Monitor", "Import Scenarios"],
    categories: {
      Security: ["Access Rights", "Roles", "Audit Trail"],
      Automation: ["Integration Monitor", "Import Scenarios", "Workflow Rules"]
    }
  }
};

const state = {
  workspace: "Favorites",
  view: "Quick Menu",
  openForm: "Executive Dashboard",
  toast: "EnterpriseOne ERP loaded.",
  tabs: ["Executive Dashboard"],
  savedForms: {},
  refreshCount: 0,
  activity: ["EnterpriseOne ERP loaded", "Executive Dashboard opened"],
  approvals: [
    { id: "PO1048", type: "Purchase Order", customer: "North Hub Buildout", amount: 1280000, status: "Pending" },
    { id: "AP7781", type: "Supplier Payment", customer: "Pacific Steel Supply", amount: 640000, status: "Review" },
    { id: "HR2210", type: "Headcount Request", customer: "Operations Department", amount: 0, status: "Pending" },
    { id: "PR5520", type: "Change Order", customer: "ERP Phase 2 Rollout", amount: 420000, status: "Review" }
  ],
  inventory: [
    { sku: "RM-STEEL-001", item: "Steel sheets", warehouse: "Cavite", stock: 340, reorder: 220 },
    { sku: "IT-LAP-014", item: "Laptop units", warehouse: "Makati", stock: 28, reorder: 20 },
    { sku: "PKG-BOX-100", item: "Shipping boxes", warehouse: "Laguna", stock: 980, reorder: 600 },
    { sku: "RM-COPPER-008", item: "Copper wire", warehouse: "Cebu", stock: 88, reorder: 120 }
  ],
  projects: [
    { name: "North Distribution Hub", owner: "Operations", progress: 72, budget: 18400000 },
    { name: "ERP Phase 2 Rollout", owner: "IT PMO", progress: 48, budget: 6200000 },
    { name: "Fleet Renewal Program", owner: "Logistics", progress: 31, budget: 9800000 }
  ],
  leads: [
    { company: "Atlas Manufacturing", value: 4200000, stage: "Proposal" },
    { company: "Prime Logistics", value: 2800000, stage: "Negotiation" },
    { company: "Bayan Health Group", value: 1500000, stage: "Qualified" }
  ]
};

function statusClass(status) {
  if (["Approved", "Healthy", "Open"].includes(status)) return "";
  if (["Rejected", "Critical"].includes(status)) return " danger";
  return " warn";
}

function openForm(name) {
  state.openForm = name;
  if (!state.tabs.includes(name)) state.tabs.push(name);
  state.toast = `${name} opened.`;
  state.activity.push(`${name} opened`);
  render();
}

function render() {
  app.innerHTML = `
    <section class="erp-shell">
      <header class="top-pane">
        <a class="brand-mini" href="../index.html#catalog">
          <span class="brand-mark">ERP</span>
          <span>EnterpriseOne Cloud ERP</span>
        </a>
        <label class="global-search">
          <span>Search</span>
          <input id="globalSearch" placeholder="Search forms, reports, customers, documents">
        </label>
        <div class="top-actions">
          <span class="chip">Nexii Manufacturing PH</span>
          <span class="chip">Branch: Manila</span>
          <a class="top-button" href="../index.html#catalog">Demo Hub</a>
        </div>
      </header>

      <nav class="main-menu" aria-label="Main menu">
        ${Object.entries(workspaces).map(([name, ws]) => `
          <button class="menu-icon ${state.workspace === name ? "active" : ""}" type="button" data-workspace="${name}" title="${name}">${ws.code}</button>
        `).join("")}
      </nav>

      <aside class="workspace-menu">
        ${renderWorkspaceMenu()}
      </aside>

      <main class="content">
        <div class="tab-strip">
          ${state.tabs.map((tab) => `<button class="tab ${tab === state.openForm ? "active" : ""}" type="button" data-open-form="${tab}">${tab}${state.savedForms[tab] ? " ✓" : ""}</button>`).join("")}
        </div>
        ${state.toast ? `<div class="toast">${state.toast}</div>` : ""}
        <section class="screen">
          <header class="screen-head">
            <div>
              <h2>${state.openForm}</h2>
              <p>${screenDescription()}</p>
            </div>
            <div class="toolbar">
              <button class="secondary" type="button" data-save>Save</button>
              <button class="secondary" type="button" data-refresh>Refresh</button>
              <button class="primary" type="button" data-release>Release</button>
            </div>
          </header>
          <div class="screen-body">
            ${renderScreen()}
            ${renderActivityPanel()}
          </div>
        </section>
      </main>
    </section>
  `;
  bindEvents();
}

function renderWorkspaceMenu() {
  const ws = workspaces[state.workspace];
  const forms = state.view === "Quick Menu" ? ws.forms.slice(0, 4) : Object.values(ws.categories).flat();
  return `
    <div class="workspace-titlebar">
      <h1>${state.workspace}</h1>
      <button class="view-toggle" type="button" data-toggle-view>${state.view}</button>
    </div>
    <div class="tile-grid">
      ${forms.slice(0, 6).map((form) => `
        <button class="workspace-tile ${state.openForm === form ? "active" : ""}" type="button" data-open-form="${form}">
          <strong>${form}</strong>
          <span>${form.includes("Report") || form.includes("Review") ? "Report" : "Form"} · ${ws.code}</span>
        </button>
      `).join("")}
    </div>
    ${Object.entries(ws.categories).map(([category, links]) => `
      <section class="category">
        <h2>${category}</h2>
        <div class="link-list">
          ${links.map((link) => `<button type="button" data-open-form="${link}">${link}</button>`).join("")}
        </div>
      </section>
    `).join("")}
  `;
}

function screenDescription() {
  if (state.openForm.includes("Dashboard")) return "Role-based dashboard with KPIs, approvals, and operational exceptions.";
  if (state.openForm.includes("Inventory")) return "Inquiry screen for stock status, warehouse levels, and reorder signals.";
  if (state.openForm.includes("Project")) return "Project workspace for budgets, progress, change orders, and portfolio health.";
  if (state.openForm.includes("Opportunity")) return "CRM pipeline inquiry for enterprise sales teams.";
  if (state.openForm.includes("Report") || state.openForm.includes("Review")) return "Board-ready reporting view generated from operational data.";
  return "Enterprise transaction form with toolbar actions, approvals, and audit-ready status.";
}

function renderScreen() {
  if (state.openForm.includes("Inventory") || state.openForm.includes("Stock") || state.openForm.includes("Warehouse")) return renderInventory();
  if (state.openForm.includes("Project") || state.openForm.includes("Change")) return renderProjects();
  if (state.openForm.includes("Opportunity") || state.openForm.includes("CRM") || state.openForm.includes("Business")) return renderCrm();
  if (state.openForm.includes("Report") || state.openForm.includes("Review") || state.openForm.includes("Cash Position")) return renderReport();
  return renderDashboard();
}

function renderKpis() {
  const pending = state.approvals.filter((item) => ["Pending", "Review"].includes(item.status));
  const pendingValue = pending.reduce((sum, item) => sum + item.amount, 0);
  const revenue = 84200000;
  const expenses = 51600000;
  return `
    <section class="kpi-grid">
      <article class="kpi-card"><span>Revenue YTD</span><strong>${money.format(revenue)}</strong><small>+18.4%</small></article>
      <article class="kpi-card"><span>Operating Margin</span><strong>${Math.round(((revenue - expenses) / revenue) * 100)}%</strong><small>Healthy</small></article>
      <article class="kpi-card"><span>Pending Approvals</span><strong>${pending.length}</strong><small>${money.format(pendingValue)}</small></article>
      <article class="kpi-card"><span>Inventory Alerts</span><strong>${state.inventory.filter((item) => item.stock <= item.reorder).length}</strong><small>Needs review</small></article>
    </section>
  `;
}

function renderDashboard() {
  return `
    ${renderKpis()}
    <section class="two-col">
      <div class="panel">
        <div class="panel-head"><h3>Approval Map</h3><button class="secondary" type="button" data-new-approval>Add Approval</button></div>
        ${renderApprovalTable()}
      </div>
      <aside class="panel">
        <div class="panel-head"><h3>Workflow Actions</h3></div>
        <form class="form-grid" id="requestForm">
          <select name="type"><option>Purchase Order</option><option>Supplier Payment</option><option>Headcount Request</option><option>Project Change Order</option></select>
          <input name="customer" value="Enterprise Expansion Program">
          <input name="amount" type="number" value="250000">
          <button class="primary" type="submit">Submit Transaction</button>
        </form>
      </aside>
    </section>
  `;
}

function renderActivityPanel() {
  return `
    <section class="panel activity-panel">
      <div class="panel-head"><h3>System Activity</h3><span class="status">Live</span></div>
      <div class="record-list">
        ${state.activity.slice(-5).reverse().map((item) => `
          <article class="record-card"><div class="split-line"><strong>${item}</strong><span>${new Date().toLocaleTimeString()}</span></div></article>
        `).join("")}
      </div>
    </section>
  `;
}

function renderApprovalTable() {
  return `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Reference</th><th>Type</th><th>Account / Program</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          ${state.approvals.map((item, index) => `
            <tr>
              <td><strong>${item.id}</strong></td>
              <td>${item.type}</td>
              <td>${item.customer}</td>
              <td>${item.amount ? money.format(item.amount) : "-"}</td>
              <td><span class="status${statusClass(item.status)}">${item.status}</span></td>
              <td>
                <button class="small-action" data-approve="${index}" type="button">Approve</button>
                <button class="small-action" data-reject="${index}" type="button">Reject</button>
              </td>
            </tr>
          `).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function renderInventory() {
  return `
    ${renderKpis()}
    <section class="panel">
      <div class="panel-head"><h3>Inventory Summary</h3><button class="secondary" data-restock type="button">Run Reorder Process</button></div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>SKU</th><th>Item</th><th>Warehouse</th><th>Qty On Hand</th><th>Reorder Point</th><th>Status</th></tr></thead>
          <tbody>
            ${state.inventory.map((item) => `
              <tr><td><strong>${item.sku}</strong></td><td>${item.item}</td><td>${item.warehouse}</td><td>${item.stock}</td><td>${item.reorder}</td><td><span class="status${item.stock <= item.reorder ? " warn" : ""}">${item.stock <= item.reorder ? "Reorder" : "Healthy"}</span></td></tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </section>
  `;
}

function renderProjects() {
  return `
    ${renderKpis()}
    <section class="panel">
      <div class="panel-head"><h3>Project Portfolio</h3></div>
      <div class="record-list">
        ${state.projects.map((project) => `
          <article class="record-card">
            <div class="split-line"><strong>${project.name}</strong><span>${money.format(project.budget)}</span></div>
            <p>Owner: ${project.owner}</p>
            <div class="progress"><span style="width: ${project.progress}%"></span></div>
            <p>${project.progress}% complete</p>
          </article>
        `).join("")}
      </div>
    </section>
  `;
}

function renderCrm() {
  return `
    ${renderKpis()}
    <section class="panel">
      <div class="panel-head"><h3>Opportunity Pipeline</h3><button class="secondary" data-add-lead type="button">Add Opportunity</button></div>
      <div class="record-list">
        ${state.leads.map((lead) => `
          <article class="record-card"><div class="split-line"><strong>${lead.company}</strong><span class="status warn">${lead.stage}</span></div><p>Estimated value: ${money.format(lead.value)}</p></article>
        `).join("")}
      </div>
    </section>
  `;
}

function renderReport() {
  return `
    ${renderKpis()}
    <section class="panel">
      <div class="panel-head"><h3>Executive Report Preview</h3><button class="secondary" data-export type="button">Export Snapshot</button></div>
      <pre class="report-preview">${buildReport()}</pre>
    </section>
  `;
}

function buildReport() {
  return [
    "ENTERPRISEONE ERP SNAPSHOT",
    `Company: Nexii Manufacturing PH`,
    `Generated: ${new Date().toLocaleString()}`,
    "",
    `Workspace: ${state.workspace}`,
    `Open Form: ${state.openForm}`,
    `Pending Approvals: ${state.approvals.filter((item) => ["Pending", "Review"].includes(item.status)).length}`,
    `Inventory Alerts: ${state.inventory.filter((item) => item.stock <= item.reorder).length}`,
    `Open Opportunities: ${state.leads.length}`,
    `Active Projects: ${state.projects.length}`,
    "",
    "Recommended Actions:",
    "- Release approved purchase orders.",
    "- Review reorder exceptions before end of day.",
    "- Escalate projects below 50% progress."
  ].join("\n");
}

function bindEvents() {
  document.querySelectorAll("[data-workspace]").forEach((button) => {
    button.addEventListener("click", () => {
      state.workspace = button.dataset.workspace;
      state.openForm = workspaces[state.workspace].forms[0];
      if (!state.tabs.includes(state.openForm)) state.tabs.push(state.openForm);
      state.toast = `${state.workspace} workspace opened.`;
      state.activity.push(`${state.workspace} workspace opened`);
      render();
    });
  });

  document.querySelectorAll("[data-open-form]").forEach((button) => {
    button.addEventListener("click", () => openForm(button.dataset.openForm));
  });

  document.querySelector("[data-toggle-view]")?.addEventListener("click", () => {
    state.view = state.view === "Quick Menu" ? "Full Menu" : "Quick Menu";
    state.toast = `${state.workspace} switched to ${state.view}.`;
    state.activity.push(`${state.workspace} switched to ${state.view}`);
    render();
  });

  document.querySelector("#globalSearch")?.addEventListener("keydown", (event) => {
    if (event.key !== "Enter") return;
    const value = event.target.value.trim();
    if (value) openForm(value);
  });

  document.querySelectorAll("[data-approve]").forEach((button) => {
    button.addEventListener("click", () => {
      const item = state.approvals[Number(button.dataset.approve)];
      item.status = "Approved";
      state.toast = `${item.id} approved and released to the next workflow step.`;
      state.activity.push(`${item.id} approved`);
      render();
    });
  });

  document.querySelectorAll("[data-reject]").forEach((button) => {
    button.addEventListener("click", () => {
      const item = state.approvals[Number(button.dataset.reject)];
      item.status = "Rejected";
      state.toast = `${item.id} rejected and returned to requester.`;
      state.activity.push(`${item.id} rejected`);
      render();
    });
  });

  document.querySelector("#requestForm")?.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    state.approvals.unshift({
      id: `REQ${Math.floor(1000 + Math.random() * 8999)}`,
      type: data.type,
      customer: data.customer,
      amount: Number(data.amount || 0),
      status: "Pending"
    });
    state.toast = "Transaction submitted for approval.";
    state.activity.push(`${data.type} submitted for approval`);
    render();
  });

  document.querySelector("[data-new-approval]")?.addEventListener("click", () => {
    state.approvals.unshift({ id: `PO${Math.floor(2000 + Math.random() * 7000)}`, type: "Purchase Order", customer: "New Supplier Request", amount: 375000, status: "Pending" });
    state.toast = "Approval record added.";
    state.activity.push("Approval record added");
    render();
  });

  document.querySelector("[data-restock]")?.addEventListener("click", () => {
    state.inventory = state.inventory.map((item) => item.stock <= item.reorder ? { ...item, stock: item.stock + 500 } : item);
    state.toast = "Reorder process completed.";
    state.activity.push("Inventory reorder process completed");
    render();
  });

  document.querySelector("[data-add-lead]")?.addEventListener("click", () => {
    state.leads.unshift({ company: "Enterprise Demo Account", value: 3500000, stage: "Qualified" });
    state.toast = "Opportunity added.";
    state.activity.push("CRM opportunity added");
    render();
  });

  document.querySelector("[data-save]")?.addEventListener("click", () => {
    state.savedForms[state.openForm] = true;
    state.toast = `${state.openForm} saved. The tab now shows a check mark.`;
    state.activity.push(`${state.openForm} saved`);
    render();
  });
  document.querySelector("[data-refresh]")?.addEventListener("click", () => {
    state.refreshCount += 1;
    if (state.openForm.includes("Inventory") || state.openForm.includes("Stock") || state.openForm.includes("Warehouse")) {
      state.inventory = state.inventory.map((item, index) => ({ ...item, stock: Math.max(0, item.stock + (index % 2 === 0 ? 4 : -2)) }));
    }
    if (state.openForm.includes("Opportunity") || state.openForm.includes("CRM") || state.openForm.includes("Business")) {
      state.leads = state.leads.map((lead, index) => index === 0 ? { ...lead, value: lead.value + 150000 } : lead);
    }
    state.toast = `${state.openForm} refreshed. Live values updated.`;
    state.activity.push(`${state.openForm} refreshed`);
    render();
  });
  document.querySelector("[data-release]")?.addEventListener("click", () => {
    const nextApproval = state.approvals.find((item) => ["Pending", "Review"].includes(item.status));
    if (nextApproval) {
      nextApproval.status = "Approved";
      state.toast = `${nextApproval.id} released from ${state.openForm}.`;
      state.activity.push(`${nextApproval.id} released`);
    } else {
      state.toast = `${state.openForm} release completed. No pending documents remain.`;
      state.activity.push(`${state.openForm} release completed`);
    }
    render();
  });
  document.querySelector("[data-export]")?.addEventListener("click", () => {
    downloadText("enterpriseone-snapshot.txt", buildReport());
    state.toast = "Executive snapshot exported.";
    state.activity.push("Executive snapshot exported");
    render();
  });
}

function downloadText(filename, content) {
  const blob = new Blob([content], { type: "text/plain;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  link.click();
  URL.revokeObjectURL(url);
}

render();
