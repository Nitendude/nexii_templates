const app = document.querySelector("#erpApp");

const money = new Intl.NumberFormat("en-PH", {
  style: "currency",
  currency: "PHP",
  maximumFractionDigits: 0
});

const state = {
  active: "Executive",
  toast: "ERP demo loaded with enterprise sample data.",
  approvals: [
    { id: "PO-1048", type: "Purchase Order", owner: "Procurement", amount: 1280000, status: "Pending" },
    { id: "AP-7781", type: "Supplier Payment", owner: "Finance", amount: 640000, status: "Review" },
    { id: "HR-2210", type: "Headcount Request", owner: "HR", amount: 0, status: "Pending" },
    { id: "PRJ-5520", type: "Project Change Order", owner: "PMO", amount: 420000, status: "Review" }
  ],
  inventory: [
    { sku: "RM-STEEL-001", item: "Steel sheets", stock: 340, reorder: 220, warehouse: "Cavite" },
    { sku: "IT-LAP-014", item: "Laptop units", stock: 28, reorder: 20, warehouse: "Makati" },
    { sku: "PKG-BOX-100", item: "Shipping boxes", stock: 980, reorder: 600, warehouse: "Laguna" }
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
  ],
  audit: ["System initialized", "Executive dashboard opened"]
};

const modules = {
  Executive: {
    title: "Executive Command Center",
    description: "Live visibility across revenue, expenses, approvals, projects, inventory, and workforce performance."
  },
  Finance: {
    title: "Finance and Accounting",
    description: "Accounts payable, receivables, budgets, cash position, purchase orders, and payment approvals."
  },
  Procurement: {
    title: "Procurement and Suppliers",
    description: "Vendor records, requisitions, purchase orders, supplier scorecards, and contract monitoring."
  },
  Inventory: {
    title: "Inventory and Warehousing",
    description: "Stock levels, reorder alerts, warehouse transfers, SKU movement, and landed cost tracking."
  },
  HR: {
    title: "Human Resources",
    description: "Employee records, headcount requests, leave balances, payroll readiness, and department staffing."
  },
  CRM: {
    title: "Sales and CRM",
    description: "Pipeline, accounts, opportunity values, sales activities, and conversion reporting."
  },
  Projects: {
    title: "Projects and PMO",
    description: "Milestones, budgets, progress, change orders, approvals, and executive portfolio health."
  },
  Reports: {
    title: "Business Intelligence",
    description: "Board-ready reports, operational exports, audit trails, and KPI snapshots."
  }
};

function totalPending() {
  return state.approvals.filter((item) => ["Pending", "Review"].includes(item.status)).length;
}

function totalApprovalValue() {
  return state.approvals.reduce((sum, item) => ["Pending", "Review"].includes(item.status) ? sum + item.amount : sum, 0);
}

function statusClass(status) {
  if (status === "Approved" || status === "Healthy") return "";
  if (status === "Rejected" || status === "Critical") return " danger";
  return " warn";
}

function render() {
  app.innerHTML = `
    <section class="erp-shell">
      <aside class="sidebar">
        <a class="brand" href="../index.html#catalog">
          <span class="brand-mark">ERP</span>
          <span><strong>EnterpriseOne</strong><span>Business Management</span></span>
        </a>
        <nav class="nav-list" aria-label="ERP modules">
          ${Object.keys(modules).map((name) => `<button class="${state.active === name ? "active" : ""}" type="button" data-module="${name}">${name}</button>`).join("")}
        </nav>
        <div class="side-note">
          <strong>Enterprise demo</strong>
          <p>Built for boardroom presentations: approve requests, review KPIs, inspect modules, and generate executive snapshots.</p>
        </div>
        <a class="hub-link" href="../index.html#catalog">Back to Demo Hub</a>
      </aside>
      <main class="workspace">
        <header class="topbar">
          <div>
            <p class="eyebrow">Enterprise ERP Suite</p>
            <h1>${modules[state.active].title}</h1>
            <p>${modules[state.active].description}</p>
          </div>
          <div class="top-actions">
            <button class="secondary-btn" type="button" data-demo-reset>Reset Demo</button>
            <button class="primary-btn" type="button" data-generate-report>Generate Executive Report</button>
          </div>
        </header>
        ${state.toast ? `<div class="toast">${state.toast}</div>` : ""}
        ${renderKpis()}
        <section class="main-grid">
          <div>
            ${renderModuleOverview()}
            ${renderPrimaryPanel()}
          </div>
          <aside>
            ${renderCreateRequest()}
            ${renderExecutiveReport()}
          </aside>
        </section>
      </main>
    </section>
  `;
  bindEvents();
}

function renderKpis() {
  const revenue = 84200000;
  const expenses = 51600000;
  const projectAvg = Math.round(state.projects.reduce((sum, item) => sum + item.progress, 0) / state.projects.length);
  return `
    <section class="kpi-grid" aria-label="ERP KPI overview">
      <article class="kpi-card"><span>Revenue YTD</span><strong>${money.format(revenue)}</strong><small>+18.4% vs last year</small></article>
      <article class="kpi-card"><span>Operating Margin</span><strong>${Math.round(((revenue - expenses) / revenue) * 100)}%</strong><small>Healthy</small></article>
      <article class="kpi-card"><span>Pending Approvals</span><strong>${totalPending()}</strong><small>${money.format(totalApprovalValue())} pending value</small></article>
      <article class="kpi-card"><span>Project Health</span><strong>${projectAvg}%</strong><small>Portfolio progress</small></article>
    </section>
  `;
}

function renderModuleOverview() {
  return `
    <section class="panel">
      <div class="panel-head">
        <div><p class="eyebrow">ERP Modules</p><h2>Navigate the enterprise workspace</h2></div>
      </div>
      <div class="module-grid">
        ${Object.entries(modules).map(([name, mod]) => `
          <button class="module-card ${state.active === name ? "active" : ""}" type="button" data-module="${name}">
            <strong>${name}</strong>
            <span>${mod.description}</span>
          </button>
        `).join("")}
      </div>
    </section>
  `;
}

function renderPrimaryPanel() {
  if (state.active === "Inventory") return renderInventoryPanel();
  if (state.active === "CRM") return renderCrmPanel();
  if (state.active === "Projects") return renderProjectsPanel();
  if (state.active === "Reports") return renderReportPanel();
  return renderApprovalsPanel();
}

function renderApprovalsPanel() {
  return `
    <section class="panel">
      <div class="panel-head">
        <div><p class="eyebrow">${state.active} Workflow</p><h2>Approval command queue</h2></div>
        <button class="secondary-btn" type="button" data-add-approval>Add Sample Approval</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Reference</th><th>Type</th><th>Owner</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
            ${state.approvals.map((item, index) => `
              <tr>
                <td><strong>${item.id}</strong></td>
                <td>${item.type}</td>
                <td>${item.owner}</td>
                <td>${item.amount ? money.format(item.amount) : "-"}</td>
                <td><span class="status${statusClass(item.status)}">${item.status}</span></td>
                <td class="action-row">
                  <button class="mini-btn" type="button" data-approve="${index}">Approve</button>
                  <button class="mini-btn" type="button" data-reject="${index}">Reject</button>
                </td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </section>
  `;
}

function renderInventoryPanel() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">Inventory Control</p><h2>Warehouse stock levels</h2></div><button class="secondary-btn" data-restock type="button">Run Reorder Check</button></div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>SKU</th><th>Item</th><th>Warehouse</th><th>Stock</th><th>Reorder Point</th><th>Status</th></tr></thead>
          <tbody>
            ${state.inventory.map((item) => `
              <tr>
                <td><strong>${item.sku}</strong></td><td>${item.item}</td><td>${item.warehouse}</td><td>${item.stock}</td><td>${item.reorder}</td>
                <td><span class="status${item.stock <= item.reorder ? " warn" : ""}">${item.stock <= item.reorder ? "Reorder" : "Healthy"}</span></td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </section>
  `;
}

function renderCrmPanel() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">CRM Pipeline</p><h2>Enterprise sales opportunities</h2></div><button class="secondary-btn" data-add-lead type="button">Add Opportunity</button></div>
      <div class="record-list">
        ${state.leads.map((lead) => `
          <article class="record-card">
            <div class="split-line"><strong>${lead.company}</strong><span class="status warn">${lead.stage}</span></div>
            <p>Estimated contract value: ${money.format(lead.value)}</p>
          </article>
        `).join("")}
      </div>
    </section>
  `;
}

function renderProjectsPanel() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">PMO Portfolio</p><h2>Strategic project delivery</h2></div></div>
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

function renderReportPanel() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">Business Intelligence</p><h2>Board-ready snapshot</h2></div></div>
      <pre class="report-preview">${buildReport()}</pre>
    </section>
  `;
}

function renderCreateRequest() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">Create Request</p><h2>New ERP transaction</h2></div></div>
      <form class="form-grid" id="requestForm">
        <select name="type">
          <option>Purchase Order</option>
          <option>Supplier Payment</option>
          <option>Headcount Request</option>
          <option>Project Change Order</option>
        </select>
        <input name="owner" placeholder="Department owner" value="Operations">
        <input name="amount" type="number" placeholder="Amount" value="250000">
        <textarea name="notes" placeholder="Business reason">Sample enterprise request for executive approval.</textarea>
        <button class="primary-btn" type="submit">Submit for Approval</button>
      </form>
    </section>
  `;
}

function renderExecutiveReport() {
  return `
    <section class="panel">
      <div class="panel-head"><div><p class="eyebrow">Executive Snapshot</p><h2>Live report preview</h2></div></div>
      <pre class="report-preview" id="reportPreview">${buildReport()}</pre>
    </section>
  `;
}

function buildReport() {
  return [
    "ENTERPRISE ERP SNAPSHOT",
    `Generated: ${new Date().toLocaleString()}`,
    "",
    `Pending approvals: ${totalPending()}`,
    `Pending value: ${money.format(totalApprovalValue())}`,
    `Open opportunities: ${state.leads.length}`,
    `Active projects: ${state.projects.length}`,
    `Inventory alerts: ${state.inventory.filter((item) => item.stock <= item.reorder).length}`,
    "",
    "Recommended actions:",
    "- Review high-value approvals before end of day.",
    "- Monitor procurement and warehouse reorder points.",
    "- Escalate project risks above PHP 5M budget exposure."
  ].join("\n");
}

function bindEvents() {
  document.querySelectorAll("[data-module]").forEach((button) => {
    button.addEventListener("click", () => {
      state.active = button.dataset.module;
      state.toast = `${state.active} module opened.`;
      state.audit.push(`${state.active} module opened`);
      render();
    });
  });

  document.querySelectorAll("[data-approve]").forEach((button) => {
    button.addEventListener("click", () => {
      const item = state.approvals[Number(button.dataset.approve)];
      item.status = "Approved";
      state.toast = `${item.id} approved.`;
      state.audit.push(`${item.id} approved`);
      render();
    });
  });

  document.querySelectorAll("[data-reject]").forEach((button) => {
    button.addEventListener("click", () => {
      const item = state.approvals[Number(button.dataset.reject)];
      item.status = "Rejected";
      state.toast = `${item.id} rejected.`;
      state.audit.push(`${item.id} rejected`);
      render();
    });
  });

  const form = document.querySelector("#requestForm");
  form?.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    state.approvals.unshift({
      id: `REQ-${Math.floor(1000 + Math.random() * 8999)}`,
      type: data.type,
      owner: data.owner,
      amount: Number(data.amount || 0),
      status: "Pending"
    });
    state.toast = `${data.type} submitted for approval.`;
    render();
  });

  document.querySelector("[data-add-approval]")?.addEventListener("click", () => {
    state.approvals.unshift({ id: `PO-${Math.floor(2000 + Math.random() * 7000)}`, type: "Purchase Order", owner: "Procurement", amount: 375000, status: "Pending" });
    state.toast = "Sample approval added.";
    render();
  });

  document.querySelector("[data-add-lead]")?.addEventListener("click", () => {
    state.leads.unshift({ company: "Enterprise Demo Account", value: 3500000, stage: "Qualified" });
    state.toast = "CRM opportunity added.";
    render();
  });

  document.querySelector("[data-restock]")?.addEventListener("click", () => {
    state.inventory = state.inventory.map((item) => item.stock <= item.reorder ? { ...item, stock: item.stock + 500 } : item);
    state.toast = "Reorder check completed and low stock replenished.";
    render();
  });

  document.querySelector("[data-generate-report]")?.addEventListener("click", () => {
    state.active = "Reports";
    state.toast = "Executive report generated.";
    render();
  });

  document.querySelector("[data-demo-reset]")?.addEventListener("click", () => location.reload());
}

render();
