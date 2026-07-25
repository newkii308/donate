import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ชุดสีธีมที่เลือกได้ (sync กับ [data-accent] ใน app.css)
export const NL_ACCENTS = [
    { key: 'purple', label: 'ມ່ວງນີອອນ', color: '#7c3aed' },
    { key: 'blue',   label: 'ສີຟ້າ',      color: '#2563eb' },
    { key: 'cyan',   label: 'ຟ້ານີອອນ',  color: '#06b6d4' },
    { key: 'pink',   label: 'ສີບົວ',      color: '#db2777' },
    { key: 'green',  label: 'ສີຂຽວ',      color: '#059669' },
    { key: 'orange', label: 'ສີສົ້ມ',      color: '#ea580c' },
    { key: 'red',    label: 'ສີແດງ',      color: '#dc2626' },
];

// Store ธีม: โหมดมืด/สว่าง + สีหลัก (accent) — จำค่าไว้ใน localStorage
Alpine.store('theme', {
    dark: document.documentElement.classList.contains('dark'),
    accent: document.documentElement.getAttribute('data-accent') || 'purple',
    accents: NL_ACCENTS,

    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('nl-theme', this.dark ? 'dark' : 'light');
    },

    setAccent(key) {
        this.accent = key;
        document.documentElement.setAttribute('data-accent', key);
        localStorage.setItem('nl-accent', key);
    },
});

Alpine.start();
