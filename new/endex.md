<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f4f6fa;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .login-container {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.08);
      width: 100%;
      max-width: 370px;
    }
    .login-container h2 {
      margin: 0 0 1.2rem 0;
      color: #2563eb;
      font-weight: 700;
      letter-spacing: 1px;
      text-align: center;
    }
    .form-group {
      margin-bottom: 1.2rem;
    }
    label {
      font-size: 1rem;
      color: #222;
      margin-bottom: 0.5rem;
      display: block;
    }
    input[type="file"] {
      width: 100%;
      padding: 0.5rem;
    }
    button {
      background: #2563eb;
      color: #fff;
      border: none;
      border-radius: 5px;
      width: 100%;
      padding: 0.7rem;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      margin-top: 0.7rem;
      transition: background 0.18s;
    }
    button:hover {
      background: #1742ad;
    }
    .msg {
      color: red;
      text-align: center;
      margin-top: 0.8rem;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Admin Login</h2>
    <form action="usb-login.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="usbfile">Plug in your USB and select your Key File:</label>
        <input type="file" id="usbfile" name="usbfile" required>
      </div>
      <button type="submit">Login</button>
    </form>
    <?php if (isset($_GET['error'])): ?>
      <div class="msg">Invalid USB key. Access denied.</div>
    <?php endif; ?>
  </div>
</body>
</html>