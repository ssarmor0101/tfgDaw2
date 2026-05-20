import { useState, useEffect, useMemo } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { friendlyError, httpErrorMessage } from '../../utils/friendlyError'
import type { Logro, Resultado, Juego } from '../../types'

// ─── hooks ────────────────────────────────────────────────────────────────────

function useGameName(gameId: number) {
  const [name, setName] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    fetch(API_ROUTES.GAMES.DETAIL(gameId), { headers: { Accept: 'application/json' } })
      .then(async r => {
        if (!r.ok) return
        const json = await r.json() as { data: Juego }
        if (!cancelled) setName(json.data.name)
      })
      .catch(() => {/* silently ignore */})
    return () => { cancelled = true }
  }, [gameId])

  return name
}

function useLogros(gameId: number) {
  const [logros, setLogros] = useState<Logro[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    setLoading(true)
    setError(null)
    fetch(API_ROUTES.LOGROS.BY_GAME(gameId), { headers: { Accept: 'application/json' } })
      .then(async r => {
        if (!r.ok) throw new Error(httpErrorMessage(r.status))
        const json = await r.json() as { data: Logro[] }
        if (!cancelled) setLogros(Array.isArray(json.data) ? json.data : [])
      })
      .catch(err => { if (!cancelled) setError(friendlyError(err)) })
      .finally(() => { if (!cancelled) setLoading(false) })
    return () => { cancelled = true }
  }, [gameId])

  return { logros, loading, error }
}

function useMyResultados(gameId: number, token: string | null) {
  const [resultados, setResultados] = useState<Resultado[]>([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (!token) { setResultados([]); return }
    let cancelled = false
    setLoading(true)
    fetch(API_ROUTES.RESULTADOS.MY_BY_GAME(gameId), {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
    })
      .then(async r => {
        if (!r.ok) return
        const json = await r.json() as { data: Resultado[] }
        if (!cancelled) setResultados(Array.isArray(json.data) ? json.data : [])
      })
      .catch(() => {/* silently ignore */})
      .finally(() => { if (!cancelled) setLoading(false) })
    return () => { cancelled = true }
  }, [gameId, token])

  return { resultados, loading }
}

// ─── helpers ──────────────────────────────────────────────────────────────────

function CheckIcon() {
  return (
    <svg className="w-5 h-5 text-arcade-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m4.5 12.75 6 6 9-13.5" />
    </svg>
  )
}

function LockIcon() {
  return (
    <svg className="w-5 h-5 text-arcade-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
  )
}

function TrophyIcon({ className }: { className?: string }) {
  return (
    <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
        d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
    </svg>
  )
}

// ─── pagination ───────────────────────────────────────────────────────────────

const PER_PAGE = 10

interface PaginationProps {
  page: number
  lastPage: number
  total: number
  onPage: (p: number) => void
}

function Pagination({ page, lastPage, total, onPage }: PaginationProps) {
  if (lastPage <= 1) return null

  // Build page numbers with ellipsis: always show first, last, current ±1
  const pages: (number | '…')[] = []
  const add = (n: number) => { if (!pages.includes(n)) pages.push(n) }

  add(1)
  if (page - 2 > 2) pages.push('…')
  for (let p = Math.max(2, page - 1); p <= Math.min(lastPage - 1, page + 1); p++) add(p)
  if (page + 2 < lastPage - 1) pages.push('…')
  if (lastPage > 1) add(lastPage)

  const btnBase = 'w-9 h-9 flex items-center justify-center rounded text-sm transition-colors'
  const btnActive = `${btnBase} bg-arcade-purple text-white font-semibold`
  const btnIdle = `${btnBase} text-arcade-muted hover:text-white hover:bg-white/5`
  const btnDisabled = `${btnBase} text-arcade-border cursor-not-allowed`

  return (
    <div className="flex items-center justify-between gap-4">
      <p className="text-xs text-arcade-muted">
        {total} logro{total !== 1 ? 's' : ''} en total
      </p>

      <div className="flex items-center gap-1">
        {/* Prev */}
        <button
          onClick={() => onPage(page - 1)}
          disabled={page === 1}
          className={page === 1 ? btnDisabled : btnIdle}
          aria-label="Página anterior"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m15.75 19.5-7.5-7.5 7.5-7.5" />
          </svg>
        </button>

        {pages.map((p, i) =>
          p === '…' ? (
            <span key={`ellipsis-${i}`} className="w-9 h-9 flex items-center justify-center text-arcade-muted text-sm select-none">
              …
            </span>
          ) : (
            <button
              key={p}
              onClick={() => onPage(p)}
              className={p === page ? btnActive : btnIdle}
            >
              {p}
            </button>
          )
        )}

        {/* Next */}
        <button
          onClick={() => onPage(page + 1)}
          disabled={page === lastPage}
          className={page === lastPage ? btnDisabled : btnIdle}
          aria-label="Página siguiente"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
          </svg>
        </button>
      </div>
    </div>
  )
}

// ─── skeleton ─────────────────────────────────────────────────────────────────

function TableSkeleton() {
  return (
    <div className="animate-pulse space-y-px">
      {Array.from({ length: 6 }).map((_, i) => (
        <div key={i} className="flex items-center gap-4 px-5 py-4 bg-arcade-card rounded-lg">
          <div className="w-3 h-8 rounded bg-white/10 shrink-0" />
          <div className="flex-1 space-y-2">
            <div className="h-3.5 w-1/3 bg-white/10 rounded" />
            <div className="h-3 w-2/3 bg-white/10 rounded" />
          </div>
          <div className="w-5 h-5 rounded-full bg-white/10 shrink-0" />
        </div>
      ))}
    </div>
  )
}

// ─── main view ────────────────────────────────────────────────────────────────

export function GameLogros() {
  const { id } = useParams<{ id: string }>()
  const gameId = Number(id)

  const { user, token } = useAuth()
  const gameName = useGameName(gameId)
  const { logros, loading: logrosLoading, error } = useLogros(gameId)
  const { resultados, loading: resultadosLoading } = useMyResultados(gameId, token)

  // Set of logro ids unlocked by the current user
  const unlockedIds = new Set(resultados.map(r => r.logro_id))

  const total    = logros.length
  const unlocked = user ? unlockedIds.size : 0
  const pct      = total > 0 ? Math.round((unlocked / total) * 100) : 0

  const isLoading = logrosLoading || (!!token && resultadosLoading)

  // ── Pagination ──
  const [page, setPage] = useState(1)
  const lastPage = Math.max(1, Math.ceil(total / PER_PAGE))

  // Reset to page 1 when data changes
  useEffect(() => { setPage(1) }, [total])

  const pageItems = useMemo(
    () => logros.slice((page - 1) * PER_PAGE, page * PER_PAGE),
    [logros, page]
  )

  return (
    <main className="max-w-5xl mx-auto px-6 py-8 space-y-8">

      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-xs text-arcade-muted uppercase tracking-widest">
        <Link to="/juegos" className="hover:text-white transition-colors">Juegos</Link>
        <span>/</span>
        {gameName ? (
          <Link to={`/juegos/${gameId}`} className="hover:text-white transition-colors">
            {gameName}
          </Link>
        ) : (
          <span className="w-24 h-3 bg-white/10 rounded animate-pulse inline-block align-middle" />
        )}
        <span>/</span>
        <span className="text-white">Logros</span>
      </nav>

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-end gap-4 justify-between">
        <div>
          <p className="text-xs font-semibold text-arcade-purple uppercase tracking-widest mb-1.5">
            Progreso de jugador
          </p>
          <h1 className="text-3xl font-bold text-white">
            Logros de&nbsp;
            <span className="text-arcade-purple">
              {gameName ?? '…'}
            </span>
          </h1>
          <p className="text-arcade-muted text-sm mt-2 max-w-lg leading-relaxed">
            {user
              ? 'Consulta todos los hitos disponibles y tu progreso actual en este juego.'
              : 'Inicia sesión para ver tus logros desbloqueados. Aquí puedes explorar todos los hitos disponibles.'}
          </p>
        </div>

        {/* Progress pill */}
        <div className="shrink-0 flex flex-col items-center bg-arcade-surface border border-arcade-border rounded-xl px-8 py-4 min-w-[140px]">
          <span className="text-4xl font-bold text-white tabular-nums">{pct}%</span>
          <span className="text-xs text-arcade-muted mt-1 uppercase tracking-widest">
            {unlocked}/{total} logros
          </span>
        </div>
      </div>

      {/* Progress bar */}
      <div className="w-full h-2 bg-arcade-card rounded-full overflow-hidden">
        <div
          className="h-full bg-gradient-to-r from-arcade-purple to-pink-500 rounded-full transition-all duration-500"
          style={{ width: `${pct}%` }}
        />
      </div>

      {/* Table */}
      {error ? (
        <ErrorMessage message={error} />
      ) : isLoading ? (
        <TableSkeleton />
      ) : total === 0 ? (
        <div className="flex flex-col items-center justify-center py-20 gap-4 text-center">
          <TrophyIcon className="w-12 h-12 text-arcade-border" />
          <div>
            <p className="text-white font-semibold text-sm uppercase tracking-widest">Sin logros</p>
            <p className="text-arcade-muted text-xs mt-1.5 max-w-[260px]">
              Este juego aún no tiene logros registrados.
            </p>
          </div>
        </div>
      ) : (
        <div className="bg-arcade-surface border border-arcade-border rounded-xl overflow-hidden">

          {/* Table header */}
          <div className="grid grid-cols-[1fr_2fr_56px] gap-4 px-5 py-3 border-b border-arcade-border
                          text-xs font-semibold text-arcade-muted uppercase tracking-widest">
            <span>Título</span>
            <span>Descripción</span>
            <span className="text-center">Estado</span>
          </div>

          {/* Rows */}
          <div className="divide-y divide-arcade-border/40">
            {pageItems.map(logro => {
              const done = user ? unlockedIds.has(logro.id) : false

              return (
                <div
                  key={logro.id}
                  className={`grid grid-cols-[1fr_2fr_56px] gap-4 px-5 py-4 items-center
                    transition-colors hover:bg-white/[0.02]
                    ${done ? '' : 'opacity-60'}`}
                >
                  {/* Title with accent bar */}
                  <div className="flex items-center gap-3 min-w-0">
                    <span className={`w-0.5 h-8 rounded-full shrink-0
                      ${done ? 'bg-arcade-purple' : 'bg-arcade-border'}`}
                    />
                    <span className={`font-semibold text-sm truncate
                      ${done ? 'text-white' : 'text-arcade-muted'}`}>
                      {logro.name}
                    </span>
                  </div>

                  {/* Description */}
                  <p className={`text-sm leading-relaxed line-clamp-2
                    ${done ? 'text-gray-300' : 'text-arcade-muted/70'}`}>
                    {logro.description ?? '—'}
                  </p>

                  {/* Status icon */}
                  <div className="flex justify-center">
                    {!user || !done ? <LockIcon /> : <CheckIcon />}
                  </div>
                </div>
              )
            })}
          </div>

          {/* Pagination footer */}
          {lastPage > 1 && (
            <div className="px-5 py-4 border-t border-arcade-border">
              <Pagination
                page={page}
                lastPage={lastPage}
                total={total}
                onPage={setPage}
              />
            </div>
          )}
        </div>
      )}

      {/* Footer stats */}
      {!isLoading && total > 0 && (
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5">
            <p className="text-xs text-arcade-muted uppercase tracking-widest mb-2">Total logros</p>
            <p className="text-3xl font-bold text-white">{total}</p>
          </div>
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5">
            <p className="text-xs text-arcade-muted uppercase tracking-widest mb-2">Conseguidos</p>
            <p className="text-3xl font-bold text-arcade-green">{unlocked}</p>
          </div>
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5">
            <p className="text-xs text-arcade-muted uppercase tracking-widest mb-2">Progreso</p>
            <p className={`text-3xl font-bold ${pct === 100 ? 'text-yellow-400' : 'text-arcade-purple'}`}>
              {pct}%
            </p>
          </div>
        </div>
      )}

      {/* Login prompt for anonymous */}
      {!user && total > 0 && (
        <div className="flex items-center gap-4 px-5 py-4 bg-arcade-card border border-arcade-border/50 rounded-xl">
          <svg className="w-5 h-5 text-arcade-purple shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
              d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
          </svg>
          <p className="text-sm text-gray-400 flex-1">
            <Link to="/login" className="text-arcade-purple hover:underline font-medium">
              Inicia sesión
            </Link>
            {' '}para ver qué logros ya has desbloqueado en este juego.
          </p>
        </div>
      )}

    </main>
  )
}
