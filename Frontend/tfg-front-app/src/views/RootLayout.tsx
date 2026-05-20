import { Outlet } from 'react-router-dom'
import { Header } from '../components/header/Header'

const FOOTER_LINKS = [
  { href: '/#', label: 'Privacidad' },
  { href: '/#', label: 'Términos' },
  { href: '/#', label: 'Soporte' },
]

export function RootLayout() {
  return (
    <div className="min-h-screen bg-arcade-bg text-white flex flex-col">
      <Header />
      <div className="flex-1">
        <Outlet />
      </div>
      <footer className="border-t border-arcade-border mt-20">
        <div className="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-arcade-muted uppercase tracking-widest">
          <span>© 2026 ClassicGames Platform. Todos los derechos reservados.</span>
          <div className="flex items-center gap-6">
            {FOOTER_LINKS.map(({ href, label }) => (
              <a key={href} href={href} className="hover:text-white transition-colors">
                {label}
              </a>
            ))}
          </div>
        </div>
      </footer>
    </div>
  )
}
