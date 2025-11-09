// ----------------- CONFIGURATION -----------------
const AUTH_API = "http://43.204.140.219:8001";
const REPORT_API = "http://43.204.140.219:8002";
const VIEW_API = "http://43.204.140.219:8003";

// ----------------- APPLICATION STATE -----------------
let currentUser = null;
let issues = [];

// ----------------- DOM ELEMENTS -----------------
const loginPage = document.getElementById("loginPage");
const registerPage = document.getElementById("registerPage");
const userDashboard = document.getElementById("userDashboard");
const adminDashboard = document.getElementById("adminDashboard");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const reportPage = document.getElementById("reportPage");
const viewPage = document.getElementById("viewPage");

// ----------------- INITIALIZE -----------------
document.addEventListener("DOMContentLoaded", () => {
  const savedUser = localStorage.getItem("currentUser");
  if (savedUser) {
    currentUser = JSON.parse(savedUser);
    showDashboard();
  }
  setupEventListeners();
  loadIssues();
});

// ----------------- EVENT LISTENERS -----------------
function setupEventListeners() {
  document.getElementById("loginForm").addEventListener("submit", handleLogin);
  document.getElementById("registerForm").addEventListener("submit", handleRegister);

  document.getElementById("showRegister").addEventListener("click", () => {
    loginPage.classList.add("hidden");
    registerPage.classList.remove("hidden");
  });

  document.getElementById("showLogin").addEventListener("click", () => {
    registerPage.classList.add("hidden");
    loginPage.classList.remove("hidden");
  });

  document.getElementById("reportForm").addEventListener("submit", handleReportSubmit);

  document.getElementById("logoutBtn").addEventListener("click", handleLogout);
  document.getElementById("adminLogoutBtn").addEventListener("click", handleLogout);

  document.getElementById("showReportPage").addEventListener("click", showReportPage);
  document.getElementById("showViewPage").addEventListener("click", showViewPage);
  document.getElementById("goToReport").addEventListener("click", showReportPage);
}

// ----------------- DASHBOARD TOGGLES -----------------
function showReportPage() {
  reportPage.classList.remove("hidden");
  viewPage.classList.add("hidden");
}

function showViewPage() {
  reportPage.classList.add("hidden");
  viewPage.classList.remove("hidden");
  loadUserIssues();
}

// ----------------- LOGIN -----------------
async function handleLogin(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const loginData = {
    email: formData.get("email"),
    password: formData.get("password"),
    userType: formData.get("userType"),
  };

  try {
    const response = await fetch(`${AUTH_API}/login.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(loginData),
    });
    const result = await response.json();

    if (result.success) {
      currentUser = result.user;
      localStorage.setItem("currentUser", JSON.stringify(currentUser));
      showDashboard();
    } else {
      alert("Login failed: " + result.message);
    }
  } catch (error) {
    console.error("Login error:", error);
    alert("Login failed. Please try again.");
  }
}

// ----------------- REGISTER -----------------
async function handleRegister(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const registerData = {
    name: formData.get("name"),
    email: formData.get("email"),
    password: formData.get("password"),
    phone: formData.get("phone"),
  };

  try {
    const response = await fetch(`${AUTH_API}/register.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(registerData),
    });
    const result = await response.json();

    if (result.success) {
      alert("Registration successful! Please login.");
      registerPage.classList.add("hidden");
      loginPage.classList.remove("hidden");
    } else {
      alert("Registration failed: " + result.message);
    }
  } catch (error) {
    console.error("Registration error:", error);
    alert("Registration failed. Please try again.");
  }
}

// ----------------- DASHBOARD -----------------
function showDashboard() {
  loginPage.classList.add("hidden");
  registerPage.classList.add("hidden");

  if (currentUser.type === "admin") {
    adminDashboard.classList.remove("hidden");
    document.getElementById("adminName").textContent = currentUser.name;
    loadAdminData();
  } else {
    userDashboard.classList.remove("hidden");
    document.getElementById("userName").textContent = currentUser.name;
    showReportPage();
  }
}

// ----------------- REPORT ISSUE -----------------
async function handleReportSubmit(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.append("userId", currentUser.id);

  try {
    const response = await fetch(`${REPORT_API}/report.php`, {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.success) {
      alert("Issue reported successfully!");
      e.target.reset();
      showViewPage();
    } else {
      alert("Failed to submit issue: " + result.message);
    }
  } catch (error) {
    console.error("Submit error:", error);
    alert("Network error while submitting issue.");
  }
}

// ----------------- VIEW ISSUES -----------------
async function loadIssues() {
  try {
    const response = await fetch(`${VIEW_API}/view.php`);
    const result = await response.json();
    if (result.success) issues = result.issues;
  } catch (error) {
    console.error("Load issues error:", error);
  }
}

function loadUserIssues() {
  const issuesList = document.getElementById("issuesList");
  loadingState.classList.remove("hidden");
  emptyState.classList.add("hidden");

  issuesList.querySelectorAll(".issue-card").forEach((card) => card.remove());

  setTimeout(() => {
    loadingState.classList.add("hidden");
    if (issues.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    }
    issues.forEach((issue) => {
      const issueElement = createIssueCard(issue);
      issuesList.appendChild(issueElement);
    });
  }, 500);
}

function createIssueCard(issue) {
  const div = document.createElement("div");
  div.className =
    "issue-card glass-card rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-teal-100";

  const statusColors = {
    pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
    "in-progress": "bg-blue-100 text-blue-800 border-blue-200",
    resolved: "bg-green-100 text-green-800 border-green-200",
  };

  // ✅ Handle image URL (with fallback for relative paths)
  let imageUrl = issue.image_url;
  if (imageUrl && !imageUrl.startsWith("http")) {
    imageUrl = "http://43.204.140.219:8002/" + imageUrl.replace(/^\/+/, "");
  }

  const imageHtml = imageUrl
    ? `<div class="mb-6">
         <img src="${imageUrl}" 
              alt="Issue image"
              class="w-full h-64 object-cover rounded-xl border-2 border-teal-200 shadow-md" 
              onerror="this.style.display='none'">
       </div>`
    : "";

  div.innerHTML = `
    ${imageHtml}
    <div class="flex justify-between items-start mb-4">
      <h3 class="font-bold text-xl text-gray-800">${issue.title}</h3>
      <span class="px-4 py-2 rounded-full text-xs font-bold border ${
        statusColors[issue.status] || statusColors.pending
      }">
        ${(issue.status || "pending").toUpperCase()}
      </span>
    </div>
    <p class="text-gray-700 mb-6">${issue.description}</p>
    <div class="flex justify-between text-sm text-gray-600">
      <span>${issue.category}</span>
      <span>${issue.location}</span>
    </div>
  `;

  return div;
}

// ----------------- ADMIN -----------------
function loadAdminData() {
  updateAdminStats();
  loadAdminIssues();
}

function updateAdminStats() {
  const total = issues.length;
  const pending = issues.filter((i) => i.status === "pending").length;
  const inProgress = issues.filter((i) => i.status === "in-progress").length;
  const resolved = issues.filter((i) => i.status === "resolved").length;

  document.getElementById("totalIssues").textContent = total;
  document.getElementById("pendingIssues").textContent = pending;
  document.getElementById("inProgressIssues").textContent = inProgress;
  document.getElementById("resolvedIssues").textContent = resolved;
}

function loadAdminIssues() {
  const tbody = document.getElementById("adminIssuesList");
  tbody.innerHTML = "";
  issues.forEach((issue) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="py-3 px-4">#${issue.id}</td>
      <td class="py-3 px-4">${issue.title}</td>
      <td class="py-3 px-4">${issue.category}</td>
      <td class="py-3 px-4">${issue.location}</td>
      <td class="py-3 px-4">${issue.status}</td>
    `;
    tbody.appendChild(row);
  });
}

// ----------------- LOGOUT -----------------
function handleLogout() {
  currentUser = null;
  localStorage.removeItem("currentUser");
  userDashboard.classList.add("hidden");
  adminDashboard.classList.add("hidden");
  loginPage.classList.remove("hidden");
}
