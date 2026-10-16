import { spawn } from 'node:child_process';
import { mkdtemp, mkdir, readFile, rm, writeFile } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const baseUrl = process.env.DEMO_BASE_URL ?? 'http://localhost:8001';
const email = process.env.DEMO_EMAIL ?? 'admin@velntra.com';
const password = process.env.DEMO_PASSWORD ?? 'admin@velntra.com';
const chromeBinary = process.env.CHROME_BINARY ?? 'google-chrome';
const outputDir = new URL('../docs/screenshots/', import.meta.url);

const demoUrl = new URL(baseUrl);
if (!['localhost', '127.0.0.1'].includes(demoUrl.hostname)) {
    throw new Error('Este capturador solo admite una instancia demo local y aislada.');
}
if (demoUrl.port !== '8001' && process.env.DEMO_CONFIRM_ISOLATED !== '1') {
    throw new Error('Para usar un puerto diferente a 8001, confirma que la base es aislada con DEMO_CONFIRM_ISOLATED=1.');
}

const sleep = (milliseconds) => new Promise((resolve) => setTimeout(resolve, milliseconds));

class DevTools {
    constructor(socket) {
        this.socket = socket;
        this.nextId = 1;
        this.pending = new Map();
        socket.addEventListener('message', ({ data }) => {
            const message = JSON.parse(data);
            const request = this.pending.get(message.id);
            if (!request) return;
            this.pending.delete(message.id);
            if (message.error) request.reject(new Error(message.error.message));
            else request.resolve(message.result);
        });
    }

    send(method, params = {}) {
        const id = this.nextId++;
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.socket.send(JSON.stringify({ id, method, params }));
        });
    }

    async evaluate(expression) {
        const response = await this.send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
        if (response.exceptionDetails) throw new Error(response.exceptionDetails.text);
        return response.result.value;
    }

    async waitFor(expression, timeout = 20000) {
        const started = Date.now();
        while (Date.now() - started < timeout) {
            try {
                if (await this.evaluate(expression)) return;
            } catch {
                // La navegación puede destruir temporalmente el contexto JavaScript.
            }
            await sleep(250);
        }
        throw new Error(`Tiempo de espera agotado: ${expression}`);
    }
}

const profileDir = await mkdtemp(join(tmpdir(), 'velntra-demo-chrome-'));
const browser = spawn(chromeBinary, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--disable-dev-shm-usage',
    '--remote-debugging-port=0',
    `--user-data-dir=${profileDir}`,
    'about:blank',
], { stdio: 'ignore' });

let devtools;

try {
    let port;
    for (let attempt = 0; attempt < 80; attempt++) {
        try {
            port = Number((await readFile(join(profileDir, 'DevToolsActivePort'), 'utf8')).split('\n')[0]);
            break;
        } catch {
            if (browser.exitCode !== null) throw new Error('Chrome terminó antes de iniciar DevTools.');
            await sleep(250);
        }
    }
    if (!port) throw new Error('Chrome no inició DevTools.');

    const targets = await (await fetch(`http://127.0.0.1:${port}/json`)).json();
    const target = targets.find((item) => item.type === 'page');
    if (!target) throw new Error('Chrome no creó una pestaña.');

    const socket = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((resolve, reject) => {
        socket.addEventListener('open', resolve, { once: true });
        socket.addEventListener('error', reject, { once: true });
    });
    devtools = new DevTools(socket);
    await devtools.send('Page.enable');
    await devtools.send('Runtime.enable');

    async function navigate(path, readySelector) {
        await devtools.send('Page.navigate', { url: new URL(path, baseUrl).href });
        await devtools.waitFor(`document.readyState === 'complete' && !!document.querySelector(${JSON.stringify(readySelector)})`);
        await sleep(450);
    }

    async function screenshot(filename, width = 1440, height = 1000) {
        await devtools.send('Emulation.setDeviceMetricsOverride', {
            width, height, deviceScaleFactor: 1, mobile: width < 768,
        });
        await sleep(300);
        const { data } = await devtools.send('Page.captureScreenshot', {
            format: 'png', fromSurface: true, captureBeyondViewport: width >= 768,
        });
        await writeFile(new URL(filename, outputDir), Buffer.from(data, 'base64'));
        process.stdout.write(`Captura: ${filename}\n`);
    }

    await navigate('/login', '#email');
    await devtools.evaluate(`(() => {
        const email = document.querySelector('#email');
        const password = document.querySelector('#password');
        email.value = ${JSON.stringify(email)};
        password.value = ${JSON.stringify(password)};
        email.dispatchEvent(new Event('input', { bubbles: true }));
        password.dispatchEvent(new Event('input', { bubbles: true }));
        document.querySelector('form button[type="submit"]').click();
    })()`);
    await devtools.waitFor("location.pathname === '/dashboard'", 25000);

    await navigate('/products', 'main');
    if (!await devtools.evaluate("document.querySelector('main')?.textContent.includes('DEMO-CUA-001')")) {
        throw new Error('No se encontró el catálogo ficticio DEMO-*; se cancelan las capturas para evitar publicar datos reales.');
    }

    await mkdir(outputDir, { recursive: true });

    await navigate('/dashboard', 'main');
    await screenshot('dashboard.png');

    await navigate('/products', 'main');
    await screenshot('products.png');

    await navigate('/sales', 'main');
    await devtools.evaluate(`(() => {
        const product = [...document.querySelectorAll('button')]
            .find((button) => button.getAttribute('wire:click')?.startsWith('addToCart('));
        if (!product) throw new Error('No hay productos demo en el POS.');
        product.click();
    })()`);
    await devtools.waitFor("document.querySelector('main')?.textContent.includes('1 ítems')");
    await sleep(4300);
    await screenshot('pos.png');

    await navigate('/reports/sales', 'main');
    await screenshot('reports.png');

    await devtools.send('Emulation.setDeviceMetricsOverride', {
        width: 390, height: 844, deviceScaleFactor: 1, mobile: true,
    });
    await navigate('/sales', '[data-pos-tab="history"]');
    await devtools.evaluate("document.querySelector('[data-pos-tab=history]').click()");
    await devtools.waitFor("!!document.querySelector('[data-sale-history-mobile-card]')");
    await devtools.evaluate(`(() => {
        const filter = [...document.querySelectorAll('select')]
            .find((select) => select.getAttribute('wire:model.live') === 'historyStatusFilter');
        if (!filter) throw new Error('No se encontró el filtro de estado del historial.');
        filter.value = 'completed';
        filter.dispatchEvent(new Event('change', { bubbles: true }));
    })()`);
    await devtools.waitFor("document.querySelector('[data-sale-history-mobile-card]')?.textContent.includes('Completada')");
    await screenshot('sales-history-mobile.png', 390, 844);

    socket.close();
} finally {
    if (browser.exitCode === null) {
        const exited = new Promise((resolve) => browser.once('exit', resolve));
        browser.kill();
        await Promise.race([exited, sleep(3000)]);
    }
    for (let attempt = 0; attempt < 5; attempt++) {
        try {
            await rm(profileDir, { recursive: true, force: true });
            break;
        } catch (error) {
            if (error.code !== 'ENOTEMPTY' || attempt === 4) throw error;
            await sleep(300);
        }
    }
}
