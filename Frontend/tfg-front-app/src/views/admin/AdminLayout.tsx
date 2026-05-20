import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'

const NAV_ITEMS = [
  { to: '/admin', label: 'Dashboard', end: true },
  { to: '/admin/usuarios', label: 'Usuarios', end: false },
  { to: '/admin/juegos', label: 'Juegos', end: false },
  { to: '/admin/logros', label: 'Logros', end: false },
  { to: '/admin/amigos', label: 'Amigos', end: false },
  { to: '/admin/puntuaciones', label: 'Puntuaciones', end: false },
  { to: '/admin/resultados', label: 'Resultados', end: false },
]

export function AdminLayout() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  function handleLogout() {
    logout()
    navigate('/')
  }

  return (
    <div className="min-h-screen flex bg-gray-950 text-gray-100">
      {/* Sidebar */}
      <aside className="w-56 flex-shrink-0 bg-gray-900 border-r border-gray-800 flex flex-col">
        <div className="px-5 py-6 border-b border-gray-800">
          <span className="text-orange-400 font-bold text-lg tracking-wider uppercase">Admin</span>
          <p className="text-gray-500 text-xs mt-1 truncate">{user?.name}</p>
        </div>

        <nav className="flex-1 px-3 py-4 space-y-1">
          {NAV_ITEMS.map(({ to, label, end }) => (
            <NavLink
              key={to}
              to={to}
              end={end}
              className={({ isActive }) =>
                `block px-3 py-2 rounded text-sm font-medium transition-colors ${
                  isActive
                    ? 'bg-orange-500/20 text-orange-400'
                    : 'text-gray-400 hover:text-gray-100 hover:bg-gray-800'
                }`
              }
            >
              {label}
            </NavLink>
          ))}
        </nav>

        <div className="px-3 py-4 border-t border-gray-800 space-y-1">
          <NavLink
            to="/"
            className="block px-3 py-2 rounded text-sm text-gray-400 hover:text-gray-100 hover:bg-gray-800 transition-colors"
          >
            ← Volver al sitio
          </NavLink>
          <button
            onClick={handleLogout}
            className="w-full text-left px-3 py-2 rounded text-sm text-red-400 hover:text-red-300 hover:bg-gray-800 transition-colors"
          >
            Cerrar sesión
          </button>
        </div>
      </aside>

      {/* Main content */}
      <main className="flex-1 overflow-auto">
        <Outlet />
      </main>
    </div>
  )
}
