const money = new Intl.NumberFormat("en-PH", {
  style: "currency",
  currency: "PHP",
  maximumFractionDigits: 0
});

function qs(selector, root = document) {
  return root.querySelector(selector);
}

function qsa(selector, root = document) {
  return [...root.querySelectorAll(selector)];
}

function statusBadge(status) {
  const warn = ["Pending", "Review", "Partial", "Overdue", "Submitted", "Draft", "In Review"].includes(status);
  return `<span class="status${warn ? " warn" : ""}">${status}</span>`;
}

function activity(title, meta) {
  return `<div class="activity-item"><strong>${title}</strong><span>${meta}</span></div>`;
}

function initOperations() {
  const modules = {
    hr: {
      title: "HR and Employee Hub",
      hero: "HR",
      cards: [
        ["Employee Profiles", "View employee records, photos, onboarding details, and document access."],
        ["Payslip Center", "Upload and publish payslips while staff download their PDF copies."],
        ["Leave Requests", "Review time-off requests and keep approval history visible."],
        ["Profile Corrections", "Approve employee-submitted corrections before records change."]
      ]
    },
    accounting: {
      title: "Accounting and Cash Advances",
      hero: "Accounting",
      cards: [
        ["Cash Advance Requests", "Create, review, approve, or return employee cash advance requests."],
        ["Liquidation Review", "Attach receipts, compare budgets, and close liquidation items."],
        ["Payment Monitoring", "Track paid, personal-paid, and pending payment states."],
        ["Reimbursable Vouchers", "Prepare voucher records and keep audit-ready documentation."]
      ]
    },
    billing: {
      title: "Billing Documents",
      hero: "Billing",
      cards: [
        ["Billing Statements", "Create drafts, upload attachments, and download PDF copies."],
        ["Service Invoices", "Manage invoice documents with edit, view, and PDF actions."],
        ["Debit and Credit Notes", "Track adjustments with document status and attachments."],
        ["Master Storage", "Centralize billing documents for faster client lookup."]
      ]
    },
    jobs: {
      title: "Operations and Job Orders",
      hero: "Jobs",
      cards: [
        ["Job Order Board", "Create, edit, and track operational job order requests."],
        ["Package Download", "Bundle job order documents into one client-ready file."],
        ["Scanner Uploads", "Attach saved scans from server folders to the right job order."],
        ["Email Packages", "Send prepared packages from the system workflow."]
      ]
    }
  };

  let approvals = [
    ["Cash advance CA-7906", "Finance", "Accounting", "Review"],
    ["Leave request", "Employee", "HR", "Pending"],
    ["Billing statement draft", "Billing", "Billing", "Draft"],
    ["Job order package", "Operations", "Jobs", "Ready"]
  ];
  let tickets = 3;
  const logs = [
    ["System ready", "Demo workspace loaded"],
    ["Audit enabled", "Admin actions will appear here"]
  ];

  function renderModule(name) {
    const mod = modules[name];
    qs("#opsModuleTitle").textContent = mod.title;
    qs("#opsActiveHero").textContent = mod.hero;
    qs("#opsModuleCards").innerHTML = mod.cards.map((card, index) => `
      <button class="workspace-card${index === 0 ? " active" : ""}" type="button">
        <strong>${card[0]}</strong><span>${card[1]}</span>
      </button>
    `).join("");
  }

  function renderApprovals() {
    qs("#opsApprovalRows").innerHTML = approvals.map((item, index) => `
      <tr>
        <td>${item[0]}</td>
        <td>${item[1]}</td>
        <td>${item[2]}</td>
        <td>${statusBadge(item[3])}</td>
        <td>
          <button class="action-btn" type="button" data-approve="${index}">Approve</button>
          <button class="action-btn" type="button" data-return="${index}">Return</button>
        </td>
      </tr>
    `).join("");
    const pending = approvals.filter((item) => !["Approved", "Returned", "Ready"].includes(item[3])).length;
    qs("#opsPending").textContent = pending;
    qs("#opsPendingHero").textContent = pending;
  }

  function renderLogs() {
    qs("#opsActivity").innerHTML = logs.slice(-6).reverse().map((item) => activity(item[0], item[1])).join("");
    qs("#opsTickets").textContent = tickets;
    qs("#opsTicketsHero").textContent = tickets;
  }

  qsa("[data-ops-module]").forEach((button) => {
    button.addEventListener("click", () => {
      qsa("[data-ops-module]").forEach((tab) => tab.classList.remove("active"));
      button.classList.add("active");
      renderModule(button.dataset.opsModule);
      logs.push([`Opened ${button.textContent}`, "Module preview changed"]);
      renderLogs();
    });
  });

  qs("#opsApprovalRows").addEventListener("click", (event) => {
    const approve = event.target.closest("[data-approve]");
    const returned = event.target.closest("[data-return]");
    if (!approve && !returned) return;
    const index = Number((approve || returned).dataset[approve ? "approve" : "return"]);
    approvals[index][3] = approve ? "Approved" : "Returned";
    logs.push([`${approvals[index][0]} ${approvals[index][3].toLowerCase()}`, approvals[index][2]]);
    renderApprovals();
    renderLogs();
  });

  qs("[data-add-approval]").addEventListener("click", () => {
    approvals.push(["Support ticket escalation", "Admin", "Support", "Pending"]);
    logs.push(["New approval added", "Support ticket escalation"]);
    renderApprovals();
    renderLogs();
  });

  qs("#opsTicketForm").addEventListener("submit", (event) => {
    event.preventDefault();
    tickets += 1;
    const form = new FormData(event.currentTarget);
    logs.push([form.get("subject"), `${form.get("priority")} priority ticket created`]);
    renderLogs();
  });

  qs("[data-fill-ops-demo]").addEventListener("click", () => {
    approvals.push(["Client record update", "Admin", "Clients", "Review"]);
    tickets += 2;
    logs.push(["Sample workday loaded", "Added approvals and support tickets"]);
    renderApprovals();
    renderLogs();
  });

  qs("[data-reset-ops]").addEventListener("click", () => location.reload());
  renderModule("hr");
  renderApprovals();
  renderLogs();
}

function initProposal() {
  let leads = [
    ["Ramos Dental", "hello@ramosdental.example", "Clinic", "Draft sent"],
    ["Green Bowl Cafe", "owner@greenbowl.example", "Restaurant", "New lead"]
  ];
  const steps = ["Lead intake", "AI draft", "CRM save", "Automation handoff"];

  function currentForm() {
    return Object.fromEntries(new FormData(qs("#proposalForm")).entries());
  }

  function generateDraft(data) {
    return `Dear ${data.company},\n\nNexii Tech Automations can help with your ${data.category.toLowerCase()} workflow, especially ${data.needs.toLowerCase()}\n\nWe can build or improve your website, set up CRM lead capture, organize inquiry handling, and add automation for follow-up messages. This gives your team a clearer way to manage requests without losing client details.\n\nWe would be glad to show a short demo and map the best setup for ${data.company}.\n\nSincerely,\nNexii Tech Automations`;
  }

  function renderLeads() {
    const query = qs("#proposalSearch").value.toLowerCase();
    const filtered = leads.filter((lead) => lead.join(" ").toLowerCase().includes(query));
    qs("#proposalLeadRows").innerHTML = filtered.map((lead) => `
      <tr><td>${lead[0]}</td><td>${lead[1]}</td><td>${lead[2]}</td><td>${statusBadge(lead[3])}</td></tr>
    `).join("");
    qs("#proposalLeadCount").textContent = leads.length;
  }

  function renderSteps(active = 1) {
    qs("#proposalSteps").innerHTML = steps.map((step, index) => `
      <div class="step-item"><strong>${step}</strong>${statusBadge(index <= active ? "Ready" : "Pending")}</div>
    `).join("");
  }

  function updateOutput() {
    const data = currentForm();
    qs("#proposalOutput").textContent = generateDraft(data);
    qs("#proposalLastCategory").textContent = data.category;
    qs("#proposalAutomation").textContent = "Drafted";
    renderSteps(1);
  }

  qs("#proposalForm").addEventListener("submit", (event) => {
    event.preventDefault();
    updateOutput();
  });
  qs("[data-save-lead]").addEventListener("click", () => {
    const data = currentForm();
    leads.unshift([data.company, data.email, data.category, "Draft sent"]);
    qs("#proposalAutomation").textContent = "Saved";
    renderSteps(3);
    renderLeads();
  });
  qs("[data-copy-proposal]").addEventListener("click", () => {
    qs("#proposalAutomation").textContent = "Copied";
    renderSteps(2);
  });
  qs("#proposalSearch").addEventListener("input", renderLeads);
  updateOutput();
  renderLeads();
}

function initStartup() {
  const packages = {
    Starter: 45000,
    Growth: 85000,
    Automation: 145000
  };
  const work = [
    ["Booking Website", "Booking", "Room selection, policies, payment upload, and guest summary."],
    ["Agency Website", "Website", "Services, packages, portfolio, FAQ, and inquiry routing."],
    ["CRM Follow-up", "Automation", "Lead capture, reminders, client status, and response tracking."],
    ["Real Estate Site", "Website", "Property search, listing pages, and tour requests."]
  ];
  let selected = "Starter";

  function renderPrice() {
    let price = packages[selected];
    if (qs("#startupRush").checked) price += 15000;
    if (qs("#startupContent").checked) price += 20000;
    qs("#startupPrice").textContent = money.format(price);
  }

  function renderWork() {
    const filter = qs("#startupFilter").value;
    qs("#startupWork").innerHTML = work
      .filter((item) => filter === "All" || item[1] === filter)
      .map((item) => `<article class="workspace-card"><strong>${item[0]}</strong><span>${item[2]}</span></article>`)
      .join("");
  }

  qsa("[data-package]").forEach((button) => {
    button.addEventListener("click", () => {
      selected = button.dataset.package;
      qsa("[data-package]").forEach((item) => item.classList.remove("active"));
      button.classList.add("active");
      renderPrice();
    });
  });
  qs("#startupRush").addEventListener("change", renderPrice);
  qs("#startupContent").addEventListener("change", renderPrice);
  qs("#startupFilter").addEventListener("change", renderWork);
  qs("#startupForm").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    qs("#startupInquiry").textContent = `${data.name} from ${data.company} requested the ${selected} package. Timeline: ${data.timeline}. Notes: ${data.message}`;
  });
  renderPrice();
  renderWork();
}

function initProperty() {
  let renters = [
    ["Maria Dela Cruz", "Unit 2B", 18000, 18000, "Paid"],
    ["Juan Reyes", "Townhouse 4", 32000, 15000, "Partial"],
    ["Ana Garcia", "Studio 8", 14500, 0, "Overdue"],
    ["Paolo Santos", "Unit 6A", 22000, 0, "Pending"]
  ];
  let reports = [
    ["Juan Reyes", "Water damage", "In Review", "Bayanihan Maintenance"],
    ["Ana Garcia", "Appliance", "Submitted", "Unassigned"],
    ["Maria Dela Cruz", "Door lock", "Scheduled", "Kandado Pros"]
  ];

  function renderStats() {
    const due = renters.reduce((sum, row) => sum + row[2], 0);
    const paid = renters.reduce((sum, row) => sum + row[3], 0);
    qs("#propertyDue").textContent = money.format(due);
    qs("#propertyCollected").textContent = money.format(paid);
    qs("#propertyCollection").textContent = `${Math.round((paid / due) * 100)}%`;
    qs("#propertyOverdue").textContent = renters.filter((row) => row[4] === "Overdue").length;
    qs("#propertyRepairs").textContent = reports.length;
    qs("#propertyOpenReports").textContent = reports.filter((row) => row[2] !== "Resolved").length;
  }

  function renderRows() {
    qs("#propertyRows").innerHTML = renters.map((row, index) => `
      <tr>
        <td>${row[0]}</td><td>${row[1]}</td><td>${money.format(row[2])}</td><td>${money.format(row[3])}</td>
        <td>${statusBadge(row[4])}</td>
        <td><button class="action-btn" type="button" data-pay="${index}">Mark paid</button></td>
      </tr>
    `).join("");
  }

  function renderReports() {
    qs("#propertyReports").innerHTML = reports.map((report, index) => `
      <article class="report-card">
        <strong>${report[1]}</strong>
        <div class="report-meta"><span>${report[0]}</span><span>${report[3]}</span>${statusBadge(report[2])}</div>
        <div class="split-actions">
          <button class="action-btn" type="button" data-assign="${index}">Assign vendor</button>
          <button class="action-btn" type="button" data-close="${index}">Resolve</button>
        </div>
      </article>
    `).join("");
  }

  qs("#propertyRows").addEventListener("click", (event) => {
    const button = event.target.closest("[data-pay]");
    if (!button) return;
    const row = renters[Number(button.dataset.pay)];
    row[3] = row[2];
    row[4] = "Paid";
    renderRows();
    renderStats();
  });
  qs("#propertyReports").addEventListener("click", (event) => {
    const assign = event.target.closest("[data-assign]");
    const close = event.target.closest("[data-close]");
    if (!assign && !close) return;
    const index = Number((assign || close).dataset[assign ? "assign" : "close"]);
    if (assign) {
      reports[index][2] = "Scheduled";
      reports[index][3] = "Bayanihan Maintenance";
    } else {
      reports[index][2] = "Resolved";
    }
    renderReports();
    renderStats();
  });
  qs("#propertyReportForm").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    reports.unshift([data.tenant, data.category, "Submitted", "Unassigned"]);
    renderReports();
    renderStats();
  });
  qs("[data-export-property]").addEventListener("click", () => {
    qs("#propertyExport").textContent = [
      "tenant,unit,rent,paid,status",
      ...renters.map((row) => row.join(","))
    ].join("\n");
  });
  qsa("[data-property-mode]").forEach((button) => {
    button.addEventListener("click", () => {
      qsa("[data-property-mode]").forEach((item) => item.classList.remove("active"));
      button.classList.add("active");
      qs("#propertyModeLabel").textContent = button.dataset.propertyMode === "tenant" ? "Tenant" : "Landlord";
    });
  });
  renderRows();
  renderReports();
  renderStats();
}

function initProGreen() {
  const roles = {
    Admin: [["1,240", "Total users"], ["15", "Partners"], ["8.5t", "Waste collected"], ["PHP 2.4k", "Rewards distributed"]],
    Company: [["42", "Pickups this month"], ["1.8t", "Recyclables"], ["88%", "Compliance score"], ["12", "Open requests"]],
    LGU: [["7", "Barangays"], ["3.2t", "Community waste"], ["18", "Active drives"], ["94%", "Report completion"]],
    User: [["320", "Reward points"], ["18kg", "Personal impact"], ["2", "Scheduled pickups"], ["4", "Badges earned"]]
  };
  const feed = [
    ["Partner approved", "EcoCollect PH added to active partners"],
    ["Pickup completed", "Barangay San Isidro collected 24kg"],
    ["Reward issued", "PHP 140 worth of points distributed"]
  ];

  function renderRole(role) {
    qs("#progreenRoleHero").textContent = role;
    qs("#progreenPhone").innerHTML = `
      <span class="panel-label">${role} dashboard</span>
      <h2>${role === "User" ? "My Green Impact" : `${role} Overview`}</h2>
      ${roles[role].map((card) => `
        <div class="screen-card">
          <h3>${card[0]}</h3><p>${card[1]}</p>
          <div class="progress"><span style="width: ${40 + Math.floor(Math.random() * 45)}%"></span></div>
        </div>
      `).join("")}
    `;
  }

  function renderFeed() {
    qs("#progreenFeed").innerHTML = feed.slice(-6).reverse().map((item) => activity(item[0], item[1])).join("");
  }

  qsa("[data-progreen-role]").forEach((button) => {
    button.addEventListener("click", () => {
      qsa("[data-progreen-role]").forEach((item) => item.classList.remove("active"));
      button.classList.add("active");
      renderRole(button.dataset.progreenRole);
    });
  });
  qs("#progreenPickupForm").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    feed.push(["Pickup created", `${data.location}: ${data.weight}kg of ${data.material}`]);
    qs("#progreenWasteHero").textContent = `${(8.5 + Number(data.weight) / 1000).toFixed(2)}t`;
    renderFeed();
  });
  renderRole("Admin");
  renderFeed();
}

function initBooking() {
  function nightsBetween(checkin, checkout) {
    const start = new Date(checkin);
    const end = new Date(checkout);
    const diff = Math.max(1, Math.round((end - start) / 86400000));
    return Number.isFinite(diff) ? diff : 1;
  }

  function renderSummary(data) {
    const option = qs("#bookingForm select[name='room']").selectedOptions[0];
    const rate = Number(option.dataset.rate);
    const nights = nightsBetween(data.checkin, data.checkout);
    const breakfast = data.breakfast ? Number(data.guests) * 250 * nights : 0;
    const bonfire = data.bonfire ? 1200 : 0;
    const total = rate * nights + breakfast + bonfire;
    qs("#bookingSummary").textContent = `Booking reference: BHP-${Math.floor(1000 + Math.random() * 8999)}
Room: ${data.room}
Dates: ${data.checkin} to ${data.checkout}
Guests: ${data.guests}
Stay length: ${nights} night(s)
Add-ons: ${data.breakfast ? "Breakfast package" : "No breakfast"}${data.bonfire ? ", bonfire setup" : ""}
Estimated total: ${money.format(total)}

Status: Available for demo confirmation.`;
  }

  qs("#bookingForm").addEventListener("submit", (event) => {
    event.preventDefault();
    renderSummary(Object.fromEntries(new FormData(event.currentTarget).entries()));
  });
  renderSummary(Object.fromEntries(new FormData(qs("#bookingForm")).entries()));
}

function initCorporate() {
  const projects = [
    ["Commercial Office Fit-out", "Commercial", "Corporate office renovation with phased delivery and documentation."],
    ["Residential Development Support", "Residential", "Site coordination, client reporting, and billing preparation."],
    ["Operations Portal Rollout", "Operations", "Internal forms, job order tracking, document storage, and approvals."],
    ["Warehouse Improvement", "Commercial", "Procurement tracking and project update dashboard for stakeholders."]
  ];

  function renderProjects() {
    const filter = qs("#corporateFilter").value;
    qs("#corporateProjects").innerHTML = projects
      .filter((project) => filter === "All" || project[1] === filter)
      .map((project) => `<article class="workspace-card"><strong>${project[0]}</strong><span>${project[2]}</span></article>`)
      .join("");
  }

  qs("#corporateFilter").addEventListener("change", renderProjects);
  qs("#corporateForm").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    qs("#corporateOutput").textContent = `${data.name} from ${data.company} requested ${data.service}.

Details: ${data.details}

Next step: Sales team can call, send a proposal, or route this to the operations CRM.`;
  });
  renderProjects();
}

function initRealty() {
  const listings = [
    ["Parkview Condo", "Condo", "Makati", 6500000, "2BR with parking near business district.", "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=900&q=80"],
    ["Mabuhay Townhouse", "House", "Quezon City", 11200000, "Family townhouse with 3 bedrooms and pocket garden.", "https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=900&q=80"],
    ["Tagaytay View Lot", "Lot", "Tagaytay", 4800000, "Residential lot with mountain breeze and wide frontage.", "https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=80"],
    ["Bayfront Studio", "Condo", "Pasay", 3900000, "Compact studio unit for rental income or city living.", "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=80"]
  ];
  let type = "All";

  function filteredListings() {
    const query = qs("#realtySearch").value.toLowerCase();
    return listings.filter((item) => {
      const typeMatch = type === "All" || item[1] === type;
      const queryMatch = item.join(" ").toLowerCase().includes(query);
      return typeMatch && queryMatch;
    });
  }

  function renderListings() {
    const items = filteredListings();
    qs("#realtyListings").innerHTML = items.map((item) => `
      <article class="workspace-card">
        <img src="${item[5]}" alt="${item[0]}">
        <strong>${item[0]}</strong>
        <span>${item[1]} in ${item[2]} · ${money.format(item[3])}</span>
        <p>${item[4]}</p>
      </article>
    `).join("");
    qs("#realtyTourProperty").innerHTML = listings.map((item) => `<option>${item[0]}</option>`).join("");
  }

  qsa("[data-realty-type]").forEach((button) => {
    button.addEventListener("click", () => {
      qsa("[data-realty-type]").forEach((item) => item.classList.remove("active"));
      button.classList.add("active");
      type = button.dataset.realtyType;
      renderListings();
    });
  });
  qs("#realtySearch").addEventListener("input", renderListings);
  qs("#realtyCalc").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    const principal = Math.max(0, Number(data.price) - Number(data.down));
    const months = Math.max(1, Number(data.years) * 12);
    qs("#realtyPayment").textContent = `Estimated monthly payment: ${money.format(principal / months)}`;
  });
  qs("#realtyTour").addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget).entries());
    qs("#realtyOutput").textContent = `${data.name} requested a tour for ${data.property}.
Contact: ${data.phone}
Status: New buyer lead ready for agent follow-up.`;
  });
  renderListings();
}

const demo = document.body.dataset.demo;
if (demo === "booking") initBooking();
if (demo === "corporate") initCorporate();
if (demo === "realty") initRealty();
if (demo === "operations") initOperations();
if (demo === "proposal") initProposal();
if (demo === "startup") initStartup();
if (demo === "property") initProperty();
if (demo === "progreen") initProGreen();
