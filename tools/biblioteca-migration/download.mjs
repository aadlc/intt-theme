#!/usr/bin/env node
/**
 * Descargador de PDFs de la biblioteca del INTT.
 *
 * Lee manifest.json, descarga cada PDF siguiendo los redirects de WPDM
 * y guarda los archivos en pdfs-descargados/{categoria_slug}/{nombre.pdf}.
 * Actualiza manifest.json in-place con ruta_local y tamano_bytes.
 *
 * Uso:
 *   node download.mjs                    # descarga todo
 *   node download.mjs --limit 5          # solo primeros 5 items
 *   node download.mjs --dry-run          # sin descargar
 *   node download.mjs --delay 1000       # 1s entre requests (default 500ms)
 *   node download.mjs --skip-existing    # no re-descargar los que ya tienen ruta_local
 *
 * Requisitos: Node 18+ (para fetch nativo). El proyecto está en Node 22.
 */

import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname   = path.dirname(fileURLToPath(import.meta.url));
const MANIFEST    = path.join(__dirname, 'manifest.json');
const CARPETA_OUT = path.join(__dirname, 'pdfs-descargados');

// ── Args ──────────────────────────────────────────────────────────────────────

const args = process.argv.slice(2);
const flag = name => args.includes(name);
const val  = (name, def) => {
    const i = args.indexOf(name);
    return i === -1 ? def : args[i + 1];
};

const LIMIT         = parseInt(val('--limit', '0'), 10) || Infinity;
const DELAY_MS      = parseInt(val('--delay', '500'), 10);
const DRY_RUN       = flag('--dry-run');
const SKIP_EXISTING = flag('--skip-existing');

// ── Helpers ───────────────────────────────────────────────────────────────────

const sleep = ms => new Promise(r => setTimeout(r, ms));

function slugify(texto) {
    return texto
        .normalize('NFKD').replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function nombreArchivo(urlFinal, titulo) {
    try {
        const url = new URL(urlFinal);
        const base = path.basename(url.pathname);
        if (base && base.toLowerCase().endsWith('.pdf')) return decodeURIComponent(base);
    } catch { /* fallthrough */ }
    return `${slugify(titulo)}.pdf`;
}

async function existePath(p) {
    try { await fs.access(p); return true; } catch { return false; }
}

async function descargarPDF(urlWpdm, rutaDestino) {
    const res = await fetch(urlWpdm, {
        redirect: 'follow',
        headers: {
            'User-Agent': 'Mozilla/5.0 (Migracion biblioteca INTT - one-off)',
        },
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const ct = (res.headers.get('content-type') || '').toLowerCase();
    if (!ct.includes('pdf')) {
        throw new Error(`Content-Type inesperado: ${ct}`);
    }

    const buffer = Buffer.from(await res.arrayBuffer());
    await fs.writeFile(rutaDestino, buffer);
    return { urlFinal: res.url, tamano: buffer.length };
}

// ── Main ──────────────────────────────────────────────────────────────────────

async function main() {
    const raw   = await fs.readFile(MANIFEST, 'utf-8');
    const items = JSON.parse(raw);

    console.log(`[read ] manifest.json — ${items.length} items`);
    console.log(`[flags] limit=${LIMIT === Infinity ? 'todos' : LIMIT}, delay=${DELAY_MS}ms, dryRun=${DRY_RUN}, skipExisting=${SKIP_EXISTING}`);

    await fs.mkdir(CARPETA_OUT, { recursive: true });

    let procesados = 0;
    let ok         = 0;
    let saltados   = 0;
    let fallos     = 0;

    for (let i = 0; i < items.length; i++) {
        if (procesados >= LIMIT) break;

        const doc = items[i];
        const tituloCorto = doc.titulo.length > 70 ? doc.titulo.slice(0, 67) + '…' : doc.titulo;
        console.log(`\n[${String(i + 1).padStart(3)}/${items.length}] ${tituloCorto}`);

        if (SKIP_EXISTING && doc.ruta_local && await existePath(doc.ruta_local)) {
            console.log(`           ↷ ya descargado`);
            saltados++;
            procesados++;
            continue;
        }

        if (DRY_RUN) {
            console.log(`           dry-run: ${doc.categoria} ← ${doc.url_wpdm.slice(0, 80)}...`);
            procesados++;
            continue;
        }

        const carpetaCat = path.join(CARPETA_OUT, doc.categoria);
        await fs.mkdir(carpetaCat, { recursive: true });

        // Descarga a nombre temporal, renombra al final para tener nombre definitivo
        const temp = path.join(carpetaCat, `_tmp_${i}.pdf`);
        try {
            const { urlFinal, tamano } = await descargarPDF(doc.url_wpdm, temp);

            let nombre = nombreArchivo(urlFinal, doc.titulo);
            let ruta   = path.join(carpetaCat, nombre);

            // Evita colisiones sufijando -2, -3, etc.
            let n = 2;
            while (await existePath(ruta)) {
                const parsed = path.parse(nombre);
                const base = parsed.name.replace(/-\d+$/, '');
                nombre = `${base}-${n}${parsed.ext}`;
                ruta   = path.join(carpetaCat, nombre);
                n++;
            }
            await fs.rename(temp, ruta);

            doc.ruta_local   = ruta;
            doc.url_final    = urlFinal;
            doc.tamano_bytes = tamano;
            doc.estado       = 'ok';

            console.log(`           ✓ ${nombre} (${(tamano / 1024 / 1024).toFixed(2)} MB)`);
            ok++;
        } catch (e) {
            console.log(`           ✗ ${e.message}`);
            doc.estado = `error: ${e.message}`;
            fallos++;
            // Limpia el temp si quedó
            try { await fs.unlink(temp); } catch {}
        }

        procesados++;

        // Persiste manifest cada iteración por si se corta a mitad
        await fs.writeFile(MANIFEST, JSON.stringify(items, null, 4));

        await sleep(DELAY_MS);
    }

    console.log(`\n[done ] ok=${ok}, saltados=${saltados}, fallos=${fallos}, no-procesados=${items.length - procesados}`);
    console.log(`[done ] Manifest actualizado en ${MANIFEST}`);
    console.log(`[done ] PDFs en ${CARPETA_OUT}`);
}

main().catch(e => {
    console.error(`\n[fatal] ${e.stack || e.message}`);
    process.exit(1);
});
