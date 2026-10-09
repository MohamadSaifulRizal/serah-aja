
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
    @theme {
        --color-paper: #EDEFE2;
        --color-paperline: #C7C4AC;
        --color-ink: #22301F;
        --color-green: #2F5233;
        --color-green-deep: #203823;
        --color-rust: #A6432D;
        --color-mustard: #C98A1F;
        --color-card: #F7F6ED;
        --color-muted: #6E6B57;

        --font-sans: "IBM Plex Sans", sans-serif;
        --font-display: "Space Grotesk", sans-serif;
        --font-mono: "IBM Plex Mono", monospace;

        --animate-fadein: fadein 0.5s ease both;
        @keyframes fadein {
            0% { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    }

    /* Menjaga tampilan sama seperti Tailwind v3 (default v4 berbeda): */
    @layer base {
        *, ::after, ::before, ::backdrop, ::file-selector-button {
            border-color: var(--color-gray-200, currentColor);
        }
        button:not(:disabled), [role="button"]:not(:disabled) {
            cursor: pointer;
        }
    }
</style>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/komponen/tailwind.blade.php ENDPATH**/ ?>