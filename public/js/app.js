// /* ============================================================
//    BUKU KAS DAPUR MBG — versi HTML/CSS/JS + Tailwind
//    Aplikasi pendataan & akuntansi barang untuk dapur program
//    Makan Bergizi Gratis. Dua jenis barang:
//    - Barang Olahan   : bahan baku, stok masuk dari pembelian,
//                         harga fluktuatif, dipakai habis jadi makanan.
//    - Barang Inventaris: aset dapur (AC, mobil, showcase, dll),
//                         rutin butuh biaya service tiap periode.
//    ============================================================ */

// /* ---------- utils ---------- */
// const uid = () => Math.random().toString(36).slice(2, 10);
// const todayStr = () => new Date().toISOString().slice(0, 10);
// const idr = (n) =>
//   "Rp" + Math.round(n || 0).toLocaleString("id-ID", { maximumFractionDigits: 0 });
// const fmtDate = (d) =>
//   new Date(d).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" });
// const monthKey = (d) => d.slice(0, 7);
// const thisMonthKey = () => todayStr().slice(0, 7);
// const escapeHtml = (s) =>
//   String(s ?? "").replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

// const STORAGE_KEY = "mbg-buku-kas-data";

// const seedData = () => ({
//   items: [
//     { id: "i1", name: "Beras", type: "olahan", unit: "kg", category: "Pokok", createdAt: "2026-05-02" },
//     { id: "i2", name: "Ayam Potong", type: "olahan", unit: "kg", category: "Protein", createdAt: "2026-05-02" },
//     { id: "i3", name: "Sayur Bayam", type: "olahan", unit: "ikat", category: "Sayur", createdAt: "2026-05-04" },
//     { id: "i4", name: "Tempe", type: "olahan", unit: "papan", category: "Protein", createdAt: "2026-05-04" },
//     { id: "i5", name: "Minyak Goreng", type: "olahan", unit: "liter", category: "Bumbu & Minyak", createdAt: "2026-05-05" },
//     { id: "v1", name: "AC Ruang Dapur 1", type: "inventaris", category: "Elektronik", intervalBulan: 3, createdAt: "2026-04-01" },
//     { id: "v2", name: "Mobil Distribusi B 1234 CD", type: "inventaris", category: "Kendaraan", intervalBulan: 1, createdAt: "2026-04-01" },
//     { id: "v3", name: "Showcase Pendingin", type: "inventaris", category: "Elektronik", intervalBulan: 2, createdAt: "2026-04-15" },
//   ],
//   moves: [
//     { id: uid(), itemId: "i1", date: "2026-06-01", type: "masuk", qty: 100, unitPrice: 12500, note: "Pembelian rutin - Toko Sumber Rejeki" },
//     { id: uid(), itemId: "i1", date: "2026-06-20", type: "keluar", qty: 60, note: "Diolah untuk menu mingguan" },
//     { id: uid(), itemId: "i1", date: "2026-07-02", type: "masuk", qty: 80, unitPrice: 13200, note: "Harga naik, musim panen selesai" },
//     { id: uid(), itemId: "i2", date: "2026-06-05", type: "masuk", qty: 40, unitPrice: 34000, note: "Pembelian - Pasar Tanjung" },
//     { id: uid(), itemId: "i2", date: "2026-06-18", type: "keluar", qty: 25, note: "Diolah untuk menu ayam" },
//     { id: uid(), itemId: "i2", date: "2026-07-04", type: "masuk", qty: 30, unitPrice: 37500, note: "Harga naik jelang libur" },
//     { id: uid(), itemId: "i3", date: "2026-06-10", type: "masuk", qty: 50, unitPrice: 3500, note: "Pembelian sayur segar" },
//     { id: uid(), itemId: "i3", date: "2026-06-25", type: "keluar", qty: 40, note: "Menu sayur bening" },
//     { id: uid(), itemId: "i4", date: "2026-06-08", type: "masuk", qty: 60, unitPrice: 4200, note: "Pembelian tempe" },
//     { id: uid(), itemId: "i5", date: "2026-06-03", type: "masuk", qty: 20, unitPrice: 16500, note: "Minyak goreng curah" },
//   ],
//   services: [
//     { id: uid(), itemId: "v1", date: "2026-04-10", cost: 250000, vendor: "CV Sejuk Abadi", note: "Cuci & isi freon" },
//     { id: uid(), itemId: "v2", date: "2026-05-10", cost: 450000, vendor: "Bengkel Jaya Motor", note: "Servis rutin + ganti oli" },
//     { id: uid(), itemId: "v2", date: "2026-06-10", cost: 380000, vendor: "Bengkel Jaya Motor", note: "Servis rutin" },
//     { id: uid(), itemId: "v3", date: "2026-05-01", cost: 300000, vendor: "CV Sejuk Abadi", note: "Perbaikan kompresor" },
//   ],
//   expenses: [
//     { id: uid(), date: "2026-06-01", category: "Listrik", description: "Tagihan listrik dapur Juni", amount: 850000 },
//     { id: uid(), date: "2026-06-01", category: "Gas LPG", description: "Isi ulang tabung 3x", amount: 220000 },
//     { id: uid(), date: "2026-06-05", category: "Air", description: "Tagihan PDAM", amount: 150000 },
//   ],
// });

// /* ---------- storage (localStorage — untuk file HTML mandiri) ---------- */
// function loadData() {
//   try {
//     const raw = localStorage.getItem(STORAGE_KEY);
//     if (raw) return JSON.parse(raw);
//   } catch (e) {
//     /* belum ada / rusak */
//   }
//   return null;
// }
// function saveData(data) {
//   try {
//     localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
//   } catch (e) {
//     console.error("Gagal menyimpan data", e);
//   }
// }

// /* ---------- accounting helpers ---------- */
// function buildLedger(moves, itemId) {
//   const rows = moves.filter((m) => m.itemId === itemId).sort((a, b) => a.date.localeCompare(b.date));
//   let stock = 0;
//   let avg = 0;
//   const out = [];
//   for (const m of rows) {
//     let value = 0;
//     if (m.type === "masuk") {
//       const newStock = stock + m.qty;
//       avg = newStock > 0 ? (stock * avg + m.qty * m.unitPrice) / newStock : m.unitPrice;
//       stock = newStock;
//       value = m.qty * m.unitPrice;
//     } else {
//       value = m.qty * avg;
//       stock = Math.max(0, stock - m.qty);
//     }
//     out.push({ ...m, saldo: stock, avgAfter: avg, nilai: value });
//   }
//   return { rows: out, stock, avg };
// }

// function nextServiceDate(item, services) {
//   const list = services.filter((s) => s.itemId === item.id).sort((a, b) => b.date.localeCompare(a.date));
//   if (!list.length) return null;
//   const last = new Date(list[0].date);
//   last.setMonth(last.getMonth() + (item.intervalBulan || 1));
//   return last.toISOString().slice(0, 10);
// }

// /* ============================================================
//    ICONS (SVG inline)
//    ============================================================ */
// const Ico = {
//   dash: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><rect x="3" y="3" width="7" height="9" rx="1" fill="currentColor"/><rect x="14" y="3" width="7" height="5" rx="1" fill="currentColor"/><rect x="14" y="12" width="7" height="9" rx="1" fill="currentColor"/><rect x="3" y="16" width="7" height="5" rx="1" fill="currentColor"/></svg>`,
//   box: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><path d="M3 7l9-4 9 4-9 4-9-4z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 7v10l9 4 9-4V7" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 11v10" stroke="currentColor" stroke-width="1.6"/></svg>`,
//   leaf: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><path d="M20 4C10 4 4 10 4 18c0 1 1 2 2 2 8 0 14-6 14-16z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M6 18C10 14 14 10 19 5" stroke="currentColor" stroke-width="1.6"/></svg>`,
//   gear: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><circle cx="12" cy="12" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1" stroke="currentColor" stroke-width="1.6"/></svg>`,
//   wallet: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><rect x="3" y="6" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 10h18" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14" r="1.2" fill="currentColor"/></svg>`,
//   report: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><path d="M6 3h9l4 4v14H6z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 12h6M9 16h6M9 8h3" stroke="currentColor" stroke-width="1.6"/></svg>`,
//   plus: () => `<svg viewBox="0 0 24 24" width="16" height="16" class="shrink-0"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>`,
//   x: () => `<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>`,
//   in: () => `<svg viewBox="0 0 24 24" width="14" height="14" class="shrink-0"><path d="M12 4v12M6 10l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
//   out: () => `<svg viewBox="0 0 24 24" width="14" height="14" class="shrink-0"><path d="M12 20V8M6 14l6-6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
//   menu: () => `<svg viewBox="0 0 24 24" width="16" height="16" class="shrink-0"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>`,
// };

// const NAV = [
//   { id: "dashboard", label: "Ringkasan", icon: "dash" },
//   { id: "master", label: "Master Barang", icon: "box" },
//   { id: "olahan", label: "Barang Olahan", icon: "leaf" },
//   { id: "inventaris", label: "Barang Inventaris", icon: "gear" },
//   { id: "pengeluaran", label: "Pengeluaran Lain", icon: "wallet" },
//   { id: "laporan", label: "Laporan", icon: "report" },
// ];

// /* ============================================================
//    STATE
//    ============================================================ */
// const S = {
//   data: null,
//   page: "dashboard",
//   selectedItem: null,
//   navOpen: false,
//   masterFilter: "semua",
//   laporanMonth: null,
//   modal: null, // { type, forcedType?, itemId?, moveType? }
//   modalItemType: "olahan",
// };

// function persist() {
//   saveData(S.data);
// }
// function mutate(fn) {
//   fn(S.data);
//   persist();
//   render();
// }

// /* ============================================================
//    SMALL REUSABLE PIECES
//    ============================================================ */
// const inputCls = "px-2.5 py-2 border border-paperline rounded-md text-sm bg-white w-full";
// const btnPrimary =
//   "inline-flex items-center gap-1.5 bg-green text-[#F7F6ED] font-semibold text-sm px-4 py-2.5 rounded-md hover:bg-green-deep transition-colors";
// const btnPrimaryFull = btnPrimary + " w-full justify-center mt-1.5";
// const btnGhostIn =
//   "inline-flex items-center gap-1.5 border-[1.5px] border-mustard text-[#7a5613] px-3.5 py-2 rounded-md text-[13px] hover:bg-mustard/10 transition-colors";
// const btnGhostOut =
//   "inline-flex items-center gap-1.5 border-[1.5px] border-rust text-rust px-3.5 py-2 rounded-md text-[13px] hover:bg-rust/10 transition-colors";

// function field(label, inputHtml) {
//   return `<label class="flex flex-col gap-1.5">
//     <span class="text-[11px] uppercase tracking-wide text-muted">${label}</span>
//     ${inputHtml}
//   </label>`;
// }

// function stamp(text, tone) {
//   const toneClass =
//     tone === "green" ? "text-green border-green" : tone === "mustard" ? "text-[#7a5613] border-mustard" : "text-rust border-rust";
//   return `<span class="font-mono font-semibold text-[11px] tracking-wider border-2 ${toneClass} px-3 py-1.5 rounded -rotate-3 inline-block whitespace-nowrap">${text}</span>`;
// }

// function statCard(label, value, note, ink) {
//   if (ink) {
//     return `<div class="bg-green-deep text-[#EFEAD8] rounded-lg p-4 sm:p-4.5">
//       <div class="text-xs text-[#EFEAD8]/70 mb-1.5">${label}</div>
//       <div class="font-mono text-xl font-semibold break-words">${value}</div>
//       <div class="text-[11.5px] text-[#EFEAD8]/55 mt-1.5">${note}</div>
//     </div>`;
//   }
//   return `<div class="bg-card border border-paperline rounded-lg p-4 sm:p-4.5">
//     <div class="text-xs text-muted mb-1.5">${label}</div>
//     <div class="font-mono text-xl font-semibold break-words">${value}</div>
//     <div class="text-[11.5px] text-muted mt-1.5">${note}</div>
//   </div>`;
// }

// function breakdownRow(label, value, total) {
//   if (total) {
//     return `<li class="flex justify-between pt-2.5 mt-1 border-t-2 border-ink font-semibold text-sm"><span>${label}</span><b class="font-mono">${value}</b></li>`;
//   }
//   return `<li class="flex justify-between py-2 border-b border-dashed border-paperline text-sm gap-3"><span>${label}</span><b class="font-mono whitespace-nowrap">${value}</b></li>`;
// }

// function tag(text, kind) {
//   const map = {
//     olahan: "bg-[#F1E2BE] text-[#7a5613]",
//     inventaris: "bg-[#F3DBD3] text-rust",
//     in: "bg-[#E4E9DC] text-green-deep",
//     out: "bg-[#F3DBD3] text-rust",
//   };
//   return `<span class="text-[11px] px-2.5 py-1 rounded-full font-semibold whitespace-nowrap ${map[kind] || ""}">${text}</span>`;
// }

// function statMini(label, value, danger) {
//   return `<div class="bg-paper border border-paperline rounded-md px-3.5 py-2 flex flex-col gap-0.5 min-w-[130px]">
//     <span class="text-[10.5px] text-muted uppercase tracking-wide">${label}</span>
//     <b class="font-mono text-[15px] ${danger ? "text-rust" : ""}">${value}</b>
//   </div>`;
// }

// function thCell(text) {
//   return `<th class="text-left font-mono text-[10.5px] uppercase tracking-wide text-muted px-3.5 py-2.5 border-b-2 border-ink whitespace-nowrap">${text}</th>`;
// }

// /* ============================================================
//    PAGES
//    ============================================================ */
// function pageDashboard(ctx) {
//   const { totalKasKeluarBulan, sumMasukBulan, sumPemakaianBulan, sumServiceBulan, sumExpenseBulan, totalNilaiStok, dueSoon } = ctx;
//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Ringkasan Bulan Ini · ${new Date().toLocaleDateString(
//           "id-ID",
//           { month: "long", year: "numeric" }
//         )}</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Kondisi kas & stok dapur</h1>
//       </div>
//       ${stamp("TERCATAT", "green")}
//     </header>

//     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-7">
//       ${statCard("Kas Keluar Bulan Ini", idr(totalKasKeluarBulan), "pembelian bahan + service + pengeluaran lain", true)}
//       ${statCard("Nilai Stok Barang Olahan", idr(totalNilaiStok), "dihitung dengan harga rata-rata tertimbang")}
//       ${statCard("Pemakaian Bahan (non-kas)", idr(sumPemakaianBulan), "nilai bahan terpakai dari stok yang ada")}
//       ${statCard("Biaya Service Inventaris", idr(sumServiceBulan), "AC, kendaraan, showcase, dll")}
//     </div>

//     <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-4 items-start">
//       <section class="bg-card border border-paperline rounded-lg p-4 sm:p-5">
//         <h2 class="font-display text-base text-green-deep mb-3">Rincian arus kas bulan ini</h2>
//         <ul class="list-none m-0 p-0">
//           ${breakdownRow("Pembelian bahan olahan", idr(sumMasukBulan))}
//           ${breakdownRow("Service barang inventaris", idr(sumServiceBulan))}
//           ${breakdownRow("Pengeluaran lain-lain", idr(sumExpenseBulan))}
//           ${breakdownRow("Total kas keluar", idr(totalKasKeluarBulan), true)}
//         </ul>
//         <p class="text-[13px] text-muted mt-3">Catatan: <em>pemakaian bahan</em> tidak dihitung sebagai kas keluar karena bukan transaksi uang baru — hanya konsumsi stok yang sudah dibeli.</p>
//       </section>

//       <section class="bg-card border border-paperline rounded-lg p-4 sm:p-5">
//         <h2 class="font-display text-base text-green-deep mb-3">Jadwal service mendatang</h2>
//         ${dueSoon.length === 0 ? `<p class="text-[13px] text-muted">Belum ada barang inventaris terdaftar.</p>` : ""}
//         <ul class="list-none m-0 p-0 flex flex-col gap-2">
//           ${dueSoon
//             .map(({ it, due }) => {
//               const overdue = due < todayStr();
//               return `
//               <li class="flex justify-between items-center gap-3 rounded-md border p-2.5 ${
//                 overdue ? "border-rust bg-[#F5E5E0]" : "border-paperline bg-paper"
//               }">
//                 <div class="min-w-0">
//                   <div class="font-semibold text-[13.5px] truncate">${escapeHtml(it.name)}</div>
//                   <div class="text-[11.5px] text-muted truncate">${escapeHtml(it.category)}</div>
//                 </div>
//                 <div class="text-right shrink-0">
//                   <div class="font-mono text-[13px]">${fmtDate(due)}</div>
//                   <button data-action="goto-inv" data-id="${it.id}" class="text-[11.5px] text-green hover:underline">lihat kartu →</button>
//                 </div>
//               </li>`;
//             })
//             .join("")}
//         </ul>
//       </section>
//     </div>
//   </div>`;
// }

// function pageMaster(ctx) {
//   const { items } = ctx;
//   const filter = S.masterFilter;
//   const shown = items.filter((i) => filter === "semua" || i.type === filter);
//   const chips = ["semua", "olahan", "inventaris"]
//     .map(
//       (f) => `
//     <button data-action="filter-master" data-filter="${f}" class="px-3.5 py-1.5 rounded-full text-xs border transition-colors ${
//         filter === f ? "bg-green text-white border-green" : "bg-card border-paperline hover:border-green"
//       }">
//       ${f === "semua" ? "Semua" : f === "olahan" ? "Barang Olahan" : "Barang Inventaris"}
//     </button>`
//     )
//     .join("");

//   const rows = shown
//     .map(
//       (i) => `
//     <tr>
//       <td class="font-mono font-semibold px-3.5 py-2.5 border-b border-paperline">${escapeHtml(i.name)}</td>
//       <td class="px-3.5 py-2.5 border-b border-paperline">${tag(i.type === "olahan" ? "Olahan" : "Inventaris", i.type)}</td>
//       <td class="px-3.5 py-2.5 border-b border-paperline">${escapeHtml(i.category)}</td>
//       <td class="font-mono px-3.5 py-2.5 border-b border-paperline whitespace-nowrap">${
//         i.type === "olahan" ? escapeHtml(i.unit) : `tiap ${i.intervalBulan} bulan`
//       }</td>
//       <td class="font-mono px-3.5 py-2.5 border-b border-paperline whitespace-nowrap">${fmtDate(i.createdAt)}</td>
//       <td class="px-3.5 py-2.5 border-b border-paperline"><button data-action="delete-item" data-id="${
//         i.id
//       }" class="text-[11.5px] text-rust hover:underline">hapus</button></td>
//     </tr>`
//     )
//     .join("");

//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Data Induk</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Master Barang</h1>
//       </div>
//       <button data-action="open-modal" data-modal="item" class="${btnPrimary}">${Ico.plus()} Barang Baru</button>
//     </header>
//     <div class="flex gap-2 mb-4 flex-wrap">${chips}</div>
//     <div class="overflow-x-auto bg-card border border-paperline rounded-lg">
//       <table class="w-full min-w-[640px] border-collapse text-sm">
//         <thead><tr>${["Nama Barang", "Jenis", "Kategori", "Satuan / Interval", "Terdaftar", ""].map(thCell).join("")}</tr></thead>
//         <tbody>${rows || `<tr><td colspan="6" class="text-center text-muted py-6">Belum ada barang pada kategori ini.</td></tr>`}</tbody>
//       </table>
//     </div>
//   </div>`;
// }

// function pageOlahan(ctx) {
//   const { olahanItems, ledgers } = ctx;
//   const selected = olahanItems.find((i) => i.id === S.selectedItem) || olahanItems[0];
//   const ledger = selected ? ledgers[selected.id] : null;

//   const list = olahanItems
//     .map((it) => {
//       const l = ledgers[it.id];
//       const active = selected && selected.id === it.id;
//       return `<button data-action="select-item" data-id="${it.id}" class="w-full flex justify-between items-center gap-3 bg-card border rounded-lg p-2.5 text-left transition-colors ${
//         active ? "border-green ring-1 ring-green" : "border-paperline hover:border-green/50"
//       }">
//       <div class="min-w-0">
//         <div class="font-semibold text-[13.5px] truncate">${escapeHtml(it.name)}</div>
//         <div class="text-[11.5px] text-muted truncate">${escapeHtml(it.category)} · ${escapeHtml(it.unit)}</div>
//       </div>
//       <div class="text-right shrink-0">
//         <div class="font-mono font-semibold text-sm">${l ? l.stock : 0} ${escapeHtml(it.unit)}</div>
//         <div class="text-[11.5px] text-muted font-mono">avg ${idr(l ? l.avg : 0)}</div>
//       </div>
//     </button>`;
//     })
//     .join("");

//   let detail = "";
//   if (selected) {
//     const rows = ledger.rows
//       .slice()
//       .reverse()
//       .map(
//         (r) => `
//       <tr>
//         <td class="font-mono px-3.5 py-2 border-b border-paperline whitespace-nowrap">${fmtDate(r.date)}</td>
//         <td class="px-3.5 py-2 border-b border-paperline">${tag(r.type === "masuk" ? "Masuk" : "Keluar", r.type === "masuk" ? "in" : "out")}</td>
//         <td class="font-mono px-3.5 py-2 border-b border-paperline whitespace-nowrap">${r.qty} ${escapeHtml(selected.unit)}</td>
//         <td class="font-mono px-3.5 py-2 border-b border-paperline whitespace-nowrap">${r.type === "masuk" ? idr(r.unitPrice) : "—"}</td>
//         <td class="font-mono px-3.5 py-2 border-b border-paperline whitespace-nowrap">${idr(r.nilai)}</td>
//         <td class="font-mono font-semibold px-3.5 py-2 border-b border-paperline">${r.saldo}</td>
//         <td class="text-muted text-xs px-3.5 py-2 border-b border-paperline">${escapeHtml(r.note || "")}</td>
//       </tr>`
//       )
//       .join("");

//     detail = `
//     <div class="bg-card border border-paperline rounded-xl p-4 sm:p-5">
//       <div class="flex justify-between items-start gap-3 mb-4 border-b-2 border-dashed border-paperline pb-3.5">
//         <div class="min-w-0">
//           <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Kartu Stok Barang</div>
//           <h2 class="font-display text-base text-green-deep truncate">${escapeHtml(selected.name)}</h2>
//           <div class="text-[11.5px] text-muted">${escapeHtml(selected.category)} · satuan ${escapeHtml(selected.unit)}</div>
//         </div>
//         ${stamp("OLAHAN", "mustard")}
//       </div>
//       <div class="flex gap-3.5 mb-4 flex-wrap">
//         ${statMini("Saldo Stok", `${ledger.stock} ${selected.unit}`)}
//         ${statMini("Harga Rata-rata", idr(ledger.avg))}
//         ${statMini("Nilai Stok", idr(ledger.stock * ledger.avg))}
//       </div>
//       <div class="flex gap-2.5 mb-4 flex-wrap">
//         <button data-action="open-modal" data-modal="move" data-item-id="${selected.id}" data-move-type="masuk" class="${btnGhostIn}">${Ico.in()} Catat Pembelian (Masuk)</button>
//         <button data-action="open-modal" data-modal="move" data-item-id="${selected.id}" data-move-type="keluar" class="${btnGhostOut}">${Ico.out()} Catat Pemakaian (Keluar)</button>
//       </div>
//       <div class="overflow-x-auto bg-card border border-paperline rounded-lg">
//         <table class="w-full min-w-[680px] border-collapse text-[13px]">
//           <thead><tr>${["Tanggal", "Tipe", "Qty", "Harga Satuan", "Nilai", "Saldo", "Catatan"].map(thCell).join("")}</tr></thead>
//           <tbody>${rows || `<tr><td colspan="7" class="text-center text-muted py-6">Belum ada transaksi.</td></tr>`}</tbody>
//         </table>
//       </div>
//     </div>`;
//   }

//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Bahan Baku · Stok Fluktuatif</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Barang Olahan</h1>
//       </div>
//       <button data-action="open-modal" data-modal="item" data-forced-type="olahan" class="${btnPrimary}">${Ico.plus()} Bahan Baru</button>
//     </header>
//     <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-4 items-start">
//       <div class="flex flex-col gap-1.5">${list || `<p class="text-[13px] text-muted">Belum ada barang olahan. Tambahkan bahan baru dulu.</p>`}</div>
//       ${detail}
//     </div>
//   </div>`;
// }

// function pageInventaris(ctx) {
//   const { inventarisItems, services } = ctx;
//   const selected = inventarisItems.find((i) => i.id === S.selectedItem) || inventarisItems[0];
//   const list = selected ? services.filter((s) => s.itemId === selected.id).sort((a, b) => b.date.localeCompare(a.date)) : [];
//   const due = selected ? nextServiceDate(selected, services) : null;
//   const totalCost = list.reduce((s, x) => s + x.cost, 0);

//   const itemList = inventarisItems
//     .map((it) => {
//       const d = nextServiceDate(it, services);
//       const overdue = d && d < todayStr();
//       const active = selected && selected.id === it.id;
//       return `<button data-action="select-item" data-id="${it.id}" class="w-full flex justify-between items-center gap-3 bg-card border rounded-lg p-2.5 text-left transition-colors ${
//         active ? "border-green ring-1 ring-green" : "border-paperline hover:border-green/50"
//       }">
//       <div class="min-w-0">
//         <div class="font-semibold text-[13.5px] truncate">${escapeHtml(it.name)}</div>
//         <div class="text-[11.5px] text-muted truncate">${escapeHtml(it.category)} · tiap ${it.intervalBulan} bln</div>
//       </div>
//       <div class="text-right shrink-0">
//         <div class="font-mono font-semibold text-sm ${overdue ? "text-rust" : ""}">${d ? fmtDate(d) : "belum diservis"}</div>
//         <div class="text-[11.5px] text-muted">jatuh tempo</div>
//       </div>
//     </button>`;
//     })
//     .join("");

//   let detail = "";
//   if (selected) {
//     const rows = list
//       .map(
//         (s) => `
//       <tr>
//         <td class="font-mono px-3.5 py-2 border-b border-paperline whitespace-nowrap">${fmtDate(s.date)}</td>
//         <td class="px-3.5 py-2 border-b border-paperline">${escapeHtml(s.vendor)}</td>
//         <td class="font-mono font-semibold px-3.5 py-2 border-b border-paperline whitespace-nowrap">${idr(s.cost)}</td>
//         <td class="text-muted text-xs px-3.5 py-2 border-b border-paperline">${escapeHtml(s.note || "")}</td>
//       </tr>`
//       )
//       .join("");

//     const overdueNow = due && due < todayStr();
//     detail = `
//     <div class="bg-card border border-paperline rounded-xl p-4 sm:p-5">
//       <div class="flex justify-between items-start gap-3 mb-4 border-b-2 border-dashed border-paperline pb-3.5">
//         <div class="min-w-0">
//           <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Kartu Servis Barang</div>
//           <h2 class="font-display text-base text-green-deep truncate">${escapeHtml(selected.name)}</h2>
//           <div class="text-[11.5px] text-muted">${escapeHtml(selected.category)} · interval ${selected.intervalBulan} bulan</div>
//         </div>
//         ${stamp("INVENTARIS", "rust")}
//       </div>
//       <div class="flex gap-3.5 mb-4 flex-wrap">
//         ${statMini("Total Biaya Service", idr(totalCost))}
//         ${statMini("Jumlah Service", `${list.length}x`)}
//         ${statMini("Jatuh Tempo Berikutnya", due ? fmtDate(due) : "-", overdueNow)}
//       </div>
//       <div class="flex gap-2.5 mb-4 flex-wrap">
//         <button data-action="open-modal" data-modal="service" data-item-id="${selected.id}" class="${btnGhostOut}">${Ico.plus()} Catat Service Baru</button>
//       </div>
//       <div class="overflow-x-auto bg-card border border-paperline rounded-lg">
//         <table class="w-full min-w-[480px] border-collapse text-[13px]">
//           <thead><tr>${["Tanggal", "Vendor", "Biaya", "Catatan"].map(thCell).join("")}</tr></thead>
//           <tbody>${rows || `<tr><td colspan="4" class="text-center text-muted py-6">Belum ada riwayat service.</td></tr>`}</tbody>
//         </table>
//       </div>
//     </div>`;
//   }

//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Aset Dapur · Butuh Service Berkala</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Barang Inventaris</h1>
//       </div>
//       <button data-action="open-modal" data-modal="item" data-forced-type="inventaris" class="${btnPrimary}">${Ico.plus()} Aset Baru</button>
//     </header>
//     <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-4 items-start">
//       <div class="flex flex-col gap-1.5">${itemList || `<p class="text-[13px] text-muted">Belum ada barang inventaris. Tambahkan aset baru dulu.</p>`}</div>
//       ${detail}
//     </div>
//   </div>`;
// }

// function pagePengeluaran(ctx) {
//   const { expenses } = ctx;
//   const sorted = expenses.slice().sort((a, b) => b.date.localeCompare(a.date));
//   const total = expenses.reduce((s, e) => s + e.amount, 0);
//   const rows = sorted
//     .map(
//       (e) => `
//     <tr>
//       <td class="font-mono px-3.5 py-2.5 border-b border-paperline whitespace-nowrap">${fmtDate(e.date)}</td>
//       <td class="px-3.5 py-2.5 border-b border-paperline">${tag(escapeHtml(e.category), "in")}</td>
//       <td class="px-3.5 py-2.5 border-b border-paperline">${escapeHtml(e.description || "")}</td>
//       <td class="font-mono font-semibold px-3.5 py-2.5 border-b border-paperline whitespace-nowrap">${idr(e.amount)}</td>
//     </tr>`
//     )
//     .join("");

//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Di Luar Bahan & Service</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Pengeluaran Lain</h1>
//       </div>
//       <button data-action="open-modal" data-modal="expense" class="${btnPrimary}">${Ico.plus()} Catat Pengeluaran</button>
//     </header>
//     <div class="bg-green-deep text-[#EFEAD8] rounded-lg p-4 sm:p-4.5 max-w-full sm:max-w-xs mb-6">
//       <div class="text-xs text-[#EFEAD8]/70 mb-1.5">Total Pengeluaran Lain</div>
//       <div class="font-mono text-xl font-semibold">${idr(total)}</div>
//       <div class="text-[11.5px] text-[#EFEAD8]/55 mt-1.5">listrik, air, gas, gaji, dll</div>
//     </div>
//     <div class="overflow-x-auto bg-card border border-paperline rounded-lg">
//       <table class="w-full min-w-[560px] border-collapse text-sm">
//         <thead><tr>${["Tanggal", "Kategori", "Deskripsi", "Jumlah"].map(thCell).join("")}</tr></thead>
//         <tbody>${rows || `<tr><td colspan="4" class="text-center text-muted py-6">Belum ada pengeluaran tercatat.</td></tr>`}</tbody>
//       </table>
//     </div>
//   </div>`;
// }

// function pageLaporan(ctx) {
//   const { moves, services, expenses, olahanItems, inventarisItems } = ctx;
//   const monthsSet = new Set();
//   moves.forEach((m) => monthsSet.add(monthKey(m.date)));
//   services.forEach((s) => monthsSet.add(monthKey(s.date)));
//   expenses.forEach((e) => monthsSet.add(monthKey(e.date)));
//   monthsSet.add(thisMonthKey());
//   const months = Array.from(monthsSet).sort();
//   if (!S.laporanMonth) S.laporanMonth = thisMonthKey();
//   const month = S.laporanMonth;

//   const monthMoves = moves.filter((m) => monthKey(m.date) === month);
//   const pembelian = monthMoves.filter((m) => m.type === "masuk").reduce((s, m) => s + m.qty * m.unitPrice, 0);
//   const monthServices = services.filter((s) => monthKey(s.date) === month);
//   const biayaService = monthServices.reduce((s, x) => s + x.cost, 0);
//   const monthExpenses = expenses.filter((e) => monthKey(e.date) === month);
//   const lainLain = monthExpenses.reduce((s, e) => s + e.amount, 0);
//   const totalKas = pembelian + biayaService + lainLain;
//   const maxBar = Math.max(pembelian, biayaService, lainLain, 1);

//   const byItemPembelian = {};
//   monthMoves
//     .filter((m) => m.type === "masuk")
//     .forEach((m) => {
//       byItemPembelian[m.itemId] = (byItemPembelian[m.itemId] || 0) + m.qty * m.unitPrice;
//     });

//   const options = months
//     .map(
//       (m) =>
//         `<option value="${m}" ${m === month ? "selected" : ""}>${new Date(m + "-01").toLocaleDateString("id-ID", {
//           month: "long",
//           year: "numeric",
//         })}</option>`
//     )
//     .join("");

//   const barRow = (label, value, colorClass) => `
//     <div class="grid grid-cols-[80px_1fr_64px] sm:grid-cols-[150px_1fr_100px] items-center gap-2 sm:gap-2.5 mb-3">
//       <span class="text-[11px] sm:text-[12.5px] truncate">${label}</span>
//       <div class="bg-paper rounded-full h-2.5 overflow-hidden"><div class="${colorClass} h-full rounded-full" style="width:${(value / maxBar) * 100}%"></div></div>
//       <span class="font-mono text-[11px] sm:text-xs text-right whitespace-nowrap">${idr(value)}</span>
//     </div>`;

//   const pembelianList = olahanItems
//     .map((it) => (byItemPembelian[it.id] ? breakdownRow(escapeHtml(it.name), idr(byItemPembelian[it.id])) : ""))
//     .join("");
//   const serviceList = monthServices
//     .map((s) => {
//       const it = inventarisItems.find((i) => i.id === s.itemId);
//       return breakdownRow(`${escapeHtml(it ? it.name : "")} — ${escapeHtml(s.vendor)}`, idr(s.cost));
//     })
//     .join("");

//   return `
//   <div>
//     <header class="flex flex-wrap items-end justify-between gap-4 mb-6">
//       <div>
//         <div class="font-mono uppercase tracking-wider text-[11px] text-muted mb-1">Rekap Akuntansi</div>
//         <h1 class="font-display text-2xl sm:text-[28px] text-green-deep">Laporan Bulanan</h1>
//       </div>
//       <select data-action="change-month" class="font-mono px-3 py-2 rounded-md border border-paperline bg-card text-sm">${options}</select>
//     </header>
//     <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-4 items-start">
//       <section class="bg-card border border-paperline rounded-lg p-4 sm:p-5">
//         <h2 class="font-display text-base text-green-deep mb-3">Komposisi Kas Keluar</h2>
//         ${barRow("Pembelian bahan olahan", pembelian, "bg-mustard")}
//         ${barRow("Service inventaris", biayaService, "bg-rust")}
//         ${barRow("Pengeluaran lain", lainLain, "bg-green")}
//         <ul class="list-none m-0 p-0">${breakdownRow("Total kas keluar bulan ini", idr(totalKas), true)}</ul>
//       </section>
//       <section class="bg-card border border-paperline rounded-lg p-4 sm:p-5">
//         <h2 class="font-display text-base text-green-deep mb-3">Pembelian per Bahan</h2>
//         <ul class="list-none m-0 p-0">${pembelianList}</ul>
//         ${Object.keys(byItemPembelian).length === 0 ? `<p class="text-[13px] text-muted">Tidak ada pembelian bulan ini.</p>` : ""}
//         <h2 class="font-display text-base text-green-deep mb-3 mt-5">Service Bulan Ini</h2>
//         <ul class="list-none m-0 p-0">${serviceList}</ul>
//         ${monthServices.length === 0 ? `<p class="text-[13px] text-muted">Tidak ada service bulan ini.</p>` : ""}
//       </section>
//     </div>
//   </div>`;
// }

// function renderPage(ctx) {
//   switch (S.page) {
//     case "dashboard":
//       return pageDashboard(ctx);
//     case "master":
//       return pageMaster(ctx);
//     case "olahan":
//       return pageOlahan(ctx);
//     case "inventaris":
//       return pageInventaris(ctx);
//     case "pengeluaran":
//       return pagePengeluaran(ctx);
//     case "laporan":
//       return pageLaporan(ctx);
//     default:
//       return "";
//   }
// }

// /* ============================================================
//    MODALS
//    ============================================================ */
// function renderModal() {
//   if (!S.modal) return "";
//   const m = S.modal;
//   let title = "";
//   let body = "";

//   if (m.type === "item") {
//     const type = m.forcedType || S.modalItemType || "olahan";
//     title = "Tambah Barang Baru";
//     body = `
//     <div class="flex flex-col gap-3">
//       ${
//         !m.forcedType
//           ? `
//       <div class="flex flex-col gap-1.5">
//         <span class="text-[11px] uppercase tracking-wide text-muted">Jenis Barang</span>
//         <div class="flex border border-paperline rounded-md overflow-hidden">
//           <button type="button" data-action="set-item-type" data-type="olahan" class="flex-1 py-2 text-xs transition-colors ${
//             type === "olahan" ? "bg-green text-white" : "bg-white"
//           }">Barang Olahan</button>
//           <button type="button" data-action="set-item-type" data-type="inventaris" class="flex-1 py-2 text-xs transition-colors ${
//             type === "inventaris" ? "bg-green text-white" : "bg-white"
//           }">Barang Inventaris</button>
//         </div>
//       </div>`
//           : ""
//       }
//       ${field("Nama Barang", `<input id="f-name" placeholder="mis. Wortel" class="${inputCls}">`)}
//       ${field("Kategori", `<input id="f-category" placeholder="mis. Sayur" class="${inputCls}">`)}
//       ${
//         type === "olahan"
//           ? field(
//               "Satuan",
//               `<select id="f-unit" class="${inputCls}">${["kg", "liter", "ikat", "papan", "pack", "butir"]
//                 .map((u) => `<option>${u}</option>`)
//                 .join("")}</select>`
//             )
//           : field("Interval Service (bulan)", `<input id="f-interval" type="number" min="1" value="1" class="${inputCls}">`)
//       }
//       <input type="hidden" id="f-type" value="${type}">
//       <button data-action="submit-item" class="${btnPrimaryFull}">Simpan Barang</button>
//     </div>`;
//   } else if (m.type === "move") {
//     const items = S.data.items.filter((i) => i.type === "olahan");
//     const isMasuk = m.moveType === "masuk";
//     const currentId = m.itemId || (items[0] && items[0].id);
//     const currentItem = items.find((i) => i.id === currentId);
//     title = isMasuk ? "Catat Pembelian (Masuk)" : "Catat Pemakaian (Keluar)";
//     body = `
//     <div class="flex flex-col gap-3">
//       ${field(
//         "Barang",
//         `<select id="f-item" class="${inputCls}">${items
//           .map((i) => `<option value="${i.id}" ${i.id === currentId ? "selected" : ""}>${escapeHtml(i.name)}</option>`)
//           .join("")}</select>`
//       )}
//       ${field("Tanggal", `<input id="f-date" type="date" value="${todayStr()}" class="${inputCls}">`)}
//       ${field(`Jumlah (${currentItem ? escapeHtml(currentItem.unit) : ""})`, `<input id="f-qty" type="number" min="0" class="${inputCls}">`)}
//       ${
//         isMasuk
//           ? field(
//               "Harga Satuan (harga bisa berubah setiap pembelian)",
//               `<input id="f-price" type="number" min="0" placeholder="mis. 13500" class="${inputCls}">`
//             )
//           : ""
//       }
//       ${field(
//         "Catatan",
//         `<input id="f-note" placeholder="${isMasuk ? "mis. Toko Sumber Rejeki" : "mis. diolah untuk menu"}" class="${inputCls}">`
//       )}
//       <input type="hidden" id="f-movetype" value="${m.moveType}">
//       <button data-action="submit-move" class="${btnPrimaryFull}">Simpan Transaksi</button>
//     </div>`;
//   } else if (m.type === "service") {
//     const items = S.data.items.filter((i) => i.type === "inventaris");
//     const currentId = m.itemId || (items[0] && items[0].id);
//     title = "Catat Service Barang Inventaris";
//     body = `
//     <div class="flex flex-col gap-3">
//       ${field(
//         "Barang",
//         `<select id="f-item" class="${inputCls}">${items
//           .map((i) => `<option value="${i.id}" ${i.id === currentId ? "selected" : ""}>${escapeHtml(i.name)}</option>`)
//           .join("")}</select>`
//       )}
//       ${field("Tanggal", `<input id="f-date" type="date" value="${todayStr()}" class="${inputCls}">`)}
//       ${field("Biaya Service", `<input id="f-cost" type="number" min="0" placeholder="mis. 350000" class="${inputCls}">`)}
//       ${field("Vendor / Bengkel", `<input id="f-vendor" placeholder="mis. CV Sejuk Abadi" class="${inputCls}">`)}
//       ${field("Catatan", `<input id="f-note" placeholder="mis. isi freon" class="${inputCls}">`)}
//       <button data-action="submit-service" class="${btnPrimaryFull}">Simpan Service</button>
//     </div>`;
//   } else if (m.type === "expense") {
//     title = "Catat Pengeluaran Lain";
//     body = `
//     <div class="flex flex-col gap-3">
//       ${field("Tanggal", `<input id="f-date" type="date" value="${todayStr()}" class="${inputCls}">`)}
//       ${field(
//         "Kategori",
//         `<select id="f-category" class="${inputCls}">${["Listrik", "Air", "Gas LPG", "Gaji", "Transportasi", "Lainnya"]
//           .map((c) => `<option>${c}</option>`)
//           .join("")}</select>`
//       )}
//       ${field("Deskripsi", `<input id="f-description" placeholder="mis. Tagihan listrik Juli" class="${inputCls}">`)}
//       ${field("Jumlah", `<input id="f-amount" type="number" min="0" class="${inputCls}">`)}
//       <button data-action="submit-expense" class="${btnPrimaryFull}">Simpan Pengeluaran</button>
//     </div>`;
//   }

//   return `
//   <div id="modal-veil" class="fixed inset-0 bg-black/55 z-50 flex items-center justify-center p-4">
//     <div class="bg-card rounded-xl w-full max-w-md max-h-[88vh] overflow-y-auto p-5">
//       <div class="flex justify-between items-center mb-3.5">
//         <h3 class="font-display text-lg text-green-deep">${title}</h3>
//         <button data-action="close-modal" class="text-ink hover:text-rust transition-colors">${Ico.x()}</button>
//       </div>
//       ${body}
//     </div>
//   </div>`;
// }

// /* ============================================================
//    SIDEBAR + SHELL
//    ============================================================ */
// function renderSidebar() {
//   const navItems = NAV.map(
//     (n) => `
//     <button data-action="goto" data-page="${n.id}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm transition-colors ${
//       S.page === n.id ? "bg-mustard text-[#2b1c04] font-semibold" : "text-[#EFEAD8]/80 hover:bg-white/10"
//     }">
//       ${Ico[n.icon]()}
//       <span>${n.label}</span>
//     </button>`
//   ).join("");

//   return `
//   <aside class="fixed md:static inset-y-0 left-0 z-40 w-64 sm:w-60 shrink-0 bg-green-deep text-[#EFEAD8] flex flex-col p-4 border-r-[3px] border-double border-[#16281a] transform transition-transform duration-200 ${
//     S.navOpen ? "translate-x-0" : "-translate-x-full"
//   } md:translate-x-0">
//     <div class="flex items-center gap-2.5 pb-5 border-b border-white/15 mb-3.5">
//       <div class="w-9 h-9 rounded-md bg-mustard text-[#2b1c04] flex items-center justify-center font-display font-bold text-[13px] -rotate-3 shrink-0">MBG</div>
//       <div class="min-w-0">
//         <div class="font-display font-semibold text-[15px] leading-tight">Buku Kas Dapur</div>
//         <div class="text-[11px] opacity-75">Makan Bergizi Gratis</div>
//       </div>
//       <button data-action="toggle-nav" class="ml-auto md:hidden text-[#EFEAD8]/70 hover:text-white">${Ico.x()}</button>
//     </div>
//     <nav class="flex flex-col gap-0.5 flex-1 overflow-y-auto">${navItems}</nav>
//     <div class="text-[11px] opacity-70 pt-3.5 border-t border-white/15 font-mono">Periode aktif: <b>${fmtDate(todayStr())}</b></div>
//   </aside>`;
// }

// function render() {
//   const root = document.getElementById("root");
//   const items = S.data.items;
//   const moves = S.data.moves;
//   const services = S.data.services;
//   const expenses = S.data.expenses;
//   const olahanItems = items.filter((i) => i.type === "olahan");
//   const inventarisItems = items.filter((i) => i.type === "inventaris");

//   const ledgers = {};
//   olahanItems.forEach((it) => (ledgers[it.id] = buildLedger(moves, it.id)));

//   const monthNow = thisMonthKey();
//   const sumMasukBulan = moves
//     .filter((m) => m.type === "masuk" && monthKey(m.date) === monthNow)
//     .reduce((s, m) => s + m.qty * m.unitPrice, 0);
//   const sumPemakaianBulan = Object.values(ledgers)
//     .flatMap((l) => l.rows)
//     .filter((r) => r.type === "keluar" && monthKey(r.date) === monthNow)
//     .reduce((s, r) => s + r.nilai, 0);
//   const sumServiceBulan = services.filter((s) => monthKey(s.date) === monthNow).reduce((s, x) => s + x.cost, 0);
//   const sumExpenseBulan = expenses.filter((e) => monthKey(e.date) === monthNow).reduce((s, e) => s + e.amount, 0);
//   const totalKasKeluarBulan = sumMasukBulan + sumServiceBulan + sumExpenseBulan;
//   const totalNilaiStok = Object.values(ledgers).reduce((s, l) => s + l.stock * l.avg, 0);

//   const dueSoon = inventarisItems
//     .map((it) => ({ it, due: nextServiceDate(it, services) }))
//     .filter((x) => x.due)
//     .sort((a, b) => a.due.localeCompare(b.due));

//   const ctx = {
//     items,
//     moves,
//     services,
//     expenses,
//     olahanItems,
//     inventarisItems,
//     ledgers,
//     totalKasKeluarBulan,
//     sumMasukBulan,
//     sumPemakaianBulan,
//     sumServiceBulan,
//     sumExpenseBulan,
//     totalNilaiStok,
//     dueSoon,
//   };

//   root.innerHTML = `
//     <div class="flex min-h-screen w-full">
//       ${renderSidebar()}
//       ${S.navOpen ? `<div data-action="toggle-nav" class="fixed inset-0 bg-black/40 z-30 md:hidden"></div>` : ""}
//       <button data-action="toggle-nav" class="md:hidden fixed top-3 left-3 z-40 flex items-center gap-1.5 bg-green-deep text-white px-3 py-2 rounded-md text-xs font-mono shadow-md">${Ico.menu()} Menu</button>
//       <main class="flex-1 min-w-0 px-4 pt-16 pb-8 sm:px-6 md:px-10 md:py-8 md:pt-8">
//         ${renderPage(ctx)}
//       </main>
//     </div>
//     ${renderModal()}
//   `;
// }

// /* ============================================================
//    EVENT DELEGATION
//    ============================================================ */
// function submitItem() {
//   const type = document.getElementById("f-type") ? document.getElementById("f-type").value : S.modalItemType;
//   const name = document.getElementById("f-name").value.trim();
//   const category = document.getElementById("f-category").value.trim();
//   if (!name) return;
//   let payload;
//   if (type === "olahan") {
//     const unit = document.getElementById("f-unit").value;
//     payload = { name, type, category, unit };
//   } else {
//     const interval = Number(document.getElementById("f-interval").value) || 1;
//     payload = { name, type, category, intervalBulan: interval };
//   }
//   S.modal = null;
//   mutate((d) => {
//     d.items.push({ id: uid(), createdAt: todayStr(), ...payload });
//   });
// }

// function submitMove() {
//   const itemId = document.getElementById("f-item").value;
//   const date = document.getElementById("f-date").value;
//   const qty = Number(document.getElementById("f-qty").value);
//   const moveType = document.getElementById("f-movetype").value;
//   const isMasuk = moveType === "masuk";
//   const priceEl = document.getElementById("f-price");
//   const unitPrice = priceEl ? Number(priceEl.value) : undefined;
//   const note = document.getElementById("f-note").value;
//   if (!qty || (isMasuk && !unitPrice)) return;
//   S.modal = null;
//   mutate((d) => {
//     d.moves.push({ id: uid(), itemId, date, type: moveType, qty, unitPrice: isMasuk ? unitPrice : undefined, note });
//   });
// }

// function submitService() {
//   const itemId = document.getElementById("f-item").value;
//   const date = document.getElementById("f-date").value;
//   const cost = Number(document.getElementById("f-cost").value);
//   const vendor = document.getElementById("f-vendor").value.trim();
//   const note = document.getElementById("f-note").value;
//   if (!cost || !vendor) return;
//   S.modal = null;
//   mutate((d) => {
//     d.services.push({ id: uid(), itemId, date, cost, vendor, note });
//   });
// }

// function submitExpense() {
//   const date = document.getElementById("f-date").value;
//   const category = document.getElementById("f-category").value;
//   const description = document.getElementById("f-description").value;
//   const amount = Number(document.getElementById("f-amount").value);
//   if (!amount) return;
//   S.modal = null;
//   mutate((d) => {
//     d.expenses.push({ id: uid(), date, category, description, amount });
//   });
// }

// document.addEventListener("click", (e) => {
//   if (e.target.id === "modal-veil") {
//     S.modal = null;
//     render();
//     return;
//   }
//   const el = e.target.closest("[data-action]");
//   if (!el) return;
//   const action = el.dataset.action;

//   switch (action) {
//     case "toggle-nav":
//       S.navOpen = !S.navOpen;
//       render();
//       break;
//     case "goto":
//       S.page = el.dataset.page;
//       S.selectedItem = null;
//       S.navOpen = false;
//       render();
//       break;
//     case "goto-inv":
//       S.page = "inventaris";
//       S.selectedItem = el.dataset.id;
//       S.navOpen = false;
//       render();
//       break;
//     case "filter-master":
//       S.masterFilter = el.dataset.filter;
//       render();
//       break;
//     case "select-item":
//       S.selectedItem = el.dataset.id;
//       render();
//       break;
//     case "open-modal":
//       S.modal = {
//         type: el.dataset.modal,
//         forcedType: el.dataset.forcedType,
//         itemId: el.dataset.itemId,
//         moveType: el.dataset.moveType,
//       };
//       S.modalItemType = el.dataset.forcedType || "olahan";
//       render();
//       break;
//     case "close-modal":
//       S.modal = null;
//       render();
//       break;
//     case "set-item-type":
//       S.modalItemType = el.dataset.type;
//       render();
//       break;
//     case "delete-item": {
//       const id = el.dataset.id;
//       mutate((d) => {
//         d.items = d.items.filter((i) => i.id !== id);
//         d.moves = d.moves.filter((mv) => mv.itemId !== id);
//         d.services = d.services.filter((s) => s.itemId !== id);
//       });
//       break;
//     }
//     case "submit-item":
//       submitItem();
//       break;
//     case "submit-move":
//       submitMove();
//       break;
//     case "submit-service":
//       submitService();
//       break;
//     case "submit-expense":
//       submitExpense();
//       break;
//   }
// });

// document.addEventListener("change", (e) => {
//   if (e.target.id === "f-item" && S.modal && (S.modal.type === "move" || S.modal.type === "service")) {
//     S.modal.itemId = e.target.value;
//     render();
//   } else if (e.target.dataset && e.target.dataset.action === "change-month") {
//     S.laporanMonth = e.target.value;
//     render();
//   }
// });

// /* ============================================================
//    INIT
//    ============================================================ */
// function init() {
//   S.data = loadData() || seedData();
//   render();
// }
// init();
