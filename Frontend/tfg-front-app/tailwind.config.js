/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        arcade: {
          bg: '#0d0b1a',
          surface: '#141028',
          card: '#1a1633',
          border: '#2d2550',
          purple: '#a855f7',
          cyan: '#00e5ff',
          pink: '#f0abfc',
          green: '#22c55e',
          muted: '#6b7280',
        },
      },
    },
  },
  plugins: [],
}
