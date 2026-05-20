import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { friendlyError, httpErrorMessage } from '../../utils/friendlyError'
import type { ScoreEntry, Rol } from '../../types'

// ─── types ────────────────────────────────────────────────────────────────────

interface DashboardUser {
  id: number
  name: string
  email: string
  rol_id: number
  rol?: Rol
  created_at?: string | null
}

interface DashboardData {
  recent_puntuaciones: ScoreEntry[]
  recent_users: DashboardUser[]
  today_partidas: number
  total_users: number
  total_puntuaciones: number
}

// ─── hook ─────────────────────────────────────────────────────────────────────

function useDashboard(token: string | null) {
  const [data, setData] = useState<DashboardData | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (!token) return
    let cancelled = false
    setLoading(true)
    fetch(API_ROUTES.ADMIN.DASHBOARD, {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
    })
      .then(async r => {
        const json = await r.json() as { data: DashboardData; status?: { message?: string } }
        if (!r.ok) throw new Error(json.status?.message ?? httpErrorMessage(r.status))
        if (!cancelled) setData(json.data)
      })
      .catch(err => { if (!cancelled) setError(friendlyError(err)) })
      .finally(() => { if (!cancelled) setLoading(false) })
    return () => { cancelled = true }
  }, [token])

  return { data, loading, error }
}

// ─── helpers ──────────────────────────────────────────────────────────────────

function formatDate(raw: string | null | undefined): string {
  if (!raw) return '—'
  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  }).format(new Date(raw))
}

function formatScore(n: number): string {
  return n.toLocaleString('es-ES')
}

// ─── sub-components ───────────────────────────────────────────────────────────

function StatCard({
  label, value, icon, accent = 'orange',
}: {
  label: string
  value: string | number
  icon: React.ReactNode
  accent?: 'orange' | 'blue' | 'green'
}) {
  const accentMap = {
    orange: 'bg-orange-500/10 border-orange-500/20 text-orange-400',
    blue:   'bg-blue-500/10  border-blue-500/20  text-blue-400',
    green:  'bg-green-500/10 border-green-500/20 text-green-400',
  }
  return (
    <div className="bg-gray-900 border border-gray-800 rounded-xl p-5 flex items-center gap-4">
      <div className={`w-11 h-11 rounded-lg border flex items-center justify-center shrink-0 ${accentMap[accent]}`}>
        {icon}
      </div>
      <div>
        <p className="text-xs text-gray-500 uppercase tracking-widest mb-0.5">{label}</p>
        <p className="text-2xl font-bold text-gray-100 tabular-nums">{value}</p>
      </div>
    </div>
  )
}

function SkeletonRow({ cols = 3 }: { cols?: number }) {
  return (
    <div className={`grid gap-4 px-4 py-3 border-b border-gray-800/60`}
      style={{ gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))` }}>
      {Array.from({ length: cols }).map((_, i) => (
        <div key={i} className="h-3.5 bg-gray-800 rounded animate-pulse" />
      ))}
    </div>
  )
}

// ─── main view ────────────────────────────────────────────────────────────────

export function AdminDashboard() {
  const { token } = useAuth()
  const { data, loading, error } = useDashboard(token)

  const today = new Intl.DateTimeFormat('es-ES', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
  }).format(new Date())

  return (
    <div className="p-8 space-y-8 max-w-6xl">

      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-gray-100 uppercase tracking-widest">Dashboard</h1>
        <p className="text-gray-500 text-sm mt-1 capitalize">{today}</p>
      </div>

      {error && (
        <div className="px-4 py-3 bg-red-900/20 border border-red-700/40 rounded-lg text-red-400 text-sm">
          Error al cargar el dashboard: {error}
        </div>
      )}

      {/* Stat cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <StatCard
          label="Partidas hoy"
          value={loading ? '…' : (data?.today_partidas ?? 0)}
          accent="orange"
          icon={
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>
          }
        />
        <StatCard
          label="Total usuarios"
          value={loading ? '…' : (data?.total_users ?? 0)}
          accent="blue"
          icon={
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
          }
        />
        <StatCard
          label="Total puntuaciones"
          value={loading ? '…' : (data?.total_puntuaciones ?? 0)}
          accent="green"
          icon={
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
          }
        />
      </div>

      {/* Tables row */}
      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {/* Últimas 5 puntuaciones */}
        <section className="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div className="flex items-center justify-between px-5 py-4 border-b border-gray-800">
            <h2 className="text-sm font-semibold text-gray-100 uppercase tracking-widest">
              Últimas puntuaciones
            </h2>
            <Link
              to="/admin/puntuaciones"
              className="text-xs text-orange-400 hover:text-orange-300 transition-colors"
            >
              Ver todas →
            </Link>
          </div>

          {/* Header */}
          <div className="grid grid-cols-[1fr_1fr_auto] gap-3 px-5 py-2.5
                          text-[10px] font-semibold text-gray-600 uppercase tracking-widest
                          border-b border-gray-800/60">
            <span>Jugador / Juego</span>
            <span>Puntuación</span>
            <span>Fecha</span>
          </div>

          {/* Rows */}
          <div className="divide-y divide-gray-800/60">
            {loading
              ? Array.from({ length: 5 }).map((_, i) => <SkeletonRow key={i} cols={3} />)
              : !data || data.recent_puntuaciones.length === 0
                ? (
                  <div className="flex flex-col items-center justify-center py-10 text-center gap-2">
                    <p className="text-gray-600 text-xs uppercase tracking-widest">Sin puntuaciones</p>
                  </div>
                )
                : data.recent_puntuaciones.map(entry => (
                  <div key={entry.id}
                    className="grid grid-cols-[1fr_1fr_auto] gap-3 px-5 py-3 items-center hover:bg-gray-800/40 transition-colors">
                    <div className="min-w-0">
                      <p className="text-sm text-gray-200 font-medium truncate">
                        {entry.user?.name ?? `#${entry.user_id}`}
                      </p>
                      <p className="text-xs text-gray-500 truncate">
                        {entry.juego?.name ?? `Juego #${entry.juego_id}`}
                      </p>
                    </div>
                    <span className="text-sm font-bold text-orange-400 tabular-nums">
                      {formatScore(entry.puntuacion)}
                    </span>
                    <span className="text-xs text-gray-500 tabular-nums whitespace-nowrap">
                      {formatDate(entry.created_at)}
                    </span>
                  </div>
                ))
            }
          </div>
        </section>

        {/* Últimos 5 usuarios */}
        <section className="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
          <div className="flex items-center justify-between px-5 py-4 border-b border-gray-800">
            <h2 className="text-sm font-semibold text-gray-100 uppercase tracking-widest">
              Últimos usuarios
            </h2>
            <Link
              to="/admin/usuarios"
              className="text-xs text-orange-400 hover:text-orange-300 transition-colors"
            >
              Ver todos →
            </Link>
          </div>

          {/* Header */}
          <div className="grid grid-cols-[1fr_auto_auto] gap-3 px-5 py-2.5
                          text-[10px] font-semibold text-gray-600 uppercase tracking-widest
                          border-b border-gray-800/60">
            <span>Usuario</span>
            <span>Rol</span>
            <span>Registro</span>
          </div>

          {/* Rows */}
          <div className="divide-y divide-gray-800/60">
            {loading
              ? Array.from({ length: 5 }).map((_, i) => <SkeletonRow key={i} cols={3} />)
              : !data || data.recent_users.length === 0
                ? (
                  <div className="flex flex-col items-center justify-center py-10 text-center gap-2">
                    <p className="text-gray-600 text-xs uppercase tracking-widest">Sin usuarios</p>
                  </div>
                )
                : data.recent_users.map(u => {
                  const rolName = u.rol?.name ?? (u.rol_id === 1 ? 'Administrador' : 'Usuario')
                  const isAdmin = u.rol_id === 1
                  return (
                    <div key={u.id}
                      className="grid grid-cols-[1fr_auto_auto] gap-3 px-5 py-3 items-center hover:bg-gray-800/40 transition-colors">
                      <div className="min-w-0">
                        <p className="text-sm text-gray-200 font-medium truncate">{u.name}</p>
                        <p className="text-xs text-gray-500 truncate">{u.email}</p>
                      </div>
                      <span className={`text-[10px] font-semibold uppercase tracking-widest px-2 py-0.5 rounded
                        ${isAdmin
                          ? 'bg-orange-500/15 text-orange-400 border border-orange-500/20'
                          : 'bg-gray-800 text-gray-400 border border-gray-700'
                        }`}>
                        {rolName}
                      </span>
                      <span className="text-xs text-gray-500 tabular-nums whitespace-nowrap">
                        {formatDate(u.created_at)}
                      </span>
                    </div>
                  )
                })
            }
          </div>
        </section>

      </div>
    </div>
  )
}
