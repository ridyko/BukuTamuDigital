const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const app = express();

app.use(express.json());

console.log('--- WHATSAPP GATEWAY SMKN 2 JAKARTA ---');
console.log('Menginisialisasi WhatsApp Client...');

const client = new Client({
    authStrategy: new LocalAuth({
        clientId: "smkn2-session"
    }),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--single-process',
            '--disable-gpu'
        ],
    }
});

// Event saat QR Code muncul
client.on('qr', (qr) => {
    console.log('\n[!] SILAKAN SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA:\n');
    qrcode.generate(qr, { small: true });
});

// Event saat berhasil login
client.on('ready', () => {
    console.log('\n[V] WhatsApp Gateway sudah siap dan terhubung!');
    console.log('Endpoint: http://localhost:3000/send-message');
});

// Event jika terputus
client.on('disconnected', (reason) => {
    console.log('WhatsApp terputus:', reason);
});

// Endpoint untuk menerima pesan dari Laravel
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: false, error: 'Nomor HP dan Pesan wajib diisi.' });
    }

    try {
        const chatId = phone.includes('@c.us') ? phone : `${phone}@c.us`;
        await client.sendMessage(chatId, message);
        
        console.log(`[LOG] Pesan terkirim ke ${phone}`);
        res.json({ status: true, message: 'Pesan berhasil dikirim.' });
    } catch (error) {
        console.error(`[ERROR] Gagal kirim ke ${phone}:`, error.message);
        res.status(500).json({ status: false, error: error.message });
    }
});

// Jalankan Server
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`\n[I] Server API berjalan di http://localhost:${PORT}`);
});

client.initialize();
