const demos = [
  {
    title: "Beemo's Hidden Patch Booking",
    client: "Beemo's Hidden Patch",
    industry: "Hospitality",
    status: "Template",
    audience: "client",
    type: "Booking System",
    url: "",
    pageUrl: "pages/beemo-booking.html",
    summary: "Guest-facing resort booking flow with stay dates, accommodation selection, guest details, payment upload, amenities, policies, and booking summary.",
    tags: ["Booking", "Resort", "Payments", "Amenities"],
    color: "#0f766e",
    image: "https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80",
    icon: "BH"
  },
  {
    title: "APMCB Website",
    client: "APMCB",
    industry: "Corporate",
    status: "Template",
    audience: "client",
    type: "Website",
    url: "",
    pageUrl: "pages/apmcb-corporate.html",
    summary: "Published company website that can be used as proof of completed public-facing web work and live-domain deployment.",
    tags: ["Corporate", "Live site", "Brand"],
    color: "#2563eb",
    image: "https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80",
    icon: "AP"
  },
  {
    title: "APM Realty Website Demo",
    client: "APM Realty",
    industry: "Real Estate",
    status: "Template",
    audience: "client",
    type: "Website Template",
    url: "",
    pageUrl: "pages/apm-realty.html",
    summary: "Reusable real-estate sales presentation with property search, listing cards, buyer journey, mortgage estimator positioning, and lead capture flow.",
    tags: ["Listings", "Lead capture", "Property search"],
    color: "#a16207",
    image: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80",
    icon: "RE"
  },
  {
    title: "APM Employee and Operations System",
    client: "APM",
    industry: "Operations",
    status: "Demo Page",
    audience: "internal",
    type: "Laravel System",
    url: "",
    pageUrl: "pages/apm-operations.html",
    summary: "Business portal with employee records, payslips, leave requests, cash advances, billing documents, job orders, client records, support tickets, and audit controls.",
    tags: ["HR", "Billing", "Job orders", "Approvals"],
    color: "#334155",
    image: "https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1200&q=80",
    icon: "OP"
  },
  {
    title: "Nexii Tech AI Proposal Portal",
    client: "Nexii Tech Automations",
    industry: "Sales",
    status: "Demo Page",
    audience: "internal",
    type: "Sales Automation",
    url: "",
    pageUrl: "pages/nexii-ai-proposal.html",
    summary: "Proposal and outreach portal for generating client proposals, categorizing leads, storing client data, and connecting automation endpoints.",
    tags: ["AI proposal", "CRM", "n8n", "Lead intake"],
    color: "#7c3aed",
    image: "https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1200&q=80",
    icon: "AI"
  },
  {
    title: "Startup Service Website",
    client: "Reusable startup template",
    industry: "Agency",
    status: "Demo Page",
    audience: "client",
    type: "Website Template",
    url: "",
    pageUrl: "pages/startup-service.html",
    summary: "Multi-page services website with packages, FAQ, portfolio, and service pages that can be adapted for agencies or service businesses.",
    tags: ["Services", "Packages", "Portfolio", "FAQ"],
    color: "#be123c",
    image: "https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1200&q=80",
    icon: "ST"
  },
  {
    title: "Property Rent and Damage Tracker",
    client: "Rental operators",
    industry: "Real Estate",
    status: "Demo Page",
    audience: "client",
    type: "React Dashboard",
    url: "",
    pageUrl: "pages/property-tracker-app/index.html",
    summary: "Landlord and tenant dashboard for rent tracking, payment status, damage reports, vendor assignment, exports, print previews, and client presentation mode.",
    tags: ["Rent", "Repairs", "Tenant portal", "Reports"],
    color: "#0e7490",
    image: "https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80",
    icon: "RT"
  },
  {
    title: "ProGreen Mobile Dashboards",
    client: "ProGreen",
    industry: "Sustainability",
    status: "Demo Page",
    audience: "client",
    type: "React Native App",
    url: "",
    pageUrl: "pages/progreen-mobile-app/index.html",
    summary: "Role-based mobile dashboard concept for administrators, companies, LGUs, and users, focused on waste collection metrics and rewards.",
    tags: ["Mobile app", "Admin", "LGU", "Rewards"],
    color: "#15803d",
    image: "https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=1200&q=80",
    icon: "PG"
  }
];

const state = {
  search: "",
  industry: "all",
  status: "all",
  view: "all"
};

const grid = document.querySelector("#templateGrid");
const emptyState = document.querySelector("#emptyState");
const searchInput = document.querySelector("#searchInput");
const industryFilter = document.querySelector("#industryFilter");
const statusFilter = document.querySelector("#statusFilter");
const tabButtons = document.querySelectorAll(".tab-button");

function unique(values) {
  return [...new Set(values)].sort((a, b) => a.localeCompare(b));
}

function buildIndustryOptions() {
  unique(demos.map((demo) => demo.industry)).forEach((industry) => {
    const option = document.createElement("option");
    option.value = industry;
    option.textContent = industry;
    industryFilter.append(option);
  });
}

function matchesSearch(demo) {
  const query = state.search.trim().toLowerCase();
  if (!query) return true;

  return [
    demo.title,
    demo.client,
    demo.industry,
    demo.type,
    demo.summary,
    ...demo.tags
  ].join(" ").toLowerCase().includes(query);
}

function getFilteredDemos() {
  return demos.filter((demo) => {
    const matchesIndustry = state.industry === "all" || demo.industry === state.industry;
    const matchesStatus = state.status === "all" || demo.status === state.status;
    const matchesView = state.view === "all" || demo.audience === state.view;
    return matchesSearch(demo) && matchesIndustry && matchesStatus && matchesView;
  });
}

function createActionLink(demo) {
  const primaryUrl = demo.url || demo.pageUrl;
  const primaryLabel = demo.url ? "Open live demo" : "View template";
  const target = demo.url || primaryUrl.startsWith("https://") ? ' target="_blank" rel="noreferrer"' : "";
  const secondary = demo.url && demo.pageUrl
    ? `<a class="secondary-action" href="${demo.pageUrl}">View page</a>`
    : "";

  return `
    <div class="card-actions">
      <a class="card-action" href="${primaryUrl}"${target}>${primaryLabel}</a>
      ${secondary}
    </div>
  `;
}

function renderCards() {
  const filtered = getFilteredDemos();

  grid.innerHTML = filtered.map((demo) => `
    <article class="catalog-card">
      <div class="card-media" style="--card-color: ${demo.color}">
        <img src="${demo.image}" alt="${demo.title} preview">
        <div class="card-media-overlay"></div>
        <span class="status-pill">${demo.status} · ${demo.type}</span>
        <span class="card-icon">${demo.icon}</span>
      </div>
      <div class="card-body">
        <div>
          <h3>${demo.title}</h3>
          <p>${demo.summary}</p>
        </div>
        <div class="meta-row" aria-label="Project metadata">
          <span>${demo.client}</span>
          <span>${demo.industry}</span>
        </div>
        <div class="tag-row" aria-label="Features">
          ${demo.tags.map((tag) => `<span>${tag}</span>`).join("")}
        </div>
        ${createActionLink(demo)}
      </div>
    </article>
  `).join("");

  emptyState.hidden = filtered.length > 0;
  document.querySelector("#visibleCount").textContent = filtered.length;
}

function renderStats() {
  document.querySelector("#totalCount").textContent = demos.length;
  document.querySelector("#liveCount").textContent = demos.filter((demo) => demo.audience === "client").length;
  document.querySelector("#localCount").textContent = demos.filter((demo) => demo.pageUrl).length;
  document.querySelector("#industryCount").textContent = unique(demos.map((demo) => demo.industry)).length;
}

function bindEvents() {
  searchInput.addEventListener("input", (event) => {
    state.search = event.target.value;
    renderCards();
  });

  industryFilter.addEventListener("change", (event) => {
    state.industry = event.target.value;
    renderCards();
  });

  statusFilter.addEventListener("change", (event) => {
    state.status = event.target.value;
    renderCards();
  });

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      tabButtons.forEach((tab) => tab.classList.remove("active"));
      button.classList.add("active");
      state.view = button.dataset.view;
      renderCards();
    });
  });
}

buildIndustryOptions();
renderStats();
renderCards();
bindEvents();
