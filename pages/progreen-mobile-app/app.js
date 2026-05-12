const app = document.querySelector("#app");

const state = {
  user: null,
  role: "USER",
  username: "Maria Santos",
  points: 1500,
  pickups: [
    { user: "Juan Dela Cruz", type: "Plastic", weight: 2.5, time: "10 mins ago", status: "Pending" },
    { user: "Maria Santos", type: "Paper", weight: 5, time: "25 mins ago", status: "Pending" }
  ],
  vouchers: [
    { name: "Free Coffee", claimed: 45, remaining: 55 },
    { name: "10% Grocery Discount", claimed: 120, remaining: 80 }
  ],
  feed: ["System ready", "Sample recycling records loaded"]
};

const roleNames = {
  USER: "User Dashboard",
  LGU: "LGU Dashboard",
  COMPANY: "Company Dashboard",
  ADMIN: "Admin Dashboard"
};

function render() {
  if (!state.user) {
    renderLogin();
    return;
  }
  renderApp();
}

function renderLogin() {
  app.innerHTML = `
    <a class="hub-link" href="../../index.html#catalog">Back to Demo Hub</a>
    <section class="login-page">
      <div class="login-shell">
        <section class="login-hero">
          <div class="brand">
            <div class="brand-mark">PG</div>
            <div><strong>ProGreen</strong><span>Recycling Rewards System</span></div>
          </div>
          <div>
            <h1>Track recycling, rewards, and verification in one mobile workflow.</h1>
            <p>This demo follows the React Native screens in your ProGreen project and turns them into a publishable client-facing web preview.</p>
          </div>
        </section>
        <section class="login-panel">
          <div>
            <p class="eyebrow">Demo login</p>
            <h2>Select a role</h2>
            <p>Choose a persona to show clients how the same app changes for users, LGUs, partners, and admins.</p>
          </div>
          <form class="login-form" id="loginForm">
            <label class="field">Username
              <input name="username" value="${state.username}" placeholder="Enter username">
            </label>
            <div class="field">
              Select Role
              <div class="role-grid">
                ${Object.keys(roleNames).map((role) => `<button class="role-button ${role === state.role ? "active" : ""}" type="button" data-role="${role}">${role}</button>`).join("")}
              </div>
            </div>
            <button class="primary-button" type="submit">LOGIN</button>
          </form>
        </section>
      </div>
    </section>
  `;

  document.querySelectorAll("[data-role]").forEach((button) => {
    button.addEventListener("click", () => {
      state.role = button.dataset.role;
      document.querySelectorAll("[data-role]").forEach((item) => item.classList.toggle("active", item === button));
    });
  });

  document.querySelector("#loginForm").addEventListener("submit", (event) => {
    event.preventDefault();
    const username = new FormData(event.currentTarget).get("username").trim();
    state.user = username || "Demo User";
    state.username = state.user;
    state.feed.push(`${state.user} signed in as ${state.role}`);
    render();
  });
}

function renderApp() {
  app.innerHTML = `
    <a class="hub-link" href="../../index.html#catalog">Back to Demo Hub</a>
    <section class="mobile-stage">
      <div class="mobile-device">
        <main class="mobile-screen">
          <header class="mobile-header">
          <div>
            <span>${state.role}</span>
            <h1>${roleNames[state.role]}</h1>
          </div>
          <div class="toolbar">
            <span class="account-chip">${state.user}</span>
          </div>
          </header>
          <section class="mobile-body">
            <p class="eyebrow">${headlineForRole()}</p>
            ${contentForRole()}
          </section>
          <nav class="mobile-tabs" aria-label="Role navigation">
            ${Object.keys(roleNames).map((role) => `<button class="${role === state.role ? "active" : ""}" type="button" data-switch-role="${role}">${role}</button>`).join("")}
          </nav>
        </main>
      </div>
    </section>
  `;

  document.querySelectorAll("[data-switch-role]").forEach((button) => {
    button.addEventListener("click", () => {
      state.role = button.dataset.switchRole;
      state.feed.push(`Switched to ${roleNames[state.role]}`);
      render();
    });
  });
  bindRoleActions();
}

function headlineForRole() {
  if (state.role === "USER") return `Welcome, ${state.user}. Redeem rewards and track your impact.`;
  if (state.role === "LGU") return "Verify recycling submissions and monitor center activity.";
  if (state.role === "COMPANY") return "Manage reward vouchers and sustainability performance.";
  return "Oversee users, partners, collections, and system reports.";
}

function metrics(items) {
  return `<section class="metric-grid">${items.map((item) => `<article class="metric-card"><span>${item[0]}</span><strong>${item[1]}</strong></article>`).join("")}</section>`;
}

function phonePreview() {
  return `
    <div class="phone-frame">
      <div class="phone-screen">
        <div class="phone-header"><strong>${roleNames[state.role]}</strong><span>Logout</span></div>
        <div class="phone-content">${phoneContent()}</div>
      </div>
    </div>
  `;
}

function phoneContent() {
  if (state.role === "USER") {
    return `
      <h2>Welcome, ${state.user}!</h2>
      <div class="points-card"><span>Total Points</span><strong>${state.points.toLocaleString()}</strong><p>Recent: Recycled 2kg PET</p></div>
      <button class="primary-button">Redeem Rewards</button>
      <button class="secondary-button">Find Collection Center</button>
      <h3>Environmental Impact</h3>
      <div class="stat-row"><div class="stat-box"><strong>15kg</strong><span>Plastic</span></div><div class="stat-box"><strong>8kg</strong><span>Paper</span></div><div class="stat-box"><strong>5kg</strong><span>Metal</span></div></div>
    `;
  }
  if (state.role === "LGU") {
    return `
      <h2>Center: ${state.user}</h2>
      <div class="stat-row"><div class="stat-box"><strong>${state.pickups.filter((item) => item.status === "Pending").length}</strong><span>Pending</span></div><div class="stat-box"><strong>450kg</strong><span>Total Today</span></div></div>
      <h3>Pending Verifications</h3>
      ${state.pickups.map((item) => `<div class="item-card"><span><strong>${item.user}</strong><small>${item.type} - ${item.weight}kg</small></span><span class="status-pill">${item.status}</span></div>`).join("")}
      <button class="primary-button">Scan QR Code</button>
    `;
  }
  if (state.role === "COMPANY") {
    return `
      <h2>Partner: ${state.user}</h2>
      <div class="points-card"><span>Total Impact Score</span><strong>85/100</strong><p>Based on redeemed vouchers and sustainability support</p></div>
      <h3>Active Reward Vouchers</h3>
      ${state.vouchers.map((voucher) => `<div class="item-card"><span><strong>${voucher.name}</strong><small>Claimed: ${voucher.claimed} | Remaining: ${voucher.remaining}</small></span><button class="mini-action">Edit</button></div>`).join("")}
    `;
  }
  return `
    <h2>System Administrator</h2>
    <div class="stat-row"><div class="stat-box"><strong>1,240</strong><span>Total Users</span></div><div class="stat-box"><strong>15</strong><span>Partners</span></div></div>
    <div class="stat-row"><div class="stat-box"><strong>8.5t</strong><span>Waste</span></div><div class="stat-box"><strong>PHP 2.4k</strong><span>Rewards</span></div></div>
    <button class="secondary-button">User Management</button>
    <button class="primary-button">Generate Global Report</button>
  `;
}

function contentForRole() {
  if (state.role === "USER") return userContent();
  if (state.role === "LGU") return lguContent();
  if (state.role === "COMPANY") return companyContent();
  return adminContent();
}

function userContent() {
  return `
    ${metrics([["Total points", state.points.toLocaleString()], ["Plastic", "15kg"], ["Paper", "8kg"], ["Metal", "5kg"]])}
    <section class="content-split">
      <div class="panel">
        <div class="panel-header"><div><p class="eyebrow">Reward actions</p><h2>User recycling workflow</h2></div></div>
        <div class="list">
          <button class="item-card" type="button" id="redeemReward"><span><strong>Redeem Rewards</strong><small>Spend 500 points on partner rewards.</small></span><span class="status-pill">Available</span></button>
          <button class="item-card" type="button" id="findCenter"><span><strong>Find Collection Center</strong><small>Locate the nearest LGU verification center.</small></span><span class="status-pill">Open</span></button>
          <form class="form-grid" id="recycleForm">
            <label class="field">Material<select name="material"><option>PET Plastic</option><option>Paper</option><option>Metal</option></select></label>
            <label class="field">Weight in kg<input name="weight" type="number" min="1" value="2"></label>
            <button class="primary-button" type="submit">Log recycling</button>
          </form>
        </div>
      </div>
    </section>
  `;
}

function lguContent() {
  return `
    ${metrics([["Pending", state.pickups.filter((item) => item.status === "Pending").length], ["Total today", "450kg"], ["Verified", state.pickups.filter((item) => item.status === "Verified").length], ["Centers", "7"]])}
    <section class="content-split">
      <div class="panel">
        <div class="panel-header"><div><p class="eyebrow">Pending verifications</p><h2>Review submitted recyclables</h2></div><button class="secondary-button" id="scanQr">Scan QR Code</button></div>
        <div class="list">${state.pickups.map((item, index) => `<article class="item-card"><span><strong>${item.user}</strong><small>${item.type} - ${item.weight}kg · ${item.time}</small></span><button class="mini-action" data-verify="${index}">${item.status === "Verified" ? "Verified" : "Verify"}</button></article>`).join("")}</div>
      </div>
    </section>
  `;
}

function companyContent() {
  return `
    ${metrics([["Impact score", "85/100"], ["Active vouchers", state.vouchers.length], ["Claimed", state.vouchers.reduce((sum, item) => sum + item.claimed, 0)], ["Remaining", state.vouchers.reduce((sum, item) => sum + item.remaining, 0)]])}
    <section class="content-split">
      <div class="panel">
        <div class="panel-header"><div><p class="eyebrow">Reward vouchers</p><h2>Partner reward management</h2></div></div>
        <form class="form-grid" id="voucherForm">
          <input name="name" placeholder="Voucher name" value="Free Eco Tote Bag">
          <input name="remaining" type="number" value="40">
          <button class="primary-button" type="submit">Add voucher</button>
        </form>
        <div class="list" style="margin-top: 14px">${state.vouchers.map((item) => `<article class="item-card"><span><strong>${item.name}</strong><small>Claimed: ${item.claimed} | Remaining: ${item.remaining}</small></span><span class="status-pill">Active</span></article>`).join("")}</div>
      </div>
    </section>
  `;
}

function adminContent() {
  return `
    ${metrics([["Total Users", "1,240"], ["Partners", "15"], ["Waste Collected", "8.5t"], ["Rewards", "PHP 2.4k"]])}
    <section class="content-split">
      <div class="panel">
        <div class="panel-header"><div><p class="eyebrow">Management tools</p><h2>System administration</h2></div></div>
        <div class="list">
          <button class="item-card" type="button" data-admin-action="User Management"><span><strong>User Management</strong><small>Review accounts, roles, and permissions.</small></span><span class="status-pill">Open</span></button>
          <button class="item-card" type="button" data-admin-action="Partner Approvals"><span><strong>Partner Approvals</strong><small>Approve reward partners and LGU centers.</small></span><span class="status-pill">3 pending</span></button>
          <button class="item-card" type="button" data-admin-action="Global Report"><span><strong>Generate Global Report</strong><small>Export program-wide collection and rewards data.</small></span><span class="status-pill">Ready</span></button>
        </div>
      </div>
      <div class="panel">
        <div class="panel-header"><div><p class="eyebrow">Activity feed</p><h2>Latest actions</h2></div></div>
        <div class="list">${state.feed.slice(-6).reverse().map((item) => `<article class="item-card"><span><strong>${item}</strong><small>Demo activity</small></span></article>`).join("")}</div>
      </div>
    </section>
  `;
}

function bindRoleActions() {
  const recycleForm = document.querySelector("#recycleForm");
  if (recycleForm) {
    recycleForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const data = Object.fromEntries(new FormData(event.currentTarget).entries());
      const weight = Number(data.weight || 0);
      state.points += weight * 50;
      state.feed.push(`${state.user} logged ${weight}kg ${data.material}`);
      render();
    });
  }

  document.querySelectorAll("[data-verify]").forEach((button) => {
    button.addEventListener("click", () => {
      const item = state.pickups[Number(button.dataset.verify)];
      item.status = "Verified";
      state.feed.push(`${item.user}'s ${item.type} submission verified`);
      render();
    });
  });

  const voucherForm = document.querySelector("#voucherForm");
  if (voucherForm) {
    voucherForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const data = Object.fromEntries(new FormData(event.currentTarget).entries());
      state.vouchers.unshift({ name: data.name, claimed: 0, remaining: Number(data.remaining || 0) });
      state.feed.push(`${data.name} voucher added`);
      render();
    });
  }

  document.querySelectorAll("[data-admin-action]").forEach((button) => {
    button.addEventListener("click", () => {
      state.feed.push(`${button.dataset.adminAction} opened`);
      render();
    });
  });

  const redeem = document.querySelector("#redeemReward");
  if (redeem) {
    redeem.addEventListener("click", () => {
      state.points = Math.max(0, state.points - 500);
      state.feed.push(`${state.user} redeemed 500 points`);
      render();
    });
  }
}

render();
