const express = require('express');
const nodemailer = require('nodemailer');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
app.use(express.static(path.join(__dirname)));

// ── Route envoi email ──
app.post('/send_email.php', async (req, res) => {
  const { nom, email, objet, message } = req.body;

  if (!nom || !email || !message) {
    return res.status(400).json({ error: 'Champs requis manquants' });
  }

  const transporter = nodemailer.createTransport({
    host: 'smtp.gmail.com',
    port: 465,
    secure: true,
    auth: {
      user: process.env.SMTP_USER,
      pass: process.env.SMTP_PASS,
    },
  });

  try {
    await transporter.sendMail({
      from: `"ANIMA TERRAE" <${process.env.SMTP_USER}>`,
      to: 'remi@animaterrae.fr',
      replyTo: email,
      subject: objet
        ? `[${objet}] Message de ${nom} via animaterrae.fr`
        : `Message de ${nom} via animaterrae.fr`,
      text: `Nouveau message reçu depuis le formulaire de contact ANIMA TERRAE
─────────────────────────────────────────

Nom    : ${nom}
Email  : ${email}
Objet  : ${objet}

Message :
${message}

─────────────────────────────────────────
Envoyé depuis animaterrae.fr`,
    });

    res.json({ success: true });
  } catch (err) {
    console.error('Erreur envoi email:', err);
    res.status(500).json({ error: 'Erreur lors de l\'envoi' });
  }
});

// ── Route principale ──
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Serveur démarré sur le port ${PORT}`);
});
