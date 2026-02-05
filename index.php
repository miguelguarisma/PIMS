<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['active_form'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';
session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PIMS Login</title>

<style>
/* === Background image === */
body {
  margin: 0;
  font-family: "Poppins", sans-serif;
  height: 100vh;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  background: url('pnp2.jpg') no-repeat center center/cover;
  position: relative;
}

/* === Overlay dark filter === */
body::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(0, 10, 25, 0.8);
  z-index: 0;
}

/* === Soft glow pulse behind form === */
.glow-behind {
  position: absolute;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(0,255,204,0.4) 0%, rgba(0,0,0,0) 70%);
  border-radius: 50%;
  z-index: 1;
  animation: pulseGlow 5s ease-in-out infinite alternate;
}
@keyframes pulseGlow {
  0% { transform: scale(1); opacity: 0.7; }
  100% { transform: scale(1.3); opacity: 0.3; }
}

/* === Particle canvas === */
#particles {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  z-index: 1;
  pointer-events: none;
}

/* === Container === */
.container {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.1);
  padding: 40px;
  border-radius: 20px;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 40px rgba(0,0,0,0.5);
  width: 360px;
  text-align: center;
  animation: fadeIn 1s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* === PNP Logo === */
.logo {
  width: 90px;
  height: 90px;
  margin-bottom: 20px;
  border-radius: 50%;
  box-shadow: 0 0 20px rgba(0,255,204,0.6);
  animation: glowPulse 2s infinite alternate;
}
@keyframes glowPulse {
  from { box-shadow: 0 0 10px rgba(0,255,204,0.4); }
  to { box-shadow: 0 0 25px rgba(0,255,204,0.8); }
}

/* === Form === */
.form-box h2 {
  margin-bottom: 15px;
  color: #fff;
  letter-spacing: 1px;
}

.form-box input {
  width: 90%;
  padding: 10px;
  margin: 10px 0;
  border: none;
  border-radius: 8px;
  background: rgba(255,255,255,0.2);
  color: white;
  outline: none;
  transition: all 0.3s ease;
}
.form-box input:focus {
  background: rgba(255,255,255,0.35);
  box-shadow: 0 0 10px #00ffcc;
}

.form-box button {
  width: 95%;
  padding: 12px;
  margin-top: 10px;
  border: none;
  border-radius: 8px;
  background: linear-gradient(90deg, #00ffcc, #00b3ff);
  color: #0d1b2a;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.4s ease;
}
.form-box button:hover {
  background: linear-gradient(90deg, #00b3ff, #00ffcc);
  box-shadow: 0 0 20px #00ffcc;
  transform: scale(1.05);
}

/* === Errors === */
.error-message {
  color: #ff6666;
  font-size: 0.9em;
  margin-bottom: 10px;
}

.alert {
  color: #00ffcc;
  font-size: 0.9em;
  margin-top: 10px;
}
</style>
</head>

<body>

<!-- ✨ Canvas for Particles -->
<canvas id="particles"></canvas>

<!-- 💡 Glow Effect -->
<div class="glow-behind"></div>

<!-- 🔰 Login Form -->
<div class="container">
  <img src="pnp.jpg" alt="PNP Logo" class="logo">

  <div class="form-box active <?= isActiveForm('login',$activeForm); ?>" id="login-form">
    <form action="login_register.php" method="post"> 
      <h2>Police Information Management System</h2>
      <?= showError($errors['login']); ?>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>

      <?php if (isset($_SESSION['logout_message'])): ?>
      <div class="alert"><?php echo $_SESSION['logout_message']; ?></div>
      <?php unset($_SESSION['logout_message']); ?>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- 🌀 Particle Animation with Mouse Trail -->
<script>
const canvas = document.getElementById('particles');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particlesArray = [];
const mouse = { x: null, y: null };

window.addEventListener('mousemove', function(event) {
  mouse.x = event.x;
  mouse.y = event.y;
  for (let i = 0; i < 3; i++) { // extra trail sparkle
    particlesArray.push(new Particle(mouse.x, mouse.y, 2, '#00ffcc'));
  }
});

window.addEventListener('resize', () => {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  init();
});

class Particle {
  constructor(x, y, size, color) {
    this.x = x;
    this.y = y;
    this.size = size;
    this.color = color;
    this.speedX = (Math.random() - 0.5) * 2;
    this.speedY = (Math.random() - 0.5) * 2;
    this.alpha = 1;
  }
  update() {
    this.x += this.speedX;
    this.y += this.speedY;
    this.alpha -= 0.01;
  }
  draw() {
    ctx.save();
    ctx.globalAlpha = this.alpha;
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fillStyle = this.color;
    ctx.shadowBlur = 20;
    ctx.shadowColor = this.color;
    ctx.fill();
    ctx.restore();
  }
}

function init() {
  particlesArray = [];
  for (let i = 0; i < 50; i++) {
    const x = Math.random() * canvas.width;
    const y = Math.random() * canvas.height;
    particlesArray.push(new Particle(x, y, Math.random() * 2 + 1, '#00ffcc'));
  }
}

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particlesArray = particlesArray.filter(p => p.alpha > 0);
  for (let particle of particlesArray) {
    particle.update();
    particle.draw();
  }
  requestAnimationFrame(animate);
}

init();
animate();
</script>

</body>
</html>
