<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard with Graphs</title>
  <style>
    :root {
      --sidebar-width: 220px;
      --primary: #2563eb;
      --bg: #f4f6fa;
      --card-bg: #fff;
      --text: #222;
      --text-light: #666;
      --border: #e0e0e0;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
    }
    .sidebar {
      position: fixed;
      left: 0; top: 0; bottom: 0;
      width: var(--sidebar-width);
      background: var(--primary);
      color: #fff;
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
      z-index: 100;
      transition: transform 0.3s;
    }
    .sidebar .logo {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 2rem;
      letter-spacing: 1px;
      text-align: center;
    }
    .sidebar nav {
      flex: 1;
    }
    .sidebar nav a {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: #fff;
      padding: 0.7rem 1rem;
      border-radius: 6px;
      text-decoration: none;
      margin-bottom: 0.3rem;
      font-size: 1.04rem;
      transition: background 0.2s;
    }
    .sidebar nav a.active, .sidebar nav a:hover {
      background: rgba(255,255,255,0.12);
      font-weight: bold;
    }
    .sidebar .logout {
      margin-top: 2rem;
      color: #fff;
      text-align: center;
      cursor: pointer;
      font-size: 1rem;
      opacity: 0.8;
      transition: opacity 0.2s;
    }
    .sidebar .logout:hover {
      opacity: 1;
      text-decoration: underline;
    }
    .main {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: margin-left 0.3s;
    }
    .topbar {
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .topbar .mobile-menu {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 2rem;
      color: var(--primary);
      margin-right: 1rem;
    }
    .topbar .username {
      font-weight: bold;
      color: var(--primary);
      letter-spacing: 1px;
    }
    .content {
      flex: 1;
      padding: 2rem;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .card {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.04);
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      border: 1px solid var(--border);
      position: relative;
    }
    .card .card-title {
      font-size: 1rem;
      color: var(--text-light);
      margin-bottom: 0.6rem;
    }
    .card .card-value {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 0.2rem;
      color: var(--primary);
    }
    .card .view-graph-btn {
      margin-top: 1rem;
      padding: 0.45rem 1rem;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.98rem;
      transition: background 0.15s;
    }
    .card .view-graph-btn:hover {
      background: #1e40af;
    }
    /* Modal styles */
    .modal-bg {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.33);
      align-items: center;
      justify-content: center;
    }
    .modal-bg.active {
      display: flex;
    }
    .modal {
      background: #fff;
      padding: 2rem 1.5rem 1.4rem 1.5rem;
      border-radius: 10px;
      box-shadow: 0 6px 32px rgba(0,0,0,0.12);
      max-width: 90vw;
      width: 420px;
      position: relative;
    }
    .modal .close-btn {
      position: absolute;
      right: 1rem; top: 1rem;
      background: none;
      border: none;
      font-size: 1.4rem;
      cursor: pointer;
      color: var(--primary);
    }
    .modal h3 {
      margin: 0 0 1rem 0;
      font-size: 1.15rem;
      text-align: center;
      color: var(--primary);
    }
    /* Responsive styles */
    @media (max-width: 900px) {
      .main { margin-left: 0; }
      .sidebar {
        transform: translateX(-100%);
        position: fixed;
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .topbar .mobile-menu { display: inline-block; }
    }
    @media (max-width: 600px) {
      .content { padding: 1rem; }
      .cards { gap: 1rem; }
      .card { padding: 1rem; }
      .modal { width: 95vw; padding: 1.2rem 0.5rem 1.2rem 0.5rem; }
    }
  </style>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <div class="logo">AdminPanel</div>
    <nav>
      <a href="#" class="active">
        <!-- dashboard icon -->
        <svg width="20" height="20" fill="none"><rect x="2" y="2" width="7" height="7" rx="2" fill="#fff" /><rect x="11" y="2" width="7" height="7" rx="2" fill="#fff" fill-opacity="0.7"/><rect x="2" y="11" width="7" height="7" rx="2" fill="#fff" fill-opacity="0.7"/><rect x="11" y="11" width="7" height="7" rx="2" fill="#fff"/></svg>
        Dashboard
      </a>
      <a href="#">
        <!-- users icon -->
        <svg width="20" height="20" fill="none"><circle cx="10" cy="7" r="4" fill="#fff"/><ellipse cx="10" cy="15.5" rx="7" ry="3.5" fill="#fff" fill-opacity="0.7"/></svg>
        Users
      </a>
      <a href="#">
        <!-- analytics icon -->
        <svg width="20" height="20" fill="none"><path d="M4 16V8M10 16V4M16 16V12" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
        Analytics
      </a>
      <a href="#">
        <!-- settings icon -->
        <svg width="20" height="20" fill="none"><circle cx="10" cy="10" r="3" fill="#fff"/><path d="M2 10a8 8 0 0116 0" stroke="#fff" stroke-width="2"/></svg>
        Settings
      </a>
    </nav>
    <div class="logout" onclick="window.location.href='logout.php'">Log out</div>
  </div>
  <div class="main">
    <div class="topbar">
      <button class="mobile-menu" id="menuBtn">&#9776;</button>
      <div>Admin Dashboard</div>
      <div class="username">Admin</div>
    </div>
    <div class="content">
      <div class="cards">
        <div class="card">
          <div class="card-title">Total Users</div>
          <div class="card-value">1,204</div>
          <div style="font-size:0.9rem; color:var(--text-light);">+32 this week</div>
          <button class="view-graph-btn" data-graph="users">View Graph</button>
        </div>
        <div class="card">
          <div class="card-title">Active Sessions</div>
          <div class="card-value">478</div>
          <div style="font-size:0.9rem; color:var(--text-light);">+5.7%</div>
          <button class="view-graph-btn" data-graph="sessions">View Graph</button>
        </div>
        <div class="card">
          <div class="card-title">Revenue</div>
          <div class="card-value">$9,340</div>
          <div style="font-size:0.9rem; color:var(--text-light);">+12% MoM</div>
          <button class="view-graph-btn" data-graph="revenue">View Graph</button>
        </div>
        <div class="card">
          <div class="card-title">New Signups</div>
          <div class="card-value">71</div>
          <div style="font-size:0.9rem; color:var(--text-light);">Last 24h</div>
          <button class="view-graph-btn" data-graph="signups">View Graph</button>
        </div>
      </div>
      <div class="chart">
        [Analytics Chart Placeholder]
      </div>
    </div>
  </div>
  <!-- Modal for Graph -->
  <div class="modal-bg" id="modalBg">
    <div class="modal">
      <button class="close-btn" id="closeModal">&times;</button>
      <h3 id="modalTitle">Graph</h3>
      <canvas id="modalChart" width="370" height="230"></canvas>
    </div>
  </div>
  <script>
    // Responsive sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    menuBtn.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
    sidebar.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        if(window.innerWidth < 900) sidebar.classList.remove('open');
      });
    });

    // Modal + Chart.js logic
    const modalBg = document.getElementById('modalBg');
    const closeModal = document.getElementById('closeModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalChart = document.getElementById('modalChart');
    let chartInstance = null;

    // Sample data for each graph
    const graphData = {
      users: {
        title: 'Total Users (Last 6 Months)',
        labels: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        data: [850, 930, 1040, 1130, 1190, 1204],
        color: 'rgba(37,99,235,0.8)'
      },
      sessions: {
        title: 'Active Sessions (Last 7 Days)',
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [410, 415, 420, 430, 445, 465, 478],
        color: 'rgba(99,102,241,0.8)'
      },
      revenue: {
        title: 'Revenue ($, Last 6 Months)',
        labels: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        data: [5200, 6000, 7500, 8000, 8900, 9340],
        color: 'rgba(34,197,94,0.8)'
      },
      signups: {
        title: 'New Signups (Last 7 Days)',
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [10, 15, 8, 13, 12, 6, 7],
        color: 'rgba(251,191,36,0.8)'
      }
    };

    document.querySelectorAll('.view-graph-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const graphKey = btn.getAttribute('data-graph');
        const { title, labels, data, color } = graphData[graphKey];
        modalTitle.textContent = title;

        // Destroy previous chart if any
        if(chartInstance) chartInstance.destroy();

        chartInstance = new Chart(modalChart, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: title,
              data,
              fill: false,
              borderColor: color,
              backgroundColor: color,
              tension: 0.3,
              pointRadius: 4,
            }]
          },
          options: {
            responsive: false,
            plugins: { legend: { display: false } },
            scales: {
              y: { beginAtZero: false, ticks: { color: '#333', font: { size: 13 } } },
              x: { ticks: { color: '#333', font: { size: 13 } } }
            }
          }
        });

        modalBg.classList.add('active');
      });
    });

    closeModal.addEventListener('click', () => {
      modalBg.classList.remove('active');
      if(chartInstance) chartInstance.destroy();
    });

    // Close modal when clicking outside modal
    modalBg.addEventListener('click', (e) => {
      if(e.target === modalBg) {
        modalBg.classList.remove('active');
        if(chartInstance) chartInstance.destroy();
      }
    });
  </script>
</body>
</html>