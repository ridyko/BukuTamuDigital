const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require("@whiskeysockets/baileys");
const express = require("express");
const QRCode = require("qrcode");
const pino = require("pino");
const fs = require("fs");
const path = require("path");

const app = express();
app.use(express.json());

let sock;
let qrCodeText = "";
const publicPath = path.join(__dirname, '../storage/app/public');

async function connectToWhatsApp() {
    console.log("[LOG] Mencari versi WhatsApp terbaru...");
    const { version, isLatest } = await fetchLatestBaileysVersion();
    console.log(`[LOG] Menggunakan WA Web v${version.join('.')} (Latest: ${isLatest})`);

    const { state, saveCreds } = await useMultiFileAuthState('auth_session');
    
    sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: false,
        logger: pino({ level: 'silent' }),
        browser: ["Ubuntu", "Chrome", "20.0.04"], // Identitas lebih umum
    });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            qrCodeText = qr;
            console.log("[!] QR Code baru tersedia.");
            fs.writeFileSync(path.join(publicPath, 'wa_qr.txt'), qr);
        }

        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            console.log(`[!] Koneksi Terputus. Status: ${statusCode}`);
            
            // Jika status 401 atau 405, biasanya sesi sudah tidak valid
            if (statusCode === DisconnectReason.loggedOut || statusCode === 401 || statusCode === 405) {
                console.log("[!] Sesi tidak valid atau telah logout. Menghapus folder sesi...");
                if (fs.existsSync('auth_session')) fs.rmSync('auth_session', { recursive: true, force: true });
                setTimeout(() => connectToWhatsApp(), 2000);
            } else {
                console.log("[!] Mencoba menghubungkan kembali dalam 5 detik...");
                setTimeout(() => connectToWhatsApp(), 5000);
            }
        } else if (connection === 'open') {
            console.log('[V] WhatsApp Gateway BERHASIL TERHUBUNG!');
            qrCodeText = "CONNECTED";
            if (fs.existsSync(path.join(publicPath, 'wa_qr.txt'))) {
                fs.unlinkSync(path.join(publicPath, 'wa_qr.txt'));
            }
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

app.post("/send-message", async (req, res) => {
    const { phone, message } = req.body;
    if (!sock || qrCodeText !== "CONNECTED") {
        console.log(`[X] GAGAL: Mencoba kirim ke ${phone} tapi gateway belum terhubung.`);
        return res.status(500).json({ status: false, error: "Gateway belum siap" });
    }

    try {
        const id = phone.includes("@s.whatsapp.net") ? phone : `${phone}@s.whatsapp.net`;
        await sock.sendMessage(id, { text: message });
        console.log(`[SEND] Berhasil kirim pesan ke: ${phone}`);
        res.json({ status: true });
    } catch (error) {
        console.log(`[X] ERROR kirim ke ${phone}: ${error.message}`);
        res.status(500).json({ status: false, error: error.message });
    }
});

app.listen(3000, () => {
    console.log("--- WHATSAPP GATEWAY AKTIF ---");
    connectToWhatsApp();
});
